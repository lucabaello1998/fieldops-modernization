/**
 * Verificación funcional end-to-end contra la app real (localhost:8080).
 *
 * Uso:  node tools/verificar.mjs        (con la app levantada y `npm install` hecho)
 * Chrome: por defecto el de Windows; se puede cambiar con CHROME_PATH.
 *
 * Maneja la app con Chrome headless: login, arrastre de una tarjeta del Kanban, alta de tarea,
 * movimiento de stock, búsqueda con comillas y operadores, alta y baja de usuario. Cada paso se
 * valida contra el DOM y las respuestas HTTP.
 */
import puppeteer from 'puppeteer-core';

const CHROME =
  process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const BASE = process.env.BASE || 'http://localhost:8080';
const ok = [];
const fail = [];
const check = (cond, msg) => (cond ? ok : fail).push(msg);

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: 'new',
  args: ['--no-sandbox', '--disable-dev-shm-usage'],
});
const page = await browser.newPage();
await page.setViewport({ width: 1600, height: 1000 });
const errores = [];
page.on('pageerror', (e) => errores.push('pageerror: ' + e.message));
page.on('response', (r) => {
  if (r.status() >= 400) errores.push(`http ${r.status()} ${r.url()}`);
});

// Espera a que el layout de la app este realmente montado y, si no lo esta,
// devuelve un diagnostico en vez de cortar el script.
async function esperarLayout() {
  try {
    await page.waitForFunction(
      () => {
        const a = document.querySelector('aside');
        return a && !a.hasAttribute('x-cloak');
      },
      { timeout: 15000 },
    );
    return 'ok';
  } catch (e) {
    const diag = await page.evaluate(() => ({
      url: location.href,
      texto: document.body.innerText.slice(0, 200).replace(/\s+/g, ' '),
    }));
    return 'FALLO ' + JSON.stringify(diag);
  }
}
async function login() {
  await page.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
  await page.waitForFunction(() => document.querySelector('input[name=_token]') !== null);
  await Promise.all([
    page.waitForFunction(() => document.querySelector('aside') !== null, { timeout: 30000 }),
    page.evaluate(() => document.querySelector('form').requestSubmit()),
  ]);
}

async function irA(ruta) {
  await page.goto(BASE + ruta, { waitUntil: 'load' });
  await page.waitForFunction(() => {
    const a = document.querySelector('aside');
    return a && !a.hasAttribute('x-cloak');
  }, { timeout: 20000 });
}

async function arrastrar(desde, hacia) {
  const a = await (await page.$(desde)).boundingBox();
  const b = await (await page.$(hacia)).boundingBox();
  await page.mouse.move(a.x + a.width / 2, a.y + a.height / 2);
  await page.mouse.down();
  const pasos = 12;
  for (let i = 1; i <= pasos; i++) {
    await page.mouse.move(
      a.x + a.width / 2 + ((b.x + b.width / 2 - a.x - a.width / 2) * i) / pasos,
      a.y + a.height / 2 + ((b.y + 120 - a.y - a.height / 2) * i) / pasos,
    );
    await new Promise((r) => setTimeout(r, 25));
  }
  await page.mouse.up();
  await new Promise((r) => setTimeout(r, 1200));
}

console.log('1) login');
await login();
check(page.url() === BASE + '/', `login redirige al tablero (${page.url()})`);

// ---------------------------------------------------------------- kanban
console.log('2) kanban: drag and drop');
await irA('/tareas');
const antes = await page.evaluate(() => {
  const col = document.querySelector('[data-lista="Pendiente"]');
  const card = col.querySelector('article');
  return { id: card.dataset.id, titulo: card.querySelector('h3').innerText.trim() };
});
await arrastrar('[data-lista="Pendiente"] article:first-child', '[data-lista="En curso"]');
await page.reload({ waitUntil: 'load' });
await new Promise((r) => setTimeout(r, 800));
const despues = await page.evaluate((id) => {
  const col = document.querySelector('[data-lista="En curso"]');
  const card = col.querySelector(`article[data-id="${id}"]`);
  return { enCurso: !!card, titulo: card ? card.querySelector('h3').innerText.trim() : null };
}, antes.id);
check(despues.enCurso, `la tarea #${antes.id} ("${antes.titulo}") quedo en "En curso" tras el drag`);

const json = await page.evaluate(async (id) => {
  const r = await fetch(`/tareas/${id}`, { headers: { Accept: 'application/json' } });
  return r.json();
}, antes.id);
check(
  /movio la tarea de "Pendiente" a "En curso"/.test(json.mensaje),
  `quedo registrada la trazabilidad en la bitacora de #${antes.id}`,
);
check(errores_limpio(), 'sin errores de red ni de JS en el kanban');

function errores_limpio() {
  return errores.length === 0;
}

