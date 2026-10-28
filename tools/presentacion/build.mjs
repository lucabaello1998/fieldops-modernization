/**
 * Generador de la presentación de FieldOps (.pptx, 16:9).
 *
 * Uso:  node tools/presentacion/build.mjs
 * Entrada: docs/capturas/deck/*.png (capturas reales de la app con Chrome headless)
 * Salida:  docs/FieldOps.pptx
 *
 * Estética alineada al sistema de diseño de la app (docs/diseno.md):
 * claro + panel oscuro, acento indigo, números en tipografía tabular.
 */
import pptxgen from 'pptxgenjs';
import JSZip from 'jszip';
import path from 'node:path';
import fs from 'node:fs';
import { fileURLToPath } from 'node:url';

const raiz = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', '..');
const OUT = path.join(raiz, 'docs', 'FieldOps.pptx');
const IMG = path.join(raiz, 'docs', 'capturas', 'deck');

// ---------------------------------------------------------------- paleta
const C = {
  ink: '0F172A', // slate-900 (fondo oscuro)
  ink2: '1E293B',
  blanco: 'FFFFFF',
  superficie: 'F8FAFC',
  borde: 'E2E8F0',
  texto: '334155',
  suave: '64748B',
  brand: '4F46E5',
  brandSuave: 'EEF2FF',
  rose: 'E11D48',
  roseSuave: 'FFF1F2',
  emerald: '059669',
  emeraldSuave: 'ECFDF5',
  amber: 'D97706',
  amberSuave: 'FFFBEB',
  sky: '0284C7',
  skySuave: 'F0F9FF',
};

const FUENTE = 'Calibri';

const pres = new pptxgen();
pres.layout = 'LAYOUT_WIDE'; // 13,33" x 7,5"
pres.author = 'Luca Baello';
pres.title = 'FieldOps';
pres.subject = 'Demo funcional · Laravel 12 + PHP 8.3 + MySQL 8';

const nueva = (fondo = C.blanco) => {
  const s = pres.addSlide();
  s.background = { color: fondo };
  return s;
};

const sombra = () => ({ type: 'outer', color: C.ink, opacity: 0.1, blur: 9, offset: 2, angle: 90 });

const txt = (s, texto, o) => s.addText(texto, { fontFace: FUENTE, margin: 0, isTextBox: true, ...o });

// Encabezado estándar de slide de contenido
const titulo = (s, t, sub, colorTitulo = C.ink, colorSub = C.suave) => {
  txt(s, t, { x: 0.62, y: 0.42, w: 12.1, h: 0.55, fontSize: 30, bold: true, color: colorTitulo });
  if (sub) txt(s, sub, { x: 0.62, y: 1.0, w: 12.1, h: 0.35, fontSize: 13, color: colorSub });
};

// Tarjeta con sombra suave
const tarjeta = (s, x, y, w, h) => {
  s.addShape(pres.ShapeType.roundRect, {
    x, y, w, h, rectRadius: 0.08,
    fill: { color: C.blanco },
    line: { color: C.borde, width: 1 },
    shadow: sombra(),
  });
};

// Icono/cifra dentro de un círculo de color
const circulo = (s, x, y, d, colorFondo, contenido, colorTexto, fontSize, bold = true) => {
  s.addShape(pres.ShapeType.ellipse, { x, y, w: d, h: d, fill: { color: colorFondo }, line: { color: colorFondo, width: 0 } });
  txt(s, contenido, {
    x, y, w: d, h: d, fontSize, bold, color: colorTexto,
    align: 'center', valign: 'middle',
  });
};

// Captura de la app con marco blanco
const captura = (s, archivo, x, y, w) => {
  const p = path.join(IMG, archivo);
  if (!fs.existsSync(p)) throw new Error('Falta la captura: ' + p);
  const h = w / 1.6; // las capturas son 1600x1000
  s.addShape(pres.ShapeType.roundRect, {
    x: x - 0.1, y: y - 0.1, w: w + 0.2, h: h + 0.2, rectRadius: 0.06,
    fill: { color: C.blanco }, line: { color: C.borde, width: 1 }, shadow: sombra(),
  });
  s.addImage({ path: p, x, y, w, h });
  return h;
};

