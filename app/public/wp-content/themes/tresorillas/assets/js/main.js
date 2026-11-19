/* 3 Orillas — interacciones y movimiento.
   Adaptado del diseño (index.html): mismas animaciones y efectos, pero con páginas reales
   en lugar de rutas #, datos desde WordPress (window.TOR) y formularios que sí envían. */
(function () {
'use strict';

const TOR = window.TOR || {};
const CATS = TOR.cats || [];
const CATN = Object.fromEntries(CATS.map(c => [c[0], c[1]]));
const PROJECTS = TOR.projects || [];
const LINES = TOR.lines || [];
const PHASES = TOR.phases || [];
const PLANS = TOR.plans || {};
const U = TOR.urls || {};
const AJ = TOR.ajustes || {};
const HALO = { e: 'rgba(201,179,138,.55)', c: 'rgba(169,194,160,.55)', t: 'rgba(150,165,250,.42)' };
const byId = Object.fromEntries(PROJECTS.map(p => [p.id, p]));
const $ = s => document.querySelector(s), $$ = s => Array.from(document.querySelectorAll(s));
const esc = s => String(s == null ? '' : s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const G = window.gsap, ST = window.ScrollTrigger;
if (G && ST) G.registerPlugin(ST); if (G && window.Flip) G.registerPlugin(window.Flip);

const urlCat = c => c === 'todo' ? U.trabajo : U.trabajo + (U.trabajo.indexOf('?') > -1 ? '&' : '?') + 'cat=' + encodeURIComponent(c);
const urlDemo = d => U.demoLab + (U.demoLab.indexOf('?') > -1 ? '&' : '?') + 'app=' + encodeURIComponent(d);
const urlWa = t => 'https://wa.me/' + encodeURIComponent(AJ.wa || '') + (t ? '?text=' + encodeURIComponent(t) : '');
const hostDe = u => { try { return new URL(u).host; } catch (e) { return String(u || ''); } };
const svgFlecha = '<svg class="flecha" viewBox="0 0 14 14" aria-hidden="true"><path d="M4 10L10 4M5 4h5v5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>';

/* ================= Horario de atención (hora de Bogotá) ================= */
function enHorario() {
  const ahora = new Date(), bog = new Date(ahora.getTime() + ahora.getTimezoneOffset() * 60000 - 5 * 3600000);
  const d = bog.getDay(), h = bog.getHours() + bog.getMinutes() / 60;
  return d >= 1 && d <= 5 && h >= (AJ.hIni || 8) && h < (AJ.hFin || 18);
}
(function estado() {
  const on = enHorario();
  const txt = { corto: on ? 'Asesores en línea' : 'Respondemos en horario hábil', largo: on ? 'Asesores en línea ahora' : 'Fuera de horario · te respondemos desde las ' + (AJ.hIni || 8) + ':00', quince: on ? 'En línea · te respondemos en menos de 15 minutos' : 'Fuera de horario · te respondemos desde las ' + (AJ.hIni || 8) + ':00', cartel: on ? 'Asesores en línea' : 'Te respondemos en horario hábil' };
  $$('[data-estado]').forEach(el => { el.textContent = txt[el.dataset.estado] || el.textContent; });
  $$('[data-live]').forEach(el => el.classList.toggle('apagado', !on));
})();

/* ================= Envío de formularios ================= */
async function enviar(datos) {
  const fd = new FormData();
  Object.entries(datos).forEach(([k, v]) => fd.append(k, v == null ? '' : v));
  fd.append('action', 'tor_contacto'); fd.append('pagina', location.href);
  const ir = async () => { fd.set('nonce', TOR.nonce || ''); const r = await fetch(TOR.ajax, { method: 'POST', body: fd, credentials: 'same-origin' }); let j = {}; try { j = await r.json(); } catch (e) { /* respuesta vacía */ } return { r, j }; };
  let { r, j } = await ir();
  if (r.status === 403 && j && j.data && j.data.codigo === 'nonce') {
    try { const n = await fetch(TOR.ajax + '?action=tor_nonce', { credentials: 'same-origin' }).then(x => x.json()); if (n && n.data && n.data.nonce) { TOR.nonce = n.data.nonce; ({ r, j } = await ir()); } } catch (e) { /* sin red */ }
  }
  if (j && j.success) return { ok: true };
  return { ok: false, msg: (j && j.data && j.data.msg) || 'No pudimos enviar tu mensaje. Escríbenos por WhatsApp.' };
}

/* ================= Utilidades de movimiento ================= */
function partir(el) {
  if (el.dataset.partido) return el.querySelectorAll('.wi'); el.dataset.partido = '1';
  const walk = n => Array.from(n.childNodes).forEach(ch => {
    if (ch.nodeType === 3) { const f = document.createDocumentFragment(); ch.textContent.split(/(\s+)/).forEach(t => { if (!t) return; if (/^\s+$/.test(t)) f.appendChild(document.createTextNode(t)); else { const o = document.createElement('span'); o.className = 'w'; const i = document.createElement('span'); i.className = 'wi'; i.textContent = t; o.appendChild(i); f.appendChild(o); } }); ch.replaceWith(f); }
    else if (ch.nodeType === 1 && ch.tagName !== 'BR') walk(ch);
  });
  walk(el); return el.querySelectorAll('.wi');
}
function rodar(el) {
  const to = el.dataset.to; el.innerHTML = ''; el.setAttribute('aria-label', to);
  to.split('').forEach((ch, i) => {
    const col = document.createElement('span'); col.className = 'col'; col.setAttribute('aria-hidden', 'true');
    if (/\d/.test(ch)) { for (let k = 0; k <= 10 + (+ch); k++) { const s = document.createElement('span'); s.textContent = k % 10; col.appendChild(s); } col.dataset.n = 10 + (+ch); }
    else { const s = document.createElement('span'); s.textContent = ch; col.appendChild(s); col.dataset.n = 0; }
    el.appendChild(col);
    const go = () => { col.style.transitionDelay = (i * 0.07) + 's'; col.style.transform = `translateY(-${col.dataset.n}em)`; };
    if (reduce) { col.style.transition = 'none'; go(); } else el._go = (el._go || []).concat(go);
  });
}
$$('.roll').forEach(rodar);
function dispararRoll(scope) { (scope || document).querySelectorAll('.roll').forEach(el => { if (el._go && !el._done) { el._done = true; requestAnimationFrame(() => el._go.forEach(f => f())); } }); }

/* Cartas: flotación suave (envoltorio interno para no pisar el parallax) */
$$('.card').forEach(c => { const w = document.createElement('div'); w.className = 'bob'; while (c.firstChild) w.appendChild(c.firstChild); c.appendChild(w); if (c.tagName === 'FORM') { w.style.display = 'flex'; w.style.flexDirection = 'column'; w.style.gap = '10px'; } });

/* ================= Tarjetas de proyecto ================= */
function media(p, cls) {
  if (p.tipo) return `<div class="obra-media ${cls || ''}" style="background:${esc(p.tipo[0])};color:${esc(p.tipo[1])};"><div class="tipo"><span>${esc(p.client)}</span><b>${esc(p.name)}</b><span>${esc(p.kind)}</span></div>${p.estado ? `<span class="estado cristal">${esc(p.estado)}</span>` : ''}</div>`;
  return `<div class="obra-media ${cls || ''}"><img src="${esc(p.img)}" alt="${esc(p.name)}: ${esc(p.kind)}" loading="lazy">${p.video && !p.mobile ? `<video data-src="${esc(p.video)}" muted loop playsinline preload="none" aria-hidden="true"></video>` : ''}${p.estado ? `<span class="estado cristal">${esc(p.estado)}</span>` : ''}</div>`;
}
const chips = p => `<div class="cat-chips">${p.cats.map(c => `<span>${esc(CATN[c] || c)}</span>`).join('')}</div>`;
function obra(p, size) {
  return `<a class="obra ${size || ''}" href="${esc(p.url)}" data-id="${esc(p.id)}" data-cursor="Ver caso">${media(p)}<div class="obra-info"><div><h3>${esc(p.name)}</h3><p>${esc(p.client)} · ${esc(p.kind)}</p></div><span class="anio">${esc(p.year)}</span></div>${chips(p)}</a>`;
}
function fila(p) {
  return `<a class="fila" href="${esc(p.url)}" data-id="${esc(p.id)}"><div><h3>${esc(p.name)}</h3><p class="small tenue" style="margin-top:4px;">${esc(p.client)} · ${esc(p.kind)}</p></div>${chips(p)}<span class="anio">${esc(p.year)}</span><span class="ir"><svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 7h8M7 3l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg></span></a>`;
}
function hoverVideo(root) {
  if (!root) return;
  root.querySelectorAll('.obra').forEach(c => {
    const v = c.querySelector('video'); if (!v) return;
    const on = () => { if (!v.src) v.src = v.dataset.src; c.classList.add('playing'); v.play().catch(() => {}); };
    const off = () => { c.classList.remove('playing'); v.pause(); };
    c.addEventListener('mouseenter', on); c.addEventListener('mouseleave', off); c.addEventListener('focus', on); c.addEventListener('blur', off);
  });
}
const cuenta = k => k === 'todo' ? PROJECTS.length : PROJECTS.filter(p => p.cats.includes(k)).length;

/* ================= INICIO ================= */
const stage = $('#stage');
if (stage) {
  const scene = $('#scene');
  if (!reduce) stage.addEventListener('pointermove', e => {
    const r = stage.getBoundingClientRect();
    scene.style.setProperty('--mx', ((e.clientX - r.left) / r.width - .5) * 2);
    scene.style.setProperty('--my', ((e.clientY - r.top) / r.height - .5) * 2);
  });

  /* Buscador: las tarjetas se convierten en tu propuesta */
  const pick = t => {
    t = t.toLowerCase();
    if (/whatsapp|chatbot|bot\b|\bia\b|inteligencia|automat/.test(t)) return 'ia';
    if (/\bapp|móvil|movil|celular|domicili|conductor|pasajero/.test(t) && !/vendedor|visita/.test(t)) return 'app';
    if (/software|erp|crm|plataforma|sistema|inventario|gesti|clínica|clinica|vendedor|visita|tablero/.test(t)) return 'software';
    if (/tienda|e-?commerce|web|página|pagina|sitio|online/.test(t)) return 'web';
    if (/venta|marketing|pauta|clientes|redes|campañ|anuncio/.test(t)) return 'marketing';
    return 'general';
  };
  const H_INICIAL = $('#h-inicio').innerHTML, LEAD_INICIAL = $('#stageLead').textContent;
  const swapHead = (html, lead) => {
    const h = $('#h-inicio'); h.classList.add('swap');
    /* "head" viene de los datos fijos del plugin (texto con <em>), no del visitante. */
    setTimeout(() => { h.innerHTML = html; $('#stageLead').textContent = lead; h.classList.remove('swap'); }, reduce ? 0 : 320);
  };
  let plan = null, ideaTxt = '';
  const proponer = text => {
    if (!text.trim()) { $('#idea').focus(); return; }
    const p = PLANS[pick(text)] || PLANS.general; if (!p) return;
    const ref = byId[p.ref] || PROJECTS[0];
    plan = p; ideaTxt = text.trim().slice(0, 200);
    $('#rLine').innerHTML = `<span class="dot o-${esc(p.o)}"></span>${esc(p.line)}`;
    $('#rTitle').textContent = p.title;
    $('#rSteps').innerHTML = p.steps.map((s, i) => `<li style="display:grid;grid-template-columns:20px 1fr;gap:6px;"><span style="font-family:var(--serif);color:var(--tenue);">${i + 1}</span>${esc(s)}</li>`).join('');
    if (ref) { $('#rRefImg').src = ref.shot || ref.img; $('#rRefImg').alt = ref.name; $('#rRefName').textContent = 'Parecido: ' + ref.name; $('#rRef').href = ref.url; }
    $('#rTimeV').textContent = p.time;
    $('#capMsg').textContent = 'Un asesor te escribe por WhatsApp en horario hábil.';
    const cap = $('#rCap'); cap.querySelector('.cap-fila').style.display = 'flex'; cap.querySelector('.acepto').style.display = '';
    $('#halo').style.background = HALO[p.o];
    $$('.c-ini').forEach(c => c.classList.add('hide'));
    $$('.c-res').forEach((c, i) => setTimeout(() => c.classList.remove('hide'), reduce ? 0 : 200 + i * 140));
    swapHead(p.head, 'Esto es lo que haríamos con tu idea. Déjanos tu WhatsApp y te enviamos la propuesta completa.');
    stage.classList.add('answered');
  };
  const reiniciar = () => {
    $$('.c-res').forEach(c => c.classList.add('hide'));
    $$('.c-ini').forEach((c, i) => setTimeout(() => c.classList.remove('hide'), reduce ? 0 : 150 + i * 100));
    $('#halo').style.background = ''; swapHead(H_INICIAL, LEAD_INICIAL); stage.classList.remove('answered');
    $('#idea').value = ''; $$('#ideasEj .chip').forEach(c => c.setAttribute('aria-pressed', 'false')); $('#idea').focus();
  };
  $('#ideaForm').addEventListener('submit', e => { e.preventDefault(); proponer($('#idea').value); });
  $('#ideasEj').addEventListener('click', e => { const b = e.target.closest('.chip'); if (!b) return; $$('#ideasEj .chip').forEach(c => c.setAttribute('aria-pressed', c === b)); $('#idea').value = b.textContent; proponer(b.textContent); });
  $('#reset').addEventListener('click', reiniciar);
  $('#rCap').addEventListener('submit', async e => {
    e.preventDefault(); const f = e.currentTarget, t = $('#capTel').value.trim(), msg = $('#capMsg');
    if (t.replace(/\D/g, '').length < 7) { msg.textContent = 'Escribe un número de al menos 7 dígitos.'; $('#capTel').focus(); return; }
    if (!$('#capOk').checked) { msg.textContent = 'Marca la autorización de datos para poder escribirte.'; $('#capOk').focus(); return; }
    const btn = f.querySelector('button[type=submit]'); btn.disabled = true;
    const r = await enviar({ origen: 'propuesta', telefono: t, idea: ideaTxt, linea: plan ? plan.line : '', consent: '1', sitio: f.querySelector('[name=sitio]').value });
    btn.disabled = false;
    if (r.ok) { f.querySelector('.cap-fila').style.display = 'none'; f.querySelector('.acepto').style.display = 'none'; msg.innerHTML = `<b style="color:var(--tinta);font-weight:600;">Listo.</b> Te escribimos al ${esc(t)} con la propuesta de ${esc((plan ? plan.line : 'tu proyecto').toLowerCase())}.`; }
    else { msg.innerHTML = `<span class="msg-error">${esc(r.msg)}</span>`; }
  });

  /* Intro del escenario: una sola secuencia orquestada */
  (function intro() {
    dispararRoll(stage);
    if (reduce || !G) return;
    const palabras = partir($('#h-inicio'));
    const tl = G.timeline({ defaults: { ease: 'power4.out' } });
    tl.from('.stage .etq', { y: 12, opacity: 0, duration: .6 })
      .from(palabras, { yPercent: 110, duration: 1.1, stagger: .06 }, '-=.3')
      .from('#stageLead', { y: 16, opacity: 0, duration: .8 }, '-=.7')
      .from('#ideaForm', { y: 24, opacity: 0, duration: .9 }, '-=.6')
      .from('.retrato img', { scale: 1.08, opacity: 0, filter: 'blur(12px) grayscale(1)', duration: 1.6, ease: 'power2.out' }, 0.2)
      .fromTo('#orb1', { strokeDasharray: 630, strokeDashoffset: 630 }, { strokeDashoffset: 0, duration: 2, ease: 'power2.inOut' }, 0.3)
      .from('.c-ini', { scale: .6, opacity: 0, x: (i, el) => (el.offsetLeft < 300 ? 120 : -120), y: 60, duration: 1.2, stagger: .12, ease: 'expo.out', clearProps: 'transform,opacity,scale,x,y' }, 0.6);
  })();

  /* Viajeros sobre la órbita */
  (function orbitar() {
    if (reduce) return; let t = 0;
    const a = $('#via1'), b = $('#via2');
    (function f() { t += .0025; a.setAttribute('cx', 100 + 99 * Math.cos(t * 2 - 1.2)); a.setAttribute('cy', 100 + 99 * Math.sin(t * 2 - 1.2)); b.setAttribute('cx', 100 + 70 * Math.cos(-t * 3)); b.setAttribute('cy', 100 + 70 * Math.sin(-t * 3)); requestAnimationFrame(f); })();
  })();

  /* Tarjeta que rota entre plataformas, con barra de progreso */
  (function ciclo() {
    const lista = ['crm', 'seeds', 'insivo', 'hab'].map(id => byId[id]).filter(Boolean); if (!lista.length) return; let i = 0;
    const box = $('#ciclo'), bar = $('#cicloBar');
    function paso() {
      const p = lista[i % lista.length];
      const nueva = document.createElement('img'); nueva.src = p.shot || p.img; nueva.alt = p.name; nueva.className = 'fuera'; box.appendChild(nueva);
      requestAnimationFrame(() => requestAnimationFrame(() => nueva.classList.remove('fuera')));
      const viejas = Array.from(box.querySelectorAll('img')).slice(0, -1); viejas.forEach(v => { v.classList.add('fuera'); setTimeout(() => v.remove(), 900); });
      $('#cicloNom').textContent = p.name; $('#cicloLink').href = p.demo ? urlDemo(p.demo) : p.url;
      bar.style.transition = 'none'; bar.style.width = '0'; requestAnimationFrame(() => { bar.style.transition = 'width 4s linear'; bar.style.width = '100%'; });
      i++;
    }
    paso(); if (!reduce) setInterval(paso, 4000);
  })();

  /* Showreel tipo historias */
  (function reel() {
    const esc_ = $('#reelEscena'), barras = $('#reelBarras'); const DUR = 6500; let cur = 0, t0 = performance.now(), pausa = false, acumulado = 0;
    const REEL = ['seeds', 'crm', 'marca-3o', 'raii', 'seeds-pauta', 'insivo', 'asistente'].map(id => byId[id]).filter(p => p && p.img);
    if (!REEL.length) return;
    barras.style.gridTemplateColumns = `repeat(${REEL.length}, minmax(0, 1fr))`;
    esc_.innerHTML = REEL.map((p, i) => `<div class="slide" data-i="${i}"><img src="${esc(p.img)}" alt="${esc(p.name)}: ${esc(p.kind)}"${i ? ' loading="lazy"' : ''}><div class="leyenda"><span class="tag"><span class="dot o-${esc(p.o)}"></span>${esc(p.kind)}</span><h3>${esc(p.name)}</h3><span class="chipr cristal">${esc(p.chip)}</span></div>${p.video && !p.mobile ? `<div class="ventana"><div class="barra"><i></i><i></i><i></i></div><video data-src="${esc(p.video)}" muted loop playsinline preload="none" aria-hidden="true"></video></div>` : ''}</div>`).join('');
    esc_.querySelectorAll('.leyenda h3').forEach(partir);
    barras.innerHTML = REEL.map((p, i) => `<button role="tab" aria-label="${esc(p.name)}" data-i="${i}"><i><b></b></i></button>`).join('');
    const slides = esc_.querySelectorAll('.slide'), bs = barras.querySelectorAll('button');
    function ir(n) {
      n = (n + slides.length) % slides.length;
      slides.forEach((s, i) => { s.classList.toggle('sale', i === cur && i !== n); s.classList.toggle('on', i === n); const v = s.querySelector('video'); if (v) { if (i === n) { if (!v.src) v.src = v.dataset.src; v.play().catch(() => {}); } else v.pause(); } });
      setTimeout(() => slides.forEach((s, i) => { if (i !== n) s.classList.remove('sale'); }), 1100);
      bs.forEach((b, i) => { b.setAttribute('aria-selected', i === n); b.querySelector('b').style.width = i < n ? '100%' : '0'; });
      cur = n; t0 = performance.now(); acumulado = 0;
      const p = REEL[n]; esc_.href = p.url; $('#reelTag').innerHTML = `<span class="dot o-${esc(p.o)}"></span>${esc(p.name)} · ${esc(p.orilla)}`;
    }
    barras.addEventListener('click', e => { const b = e.target.closest('button'); if (b) ir(+b.dataset.i); });
    $('#rPrev').onclick = () => ir(cur - 1); $('#rNext').onclick = () => ir(cur + 1);
    const setPausa = v => { if (v) acumulado += performance.now() - t0; else t0 = performance.now(); pausa = v; $('#rPausa').setAttribute('aria-label', v ? 'Reproducir' : 'Pausar'); $('#rPausaIco').innerHTML = v ? '<path d="M4 2l8 5-8 5z" fill="currentColor"/>' : '<path d="M4 2v10M10 2v10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>'; };
    $('#rPausa').onclick = () => setPausa(!pausa);
    esc_.addEventListener('mouseenter', () => !pausa && setPausa(true)); esc_.addEventListener('mouseleave', () => pausa && setPausa(false));
    (function tick(now) { if (!pausa && !reduce) { const k = Math.min(1, (acumulado + now - t0) / DUR); bs[cur].querySelector('b').style.width = (k * 100) + '%'; if (k >= 1) ir(cur + 1); } requestAnimationFrame(tick); })(performance.now());
    ir(0);
  })();

  /* Antes y después */
  (function ab() {
    const box = $('#ab'), r = $('#abRange'); if (!box) return;
    const seeds = byId.seeds, crm = byId.crm;
    const SETS = [[U.media + 'antes-excel.jpg', 'Antes: hoja de cálculo de ventas', (seeds && seeds.gal[2]) || U.media + 'p/seeds-2.jpg', 'Después: inventario en el ERP de Seeds'], [U.media + 'antes-whatsapp.jpg', 'Antes: reportes de vendedores por WhatsApp', (crm && crm.img) || U.media + 'p/crm-0.jpg', 'Después: mapa de visitas verificadas por GPS']];
    const set = v => box.style.setProperty('--p', v + '%');
    r.addEventListener('input', () => set(r.value));
    box.addEventListener('pointermove', e => { if (e.pointerType === 'mouse') { const b = box.getBoundingClientRect(); const v = Math.max(0, Math.min(100, (e.clientX - b.left) / b.width * 100)); r.value = v; set(v); } });
    $$('[data-ab]').forEach(b => b.onclick = () => { const k = SETS[+b.dataset.ab]; $$('[data-ab]').forEach(x => x.setAttribute('aria-pressed', x === b)); $('#abAntes').src = k[0]; $('#abAntes').alt = k[1]; $('#abDespues').src = k[2]; $('#abDespues').alt = k[3]; pista(); });
    function pista() { if (reduce || !G) return; const o = { v: 85 }; G.fromTo(o, { v: 85 }, { v: 30, duration: 1.4, ease: 'power3.inOut', yoyo: true, repeat: 1, onUpdate: () => { set(o.v); r.value = o.v; }, onComplete: () => { G.to(o, { v: 50, duration: .8, ease: 'power2.out', onUpdate: () => { set(o.v); r.value = o.v; } }); } }); }
    if (ST && !reduce) ST.create({ trigger: box, start: 'top 70%', once: true, onEnter: pista });
  })();

  /* Texto que se llena con el scroll */
  (function scrub() {
    const el = $('#scrub'); if (!el) return; const ws = partir(el); ws.forEach(w => w.classList.add('pal'));
    if (reduce || !ST) { ws.forEach(w => w.classList.add('lit')); return; }
    ST.create({ trigger: el, start: 'top 80%', end: 'bottom 45%', scrub: true, onUpdate: self => { const n = Math.round(self.progress * ws.length); ws.forEach((w, i) => w.classList.toggle('lit', i < n)); } });
  })();
}

/* ================= TRABAJO ================= */
const catsEl = $('#cats');
if (catsEl) {
  let wf = $('#catBar').dataset.inicial || 'todo', wq = '', wv = 'grid';
  if (!CATN[wf]) wf = 'todo';
  const pistaCats = () => { const b = $('#cats [aria-selected="true"]'), t = $('#cats .pista'); if (b && t) { t.style.left = b.offsetLeft + 'px'; t.style.width = b.offsetWidth + 'px'; } };
  function trabajo(anim) {
    if (!$('#cats .pista')) catsEl.innerHTML = '<span class="pista"></span>' + CATS.filter(c => cuenta(c[0]) > 0).map(([k, l]) => `<button role="tab" data-f="${esc(k)}">${esc(l)}<sup>${cuenta(k)}</sup></button>`).join('');
    $$('#cats button').forEach(b => b.setAttribute('aria-selected', b.dataset.f === wf));
    pistaCats();
    const cat = CATS.find(c => c[0] === wf); $('#catDesc').textContent = cat ? cat[2] : '';
    const q = wq.trim().toLowerCase();
    const lista = PROJECTS.filter(p => (wf === 'todo' || p.cats.includes(wf)) && (!q || (p.name + ' ' + p.client + ' ' + p.kind + ' ' + p.cats.map(c => CATN[c]).join(' ')).toLowerCase().includes(q)));
    $('#conteo').textContent = lista.length === 1 ? '1 proyecto' : lista.length + ' proyectos';
    $('#vacio').hidden = lista.length > 0;
    const g = $('#obras'), l = $('#listaObras');
    g.hidden = wv !== 'grid'; l.hidden = wv !== 'lista';
    const estado = (G && window.Flip && anim && !reduce) ? window.Flip.getState(g.querySelectorAll('.obra')) : null;
    const patron = ['ancha', '', '', '', 'media', 'media'];
    g.innerHTML = lista.map((p, i) => obra(p, lista.length > 2 ? patron[i % patron.length] : 'media')).join('');
    l.innerHTML = lista.map(fila).join('');
    hoverVideo(g);
    if (estado) window.Flip.from(estado, { targets: g.querySelectorAll('.obra'), duration: .7, ease: 'power3.inOut', absolute: true, stagger: .02, onEnter: els => G.fromTo(els, { opacity: 0, scale: .94, y: 20 }, { opacity: 1, scale: 1, y: 0, duration: .6, stagger: .04 }), onLeave: els => G.to(els, { opacity: 0, scale: .94, duration: .3 }) });
    else if (G && anim && !reduce) G.from(g.querySelectorAll('.obra'), { opacity: 0, y: 24, duration: .6, stagger: .04, ease: 'power3.out' });
  }
  catsEl.addEventListener('click', e => { const b = e.target.closest('[data-f]'); if (!b) return; wf = b.dataset.f; history.replaceState(null, '', urlCat(wf)); trabajo(true); b.scrollIntoView({ block: 'nearest', inline: 'center', behavior: reduce ? 'auto' : 'smooth' }); });
  $('#buscar').addEventListener('input', e => { wq = e.target.value; trabajo(true); });
  $('#vGrid').onclick = () => { wv = 'grid'; $('#vGrid').setAttribute('aria-pressed', 'true'); $('#vLista').setAttribute('aria-pressed', 'false'); trabajo(true); };
  $('#vLista').onclick = () => { wv = 'lista'; $('#vLista').setAttribute('aria-pressed', 'true'); $('#vGrid').setAttribute('aria-pressed', 'false'); trabajo(true); };
  addEventListener('resize', pistaCats);
  if (document.fonts && document.fonts.ready) document.fonts.ready.then(pistaCats);
  /* Imagen que sigue al cursor en la vista de lista */
  (function flota() {
    const f = $('#flota'), im = f.querySelector('img'); let x = 0, y = 0, cx = 0, cy = 0;
    $('#listaObras').addEventListener('pointermove', e => { x = e.clientX; y = e.clientY; const r = e.target.closest('.fila'); if (r) { const p = byId[r.dataset.id]; if (p && p.img) { if (im.getAttribute('src') !== p.img) im.src = p.img; f.classList.add('on'); } else { f.classList.remove('on'); } } });
    $('#listaObras').addEventListener('pointerleave', () => f.classList.remove('on'));
    (function loop() { cx += (x - cx) * .18; cy += (y - cy) * .18; f.style.left = cx + 'px'; f.style.top = cy + 'px'; requestAnimationFrame(loop); })();
  })();
  trabajo(false);
}
hoverVideo($('#cRel'));

/* ================= DEMO LAB ================= */
const labWrap = $('#labWrap');
if (labWrap) {
  const conDemo = PROJECTS.filter(x => x.demo);
  let labApp = labWrap.dataset.app || 'seeds', labTab = 'demo';
  if (!conDemo.some(x => x.demo === labApp)) labApp = conDemo.length ? conDemo[0].demo : '';
  function lab() {
    const p = conDemo.find(x => x.demo === labApp); if (!p) return;
    $('#labLista').innerHTML = conDemo.map(x => `<button class="lab-item" role="tab" aria-selected="${x.demo === labApp}" data-app="${esc(x.demo)}">${x.img ? `<img src="${esc(x.img)}" alt="">` : `<span class="lab-ini" style="background:${esc(x.tipo ? x.tipo[0] : '#1f1f23')};color:${esc(x.tipo ? x.tipo[1] : '#F5F2EC')};">${esc(x.name.charAt(0))}</span>`}<span><b>${esc(x.name)}</b><span>${x.mobile ? 'App móvil' : 'Plataforma web'}</span></span></button>`).join('');
    $('#lTag').innerHTML = `<span class="dot o-${esc(p.o)}"></span>${esc(p.kind)}`; $('#lNombre').textContent = p.name;
    $$('.tabs button').forEach(b => b.setAttribute('aria-selected', b.dataset.tab === labTab));
    let h = '';
    if (labTab === 'demo') {
      let host = p.demoUrl; try { host = new URL(p.demoUrl).host; } catch (e) { /* URL sin formato */ }
      // Se embebe salvo que la plataforma no lo permita, o que sea http dentro de un sitio https (el navegador lo bloquea).
      const embebe = !p.demoExt && !(location.protocol === 'https:' && /^http:/i.test(p.demoUrl));
      const cp = v => `<code>${esc(v)}</code><button type="button" class="lab-copiar" data-copiar="${esc(v)}">Copiar</button>`;
      const llave = p.demoUser
        ? `<span class="lab-llave">Usuario de prueba</span><span>${cp(p.demoUser)}</span>${p.demoPass ? `<span>Clave ${cp(p.demoPass)}</span>` : ''}${p.demoNota ? `<span class="tenue">${esc(p.demoNota)}</span>` : ''}`
        : `<span>¿No tienes usuario de prueba?</span><a class="btn btn-borde btn-sm" href="${esc(urlWa('Hola, quiero un usuario de prueba para ' + p.name + '.'))}" target="_blank" rel="noopener">Pedirlo por WhatsApp</a>`;
      const marco = `<iframe class="lab-webview" src="${esc(p.demoUrl)}" title="${esc(p.name)} en vivo" allow="clipboard-read; clipboard-write; fullscreen; geolocation" referrerpolicy="strict-origin-when-cross-origin" sandbox="allow-scripts allow-same-origin allow-forms allow-popups allow-popups-to-escape-sandbox allow-downloads allow-modals"></iframe><div class="lab-carga"><span class="tor-puntos"><i></i><i></i><i></i></span><span>Abriendo ${esc(p.name)}…</span></div>`;
      const barra = `<div class="barra"><i></i><i></i><i></i><span>${/^https:/i.test(p.demoUrl) ? '🔒 ' : ''}${esc(host)}</span><button type="button" class="lab-ctrl" data-lab="recargar" title="Recargar" aria-label="Recargar">↻</button><button type="button" class="lab-ctrl" data-lab="completa" title="Pantalla completa" aria-label="Pantalla completa">⤢</button><a class="lab-ctrl" href="${esc(p.demoUrl)}" target="_blank" rel="noopener" title="Abrir en pestaña nueva" aria-label="Abrir en pestaña nueva">↗</a></div>`;
      if (embebe) {
        h = p.mobile
          ? `<div class="lab-acceso movil"><div class="escena-cel"><div class="celular lab-cel">${marco}</div></div><div class="lab-lado-cel"><p class="small tenue">Así se ve en el celular. También puedes abrirla en tu teléfono: ${esc(host)}</p><a class="btn btn-marfil btn-sm" href="${esc(p.demoUrl)}" target="_blank" rel="noopener">Abrir en pestaña nueva ${svgFlecha}</a></div></div>`
          : `<div class="navegador lab-acceso lab-vivo" id="labVivo">${barra}<div class="lab-pantalla">${marco}</div></div>`;
      } else {
        const prev = p.tipo ? `<div class="tipo" style="background:${esc(p.tipo[0])};color:${esc(p.tipo[1])};"><span>${esc(p.client)}</span><b>${esc(p.name)}</b><span>${esc(p.kind)}</span></div>` : (p.video && !p.mobile ? `<video src="${esc(p.video)}" poster="${esc(p.img)}" autoplay muted loop playsinline aria-hidden="true"></video>` : `<img src="${esc(p.shot || p.img)}" alt="">`);
        h = `<div class="navegador lab-acceso"><div class="barra"><i></i><i></i><i></i><span>${esc(host)}</span></div><div class="lab-previa">${prev}<div class="lab-entrar"><a class="btn btn-marfil" href="${esc(p.demoUrl)}" target="_blank" rel="noopener">Abrir ${esc(p.name)} ${svgFlecha}</a><span>Esta plataforma se abre en una pestaña nueva</span></div></div></div>`;
      }
      $('#labCred').innerHTML = llave + (/^http:/i.test(p.demoUrl) ? '<span class="lab-aviso">Conexión sin candado (http)</span>' : '');
    } else if (labTab === 'video' && p.video) {
      h = p.mobile ? `<div class="escena-cel"><div class="celular"><video src="${esc(p.video)}" autoplay muted loop playsinline controls aria-label="Video de ${esc(p.name)}"></video></div></div>` : `<div class="navegador"><div class="barra"><i></i><i></i><i></i><span>Recorrido de ${esc(p.name)}</span></div><video src="${esc(p.video)}" autoplay muted loop playsinline controls aria-label="Video de ${esc(p.name)}"></video></div>`;
      $('#labCred').innerHTML = '';
    } else {
      h = `<div class="caso-hero" style="margin-bottom:32px;aspect-ratio:16/10;"><img src="${esc(p.img)}" alt="${esc(p.name)}: ${esc(p.kind)}" style="object-position:top left;"></div><div class="datos" style="margin-top:0;${p.result ? '' : 'grid-template-columns:repeat(2,minmax(0,1fr));'}"><div><span class="etq">El reto</span><p>${esc(p.problem)}</p></div><div><span class="etq">Lo que construimos</span><p>${esc(p.solution)}</p></div>${p.result ? `<div><span class="etq">Resultado</span><h3 class="h-m">${esc(p.result)}</h3></div>` : ''}</div>`;
      $('#labCred').innerHTML = `<a class="btn btn-borde btn-sm" href="${esc(p.url)}">Ver el caso completo</a>`;
    }
    $('#labEscena').innerHTML = h;
    const fr = $('#labEscena .lab-webview');
    if (fr) {
      const carga = fr.parentNode.querySelector('.lab-carga');
      const listo = () => { if (carga) carga.classList.add('fuera'); };
      fr.addEventListener('load', listo); setTimeout(listo, 9000);
    }
  }
  $('#labEscena').addEventListener('click', e => {
    const b = e.target.closest('[data-lab]'); if (!b) return;
    const fr = $('#labEscena .lab-webview'), vivo = $('#labVivo');
    if (b.dataset.lab === 'recargar' && fr) { const c = fr.parentNode.querySelector('.lab-carga'); if (c) c.classList.remove('fuera'); fr.src = fr.src; }
    if (b.dataset.lab === 'completa' && vivo) { if (document.fullscreenElement) document.exitFullscreen(); else if (vivo.requestFullscreen) vivo.requestFullscreen().catch(() => {}); }
  });
  $('#labLista').addEventListener('click', e => { const b = e.target.closest('[data-app]'); if (b) { labApp = b.dataset.app; labTab = 'demo'; history.replaceState(null, '', urlDemo(labApp)); lab(); } });
  $$('.tabs button').forEach(b => b.addEventListener('click', () => { labTab = b.dataset.tab; lab(); }));
  lab();
}

/* ================= FORMULARIO DE CONTACTO ================= */
const fc = $('#formContacto');
if (fc) fc.addEventListener('submit', async e => {
  e.preventDefault();
  const ok = $('#okContacto'), nom = $('#fNom').value.trim(), tel = $('#fTel').value.trim();
  const falla = (m, el) => { ok.innerHTML = `<span class="msg-error">${esc(m)}</span>`; ok.classList.add('on'); ok.style.background = '#fde8e8'; ok.style.borderColor = '#f5c2c0'; if (el) el.focus(); };
  if (!nom) return falla('Escribe tu nombre.', $('#fNom'));
  if (tel.replace(/\D/g, '').length < 7) return falla('Escribe un WhatsApp de al menos 7 dígitos.', $('#fTel'));
  if (!$('#fOk').checked) return falla('Marca la autorización de datos para poder escribirte.', $('#fOk'));
  const btn = fc.querySelector('button[type=submit]'); btn.disabled = true;
  const r = await enviar({ origen: 'contacto', nombre: nom, telefono: tel, linea: $('#fLin').value, mensaje: $('#fMsg').value, consent: '1', sitio: fc.querySelector('[name=sitio]').value });
  btn.disabled = false;
  if (!r.ok) return falla(r.msg);
  ok.style.background = ''; ok.style.borderColor = '';
  ok.textContent = 'Recibimos tu mensaje, ' + nom.split(' ')[0] + '. Te escribimos al ' + tel + ' en horario hábil.'; ok.classList.add('on'); fc.reset();
});

/* ================= COMÚN A TODAS LAS PÁGINAS ================= */

/* Fases: línea que se dibuja al entrar en pantalla */
(function observar() {
  const fs = $$('.view.on .fases');
  if (!('IntersectionObserver' in window) || reduce) { $$('.fase').forEach(f => f.classList.add('vista')); return; }
  const io = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { Array.from(e.target.children).forEach((f, i) => setTimeout(() => f.classList.add('vista'), i * 260)); io.unobserve(e.target); } }), { threshold: .35 });
  fs.forEach(f => io.observe(f));
})();
dispararRoll($('.view.on'));

