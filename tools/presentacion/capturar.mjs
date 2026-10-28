/**
 * Capturas reales de la app (Chrome headless + login real contra localhost:8080).
 *
 * Uso:  node tools/presentacion/capturar.mjs
 *   - docs/capturas/*.png        página completa (evidencia)
 *   - docs/capturas/deck/*.png   viewport 1600x1000 @1.5x (las que usa la presentación)
 *
 * Requiere la app levantada y `npm install` (puppeteer-core).
 * Chrome: por defecto el de Windows; se puede cambiar con CHROME_PATH.
 */
import puppeteer from 'puppeteer-core';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const raiz = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');
const BASE = process.env.BASE || 'http://localhost:8080';
const CHROME =
  process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';

const CAPTURAS = path.join(raiz, 'docs', 'capturas');
const DECK = path.join(CAPTURAS, 'deck');
fs.mkdirSync(DECK, { recursive: true });

const rutas = [
  ['02-tablero', '/', 'claro'],
  ['03-tablero-oscuro', '/', 'oscuro'],
  ['04-tareas-kanban', '/tareas', 'claro'],
  ['05-stock', '/stock', 'claro'],
  ['06-altas', '/altas', 'claro'],
  ['07-alta-detalle', '/altas/1', 'claro'],
  ['08-tecnicos', '/tecnicos', 'claro'],
  ['09-tecnico-detalle', '/tecnicos/1', 'claro'],
  ['10-usuarios', '/usuarios', 'claro'],
  ['11-busqueda', '/buscar?q=aguirre', 'claro'],
];

const browser = await puppeteer.launch({
  executablePath: CHROME,
  headless: 'new',
  args: ['--no-sandbox', '--disable-dev-shm-usage', '--font-render-hinting=none'],
});

const problemas = [];
const page = await browser.newPage();
await page.setViewport({ width: 1600, height: 1000, deviceScaleFactor: 1.5 });
page.on('console', (m) => m.type() === 'error' && problemas.push('[console] ' + m.text()));
page.on('pageerror', (e) => problemas.push('[pageerror] ' + e.message));
page.on('response', (r) => r.status() >= 400 && problemas.push(`[http ${r.status()}] ${r.url()}`));

const montado = (p, timeout = 20000) =>
  p.waitForFunction(
    () => {
      const a = document.querySelector('aside');
      return a && !a.hasAttribute('x-cloak') && a.getBoundingClientRect().width > 100;
    },
    { timeout },
  );

async function entrar(p) {
  await p.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
  await p.waitForFunction(() => document.querySelector('input[name=_token]') !== null);
  await p.evaluate(() => {
    const set = (sel, v) => {
      const el = document.querySelector(sel);
      el.value = v;
      el.dispatchEvent(new Event('input', { bubbles: true }));
    };
    set('input[name=email]', 'admin@fieldops.test');
    set('input[name=password]', 'password');
  });
  await Promise.all([montado(p, 30000), p.evaluate(() => document.querySelector('form').requestSubmit())]);
}

console.log('login...');
await entrar(page);
console.log('sesión iniciada en', page.url());

for (const [nombre, ruta, tema] of rutas) {
  await page.goto(BASE + ruta, { waitUntil: 'load' });
  await montado(page);
  await page.evaluate((t) => document.documentElement.classList.toggle('dark', t === 'oscuro'), tema);
  await new Promise((r) => setTimeout(r, 900)); // animaciones y gráficos
  await page.screenshot({ path: path.join(CAPTURAS, `${nombre}.png`), fullPage: true });
  await page.screenshot({ path: path.join(DECK, `${nombre}.png`) });
  console.log('ok', nombre);
}

// Flujos con modal (solo para la presentación)
await page.goto(`${BASE}/tareas`, { waitUntil: 'load' });
await montado(page);
await page.evaluate(() => {
  [...document.querySelectorAll('button')].find((b) => b.innerText.includes('Nueva tarea')).click();
});
await page.waitForFunction(() => {
  const f = document.querySelector('#form-nueva-tarea');
  return f && getComputedStyle(f).display !== 'none';
});
await page.evaluate(() => {
  const f = document.querySelector('#form-nueva-tarea');
  f.querySelector('input[name=titulo]').value = 'Reparación de acometida - OT 412889';
  f.querySelector('input[name=tarea]').value = 'Instalacion';
  f.querySelector('input[name=a_quien]').value = 'jropero';
  f.querySelector('textarea[name=descripcion]').value =
    'Cliente sin servicio desde el martes: revisar el splitter del gabinete y medir potencia óptica.';
});
await new Promise((r) => setTimeout(r, 700));
await page.screenshot({ path: path.join(DECK, '13-nueva-tarea.png') });
console.log('ok 13-nueva-tarea');

await page.goto(`${BASE}/tareas`, { waitUntil: 'load' });
await montado(page);
await page.evaluate(() => {
  (document.querySelector('[data-lista="En curso"] article') || document.querySelector('article')).click();
});
await page.waitForFunction(() => document.body.innerText.includes('Asignada a'), { timeout: 15000 });
await new Promise((r) => setTimeout(r, 900));
await page.screenshot({ path: path.join(DECK, '14-tarea-detalle.png') });
console.log('ok 14-tarea-detalle');

// Login, sin sesión
const ctx = await browser.createBrowserContext();
const p2 = await ctx.newPage();
await p2.setViewport({ width: 1600, height: 1000, deviceScaleFactor: 1.5 });
await p2.goto(`${BASE}/login`, { waitUntil: 'domcontentloaded' });
await p2.waitForFunction(() => document.querySelector('input[name=_token]') !== null);
await new Promise((r) => setTimeout(r, 700));
await p2.screenshot({ path: path.join(CAPTURAS, '01-login.png'), fullPage: true });
await p2.screenshot({ path: path.join(DECK, '01-login.png') });
await ctx.close();
console.log('ok 01-login');

await browser.close();

console.log('\nproblemas detectados:', problemas.length ? [...new Set(problemas)].join('\n') : 'ninguno');