/* ===================================================== 01 · portada (oscuro) */
{
  const s = nueva(C.ink);
  s.addShape(pres.ShapeType.ellipse, { x: 9.4, y: -1.6, w: 6.4, h: 6.4, fill: { color: C.ink2 }, line: { color: C.ink2, width: 0 } });
  s.addShape(pres.ShapeType.ellipse, { x: 11.1, y: 3.6, w: 3.4, h: 3.4, fill: { color: '221F4D' }, line: { color: '221F4D', width: 0 } });

  circulo(s, 0.9, 0.85, 0.62, C.brand, 'FO', C.blanco, 20);

  txt(s, 'FieldOps', { x: 0.9, y: 2.1, w: 9.4, h: 1.0, fontSize: 54, bold: true, color: C.blanco });
  txt(s, 'Un sistema de gestión de campo, reescrito sobre Laravel', {
    x: 0.9, y: 3.15, w: 9.2, h: 0.6, fontSize: 19, color: 'C7D2FE',
  });
  txt(s, 'Demo funcional · Laravel 12 + PHP 8.3 + MySQL 8', {
    x: 0.9, y: 3.95, w: 9.0, h: 0.4, fontSize: 14, color: '94A3B8',
  });
  txt(s, 'Luca Baello · octubre 2026', { x: 0.9, y: 6.35, w: 6.0, h: 0.35, fontSize: 13, color: '94A3B8' });

  s.addNotes('FieldOps es la version moderna de un sistema de gestion de operaciones de campo: instalaciones, stock por deposito, tecnicos y tareas. Misma base de datos, otro stack.');
}

/* ================================================= 02 · punto de partida */
{
  const s = nueva();
  titulo(s, 'El punto de partida', 'Un sistema en producción, escrito a mano y sin framework');

  const datos = [
    ['378', 'archivos PHP propios', '5,2 MB de código escrito a mano'],
    ['76', 'tablas · 1.517 columnas', 'sin foreign keys: relaciones por texto'],
    ['11,3M', 'filas en la tabla tareas', 'el corazón operativo del sistema'],
    ['7.4.33', 'versión de PHP', 'sin soporte desde noviembre de 2022'],
  ];
  datos.forEach(([n, l, h], i) => {
    const x = 0.62 + i * 3.12;
    tarjeta(s, x, 1.75, 2.9, 2.2);
    txt(s, n, { x: x + 0.28, y: 2.0, w: 2.4, h: 0.8, fontSize: 40, bold: true, color: C.brand });
    txt(s, l, { x: x + 0.28, y: 2.85, w: 2.4, h: 0.4, fontSize: 13, bold: true, color: C.ink });
    txt(s, h, { x: x + 0.28, y: 3.25, w: 2.4, h: 0.5, fontSize: 10.5, color: C.suave });
  });

  s.addShape(pres.ShapeType.roundRect, {
    x: 0.62, y: 4.35, w: 12.1, h: 1.95, rectRadius: 0.08,
    fill: { color: C.superficie }, line: { color: C.borde, width: 1 },
  });
  txt(s, 'Un sistema que creció sin planos', { x: 1.0, y: 4.62, w: 11.3, h: 0.4, fontSize: 16, bold: true, color: C.ink });
  txt(s,
    '76 tablas sin claves foráneas (todo se relaciona por nombre de texto), 2.251 consultas SQL escritas a mano, '
    + '1.767 archivos en producción y 14 años de parches encima. Funciona todos los días: el problema es a qué costo.',
    { x: 1.0, y: 5.05, w: 11.3, h: 0.75, fontSize: 13, color: C.texto, lineSpacing: 20 });
  txt(s, 'El objetivo no es cambiar el negocio: es bajar el costo de mantenerlo.', {
    x: 1.0, y: 5.78, w: 11.3, h: 0.35, fontSize: 12, bold: true, color: C.brand,
  });

  s.addNotes('El sistema legacy sigue en produccion: PHP 7.4, jQuery y MySQL. La tabla tareas tiene 11,3 millones de filas y es donde trabaja el negocio todos los dias.');
}