/* Ondas que se dibujan, revelado de imágenes, orillas y titulares */
(function animarScroll() {
  if (reduce || !G || !ST) return;
  const una = el => { if (el.dataset.anim) return false; el.dataset.anim = '1'; return true; };
  $$('.dibujar path').forEach(p => { if (!una(p)) return; const L = p.getTotalLength(); G.fromTo(p, { strokeDasharray: L, strokeDashoffset: L }, { strokeDashoffset: 0, ease: 'none', scrollTrigger: { trigger: p.closest('section, footer'), start: 'top 85%', end: 'bottom 40%', scrub: 1 } }); });
  $$('.view.on .cartel-foto img, .view.on .laptop').forEach(el => { if (una(el) && el.getBoundingClientRect().top > innerHeight) G.from(el, { clipPath: 'inset(12% 12% 12% 12% round 28px)', scale: 1.08, duration: 1.4, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 85%' } }); });
  $$('.view.on .orilla').forEach(el => { if (una(el) && el.getBoundingClientRect().top > innerHeight) G.from(el, { y: 60, rotateX: 8, opacity: 0, duration: 1.1, delay: Array.prototype.indexOf.call(el.parentNode.children, el) * .12, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 90%' } }); });
  $$('.view.on .tor-lista, .view.on .tor-cifras, .view.on .tor-manifiesto, .view.on .tor-otros').forEach(g => { if (una(g) && g.getBoundingClientRect().top > innerHeight) G.from(g.children, { y: 36, opacity: 0, duration: .9, stagger: .08, ease: 'expo.out', scrollTrigger: { trigger: g, start: 'top 88%' } }); });
  $$('.view.on .tor-retrato img, .view.on .tor-aurea').forEach(el => { if (una(el) && el.getBoundingClientRect().top > innerHeight) G.from(el, { clipPath: 'inset(10% 10% 10% 10% round 28px)', duration: 1.4, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 85%' } }); });
  $$('.view.on h2').forEach(h => { if (una(h) && h.getBoundingClientRect().top > innerHeight && !h.closest('.slide')) { const w = partir(h); G.from(w, { yPercent: 110, duration: 1, stagger: .05, ease: 'power4.out', scrollTrigger: { trigger: h, start: 'top 88%' } }); } });
  addEventListener('load', () => ST.refresh());
})();

/* Inclinación 3D de las orillas */
$$('.orilla').forEach(c => {
  c.addEventListener('pointermove', e => { if (reduce) return; const r = c.getBoundingClientRect(); const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5; c.style.transform = `perspective(900px) rotateY(${x * 8}deg) rotateX(${-y * 8}deg) translateY(-4px)`; const g = c.querySelector('.glow'); if (g) g.style.transform = `translate(${x * 80}px, ${y * 80}px)`; });
  c.addEventListener('pointerleave', () => { c.style.transform = ''; const g = c.querySelector('.glow'); if (g) g.style.transform = ''; });
});

/* Cursor con etiqueta sobre los medios */
(function cursor() {
  const cu = $('#cursor'); if (!cu || reduce || matchMedia('(hover: none)').matches) return;
  let x = 0, y = 0, cx = 0, cy = 0;
  addEventListener('pointermove', e => { x = e.clientX; y = e.clientY; const m = e.target.closest('[data-cursor], .proy, .reel-escena, .lab-item'); cu.classList.toggle('on', !!m); if (m) cu.textContent = m.dataset.cursor || (m.classList.contains('lab-item') ? 'Probar' : 'Ver'); });
  (function f() { cx += (x - cx) * .2; cy += (y - cy) * .2; cu.style.left = cx + 'px'; cu.style.top = cy + 'px'; requestAnimationFrame(f); })();
})();

/* Menú con previsualizaciones en movimiento */
(function mega() {
  const m = $('#mega'), wrap = $('#menuWrap'); if (!m || !PROJECTS.length) return; let tm;
  const flecha = '<svg width="12" height="12" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 7h8M7 3l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>';
  const SERVS = TOR.servs || [];
  const ORI = { e: ['Orilla 01', 'Estrategia', 'var(--champan)', 'var(--bronce)'], c: ['Orilla 02', 'Creatividad', 'var(--salvia)', 'var(--oliva)'], t: ['Orilla 03', 'Tecnología', 'var(--hielo)', 'var(--cobalto)'] };

  /* Trabajo: el portafolio. Categorías con su conteo y un mosaico de capturas (sin videos). */
  const trabajo = () => {
    const cats = CATS.filter(c => c[0] !== 'todo' && cuenta(c[0]) > 0);
    const usados = new Set(SERVS.map(s => s.ref));
    const mosaico = PROJECTS.filter(p => !p.tipo && p.img && !usados.has(p.id)).slice(0, 3);
    return `<div class="mg-col">
        <p class="mg-etq">Portafolio · ${PROJECTS.length} proyectos</p>
        <div class="mg-cats">${cats.map(c => `<a href="${esc(urlCat(c[0]))}"><span>${esc(c[1])}</span><i>${cuenta(c[0])}</i></a>`).join('')}</div>
        <a class="mg-todo" href="${esc(U.trabajo)}">Ver todo el trabajo ${flecha}</a>
      </div>
      <div class="mg-mosaico">${mosaico.map((p, i) => `<a class="mg-tesela t${i}" href="${esc(p.url)}"><img src="${esc(p.img)}" alt=""><span class="mg-pie"><b>${esc(p.name)}</b><small>${esc(p.kind)}</small></span></a>`).join('')}</div>`;
  };

  /* Servicios: tres columnas, una por orilla, solo texto y color. */
  const servicios = () => {
    const col = o => { const g = SERVS.filter(s => s.o === o); if (!g.length) return ''; const r = ORI[o];
      return `<div class="mg-orilla" style="--c:${r[2]};--ct:${r[3]};"><p class="mg-etq"><i></i>${r[0]} · ${r[1]}</p>${g.map(s => `<a href="${esc(s.url)}"><b>${esc(s.name)}</b><small>${esc(s.corto || '')}</small></a>`).join('')}</div>`; };
    return `<div class="mg-orillas">${col('e')}${col('c')}${col('t')}</div>
      <div class="mg-base"><span>Todos empiezan con un diagnóstico de una semana.</span><a class="btn btn-tinta btn-sm" href="${esc(U.servicios)}">Ver todos los servicios ${flecha}</a></div>`;
  };

  /* Demo Lab: panel oscuro con la lista de demos y una vista previa en vivo. */
  const demos = PROJECTS.filter(p => p.demo);
  const demoLab = () => {
    const p0 = demos.find(p => p.video && !p.mobile) || demos[0];
    return `<div class="mg-lista">
        <p class="mg-etq"><span class="live"></span>${demos.length} plataformas navegables</p>
        ${demos.map(p => `<a href="${esc(urlDemo(p.demo))}" data-demo="${esc(p.id)}"${p === p0 ? ' class="on"' : ''}><b>${esc(p.name)}</b><small>${esc(p.kind)}</small><em>Entrar ${flecha}</em></a>`).join('')}
      </div>
      <div class="mg-vista"><div class="mg-barra"><i></i><i></i><i></i><span id="mgUrl">${esc(hostDe(p0.demoUrl))}</span></div>
        ${p0.video && !p0.mobile ? `<video id="mgVid" src="${esc(p0.video)}" poster="${esc(p0.img)}" autoplay muted loop playsinline aria-hidden="true"></video>` : `<img id="mgVid" src="${esc(p0.img)}" alt="">`}
        <span class="mg-acceso">Acceso con usuario de prueba</span></div>`;
  };

  const DATA = { trabajo, servicios, 'demo-lab': demoLab };
  const cerrar = () => { m.classList.remove('on'); m.setAttribute('aria-hidden', 'true'); setTimeout(() => { if (!m.classList.contains('on')) { m.innerHTML = ''; m.dataset.tipo = ''; } }, 300); };
  $$('.menu a').forEach(a => {
    const k = a.dataset.nav; if (!DATA[k]) { a.addEventListener('mouseenter', cerrar); return; }
    a.addEventListener('mouseenter', () => {
      clearTimeout(tm);
      if (m.dataset.tipo !== k) {
        m.innerHTML = DATA[k](); m.dataset.tipo = k;
        if (G && !reduce) G.from(m.querySelectorAll('.mg-cats a, .mg-tesela, .mg-orilla, .mg-lista a, .mg-vista'), { y: 12, opacity: 0, duration: .45, stagger: .025, ease: 'power3.out' });
      }
      m.classList.add('on'); m.setAttribute('aria-hidden', 'false');
    });
  });
  /* Vista previa del Demo Lab según la demo bajo el puntero */
  m.addEventListener('mouseover', e => {
    const d = e.target.closest('[data-demo]'); if (!d || d.classList.contains('on')) return;
    const p = byId[d.dataset.demo]; const v = $('#mgVid'); if (!p || !v) return;
    m.querySelectorAll('[data-demo]').forEach(x => x.classList.toggle('on', x === d));
    $('#mgUrl').textContent = hostDe(p.demoUrl);
    if (v.tagName === 'VIDEO' && p.video && !p.mobile) { v.poster = p.img; v.src = p.video; v.play().catch(() => {}); }
    else if (v.tagName === 'VIDEO') { v.removeAttribute('src'); v.load(); v.poster = p.img; }
    else v.src = p.img;
  });
  wrap.addEventListener('mouseleave', () => { tm = setTimeout(cerrar, 150); });
  m.addEventListener('mouseenter', () => clearTimeout(tm));
  m.addEventListener('click', e => { if (e.target.closest('a')) cerrar(); });
})();

/* Cabecera y menú móvil */
$('#burger').onclick = () => { const o = $('#menu').classList.toggle('open'); $('#burger').setAttribute('aria-expanded', o); };
addEventListener('scroll', () => $('#top').classList.toggle('scrolled', scrollY > 8), { passive: true });

/* ================= CHAT «Cotiza en vivo» =================
   Tres preguntas, luego deja el WhatsApp (se guarda como contacto) o sigue por WhatsApp.
   No inventa precios ni simula un asesor. */
const panel = $('#panel'), msgs = $('#msgs');
const ch = { on: false };
function decir(t, yo) { const d = document.createElement('div'); d.className = 'msg' + (yo ? ' yo' : ''); d.textContent = t; msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight; }
function opciones(l, cb) { const d = document.createElement('div'); d.className = 'opts'; l.forEach(t => { const b = document.createElement('button'); b.type = 'button'; b.className = 'chip'; b.textContent = t; b.onclick = () => { d.remove(); decir(t, true); setTimeout(() => cb(t), 420); }; d.appendChild(b); }); msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight; }
function resumen() { return 'Hola, quiero cotizar: ' + [ch.l, ch.s, ch.p].filter(Boolean).join(' · ') + (ch.extra ? '. ' + ch.extra : ''); }
function iniciarChat() {
  if (ch.on) return; ch.on = true;
  decir('Hola. Te ayudo a preparar tu cotización en tres preguntas. ¿Qué quieres construir?');
  opciones(['Software a la medida', 'App móvil', 'Web o e-commerce', 'Marketing digital', 'Chatbot o IA'], l => {
    ch.l = l; decir('¿De qué tamaño es el proyecto?');
    opciones(['Pequeño, hasta 5 usuarios', 'Mediano, 5 a 50', 'Grande, más de 50'], s => {
      ch.s = s; decir('¿Para cuándo lo necesitas?');
      opciones(['Este mes', 'En 1 a 3 meses', 'Estoy explorando'], p => { ch.p = p; cerrarCotizacion(); });
    });
  });
}
function cerrarCotizacion() {
  decir('Gracias. Con esto un asesor te prepara un estimado según proyectos parecidos. El valor final se define en el diagnóstico.');
  const q = document.createElement('div'); q.className = 'cotiz'; q.innerHTML = `<span class="small tenue">Tu solicitud</span><b>${esc(ch.l)}</b><span class="small tenue">${esc(ch.s)} · ${esc(ch.p)}</span>`; msgs.appendChild(q);
  const f = document.createElement('form'); f.className = 'chat-form'; f.noValidate = true;
  f.innerHTML = `<label for="chatTel" class="small" style="font-weight:600;">¿A qué WhatsApp te enviamos el estimado?</label><div class="fila-in"><input class="input" id="chatTel" type="tel" inputmode="tel" placeholder="+57 300 000 0000" autocomplete="tel" maxlength="40"><button class="btn btn-tinta btn-sm" type="submit">Enviar</button></div><label class="acepto" for="chatOk"><input type="checkbox" id="chatOk"><span>Autorizo el tratamiento de mis datos según la <a href="${esc(U.politica)}" target="_blank" rel="noopener">política de datos</a>.</span></label><span class="trampa" aria-hidden="true"><input type="text" name="sitio" tabindex="-1" autocomplete="off"></span><span class="small tenue" id="chatMsg"></span>`;
  f.addEventListener('submit', async e => {
    e.preventDefault(); const t = f.querySelector('#chatTel').value.trim(), m = f.querySelector('#chatMsg');
    if (t.replace(/\D/g, '').length < 7) { m.textContent = 'Escribe un número de al menos 7 dígitos.'; return; }
    if (!f.querySelector('#chatOk').checked) { m.textContent = 'Marca la autorización de datos para poder escribirte.'; return; }
    const btn = f.querySelector('button'); btn.disabled = true;
    const r = await enviar({ origen: 'chat', telefono: t, linea: ch.l, mensaje: [ch.s, ch.p, ch.extra].filter(Boolean).join(' · '), consent: '1', sitio: f.querySelector('[name=sitio]').value });
    btn.disabled = false;
    if (!r.ok) { m.innerHTML = `<span class="msg-error">${esc(r.msg)}</span>`; return; }
    f.remove(); decir('Listo. Te escribimos al ' + t + (enHorario() ? ' en unos minutos.' : ' en cuanto abramos, desde las ' + (AJ.hIni || 8) + ':00.'));
  });
  msgs.appendChild(f);
  const c = document.createElement('button'); c.type = 'button'; c.className = 'opcion prin'; c.innerHTML = '<span class="live"></span><span><b>Prefiero una videollamada</b><small>Por Zoom, en el horario que elijas</small></span>'; c.onclick = llamar; msgs.appendChild(c);
  const w = document.createElement('a'); w.className = 'opcion'; w.href = urlWa(resumen()); w.target = '_blank'; w.rel = 'noopener'; w.innerHTML = `<span><b>Seguir por WhatsApp</b><small>${esc(AJ.waTxt || '')}</small></span>${svgFlecha}`; msgs.appendChild(w);
  msgs.scrollTop = msgs.scrollHeight;
}
$('#chatForm').addEventListener('submit', e => {
  e.preventDefault(); if (vivo) { vivo.enviar(); return; } const v = $('#chatIn').value.trim().slice(0, 500); if (!v) return; decir(v, true); $('#chatIn').value = '';
  ch.extra = ch.extra ? ch.extra + ' ' + v : v;
  setTimeout(() => {
    decir('Gracias, lo agregamos a tu solicitud. Para hablar ya con una persona, sigue por WhatsApp.');
    const w = document.createElement('a'); w.className = 'opcion'; w.href = urlWa(ch.l ? resumen() : 'Hola, ' + v); w.target = '_blank'; w.rel = 'noopener'; w.innerHTML = `<span><b>Abrir WhatsApp</b><small>Con tu mensaje ya escrito</small></span>${svgFlecha}`; msgs.appendChild(w); msgs.scrollTop = msgs.scrollHeight;
  }, 450);
});
/* ================= COTIZA EN VIVO conectado al ERP =================
   Mismo panel del diseño. Flujo del plugin «Orillas Cotiza en vivo»: nombre + WhatsApp (+ autorización),
   producto de interés y conversación con asesores o IA de la bandeja del ERP. Sesión reanudable (localStorage).
   Sondeo adaptable: rápido justo después de escribir, lento en reposo, pausado con el panel cerrado o la pestaña oculta. */
const vivo = (function () {
  const C = AJ.chat; if (!C) return null;
  const KEY = 'tor_chat_v1', pie = $('#chatForm');
  const st = { token: '', msgs: [], ultimo: '', producto: '', abierto: false, enviando: false, timer: null, actividad: 0, pausa: 0 };
  try { const g = JSON.parse(localStorage.getItem(KEY) || 'null'); if (g && g.token) st.token = g.token; } catch (e) { /* sin almacenamiento */ }
  const guardar = t => { st.token = t || ''; try { t ? localStorage.setItem(KEY, JSON.stringify({ token: t, at: Date.now() })) : localStorage.removeItem(KEY); } catch (e) { /* sin almacenamiento */ } };
  const uid = () => 'ww-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 8);

  const api = async (ruta, metodo, cuerpo) => {
    const ir = () => fetch(C.api + ruta, { method: metodo || 'GET', headers: { 'Content-Type': 'application/json', 'X-TOR-Nonce': TOR.nonce || '' }, body: cuerpo ? JSON.stringify(cuerpo) : undefined, credentials: 'same-origin', cache: 'no-store' });
    let r = await ir();
    if (r.status === 403) { try { const n = await fetch(TOR.ajax + '?action=tor_nonce', { credentials: 'same-origin' }).then(x => x.json()); if (n && n.data && n.data.nonce) { TOR.nonce = n.data.nonce; r = await ir(); } } catch (e) { /* sin red */ } }
    let j = {}; try { j = await r.json(); } catch (e) { /* vacío */ }
    if (!r.ok) { const err = new Error((j && j.message) || 'No pudimos conectar con el chat.'); err.status = r.status; throw err; }
    return j;
  };

  const etiqueta = m => m.quien === 'user' ? 'Asesor · 3 Orillas' : (m.quien === 'ai_bot' ? 'Asistente IA' : '');
  const pintar = () => {
    msgs.innerHTML = '';
    st.msgs.forEach(m => {
      const d = document.createElement('div'); d.className = 'msg' + (m.quien === 'contact' ? ' yo' : ' de-' + m.quien) + (m.local ? ' enviando' : '');
      const et = etiqueta(m); if (et) { const s = document.createElement('span'); s.className = 'msg-quien'; s.textContent = et; d.appendChild(s); }
      d.appendChild(document.createTextNode(m.texto)); msgs.appendChild(d);
    });
    if (!st.msgs.some(m => m.quien === 'contact') && !st.producto) {
      const o = document.createElement('div'); o.className = 'opts';
      (C.productos || []).forEach(p => { const b = document.createElement('button'); b.type = 'button'; b.className = 'chip'; b.textContent = p; b.onclick = () => enviar(p, true); o.appendChild(b); });
      msgs.appendChild(o);
    }
    msgs.scrollTop = msgs.scrollHeight;
  };
  const unir = lista => {
    if (!lista || !lista.length) return false;
    const mapa = new Map(st.msgs.filter(m => !m.local).map(m => [m.id, m])); let nuevo = false;
    lista.forEach(m => { if (!mapa.has(m.id)) nuevo = true; mapa.set(m.id, m); });
    st.msgs = Array.from(mapa.values()).sort((a, b) => String(a.fecha).localeCompare(String(b.fecha))).concat(st.msgs.filter(m => m.local));
    const reales = st.msgs.filter(m => !m.local); if (reales.length) st.ultimo = reales[reales.length - 1].id;
    return nuevo;
  };
  const aviso = t => { const d = document.createElement('div'); d.className = 'msg msg-sistema'; d.innerHTML = `<span class="msg-error">${esc(t)}</span>`; msgs.appendChild(d); msgs.scrollTop = msgs.scrollHeight; };

  /* Sondeo adaptable */
  const ritmo = () => { const q = Date.now() - st.actividad; return q < 90000 ? 3000 : (q < 300000 ? 8000 : 20000); };
  const parar = () => { clearTimeout(st.timer); st.timer = null; };
  const programar = espera => { parar(); if (!st.abierto || !st.token || document.hidden) return; st.timer = setTimeout(sondear, espera || ritmo()); };
  async function sondear() {
    if (!st.token || !st.abierto || document.hidden) return;
    try {
      const j = await api('/' + encodeURIComponent(st.token) + '/mensajes' + (st.ultimo ? '?after=' + encodeURIComponent(st.ultimo) : ''));
      if (unir(j.mensajes)) { pintar(); st.actividad = Date.now(); }
      programar();
    } catch (e) {
      if (e.status === 404) { guardar(''); st.msgs = []; identificar('La conversación anterior expiró. Empecemos una nueva.'); return; }
      programar(e.status === 429 ? 60000 : 15000);
    }
  }
  document.addEventListener('visibilitychange', () => { if (!document.hidden && st.abierto && st.token) sondear(); });

  /* Paso 1: nombre + WhatsApp + autorización (obligatorios) */
  function identificar(nota) {
    pie.hidden = true; msgs.innerHTML = '';
    const d = document.createElement('div'); d.className = 'msg'; d.textContent = nota || 'Hola. Para empezar, déjanos tu nombre y WhatsApp. Un asesor (o nuestra IA) te responde aquí mismo.'; msgs.appendChild(d);
    const f = document.createElement('form'); f.className = 'chat-form'; f.noValidate = true;
    f.innerHTML = `<label class="campo" for="cvNom">Nombre<input class="input" id="cvNom" autocomplete="name" maxlength="120" placeholder="Tu nombre"></label>
      <label class="campo" for="cvTel">WhatsApp<input class="input" id="cvTel" type="tel" inputmode="tel" autocomplete="tel" maxlength="40" placeholder="Ej. 300 123 4567"></label>
      <label class="acepto" for="cvOk"><input type="checkbox" id="cvOk"><span>Autorizo el tratamiento de mis datos según la <a href="${esc(U.politica)}" target="_blank" rel="noopener">política de datos</a>.</span></label>
      <span class="trampa" aria-hidden="true"><input type="text" name="sitio" tabindex="-1" autocomplete="off"></span>
      <button class="btn btn-tinta btn-sm" type="submit">Empezar chat</button><span class="small" id="cvMsg" role="status"></span>`;
    f.addEventListener('submit', async e => {
      e.preventDefault(); const m = f.querySelector('#cvMsg'), err = t => { m.innerHTML = `<span class="msg-error">${esc(t)}</span>`; };
      const nom = f.querySelector('#cvNom').value.trim(), tel = f.querySelector('#cvTel').value.trim();
      if (!nom) return err('Escribe tu nombre.');
      if (tel.replace(/\D/g, '').length < 7) return err('Escribe un WhatsApp de al menos 7 dígitos.');
      if (!f.querySelector('#cvOk').checked) return err('Marca la autorización de datos para poder chatear.');
      const b = f.querySelector('button'); b.disabled = true; b.textContent = 'Conectando…';
      try {
        const j = await api('', 'POST', { nombre: nom, whatsapp: tel, producto: st.producto, token: st.token, pagina: location.href, consent: 1, sitio: f.querySelector('[name=sitio]').value });
        guardar(j.token); st.msgs = []; st.ultimo = ''; unir(j.mensajes); st.actividad = Date.now(); conversar();
      } catch (x) { b.disabled = false; b.textContent = 'Empezar chat'; err(x.message); }
    });
    msgs.appendChild(f); setTimeout(() => { const i = f.querySelector('#cvNom'); if (i) i.focus(); }, 300);
  }

  /* Paso 2: conversación */
  function conversar() { pie.hidden = false; pintar(); programar(1500); setTimeout(() => $('#chatIn').focus(), 200); }

  async function enviar(texto, esProducto) {
    texto = String(texto || '').trim().slice(0, 1500); if (!texto || !st.token || st.enviando) return;
    if (esProducto) st.producto = texto;
    st.enviando = true; const loc = { id: 'local-' + uid(), quien: 'contact', texto, fecha: new Date().toISOString(), local: true };
    st.msgs.push(loc); pintar();
    try {
      const j = await api('/' + encodeURIComponent(st.token) + '/mensajes', 'POST', { texto, id: uid(), producto: st.producto });
      st.msgs = st.msgs.filter(m => m !== loc); if (j.mensaje) unir([j.mensaje]); pintar();
      st.actividad = Date.now(); programar(2500);
    } catch (x) {
      st.msgs = st.msgs.filter(m => m !== loc); pintar(); aviso(x.message);
      if (x.status === 404) { guardar(''); identificar('La conversación expiró. Empecemos una nueva.'); }
    } finally { st.enviando = false; }
  }

  return {
    abrir() {
      st.abierto = true;
      if (!st.token) { identificar(); return; }
      pie.hidden = false; if (!st.msgs.length) msgs.innerHTML = '<div class="msg">Cargando tu conversación…</div>';
      api('/' + encodeURIComponent(st.token) + '/mensajes').then(j => { unir(j.mensajes); conversar(); }).catch(e => { if (e.status === 404) guardar(''); st.msgs = []; identificar(e.status === 404 ? 'La conversación anterior expiró. Empecemos una nueva.' : ''); });
    },
    cerrar() { st.abierto = false; parar(); },
    enviar() { const i = $('#chatIn'); const v = i.value; i.value = ''; enviar(v, false); }
  };
})();

function abrirChat() { panel.hidden = false; $('#lanzador').style.visibility = 'hidden'; if (vivo) { vivo.abrir(); return; } iniciarChat(); setTimeout(() => $('#chatIn').focus(), 300); }
function cerrarChat() { panel.hidden = true; $('#lanzador').style.visibility = ''; if (vivo) vivo.cerrar(); }
$$('[data-chat]').forEach(b => b.addEventListener('click', abrirChat)); $('#cerrarChat').onclick = cerrarChat;

/* ================= VIDEOLLAMADA =================
   Abre el enlace real configurado en Ajustes → 3 Orillas. Si la agenda permite insertarse,
   se muestra dentro de la sala; si no, en otra pestaña. Sin enlace, abre WhatsApp. */
const sala = $('#sala');
function llamar() {
  if (AJ.zoom) { agendaZoom(); return; }
  const url = AJ.llamada;
  if (!url) { window.open(urlWa('Hola, quiero agendar una videollamada con un asesor de 3 Orillas.'), '_blank', 'noopener'); return; }
  if (!AJ.embebida) { window.open(url, '_blank', 'noopener'); return; }
  const main = $('#salaMain');
  main.innerHTML = '<div class="conectando"><span class="iso" style="width:84px;height:84px;background:var(--marfil);"><img src="' + esc(U.media + 'iso-tinta.png') + '" alt="" style="width:46px;height:auto;filter:none;"></span><h3 class="h-m">Abriendo la agenda</h3><p class="small" style="color:#9E9A92;">Elige el horario que te sirva y recibirás el enlace de Zoom.</p></div>';
  const fr = document.createElement('iframe'); fr.src = url; fr.title = 'Agenda de videollamada'; fr.style.opacity = '0'; fr.onload = () => { fr.style.opacity = '1'; const c = main.querySelector('.conectando'); if (c) c.remove(); }; main.appendChild(fr);
  $('#salaFuera').href = url; sala.hidden = false; $('#finLlamada').focus();
}
function colgar() { sala.hidden = true; $('#salaMain').innerHTML = ''; $('#salaMain').className = 'sala-main agenda'; $('#salaFuera').style.display = 'inline-flex'; }
$$('[data-llamar]').forEach(b => b.addEventListener('click', llamar)); $('#finLlamada').onclick = colgar;
sala.addEventListener('click', e => { if (e.target === sala) colgar(); });
addEventListener('keydown', e => { if (e.key === 'Escape') { if (!sala.hidden) colgar(); else if (!panel.hidden) cerrarChat(); } });

/* ================= SERVICIOS ================= */
hoverVideo($('.tor-svcs')); $$('.tor-obras').forEach(hoverVideo);

/* Marco del proyecto en el encabezado: se inclina con el puntero */
$$('.tor-marco').forEach(c => {
  c.addEventListener('pointermove', e => { if (reduce) return; const r = c.getBoundingClientRect(); const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5; c.style.transform = `perspective(1200px) rotateY(${x * 6}deg) rotateX(${-y * 6}deg)`; });
  c.addEventListener('pointerleave', () => { c.style.transform = ''; });
});

/* Pestañas (automatización por área): flechas del teclado y transición suave */
$$('[data-tabs]').forEach(t => {
  const tabs = Array.from(t.querySelectorAll('[role=tab]'));
  const ver = (b, foco) => {
    tabs.forEach(x => { const on = x === b; x.setAttribute('aria-selected', on); x.tabIndex = on ? 0 : -1; const p = document.getElementById(x.getAttribute('aria-controls')); if (p) p.hidden = !on; });
    const p = document.getElementById(b.getAttribute('aria-controls'));
    if (p && G && !reduce) G.from(p.children, { y: 18, opacity: 0, duration: .5, stagger: .05, ease: 'power3.out' });
    if (foco) b.focus();
  };
  tabs.forEach((b, i) => {
    b.addEventListener('click', () => ver(b));
    b.addEventListener('keydown', e => { const d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0; if (d) { e.preventDefault(); ver(tabs[(i + d + tabs.length) % tabs.length], true); } });
  });
});

/* Conversación de ejemplo del chatbot (guion fijo, se presenta como ejemplo) */
(function chatDemo() {
  const box = $('#chatDemo'), ops = $('#chatDemoOps'); if (!box || !ops) return;
  const GUION = [
    ['Quiero saber precios', 'Con gusto. ¿Qué producto te interesa? Te muestro opciones y precios del catálogo.', 'Los kits de regalo', 'Tenemos tres kits. Te envío el catálogo con fotos y precios. ¿Quieres que un asesor te ayude a cerrar el pedido?'],
    ['Tengo un problema con mi pedido', 'Lo reviso de una. ¿Me compartes el número de pedido?', 'Es el 4821', 'Tu pedido 4821 salió hoy y llega mañana. Te aviso por aquí cuando esté en camino.'],
    ['Quiero agendar una cita', 'Claro. Tengo disponible el martes a las 10:00 a. m. o el jueves a las 3:00 p. m. ¿Cuál prefieres?', 'El jueves', 'Listo, quedó el jueves a las 3:00 p. m. Te envío un recordatorio el día anterior.']
  ];
  const inicial = box.innerHTML;
  let ocupado = false;
  const burbuja = (t, yo) => { const d = document.createElement('div'); d.className = 'tor-burbuja' + (yo ? ' yo' : ''); d.textContent = t; box.appendChild(d); box.scrollTop = box.scrollHeight; if (G && !reduce) G.from(d, { y: 12, opacity: 0, duration: .4, ease: 'power2.out' }); return d; };
  const espera = ms => new Promise(r => setTimeout(r, reduce ? 0 : ms));
  ops.addEventListener('click', async e => {
    const b = e.target.closest('[data-guion]'); if (!b || ocupado) return;
    ocupado = true; ops.querySelectorAll('button').forEach(x => { x.disabled = true; });
    const g = GUION[+b.dataset.guion] || GUION[0];
    box.innerHTML = inicial;
    for (let i = 0; i < g.length; i++) {
      if (i % 2) {
        const p = document.createElement('div'); p.className = 'tor-burbuja tor-puntos'; p.setAttribute('aria-label', 'Escribiendo'); p.innerHTML = '<i></i><i></i><i></i>'; box.appendChild(p); box.scrollTop = box.scrollHeight;
        await espera(900); box.removeChild(p); burbuja(g[i]);
      } else { burbuja(g[i], true); }
      await espera(500);
    }
    ocupado = false; ops.querySelectorAll('button').forEach(x => { x.disabled = false; });
  });
})();

/* Solicitud de eliminación de datos */
const fd_ = $('#formDatos');
if (fd_) fd_.addEventListener('submit', async e => {
  e.preventDefault();
  const ok = $('#okDatos'), nom = $('#dNom').value.trim(), cor = $('#dCor').value.trim(), tel = $('#dTel').value.trim(), msg = $('#dMsg').value.trim();
  const falla = (m, el) => { ok.innerHTML = `<span class="msg-error">${esc(m)}</span>`; ok.classList.add('on'); if (el) el.focus(); };
  if (!nom) return falla('Escribe tu nombre completo.', $('#dNom'));
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(cor) && tel.replace(/\D/g, '').length < 7) return falla('Déjanos un correo o un teléfono para confirmarte.', $('#dCor'));
  if (!msg) return falla('Cuéntanos qué datos quieres eliminar o corregir.', $('#dMsg'));
  if (!$('#dOk').checked) return falla('Marca la autorización para poder tramitar tu solicitud.', $('#dOk'));
  const btn = fd_.querySelector('button[type=submit]'); btn.disabled = true;
  const r = await enviar({ origen: 'datos', nombre: nom, correo: cor, telefono: tel, plataforma: $('#dPla').value, mensaje: msg, consent: '1', sitio: fd_.querySelector('[name=sitio]').value });
  btn.disabled = false;
  if (!r.ok) return falla(r.msg);
  ok.textContent = 'Recibimos tu solicitud, ' + nom.split(' ')[0] + '. Te confirmamos en máximo 10 días hábiles.'; ok.classList.add('on'); fd_.reset();
});

/* ================= PÁGINAS INTERNAS: escenario, río, pila, recorrido horizontal ================= */

/* Escenario: luz y parallax con el puntero, entrada coreografiada y salida con el scroll */
$$('[data-escena]').forEach(esc_ => {
  const panel = esc_.querySelector('.tor-escena-panel'), scene = esc_.querySelector('.tor-escena-scene');
  if (!reduce && panel && scene) panel.addEventListener('pointermove', e => {
    const r = panel.getBoundingClientRect(), s = scene.getBoundingClientRect();
    panel.style.setProperty('--lx', (e.clientX - r.left) + 'px'); panel.style.setProperty('--ly', (e.clientY - r.top) + 'px');
    scene.style.setProperty('--mx', ((e.clientX - s.left) / s.width - .5) * 2); scene.style.setProperty('--my', ((e.clientY - s.top) / s.height - .5) * 2);
  });
  const h1 = esc_.querySelector('.tor-escena-h1');
  if (G && !reduce) {
    const tl = G.timeline({ defaults: { ease: 'expo.out' } });
    const de = (t, v, at) => { if (t && (t.length === undefined || t.length)) tl.from(t, v, at); };
    if (h1) de(partir(h1), { yPercent: 115, duration: 1.2, stagger: .06 }, 0);
    de(esc_.querySelectorAll('.tor-escena-txt > :not(h1)'), { y: 24, opacity: 0, duration: 1, stagger: .07 }, .15);
    const nav = esc_.querySelector('.tor-ventana .navegador, .tor-escena-retrato img');
    if (nav) de(nav, { clipPath: 'inset(14% 14% 14% 14% round 18px)', scale: 1.08, opacity: 0, duration: 1.6 }, .2);
    de(esc_.querySelectorAll('.tor-ventana-pie'), { y: 30, opacity: 0, duration: 1 }, .8);
    de(esc_.querySelectorAll('.tor-flota > .bob'), { y: 40, scale: .85, opacity: 0, duration: 1.1, stagger: .14 }, .6);
    de(esc_.querySelectorAll('.tor-orbita, .tor-sello'), { scale: .7, opacity: 0, duration: 1.6 }, .3);
    de(esc_.querySelectorAll('.tor-cinta'), { opacity: 0, duration: 1 }, .9);
    esc_.querySelectorAll('.tor-escena-onda path').forEach((p, i) => { const L = p.getTotalLength(); tl.fromTo(p, { strokeDasharray: L, strokeDashoffset: L }, { strokeDashoffset: 0, duration: 2.6, ease: 'power2.inOut' }, .3 + i * .2); });
    tl.add(() => { dispararRoll(esc_); setTimeout(() => esc_.querySelectorAll('.roll').forEach(r => { r.textContent = r.dataset.to; r.classList.add('fin'); }), 2300); }, .9);
    if (ST && scene) {
      G.to(scene, { yPercent: -10, ease: 'none', scrollTrigger: { trigger: esc_, start: 'top top', end: 'bottom top', scrub: true } });
      G.to(esc_.querySelector('.tor-escena-txt'), { yPercent: 12, opacity: .2, ease: 'none', scrollTrigger: { trigger: esc_, start: 'top top', end: 'bottom top', scrub: true } });
    }
  } else { dispararRoll(esc_); }

  /* Ventana que alterna los servicios (índice) */
  const v = esc_.querySelector('[data-ciclo]'); if (!v) return;
  let lista = []; try { lista = JSON.parse(v.dataset.ciclo) || []; } catch (e) { lista = []; }
  if (lista.length < 2) return;
  const vid = v.querySelector('video'), bar = v.querySelector('.tor-ciclo-bar i'), sEl = v.querySelector('[data-ciclo-s]'), pEl = v.querySelector('[data-ciclo-p]'), url = v.querySelector('.tor-ventana-url');
  const T = 4800; let i = 0;
  const barra = () => { if (!bar) return; bar.style.transition = 'none'; bar.style.width = '0'; bar.getBoundingClientRect(); bar.style.transition = `width ${T}ms linear`; bar.style.width = '100%'; };
  const paso = () => {
    i = (i + 1) % lista.length; const c = lista[i];
    if (vid) { vid.style.opacity = '0'; setTimeout(() => { vid.poster = c.i; vid.src = c.v; vid.play().catch(() => {}); vid.onloadeddata = () => { vid.style.opacity = '1'; }; }, 350); }
    if (sEl) sEl.textContent = c.s; if (pEl) pEl.textContent = c.p; v.href = c.u;
    if (url) url.textContent = '3orillas.com/' + String(c.s).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    barra();
  };
  if (!reduce) { barra(); setInterval(() => { if (!document.hidden) paso(); }, T); }
});

/* Río de texto: avanza con el scroll */
if (G && ST && !reduce) $$('[data-rio]').forEach(el => G.fromTo(el, { xPercent: 2 }, { xPercent: -28, ease: 'none', scrollTrigger: { trigger: el.parentNode, start: 'top bottom', end: 'bottom top', scrub: .6 } }));

/* Frase que se llena al hacer scroll */
if (ST && !reduce) $$('.tor-scrub').forEach(el => { const ws = partir(el); ws.forEach(w => w.classList.add('pal')); ST.create({ trigger: el, start: 'top 82%', end: 'bottom 45%', scrub: true, onUpdate: s => { const n = Math.round(s.progress * ws.length); ws.forEach((w, k) => w.classList.toggle('lit', k < n)); } }); });

/* Tarjetas de precio apiladas: la anterior se reduce cuando llega la siguiente */
if (G && ST && !reduce) {
  const pila = $$('.tor-pila-carta');
  pila.forEach((c, k) => { const sig = pila[k + 1]; if (!sig) return; const tope = (innerWidth <= 700 ? 76 : 108) + (k + 1) * (innerWidth <= 700 ? 18 : 28); G.to(c, { scale: .93, filter: 'brightness(.92)', ease: 'none', scrollTrigger: { trigger: sig, start: 'top bottom', end: 'top ' + tope + 'px', scrub: true } }); });
}

/* Etapas: recorrido horizontal fijado en escritorio; deslizable en móvil */
$$('[data-hz]').forEach(sec => {
  const vent = sec.querySelector('.tor-hz-ventana'), pista = sec.querySelector('.tor-hz-pista'), bar = sec.querySelector('.tor-hz-barra i'); if (!vent || !pista) return;
  const progreso = p => { if (bar) bar.style.width = (Math.max(0, Math.min(1, p)) * 100) + '%'; };
  if (G && ST && !reduce && matchMedia('(min-width: 1081px)').matches) {
    const dist = () => Math.max(0, pista.scrollWidth - sec.querySelector('.wrap').clientWidth + 80);
    if (dist() > 120) {
      sec.classList.add('fijo');
      G.to(pista, { x: () => -dist(), ease: 'none', scrollTrigger: { trigger: sec, start: 'top top+=70', end: () => '+=' + dist(), pin: true, scrub: .8, invalidateOnRefresh: true, onUpdate: s => progreso(s.progress) } });
      return;
    }
  }
  const mover = () => progreso(vent.scrollLeft / Math.max(1, vent.scrollWidth - vent.clientWidth)); vent.addEventListener('scroll', mover, { passive: true }); mover();
  if (G && !reduce) G.from(pista.children, { x: 80, opacity: 0, duration: 1, stagger: .1, ease: 'expo.out', scrollTrigger: ST ? { trigger: sec, start: 'top 75%' } : undefined });
});

/* Luz que sigue el puntero dentro de las tarjetas */
document.addEventListener('pointermove', e => { const c = e.target.closest && e.target.closest('[data-luz]'); if (!c) return; const r = c.getBoundingClientRect(); c.style.setProperty('--x', (e.clientX - r.left) + 'px'); c.style.setProperty('--y', (e.clientY - r.top) + 'px'); }, { passive: true });

/* Botones con magnetismo */
if (!reduce && matchMedia('(hover: hover)').matches) $$('.tor-iman').forEach(b => {
  b.addEventListener('pointermove', e => { const r = b.getBoundingClientRect(); b.style.transform = `translate(${(e.clientX - r.left - r.width / 2) * .22}px, ${(e.clientY - r.top - r.height / 2) * .3}px)`; });
  b.addEventListener('pointerleave', () => { b.style.transform = ''; });
});

/* Entrada de bloques sueltos */
if (G && ST && !reduce) $$('.tor-pila-carta, .tor-comp, .tor-item, .tor-svc, .tor-aurea').forEach(el => { if (el.getBoundingClientRect().top > innerHeight) G.from(el, { y: 50, opacity: 0, duration: 1.1, ease: 'expo.out', scrollTrigger: { trigger: el, start: 'top 92%' } }); });

/* ================= SERVICIOS: asistente «Encuentra tu servicio» ================= */
(function guia() {
  const caja = $('[data-guia]'), cuerpo = $('#guiaCuerpo'); if (!caja || !cuerpo) return;
  const PREG = TOR.asistente || [], SV = Object.fromEntries((TOR.servs || []).map(s => [s.slug, s]));
  if (!PREG.length) return;
  const voz = $('#guiaVoz'), pasos = $$('.tor-guia-pasos i');
  const soloEm = h => String(h).replace(/<(?!\/?em>)[^>]*>/g, '');
  const ORI = { e: ['Orilla 01 · Estrategia', 'var(--champan)'], c: ['Orilla 02 · Creatividad', 'var(--salvia)'], t: ['Orilla 03 · Tecnología', 'var(--hielo)'] };
  const VOCES = ['Respóndeme tres preguntas y te digo <em>por dónde empezar.</em>', 'Bien. Ahora, <em>¿dónde se va el tiempo?</em>', 'Última: <em>¿qué tan pronto?</em>', 'Listo. <em>Esto es lo que haría yo.</em>'];
  let paso = 0, puntos = {}, resp = [];
  const entrar = () => { if (G && !reduce) G.from(cuerpo.children, { y: 26, opacity: 0, duration: .6, stagger: .05, ease: 'power3.out' }); };
  const hablar = i => { if (!voz) return; voz.innerHTML = VOCES[i] || VOCES[0]; if (G && !reduce) G.fromTo(voz, { y: 10, opacity: 0 }, { y: 0, opacity: 1, duration: .5, ease: 'power3.out' }); };
  const marcar = i => pasos.forEach((p, k) => { p.classList.toggle('on', k <= i); });
  const pregunta = () => {
    const q = PREG[paso];
    cuerpo.innerHTML = `<p class="tor-guia-n">Pregunta ${paso + 1} de ${PREG.length}</p><h2 class="tor-guia-q">${soloEm(q.q)}</h2><div class="tor-guia-opts">${q.opts.map((o, i) => `<button type="button" class="tor-guia-op" data-op="${i}" data-luz><span class="l">${String.fromCharCode(65 + i)}</span>${esc(o[0])}</button>`).join('')}</div>${paso ? '<button type="button" class="tor-guia-atras" data-atras>← Atrás</button>' : ''}`;
    marcar(paso); hablar(paso); entrar();
  };
  const resultado = () => {
    const orden = Object.entries(puntos).filter(([k]) => SV[k]).sort((a, b) => b[1] - a[1]);
    const s = SV[(orden[0] || [])[0]] || Object.values(SV)[0]; const c = orden[1] && orden[1][1] >= 2 ? SV[orden[1][0]] : null;
    const p = byId[s.ref] || {}; const o = ORI[s.o] || ORI.e;
    const resumen = 'Hola, hice el cuestionario de su web. Quiero ' + resp.map(r => r.toLowerCase()).join(' · ') + '. Me recomendaron: ' + s.name + '.';
    const media = p.video && !p.mobile ? `<video src="${esc(p.video)}" poster="${esc(p.img)}" autoplay muted loop playsinline aria-hidden="true"></video>` : (p.img ? `<img src="${esc(p.img)}" alt="">` : '');
    cuerpo.innerHTML = `<div class="tor-guia-res" style="--c:${o[1]};">
        <div class="tor-guia-res-txt">
          <p class="tor-guia-n">Te recomendamos empezar por</p>
          <span class="tor-tag"><span class="dot" style="background:${o[1]};"></span>${esc(o[0])}</span>
          <h2 class="tor-guia-q">${esc(s.name)}</h2>
          <p class="body">${esc(s.corto)}</p>
          <p class="tor-guia-porque">Porque quieres <b>${esc((resp[0] || '').toLowerCase())}</b> y hoy el tiempo se va en <b>${esc((resp[1] || '').toLowerCase())}</b>.</p>
          ${c ? `<p class="tor-guia-comp">Y para complementar: <a href="${esc(c.url)}">${esc(c.name)} →</a></p>` : ''}
          <div class="tor-acciones"><a class="btn btn-tinta" href="${esc(s.url)}">Ver el servicio ${svgFlecha}</a><a class="btn btn-borde" href="${esc(urlWa(resumen))}" target="_blank" rel="noopener">Hablarlo por WhatsApp</a></div>
          <button type="button" class="tor-guia-atras" data-reinicio>↺ Empezar de nuevo</button>
        </div>
        <a class="tor-guia-res-media" href="${esc(p.url || s.url)}" data-cursor="Ver caso">${media}${p.name ? `<span class="tor-pie cristal"><span class="small tenue">Proyecto real</span><b>${esc(p.name)}</b></span>` : ''}</a>
      </div>`;
    marcar(PREG.length); hablar(PREG.length); entrar();
    $$('#svcGrid .tor-svc').forEach(el => el.classList.toggle('tor-sugerido', el.dataset.slug === s.slug));
  };
  cuerpo.addEventListener('click', e => {
    const b = e.target.closest('[data-op]');
    if (b) {
      const o = PREG[paso].opts[+b.dataset.op]; if (!o) return;
      b.classList.add('elegida'); resp[paso] = o[0];
      Object.entries(o[1] || {}).forEach(([k, v]) => { puntos[k] = (puntos[k] || 0) + v; });
      b.dataset.pts = JSON.stringify(o[1] || {});
      setTimeout(() => { paso++; paso < PREG.length ? pregunta() : resultado(); }, reduce ? 0 : 260);
      return;
    }
    if (e.target.closest('[data-atras]') && paso > 0) {
      paso--; const prev = PREG[paso].opts.find(x => x[0] === resp[paso]);
      if (prev) Object.entries(prev[1] || {}).forEach(([k, v]) => { puntos[k] -= v; });
      resp.length = paso; pregunta(); return;
    }
    if (e.target.closest('[data-reinicio]')) { paso = 0; puntos = {}; resp = []; pregunta(); $$('#svcGrid .tor-sugerido').forEach(x => x.classList.remove('tor-sugerido')); }
  });
})();

/* ================= SERVICIOS: catálogo con buscador y filtros por orilla ================= */
(function catalogo() {
  const grid = $('#svcGrid'), q = $('#svcBuscar'); if (!grid || !q) return;
  const cards = Array.from(grid.querySelectorAll('.tor-svc')), vacio = $('#svcVacio'), F = window.Flip;
  let filtro = 'todo';
  const norm = t => String(t || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
  const aplicar = () => {
    const ts = norm(q.value).split(/\s+/).filter(t => t.length > 1);
    const st = F && !reduce ? F.getState(cards) : null;
    let n = 0;
    cards.forEach(c => { const ok = (filtro === 'todo' || c.dataset.o === filtro) && ts.every(t => c.dataset.buscar.indexOf(t) > -1); c.classList.toggle('fuera', !ok); if (ok) n++; });
    if (vacio) vacio.hidden = n > 0;
    if (st) F.from(st, { duration: .6, ease: 'power3.inOut', absolute: true, onEnter: els => G.fromTo(els, { opacity: 0, scale: .92 }, { opacity: 1, scale: 1, duration: .5 }), onLeave: els => G.to(els, { opacity: 0, scale: .92, duration: .3 }) });
  };
  let tmr; q.addEventListener('input', () => { clearTimeout(tmr); tmr = setTimeout(aplicar, 140); });
  $$('.tor-chips-f [data-f]').forEach(b => b.addEventListener('click', () => {
    filtro = b.dataset.f; $$('.tor-chips-f [data-f]').forEach(x => { const on = x === b; x.classList.toggle('on', on); x.setAttribute('aria-pressed', on); }); aplicar();
  }));
})();

/* ================= DEMO LAB: luz del color de la plataforma y entrada de la escena ================= */
(function labVida() {
  const lista = $('#labLista'), luz = $('#labLuz'); if (!lista) return;
  const pintar = () => {
    const sel = lista.querySelector('[aria-selected="true"]'); const p = sel && PROJECTS.find(x => x.demo === sel.dataset.app);
    if (p && luz) luz.style.background = HALO[p.o] || HALO.t;
    if (G && !reduce) { const e = $('#labEscena'); if (e && e.firstElementChild) G.from(e.firstElementChild, { y: 24, opacity: 0, scale: .98, duration: .7, ease: 'expo.out' }); }
  };
  new MutationObserver(pintar).observe(lista, { childList: true });
  pintar();
})();


/* Oficina: la foto se desplaza más lento que la página; la galería del equipo entra escalonada */
if (G && ST && !reduce) $$('[data-oficina] > img').forEach(im => G.fromTo(im, { yPercent: -6 }, { yPercent: 6, ease: 'none', scrollTrigger: { trigger: im.parentNode, start: 'top bottom', end: 'bottom top', scrub: true } }));
if (G && ST && !reduce) $$('.tor-galeria figure').forEach((f, i) => G.from(f, { y: 70 + i * 30, opacity: 0, duration: 1.2, ease: 'expo.out', scrollTrigger: { trigger: f, start: 'top 92%' } }));

/* ================= ZOOM: agendar una videollamada real (API del plugin) ================= */
function agendaZoom() {
  const main = $('#salaMain'), fuera = $('#salaFuera'); if (!main) return;
  fuera.style.display = 'none'; $('#salaEstado').textContent = 'Agenda tu videollamada';
  main.className = 'sala-main agenda tor-zoom';
  main.innerHTML = '<div class="tz-cargando"><span class="tor-puntos"><i></i><i></i><i></i></span><p class="small">Buscando horarios libres…</p></div>';
  sala.hidden = false; $('#finLlamada').focus();
  const post = async (accion, datos) => {
    const fd = new FormData(); Object.entries(datos || {}).forEach(([k, v]) => fd.append(k, v == null ? '' : v));
    fd.append('action', accion); fd.append('nonce', TOR.nonce || ''); fd.append('pagina', location.href);
    let r = await fetch(TOR.ajax, { method: 'POST', body: fd, credentials: 'same-origin' }); let j = {}; try { j = await r.json(); } catch (e) { /* vacío */ }
    if (r.status === 403 && j.data && j.data.codigo === 'nonce') {
      try { const n = await fetch(TOR.ajax + '?action=tor_nonce', { credentials: 'same-origin' }).then(x => x.json()); if (n && n.data && n.data.nonce) { TOR.nonce = n.data.nonce; fd.set('nonce', TOR.nonce); r = await fetch(TOR.ajax, { method: 'POST', body: fd, credentials: 'same-origin' }); j = await r.json(); } } catch (e) { /* sin red */ }
    }
    return j;
  };
  const falla = msg => { main.innerHTML = `<div class="tz-falla"><h3 class="h-m">No pudimos abrir la agenda</h3><p class="small">${esc(msg || 'Inténtalo de nuevo en un momento.')}</p><a class="btn btn-marfil btn-sm" href="${esc(urlWa('Hola, quiero agendar una videollamada con un asesor de 3 Orillas.'))}" target="_blank" rel="noopener">Agendar por WhatsApp</a></div>`; };
  let elegido = null;
  post('tor_zoom_horarios').then(j => {
    const dias = j && j.success && j.data && j.data.dias; if (!dias || !dias.length) return falla(j && j.data && j.data.msg);
    main.innerHTML = `<div class="tz">
      <div class="tz-cal">
        <p class="etq">1 · Elige el día y la hora</p>
        <div class="tz-dias" role="tablist" aria-label="Días">${dias.map((d, i) => `<button type="button" role="tab" class="tz-dia" aria-selected="${!i}" data-i="${i}">${esc(d.dia)}</button>`).join('')}</div>
        <div class="tz-horas" id="tzHoras"></div>
        <p class="small tz-zona">Hora de Bogotá · 30 minutos por Zoom</p>
      </div>
      <form class="tz-form" id="tzForm" novalidate>
        <p class="etq">2 · Tus datos</p>
        <p class="tz-sel" id="tzSel">Elige un horario.</p>
        <label class="campo" for="tzNom">Nombre<input class="input" id="tzNom" autocomplete="name" maxlength="120"></label>
        <label class="campo" for="tzTel">WhatsApp<input class="input" id="tzTel" type="tel" inputmode="tel" autocomplete="tel" maxlength="40"></label>
        <label class="campo" for="tzLin">¿De qué quieres hablar?<select class="input" id="tzLin">${(TOR.servs || []).map(s => `<option>${esc(s.name)}</option>`).join('')}<option>Otro tema</option></select></label>
        <label class="acepto" for="tzOk"><input type="checkbox" id="tzOk"><span>Autorizo el tratamiento de mis datos según la <a href="${esc(U.politica)}" target="_blank" rel="noopener">política de datos</a>.</span></label>
        <span class="trampa" aria-hidden="true"><input type="text" name="sitio" tabindex="-1" autocomplete="off"></span>
        <button class="btn btn-marfil" type="submit">Agendar videollamada</button>
        <p class="small" id="tzMsg" role="status"></p>
      </form></div>`;
    const horas = i => {
      $('#tzHoras').innerHTML = dias[i].horas.map(h => `<button type="button" class="tz-hora${elegido && elegido.t === h.t ? ' on' : ''}" data-t="${h.t}" data-txt="${esc(dias[i].dia + ' · ' + h.h)}">${esc(h.h)}</button>`).join('');
      if (G && !reduce) G.from('#tzHoras .tz-hora', { y: 10, opacity: 0, duration: .35, stagger: .015, ease: 'power2.out' });
    };
    horas(0);
    main.querySelector('.tz-dias').addEventListener('click', e => { const b = e.target.closest('.tz-dia'); if (!b) return; main.querySelectorAll('.tz-dia').forEach(x => x.setAttribute('aria-selected', x === b)); horas(+b.dataset.i); });
    $('#tzHoras').addEventListener('click', e => { const b = e.target.closest('.tz-hora'); if (!b) return; elegido = { t: +b.dataset.t, txt: b.dataset.txt }; main.querySelectorAll('.tz-hora').forEach(x => x.classList.toggle('on', x === b)); $('#tzSel').innerHTML = `<b>${esc(elegido.txt)}</b>`; $('#tzNom').focus(); });
    $('#tzForm').addEventListener('submit', async e => {
      e.preventDefault(); const m = $('#tzMsg'); const err = t => { m.innerHTML = `<span class="msg-error">${esc(t)}</span>`; };
      const nom = $('#tzNom').value.trim(), tel = $('#tzTel').value.trim();
      if (!elegido) return err('Elige un día y una hora.');
      if (!nom) return err('Escribe tu nombre.');
      if (tel.replace(/\D/g, '').length < 7) return err('Escribe un WhatsApp de al menos 7 dígitos.');
      if (!$('#tzOk').checked) return err('Marca la autorización de datos para poder agendar.');
      const btn = e.target.querySelector('button[type=submit]'); btn.disabled = true; m.textContent = 'Creando la reunión en Zoom…';
      const r = await post('tor_zoom_agendar', { t: elegido.t, nombre: nom, telefono: tel, linea: $('#tzLin').value, consent: '1', sitio: e.target.querySelector('[name=sitio]').value });
      btn.disabled = false;
      if (!r || !r.success) { if (r && r.data && r.data.codigo === 'horario') { elegido = null; } return err((r && r.data && r.data.msg) || 'No pudimos agendar. Escríbenos por WhatsApp.'); }
      const d = r.data, gcal = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + encodeURIComponent('Videollamada con 3 Orillas') + '&dates=' + d.inicio + '/' + d.fin + '&details=' + encodeURIComponent('Enlace de Zoom: ' + d.join) + '&location=' + encodeURIComponent(d.join);
      $('#salaEstado').textContent = 'Videollamada agendada';
      main.innerHTML = `<div class="tz-listo"><span class="tz-check">${svgFlecha}</span><p class="etq">Listo, ${esc(nom.split(' ')[0])}</p><h3 class="h-m">${esc(d.cuando)}</h3><p class="small">Hora de Bogotá. Guarda el enlace; también te lo confirmamos por WhatsApp.</p><div class="tz-acciones"><a class="btn btn-marfil" href="${esc(d.join)}" target="_blank" rel="noopener">Abrir en Zoom</a><a class="btn btn-borde" href="${esc(gcal)}" target="_blank" rel="noopener">Añadir a Google Calendar</a></div></div>`;
      if (G && !reduce) G.from('.tz-listo > *', { y: 16, opacity: 0, duration: .5, stagger: .06, ease: 'power3.out' });
    });
  }).catch(() => falla());
}

/* ================= DEMO LAB: acceso real a las plataformas ================= */
(function accesoReal() {
  const cred = $('#labCred'); if (!cred) return;
  cred.addEventListener('click', async e => {
    const b = e.target.closest('[data-copiar]'); if (!b) return;
    try { await navigator.clipboard.writeText(b.dataset.copiar); b.textContent = 'Copiado'; setTimeout(() => { b.textContent = 'Copiar'; }, 1600); } catch (err) { b.textContent = 'Selecciónalo y copia'; }
  });
})();

})();
