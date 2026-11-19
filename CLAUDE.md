# 3 Orillas — sitio web en WordPress

Sitio del estudio 3 Orillas. Producción: 3orillas.com, en el servidor `3-orillas` (ver `../SERVIDORES.md`). Hoy producción usa Hello Elementor + Elementor Pro; este proyecto lo reemplaza por un tema y un plugin propios.

Repositorio: https://github.com/ccspadafora/3orillas (público; rama `main`). Solo versiona el tema, el plugin, README y este archivo (ver `.gitignore`). Commits con el correo noreply de GitHub.

## Fuente de verdad del diseño (carpeta `../3Orillas/3 Orillas/`)
- `index.html`: diseño completo (CSS, contenido y JavaScript de todas las vistas).
- `demo.html`: demos navegables que se cargan en iframe dentro del Demo Lab.
- `media/`: logos, retratos, capturas y videos.
- **No editar estos archivos.** Son la referencia. La implementación debe ser fiel en diseño, interacciones, estética y efectos.

## Estructura
- Sitio LocalWP `3ors` → `http://3ors.local`. WordPress en `app/public`. Este archivo vive en la raíz del sitio (`Local Sites/3ors`).
- WP-CLI: abrir Claude Code desde la Site shell de Local. Si no, usar PHP 8.2 de Local con `PHPRC` apuntando a `~/Library/Application Support/Local/run/AdcL03qC1/conf/php` y el socket `…/run/AdcL03qC1/mysql/mysqld.sock`.
- Tema: `wp-content/themes/tresorillas/` — theme.json con los tokens (marfil, tinta, champán, salvia, hielo; Instrument Serif + Inter Tight) y plantillas PHP.
- Plugin: `wp-content/plugins/tresorillas-core/` — tipo de contenido Proyecto, categorías, formularios, registro de contactos y ajustes.
- Prefijo: `tor_` (funciones, opciones, hooks), `tor-` (handles y clases CSS propias que no vengan del diseño), `TOR_` (constantes y clases PHP).

## Decisiones (2026-11-18)
1. **URLs reales**, no rutas `#`: `/`, `/trabajo/`, `/trabajo/<proyecto>/`, `/servicios/`, `/demo-lab/`, `/metodo/`, `/estudio/`, `/contacto/`. Filtros de categoría con `?cat=` o `/trabajo/categoria/<cat>/`. Se conserva la animación de entrada de cada vista.
2. **Contactos → WordPress + correo.** Los tres formularios (WhatsApp de la propuesta del inicio, contacto, número del chat) guardan el contacto en el admin y envían correo al equipo. Casilla obligatoria de autorización de tratamiento de datos (Ley 1581) con enlace a la política. Nonce, sanitización y límite de envíos.
3. **Nada simulado.** Misma estética y animaciones, pero:
   - "Videollamada" abre un enlace real (Zoom o agenda) configurable en Ajustes.
   - El chat "Cotiza en vivo" no muestra precios inventados ni "asesores disponibles" falsos; termina en WhatsApp o en el formulario.
   - Los textos "[Resultado medible]" y "$[VALOR]" del diseño son marcadores: no publicarlos.
4. **Contenido editable desde el admin:** proyectos (cliente, tipo, año, orilla, categorías, intro, reto, solución, resultado, stack, imagen, captura, galería, video, demo, móvil, tamaño en cuadrícula, chip). Servicios, fases y textos fijos en ajustes del tema o bloques sencillos.

## Recursos
- GSAP 3.12.5 (+ ScrollTrigger, Flip) y las fuentes se sirven desde el propio tema, no desde CDN.
- WhatsApp: 311 221 8385 (`https://wa.me/573112218385`).

## Estado (2026-11-18)
- Construido: tema `tresorillas` y plugin `tresorillas-core` con todas las vistas del diseño (inicio, trabajo, caso, servicios, demo lab, método, estudio, contacto), política de datos y 404.
- Carga inicial del contenido: `wp tor sembrar --media="<ruta>/3Orillas/3 Orillas/media"` (no duplica; se puede repetir).
- Contactos: admin → Contactos. Correo de aviso, WhatsApp, horario y enlace de videollamada: Ajustes → 3 Orillas.
- Seguridad de formularios: nonce (con renovación vía `tor_nonce` si la página viene de caché), campo trampa `sitio`, límite de 5 envíos por IP cada 10 min, autorización Ley 1581 obligatoria.
- Medios de proyectos en la biblioteca (meta `_tor_origen`); logos, retratos y demo en `assets/` del tema.