/* ================================================== 03 · la decisión */
{
  const s = nueva();
  titulo(s, 'La decisión: no parchear, reescribir', 'Con 378 archivos dispersos, tapar agujeros deja el diseño intacto');

  const opciones = [
    ['Parchear el PHP actual', 'Revisar 2.251 consultas a mano: se arregla el síntoma, no la arquitectura.', C.suave, C.superficie],
    ['Migrar de versión sin framework', 'Menos fatales, mismo diseño: sin ORM, sin capa de validación, sin tests.', C.suave, C.superficie],
    ['Reescribir sobre Laravel 12', 'MVC, Eloquent, validación, autorización por roles y tests. El esquema real se conserva.', C.brand, C.brandSuave],
  ];
  opciones.forEach(([t, d, color, fondo], i) => {
    const x = 0.62 + i * 4.14;
    const elegida = i === 2;
    tarjeta(s, x, 1.75, 3.9, 2.6);
    if (elegida) {
      s.addShape(pres.ShapeType.roundRect, {
        x: x, y: 1.75, w: 3.9, h: 2.6, rectRadius: 0.08,
        fill: { type: 'solid', color: fondo }, line: { color: C.brand, width: 1.5 },
      });
    }
    txt(s, elegida ? 'Elegida' : 'Descartada', {
      x: x + 0.3, y: 1.98, w: 2.0, h: 0.3, fontSize: 10, bold: true,
      color: elegida ? C.brand : C.suave, charSpacing: 1,
    });
    txt(s, t, { x: x + 0.3, y: 2.35, w: 3.3, h: 0.5, fontSize: 15, bold: true, color: elegida ? C.brand : C.ink });
    txt(s, d, { x: x + 0.3, y: 2.9, w: 3.35, h: 1.2, fontSize: 11, color: C.texto, lineSpacing: 15 });
  });

  txt(s, 'El esquema real se conserva', { x: 0.62, y: 4.7, w: 12.1, h: 0.35, fontSize: 15, bold: true, color: C.ink });
  txt(s,
    'Las 76 tablas del legacy se migraron tal cual (nombres, tipos y relaciones por texto incluidos), así el negocio '
    + 'no cambia su forma de trabajar y los reportes siguen siendo comparables. Lo que cambia es cómo se accede a esos datos.',
    { x: 0.62, y: 5.1, w: 12.1, h: 0.8, fontSize: 12.5, color: C.texto, lineSpacing: 18 });

  const chips = ['Laravel 12', 'PHP 8.3', 'MySQL 8', 'Tailwind v4', 'Alpine + Chart.js', 'Docker'];
  let cx = 0.62;
  chips.forEach((c) => {
    const w = 0.28 + c.length * 0.1;
    s.addShape(pres.ShapeType.roundRect, {
      x: cx, y: 6.15, w, h: 0.42, rectRadius: 0.2,
      fill: { color: C.brandSuave }, line: { color: 'C7D2FE', width: 1 },
    });
    txt(s, c, { x: cx, y: 6.15, w, h: 0.42, fontSize: 11, bold: true, color: C.brand, align: 'center', valign: 'middle' });
    cx += w + 0.16;
  });

  s.addNotes('La decision fue reescribir. El esquema real se conservo completo: 76 tablas y 1.517 columnas, migradas con 55 migraciones generadas desde el esquema.');
}

