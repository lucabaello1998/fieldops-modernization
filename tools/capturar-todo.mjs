/**
 * Pasada visual completa: captura TODAS las pantallas en tema claro y oscuro,
 * mas los modales, el 404 y el 403.
 *
 * Uso:  node tools/capturar-todo.mjs
 *   -> docs/capturas/qa/<pantalla>-<tema>.png   (1440x900 @1x)
 *
 * Requiere la app levantada y `npm install` (puppeteer-core).
 */
import puppeteer from 'puppeteer-core';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const raiz = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const BASE = process.env.BASE || 'http://localhost:8080';
const CHROME =
  process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SALIDA = path.join(raiz, 'docs', 'capturas', 'qa');
fs.mkdirSync(SALIDA, { recursive: true });

const PANTALLAS = [
  ['tablero', '/'],
  ['tareas', '/tareas'],
  ['tareas-filtro', '/tareas?prioridad=1'],
  ['altas', '/altas'],
  ['alta-detalle', '/altas/1'],
  ['stock', '/stock'],
  ['stock-critico', '/stock?nivel=critico&orden=cantidad_asc'],
  ['tecnicos', '/tecnicos'],
  ['tecnico-detalle', '/tecnicos/1'],
  ['usuarios', '/usuarios'],
  ['usuarios-filtro', '/usuarios?role=Tecnico&activo=1'],
  ['buscar', '/buscar?q=aguirre'],
  ['404', '/altas/999999'],
];

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: 'new',
  args: ['--no-sandbox', '--disable-dev-shm-usage', '--font-render-hinting=none'],
});

const problemas = [];
let esperando404 = false;
const page = await browser.newPage();
await page.setViewport({ width: 1440, height: 900 });
page.on('console', (m) => {
  if (m.type() !== 'error') return;
  // El 404 se pide a proposito: su "Failed to load resource" es el esperado.
  if (esperando404 && /404/.test(m.text())) return;
  problemas.push('[console] ' + m.text());
});
page.on('pageerror', (e) => problemas.push('[pageerror] ' + e.message));

const montado = (p, timeout = 20000) =>
  p.waitForFunction(
    () => {
      const a = document.querySelector('aside');
      return a && !a.hasAttribute('x-cloak') && a.getBoundingClientRect().width > 100;
    },
    { timeout },
  );

async function entrar(p, mail = 'admin@fieldops.test', clave = 'password') {
  await p.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
  await p.waitForFunction(() => document.querySelector('input[name=_token]') !== null);
  await p.evaluate(
    (m, c) => {
      const set = (sel, v) => {
        const el = document.querySelector(sel);
        el.value = v;
        el.dispatchEvent(new Event('input', { bubbles: true }));
      };
      set('input[name=email]', m);
      set('input[name=password]', c);
    },
    mail,
    clave,
  );
  await Promise.all([montado(p, 30000), p.evaluate(() => document.querySelector('form').requestSubmit())]);
}

const tema = (t) =>
  page.evaluate((oscuro) => document.documentElement.classList.toggle('dark', oscuro), t === 'oscuro');

const shot = async (nombre) => {
  await new Promise((r) => setTimeout(r, 700));
  await page.screenshot({ path: path.join(SALIDA, `${nombre}.png`) });
  console.log('  ok', nombre);
};

// ------------------------------------------------------------- login (guest)
const ctx = await browser.createBrowserContext();
const invitado = await ctx.newPage();
await invitado.setViewport({ width: 1440, height: 900 });
await invitado.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
await invitado.waitForFunction(() => document.querySelector('input[name=_token]') !== null);
await new Promise((r) => setTimeout(r, 600));
await invitado.screenshot({ path: path.join(SALIDA, 'login-claro.png') });
await invitado.evaluate(() => document.documentElement.classList.add('dark'));
await new Promise((r) => setTimeout(r, 400));
await invitado.screenshot({ path: path.join(SALIDA, 'login-oscuro.png') });
await ctx.close();
console.log('  ok login');

// ------------------------------------------------------------ sesión admin
await entrar(page);
console.log('sesión admin en', page.url());

for (const [nombre, ruta] of PANTALLAS) {
  for (const t of ['claro', 'oscuro']) {
    esperando404 = nombre === '404';
    await page.goto(BASE + ruta, { waitUntil: 'load' });
    // El 404 no tiene layout; el resto si.
    if (nombre !== '404') await montado(page);
    await tema(t);
    await shot(`${nombre}-${t}`);
    esperando404 = false;
  }
}

// ------------------------------------------------------------------ modales
for (const t of ['claro', 'oscuro']) {
  await page.goto(`${BASE}/tareas`, { waitUntil: 'load' });
  await montado(page);
  await tema(t);
  await page.evaluate(() => {
    [...document.querySelectorAll('button')].find((b) => b.innerText.includes('Nueva tarea')).click();
  });
  await page.waitForFunction(() => {
    const f = document.querySelector('#form-nueva-tarea');
    return f && getComputedStyle(f).display !== 'none';
  });
  await shot(`modal-nueva-tarea-${t}`);

  await page.goto(`${BASE}/tareas`, { waitUntil: 'load' });
  await montado(page);
  await tema(t);
  await page.evaluate(() => {
    (document.querySelector('[data-lista="En curso"] article') || document.querySelector('article')).click();
  });
  await page.waitForFunction(() => document.body.innerText.includes('Asignada a'), { timeout: 15000 });
  await shot(`modal-tarea-detalle-${t}`);
}

// --------------------------------------------------- 403 con sesión técnico
const ctxTec = await browser.createBrowserContext();
const tec = await ctxTec.newPage();
await tec.setViewport({ width: 1440, height: 900 });
await tec.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
await tec.waitForFunction(() => document.querySelector('input[name=_token]') !== null);
await tec.evaluate(() => {
  const set = (sel, v) => {
    const el = document.querySelector(sel);
    el.value = v;
    el.dispatchEvent(new Event('input', { bubbles: true }));
  };
  set('input[name=email]', 'tecnico@fieldops.test');
  set('input[name=password]', 'password');
});
await Promise.all([tec.waitForNavigation({ waitUntil: 'load' }), tec.evaluate(() => document.querySelector('form').requestSubmit())]);
await tec.goto(`${BASE}/usuarios`, { waitUntil: 'load' });
await new Promise((r) => setTimeout(r, 600));
await tec.screenshot({ path: path.join(SALIDA, '403-tecnico.png') });
console.log('  ok 403-tecnico');
await ctxTec.close();

await browser.close();

console.log('\nproblemas detectados:', problemas.length ? [...new Set(problemas)].join('\n') : 'ninguno');