// ---------------------------------------------------------------- nueva tarea
console.log('3) crear tarea');
const tituloNueva = 'Tarea de verificacion ' + Date.now();
await page.evaluate(() => {
  const b = [...document.querySelectorAll('button')].find((x) => x.innerText.includes('Nueva tarea'));
  b.click();
});
await page.waitForFunction(() => {
  const f = document.querySelector('#form-nueva-tarea');
  return f && getComputedStyle(f).display !== 'none';
});
await Promise.all([
  page.waitForNavigation({ waitUntil: 'load', timeout: 30000 }),
  page.evaluate((t) => {
    const f = document.querySelector('#form-nueva-tarea');
    f.querySelector('input[name=titulo]').value = t;
    f.querySelector('input[name=tarea]').value = 'Instalacion';
    f.querySelector('input[name=a_quien]').value = 'jperez';
    f.querySelector('textarea[name=descripcion]').value = 'Generada por la verificacion automatica.';
    f.submit();
  }, tituloNueva),
]);
const layout1 = await esperarLayout();
if (layout1 !== 'ok') fail.push('tras crear la tarea: ' + layout1);
const creada = await page.evaluate((t) => {
  const cards = [...document.querySelectorAll('article')];
  const card = cards.find((c) => c.innerText.includes(t));
  return {
    existe: !!card,
    columna: card ? card.closest('[data-lista]').dataset.lista : null,
    flash: !!document.querySelector('.toast, [data-flash]'),
  };
}, tituloNueva);
check(creada.existe && creada.columna === 'Pendiente', `tarea nueva creada en "Pendiente"`);

// ---------------------------------------------------------------- stock
console.log('4) stock: registrar movimiento');
await irA('/stock');
await Promise.all([
  page.waitForNavigation({ waitUntil: 'load', timeout: 30000 }),
  page.evaluate(() => {
    const f = document.querySelector('form[action$="/stock/movimiento"]');
    const set = (n, v) => {
      const el = f.querySelector(`[name=${n}]`);
      el.value = v;
      el.dispatchEvent(new Event('change', { bubbles: true }));
    };
    set('tipo', 'Alta de material');
    set('centro', 'Central');
    set('cantidad', '7');
    set('obs', 'Verificacion automatica');
    const mat = f.querySelector('[name=material]');
    mat.value = mat.value || 'Cable drop verificado';
    f.submit();
  }),
]);
const layout2 = await esperarLayout();
if (layout2 !== 'ok') fail.push('tras el movimiento de stock: ' + layout2);
const mov = await page.evaluate(() => ({
  url: location.href,
  flash: document.body.innerText.includes('Movimiento registrado'),
  fila: document.body.innerText.includes('Verificacion automatica'),
}));
check(mov.flash, 'el movimiento de stock se registra y muestra confirmacion');

// ---------------------------------------------------------------- buscar
console.log('5) busqueda');
await irA('/buscar?q=aguirre');
const bus = await page.evaluate(() => ({
  marcas: document.querySelectorAll('mark').length,
  texto: document.body.innerText.length,
  hayResultados: /T[eé]cnicos|Resultados|Altas/i.test(document.body.innerText),
}));
check(bus.marcas > 0, `la busqueda resalta el termino (${bus.marcas} marcas)`);

// inyeccion: no debe romper ni devolver todo
await irA("/buscar?q=zzz' OR 1=1 -- -");
const inj = await page.evaluate(() => ({
  error: /SQLSTATE|Whoops|Server Error/i.test(document.body.innerText),
  texto: document.body.innerText.slice(0, 120).replace(/\n+/g, ' '),
}));
check(!inj.error, `la busqueda tolera comillas y operadores sin romper (${inj.texto})`);

// ---------------------------------------------------------------- usuarios
console.log('6) usuarios: alta y baja');
await irA('/usuarios');
const mail = `verificacion${Date.now()}@fieldops.test`;
await Promise.all([
  page.waitForNavigation({ waitUntil: 'load', timeout: 30000 }),
  page.evaluate((m) => {
    const f = [...document.querySelectorAll('form')].find(
      (x) => x.method === 'post' && x.action.endsWith('/usuarios'),
    );
    f.querySelector('[name=name]').value = 'Usuario Verificacion';
    f.querySelector('[name=email]').value = m;
    f.querySelector('[name=password]').value = 'password123';
    f.submit();
  }, mail),
]);
const layout3 = await esperarLayout();
if (layout3 !== 'ok') fail.push('tras crear el usuario: ' + layout3);
// La lista esta paginada: se busca al usuario por correo con el filtro de la propia pantalla.
await irA(`/usuarios?q=${encodeURIComponent(mail)}`);
const altaUsuario = await page.evaluate((m) => document.body.innerText.includes(m), mail);
check(altaUsuario, `usuario creado (${mail})`);

const baja = await page.evaluate(async (m) => {
  const fila = [...document.querySelectorAll('tr')].find((t) => t.innerText.includes(m));
  if (!fila) return false;
  const f = fila.querySelector('form[action*="/usuarios/"]');
  const id = f.action.split('/').pop();
  const token = f.querySelector('[name=_token]').value;
  const r = await fetch(`/usuarios/${id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ _method: 'DELETE', _token: token }),
  });
  return r.ok || r.redirected;
}, mail);
check(baja, 'usuario de verificacion eliminado (limpieza)');

await browser.close();

console.log('\n=== OK (%d) ===', ok.length);
ok.forEach((o) => console.log('  + ' + o));
console.log('\n=== FALLAS (%d) ===', fail.length);
fail.forEach((f) => console.log('  - ' + f));
console.log('\n=== errores de red/JS ===');
console.log(errores.length ? [...new Set(errores)].join('\n') : 'ninguno');
process.exit(fail.length ? 1 : 0);