/* ================================================= 04 · antes / después */
{
  const s = nueva();
  titulo(s, 'Antes y después', 'El mismo sistema, la misma base, otro stack');

  const filas = [
    ['PHP', '7.4.33 (EOL desde nov-2022)', '8.3.36'],
    ['Código', '378 archivos PHP + jQuery', 'Laravel 12 (MVC + Eloquent + Blade)'],
    ['Datos', '2.251 consultas SQL a mano', 'Eloquent + PDO con parámetros enlazados'],
    ['Front', 'jQuery disperso en cada vista', 'Vite + Alpine + Chart.js + Tailwind v4'],
    ['Estructura', '1.767 archivos sin capas', '8 controladores · 55 modelos · 18 vistas'],
    ['Calidad', 'sin tests ni entorno reproducible', '53 tests de feature + Docker'],
    ['Puesta en marcha', 'FTP y cambios a mano en cPanel', 'docker compose + migrate:fresh --seed'],
  ];

  const x = 0.62;
  const y = 1.75;
  const w = 12.1;
  const h = 0.62;

  s.addShape(pres.ShapeType.roundRect, { x, y, w, h: 0.5, rectRadius: 0.06, fill: { color: C.ink }, line: { color: C.ink, width: 0 } });
  txt(s, 'Aspecto', { x: x + 0.22, y, w: 1.9, h: 0.5, fontSize: 11.5, bold: true, color: 'C7D2FE', valign: 'middle' });
  txt(s, 'Sistema legacy (hoy)', { x: x + 2.2, y, w: 5.0, h: 0.5, fontSize: 11.5, bold: true, color: 'FECDD3', valign: 'middle' });
  txt(s, 'FieldOps', { x: x + 7.3, y, w: 4.6, h: 0.5, fontSize: 11.5, bold: true, color: 'A7F3D0', valign: 'middle' });

  filas.forEach(([aspecto, antes, ahora], i) => {
    const fy = y + 0.5 + i * h;
    if (i % 2 === 0) {
      s.addShape(pres.ShapeType.rect, { x, y: fy, w, h, fill: { color: C.superficie }, line: { color: C.superficie, width: 0 } });
    }
    txt(s, aspecto, { x: x + 0.22, y: fy, w: 1.9, h, fontSize: 11.5, bold: true, color: C.ink, valign: 'middle' });
    txt(s, antes, { x: x + 2.2, y: fy, w: 5.0, h, fontSize: 11.5, color: 'BE123C', valign: 'middle' });
    txt(s, ahora, { x: x + 7.3, y: fy, w: 4.6, h, fontSize: 11.5, color: '047857', valign: 'middle' });
  });

  s.addShape(pres.ShapeType.line, { x, y: y + 0.5, w, h: 0, line: { color: C.borde, width: 1 } });

  txt(s, 'Cada pantalla se reescribió sobre el mismo esquema, sin migrar ni un dato: la base del negocio queda intacta.', {
    x, y: 6.72, w, h: 0.4, fontSize: 11, color: C.suave,
  });

  s.addNotes('Todo lo de la columna derecha es verificable en el repositorio y en la demo que sigue.');
}

/* ================================================== 05 · tablero (hero) */
{
  const s = nueva();
  titulo(s, 'La nueva aplicación', 'Datos reales del esquema, con datos de prueba sintéticos', C.ink, C.suave);
  captura(s, '02-tablero.png', 1.97, 1.52, 9.4);
  s.addNotes('El tablero resume la operacion: altas del mes, tareas activas, stock critico, tecnicos activos, y cuatro graficos construidos sobre las mismas tablas del legacy.');
}

/* ============================================== 06 · tablero (oscuro) */
{
  const s = nueva(C.ink);
  titulo(s, 'Modo oscuro', 'Interfaz clara y oscura, con tokens de diseño compartidos', C.blanco, '94A3B8');
  captura(s, '03-tablero-oscuro.png', 1.97, 1.52, 9.4);
  s.addNotes('La estetica es un sistema de diseno propio (docs/diseno.md), no un tema comprado: tokens de color, tipografia Inter y componentes reutilizables.');
}