## Servicios y páginas internas (2026-11-19)
- Fuente del contenido: páginas de 3orillas.com, resumidas al tono del design system (`../3Orillas/3 Orillas/3orillas nuevo.zip`: tuteo, frases cortas, una itálica por titular, un color por orilla).
- `/servicios/` es un índice; cada servicio vive en `/servicios/<slug>/` (software, webs-y-ecommerce, marketing-digital, chatbot-whatsapp, automatizacion-ia) y Áurea en `/aurea/`. Contenido en `tor_servicios()` (plugin, `includes/servicios.php`), pintado por `servicio.php` del tema.
- Cifras confirmadas por el cliente: +10 años, +1.000 clientes, +$700M vendidos por e-commerce (`tor_cifras()`). Promesas confirmadas: precio fijo, un solo pago, 2 meses de cambios ilimitados, código del cliente y respuesta en < 15 min (solo se muestra en horario).
- No se publican: testimonios de ejemplo, logos de relleno, cifras de industria sin fuente ni el cotizador con precios.
- Conócenos se integró en `/estudio/`.
- Legales de Valfora Holdings S.A.S. con la misma URL que usa la app de Meta: `/privacy-policy/`, `/terminos-y-condiciones-de-uso/`, `/solicitud-de-eliminacion-de-datos/` (con formulario; las solicitudes llegan a Contactos con origen «Solicitud sobre datos personales»).
- Lenguaje visual de las internas (pedido del cliente: más punch, nada pálido): escenario obsidiana `parts/escena.php` (grano, órbita, luz que sigue el puntero, ventana con video, tarjetas de cristal flotantes, cinta en movimiento), río de texto que avanza con el scroll, bloques que alternan claro y obsidiana (`'tono'` en cada bloque), tarjetas de precio apiladas, etapas en recorrido horizontal fijado, frase que se llena con el scroll y luz al pasar el cursor (`data-luz`). Todo respeta `prefers-reduced-motion`.
- Índice de Servicios: asistente «Encuentra tu servicio» (3 preguntas con puntaje por servicio en `tor_asistente()`, resultado con porqué, complemento y WhatsApp prellenado) y catálogo con buscador (`claves` de cada servicio) y filtro por orilla con GSAP Flip. Retratos B/N en los escenarios (`retrato` de cada servicio).
- Demo Lab: escenario con retrato y demos alternándose, tres pasos y la sala obsidiana con luz del color de cada plataforma.
- Logos de clientes (12) y fotos del estudio tomados de 3orillas.com (2026-11-19): `assets/media/clientes/*.png` (recortados, tinta con fondo transparente; lista en `tor_logos()`, cinta en `parts/logos.php`) y `assets/media/estudio/*.webp` (fundador, oficina, equipo). Los textos alternativos del sitio anterior estaban cruzados: el emparejamiento se hizo viendo cada logo. Las fotos de oficina y equipo parecen generadas con IA (nombres de archivo «ChatGPT-Image…»); confirmar con el cliente antes de producción.
- Redirecciones 301 desde las URLs viejas en `TOR_Redirecciones` (solo actúan si la URL da 404).
- Propuestas y entregables de clientes (propuesta-*, Borgo, Trim, Panaderías, Tathem, NymeCall, Manual de marca, 3 Cordilleras) NO se migran. Antes del lanzamiento: decidir si van a un subdominio o se archivan.

## Pendientes antes de producción
- Configurar en Ajustes → 3 Orillas: correo de contactos y enlace de videollamada (sin enlace, los botones abren WhatsApp).
- Completar en la página "Política de tratamiento de datos" el NIT y la fecha de vigencia (razón social y correo ya están: Valfora Holdings S.A.S., valforaholdings.sas@gmail.com).
- Decidir qué hacer con las propuestas y entregables de clientes del sitio actual antes de apuntar el dominio.
- Resultados marcados como "[Resultado medible]" en el diseño quedaron vacíos y no se muestran: llenarlos en cada proyecto cuando existan.