/* ======================================================= 07 · kanban */
{
  const s = nueva();
  titulo(s, 'Tareas: tablero Kanban', 'Arrastrar y soltar con validación en el servidor');

  captura(s, '04-tareas-kanban.png', 4.75, 1.72, 8.0);

  const bullets = [
    ['PATCH /tareas/{id}/estado', 'Cada movimiento pasa por sesión validada, CSRF y validación del estado.'],
    ['Trazabilidad', 'Quién movió la tarea, cuándo y desde qué estado queda escrito en la bitácora.'],
    ['Alta de tareas', 'Modal con tipo, prioridad, responsable y vencimiento.'],
  ];
  let by = 1.9;
  bullets.forEach(([t, d]) => {
    s.addShape(pres.ShapeType.roundRect, { x: 0.62, y: by + 0.08, w: 0.16, h: 0.16, rectRadius: 0.05, fill: { color: C.brand }, line: { color: C.brand, width: 0 } });
    txt(s, t, { x: 0.98, y: by, w: 3.5, h: 0.32, fontSize: 13, bold: true, color: C.ink });
    txt(s, d, { x: 0.98, y: by + 0.34, w: 3.5, h: 0.9, fontSize: 11, color: C.texto, lineSpacing: 15 });
    by += 1.42;
  });

  s.addNotes('El tablero tiene cuatro columnas y se maneja arrastrando tarjetas. Cada movimiento se valida en el servidor y queda auditado.');
}

/* ========================================== 08 · trazabilidad (hero) */
{
  const s = nueva();
  titulo(s, 'Cada cambio queda registrado', 'La bitácora de la tarea: quién, cuándo y desde qué estado');
  captura(s, '14-tarea-detalle.png', 1.97, 1.52, 9.4);
  s.addNotes('Se abre cualquier tarjeta y esta el historial completo: movimientos de estado, notas agregadas y los datos de la tarea.');
}

/* ============================================== 09 · stock y altas */
{
  const s = nueva();
  titulo(s, 'Stock y altas', 'Los dos módulos de operación diaria');

  const w = 6.05;
  captura(s, '05-stock.png', 0.55, 2.05, w);
  captura(s, '06-altas.png', 6.73, 2.05, w);

  txt(s, 'Stock', { x: 0.55, y: 1.62, w: 3.0, h: 0.35, fontSize: 15, bold: true, color: C.ink });
  txt(s, '205 líneas por depósito, alertas por nivel y movimientos internos registrados con usuario y fecha.',
    { x: 0.55, y: 5.85, w: w, h: 0.7, fontSize: 11, color: C.texto, lineSpacing: 15 });

  txt(s, 'Altas', { x: 6.73, y: 1.62, w: 3.0, h: 0.35, fontSize: 15, bold: true, color: C.ink });
  txt(s, '640 instalaciones con filtros por zona, depósito, estado y fecha, y ficha completa de equipos.',
    { x: 6.73, y: 5.85, w: w, h: 0.7, fontSize: 11, color: C.texto, lineSpacing: 15 });

  s.addNotes('Los filtros y las relaciones por nombre de texto del legacy se respetan: el sistema nuevo habla el mismo idioma que la base de datos existente.');
}

/* ======================================================= 10 · búsqueda */
{
  const s = nueva();
  titulo(s, 'Búsqueda global', 'Un solo buscador para toda la operación');

  captura(s, '11-busqueda.png', 4.75, 1.72, 8.0);

  const bullets = [
    ['Un solo endpoint', 'Tareas, altas, técnicos, materiales y reclamos en una consulta paginada.'],
    ['Consultas parametrizadas', 'El término viaja como parámetro enlazado: no hay SQL que concatenar.'],
    ['Resultados agrupados', 'Cada coincidencia se resalta y se agrupa por tipo de registro.'],
  ];
  let by = 1.9;
  bullets.forEach(([t, d]) => {
    s.addShape(pres.ShapeType.roundRect, { x: 0.62, y: by + 0.08, w: 0.16, h: 0.16, rectRadius: 0.05, fill: { color: C.emerald }, line: { color: C.emerald, width: 0 } });
    txt(s, t, { x: 0.98, y: by, w: 3.5, h: 0.32, fontSize: 13, bold: true, color: C.ink });
    txt(s, d, { x: 0.98, y: by + 0.34, w: 3.5, h: 0.9, fontSize: 11, color: C.texto, lineSpacing: 15 });
    by += 1.42;
  });

  s.addNotes('La busqueda recorre cinco tablas con consultas preparadas y resalta el termino en los resultados.');
}

/* ==================================================== 11 · calidad */
{
  const s = nueva();
  titulo(s, 'Cómo se verifica', 'No alcanza con que compile: cada ruta, cada acción y cada pantalla tienen su prueba');

  const bloques = [
    ['53', 'tests de feature', '272 assertions sobre MySQL real: acceso, roles, Kanban, stock, usuarios y búsqueda.'],
    ['10', 'checks end-to-end', 'Chrome headless contra la app viva: login, drag & drop, alta de tarea, stock y usuarios.'],
    ['33', 'capturas visuales', 'Todas las pantallas en claro y oscuro, más modales, 403 y 404, revisadas una por una.'],
    ['0', 'errores de JS y de red', 'En la última corrida completa: sin excepciones ni respuestas 5xx.'],
  ];

  bloques.forEach(([n, t, d], i) => {
    const x = 0.62 + (i % 2) * 6.15;
    const y = 1.9 + Math.floor(i / 2) * 2.15;
    tarjeta(s, x, y, 5.85, 1.85);
    txt(s, n, { x: x + 0.32, y: y + 0.28, w: 1.4, h: 0.8, fontSize: 34, bold: true, color: C.brand });
    txt(s, t, { x: x + 1.65, y: y + 0.36, w: 3.9, h: 0.35, fontSize: 13, bold: true, color: C.ink });
    txt(s, d, { x: x + 1.65, y: y + 0.75, w: 3.9, h: 0.9, fontSize: 10.5, color: C.texto, lineSpacing: 14 });
  });

  txt(s, 'La suite corre contra una base aparte (fieldops_test): nunca toca los datos de la demo. Los scripts quedan en tools/.', {
    x: 0.62, y: 6.35, w: 12.1, h: 0.4, fontSize: 11.5, color: C.suave,
  });

  s.addNotes('La verificacion tiene tres capas: tests de feature con phpunit sobre MySQL real, un script de puppeteer que maneja la app como un usuario, y una captura de cada pantalla en los dos temas para revisar la parte visual.');
}

/* ================================================== 12 · correr la demo */
{
  const s = nueva(C.ink);
  titulo(s, 'Correr la demo', 'Tres comandos: base migrada, 16 seeders sintéticos y app arriba', C.blanco, '94A3B8');

  s.addShape(pres.ShapeType.roundRect, {
    x: 0.62, y: 1.8, w: 7.2, h: 2.35, rectRadius: 0.08,
    fill: { color: '020617' }, line: { color: '1E293B', width: 1 },
  });
  txt(s, 'cp .env.example .env\ndocker compose up -d --build\ndocker compose exec app php artisan migrate:fresh --seed\nnpm install && npm run build', {
    x: 0.95, y: 1.98, w: 6.7, h: 2.0, fontSize: 11.5, color: 'A5F3FC', fontFace: 'Courier New', lineSpacing: 24,
  });
  txt(s, 'http://localhost:8080 · Adminer en :8081 · MySQL en :3307', {
    x: 0.62, y: 4.3, w: 7.2, h: 0.35, fontSize: 12, color: '94A3B8',
  });

  const usuarios = [
    ['admin@fieldops.test', 'Administrador'],
    ['supervisor@fieldops.test', 'Supervisor'],
    ['tecnico@fieldops.test', 'Técnico'],
    ['deposito@fieldops.test', 'Depósito'],
  ];
  tarjeta(s, 8.2, 1.8, 4.52, 2.85);
  txt(s, 'Usuarios de demo', { x: 8.5, y: 2.0, w: 3.9, h: 0.35, fontSize: 13, bold: true, color: C.ink });
  usuarios.forEach(([mail, rol], i) => {
    const yy = 2.45 + i * 0.5;
    txt(s, mail, { x: 8.5, y: yy, w: 3.9, h: 0.3, fontSize: 11, color: C.ink, fontFace: 'Courier New' });
    txt(s, rol, { x: 8.5, y: yy + 0.2, w: 3.9, h: 0.25, fontSize: 9.5, color: C.suave });
  });
  txt(s, 'contraseña: password · hasheada con bcrypt', { x: 8.5, y: 4.3, w: 3.9, h: 0.3, fontSize: 10, color: C.suave });

  txt(s, 'Los datos de la demo son sintéticos (semilla fija mt_srand(20261023)): mismo esquema, cero datos de clientes.', {
    x: 0.62, y: 5.35, w: 12.1, h: 0.4, fontSize: 12, color: '94A3B8',
  });

  s.addNotes('La demo se levanta en un minuto y usa exclusivamente datos sinteticos generados con semilla fija, sobre el esquema real de 76 tablas.');
}

/* ================================================== 13 · próximos pasos */
{
  const s = nueva(C.ink);
  titulo(s, 'Próximos pasos', 'Lo que sigue sobre la misma base', C.blanco, '94A3B8');

  const pendientes = [
    ['Importante', 'Migrar los módulos restantes', 'ATC con mapas, devolución de equipos y calendario de recordatorios.', C.amber],
    ['Importante', 'Integración con SAP', 'Las tablas ya están migradas: falta el conector y las pantallas de sincronización.', C.amber],
    ['Deseable', 'Despliegue y observabilidad', 'Pipeline de CI que corra la suite, entornos separados y métricas de uso.', C.sky],
    ['Deseable', 'Migración de datos reales', 'Pasar de los datos sintéticos al volcado productivo con un proceso auditado.', C.sky],
  ];
  pendientes.forEach(([tag, t, d, color], i) => {
    const y = 1.85 + i * 1.28;
    s.addShape(pres.ShapeType.roundRect, {
      x: 0.62, y, w: 12.1, h: 1.1, rectRadius: 0.07,
      fill: { color: C.ink2 }, line: { color: '334155', width: 1 },
    });
    s.addShape(pres.ShapeType.roundRect, {
      x: 0.95, y: y + 0.35, w: 1.15, h: 0.4, rectRadius: 0.2,
      fill: { color: C.ink }, line: { color, width: 1 },
    });
    txt(s, tag, { x: 0.95, y: y + 0.35, w: 1.15, h: 0.4, fontSize: 10, bold: true, color, align: 'center', valign: 'middle' });
    txt(s, t, { x: 2.35, y: y + 0.24, w: 10.0, h: 0.35, fontSize: 15, bold: true, color: C.blanco });
    txt(s, d, { x: 2.35, y: y + 0.6, w: 10.0, h: 0.35, fontSize: 11.5, color: '94A3B8' });
  });

  s.addNotes('El trabajo de codigo de esta etapa esta cerrado: lo que queda es ampliar modulos, integrar y desplegar.');
}

await pres.writeFile({ fileName: OUT });

// pptxgenjs deja la ruta absoluta de cada captura en el atributo descr de la imagen.
// Se reemplaza por el nombre del archivo: el deck no debe revelar rutas locales.
const zip = await JSZip.loadAsync(fs.readFileSync(OUT));
for (const nombre of Object.keys(zip.files)) {
  if (!/^ppt\/slides\/slide\d+\.xml$/.test(nombre)) continue;
  const xml = await zip.file(nombre).async('string');
  zip.file(nombre, xml.replace(/descr="[^"]*[\\/]([^\\/"]+)"/g, 'descr="$1"'));
}
fs.writeFileSync(OUT, await zip.generateAsync({ type: 'nodebuffer', compression: 'DEFLATE' }));

console.log('OK ->', OUT);
