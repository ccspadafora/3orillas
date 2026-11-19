# 3 Orillas — sitio web en WordPress

Sitio del estudio 3 Orillas. Producción: 3orillas.com, en el servidor `3-orillas` (ver `../SERVIDORES.md`). Hoy producción usa Hello Elementor + Elementor Pro; este proyecto lo reemplaza por un tema y un plugin propios.

Repositorio: https://github.com/ccspadafora/3orillas (público; rama `main`). Solo versiona el tema, el plugin, README y este archivo (ver `.gitignore`). Commits con el correo noreply de GitHub.

## Fuente de verdad del diseño (carpeta `../3Orillas/3 Orillas/`)
- `index.html`: diseño completo (CSS, contenido y JavaScript de todas las vistas).
- `demo.html`: demos simuladas del diseño. **Ya no se usan**: el Demo Lab abre las plataformas reales (ver «Zoom y Demo Lab»).
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
   - "Videollamada" agenda una reunión real de Zoom por API (decisión del 2026-11-19, ver «Zoom y Demo Lab»). Sin credenciales, usa el enlace de Ajustes o WhatsApp.
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

## Zoom y Demo Lab (2026-11-19)
- **Zoom por API** (`includes/class-tor-zoom.php`): app Server-to-Server OAuth. Credenciales SOLO en `wp-config.php` (o variables de entorno): `TOR_ZOOM_ACCOUNT_ID`, `TOR_ZOOM_CLIENT_ID`, `TOR_ZOOM_CLIENT_SECRET` y `TOR_ZOOM_ANFITRION` (correo del usuario anfitrión). Alcances: `meeting:write:meeting:admin`, `meeting:read:list_meetings:admin`.
  - Los botones «Videollamada por Zoom» abren la sala con un agendador: días hábiles dentro del horario de Ajustes, turnos de 30 min, mínimo 2 h de anticipación, sin los horarios ya ocupados en Zoom.
  - Al agendar: se vuelve a validar el horario, se crea la reunión (sala de espera, sin entrar antes del anfitrión), se guarda en Contactos (origen «Videollamada por Zoom», campo Videollamada) y se avisa por correo. El visitante recibe el enlace para unirse y «Añadir a Google Calendar». El enlace de anfitrión (start_url) nunca sale del servidor.
  - Seguridad: nonce, campo trampa, límite de envíos compartido con Contactos, autorización Ley 1581.
- **Demo Lab con plataformas reales en producción y usuario de prueba.** Campos del proyecto: URL, usuario, clave y nota (admin → Proyectos). Solo aparecen los proyectos con URL. Se abren en pestaña nueva (Valfora, HAB y Wanti bloquean iframes; los navegadores bloquean la sesión en iframes de terceros).
  - seeds → erp.seedscol.com · crm → 16.59.184.151.sslip.io/portal · insivo → 44.214.163.14 (Clínica Sabana Norte; supuesto: es Insivo) · hab → app.hab.com.co · reto → reto.seedscol.com · valfora-erp → erp.valforaholdings.com · wanti → app.wantiapp.com.
  - Valfora ERP y Wanti son proyectos nuevos con ficha mínima («Ficha en preparación»): completar textos e imágenes en el admin.
  - Las credenciales se escriben en el admin, no en el código ni en el repositorio.
  - **Embebidas en el sitio** (iframe en sandbox, con recargar, pantalla completa y abrir aparte): Seeds, CRM de Campo, Reto (en marco de celular) e Insivo (solo mientras el sitio sea http; en producción https necesita que Insivo tenga HTTPS). HAB, Valfora ERP y Wanti tienen marcado «Abrir siempre en pestaña nueva» porque envían `X-Frame-Options: DENY`; para embeberlas, su servidor debe enviar `Content-Security-Policy: frame-ancestors 'self' https://3orillas.com https://www.3orillas.com` (y quitar X-Frame-Options) y luego desmarcar la casilla en el admin.
  - Las apps guardan la sesión con token en localStorage (funciona en iframe en todos los navegadores). El CRM de Campo además usa cookies (`credentials: include`): en Safari puede pedir abrirlo aparte.

## Chat en vivo conectado al ERP (2026-11-19)
- Adaptado del plugin `~/dev/VLF_ERP/wordpress/orillas-cotiza-en-vivo/` (no se modificó ese repositorio). **No activar ese plugin en este sitio**: el chat ya vive en `includes/class-tor-chat.php` y en el panel «Cotiza en vivo» del tema; activarlo duplicaría el lanzador.
- Mismo contrato del ERP: `POST /api/v1/chat/web-widget/sessions/` (name, whatsapp, page_url, product_interest, session_token) y `GET/POST …/sessions/{token}/messages/` (`after`, content, external_message_id, product_interest), cabecera `X-Lead-Ingest-Key`. Remitentes: contact (visitante), user (asesor), ai_bot (IA); el panel los rotula.
- Credenciales SOLO en `wp-config.php` o entorno: `TOR_CHAT_ERP_URL` (sin barra final) y `TOR_CHAT_ERP_KEY`. Sin ellas, el panel usa el flujo anterior (3 preguntas + WhatsApp, guarda en Contactos).
- Rutas propias del sitio: `/wp-json/tor/v1/chat/sesiones` y `…/sesiones/{token}/mensajes` (nonce del sitio en `X-TOR-Nonce`).
- Añadidos frente al plugin original: autorización Ley 1581 obligatoria (constancia en Contactos, origen «Chat en vivo (ERP)», sin correo), campo trampa, límites por IP (sesiones con el límite de Contactos; 40 mensajes y 900 consultas por hora), caché de 2 s del sondeo, IP real del visitante en `X-Forwarded-For` (el ERP limita por IP) y sondeo adaptable (3 s tras escribir, 8 s, 20 s en reposo; pausado con el panel cerrado o la pestaña oculta; 60 s tras un 429).
- ERP: `web_widget` está limitado a 120/hora por IP; un chat abierto una hora supera ese límite. Subirlo (p. ej. 600/hora) y confirmar que el nginx del ERP conserva `X-Forwarded-For` (`$proxy_add_x_forwarded_for`).

## Pendientes antes de producción
- Chat en vivo: poner `TOR_CHAT_ERP_URL` y `TOR_CHAT_ERP_KEY` en `wp-config.php`, habilitar public ingest en el ERP y subir el límite `web_widget`.
- Zoom: crear la app Server-to-Server OAuth y poner las 4 constantes en `wp-config.php` del servidor.
- Seeds (erp.seedscol.com) muestra en producción el correo `admin@seeds.co` y una contraseña ya escritos en su inicio de sesión: quitarlos de la app y usar un usuario de prueba con permisos mínimos.
- Demo Lab: cargar usuario y clave de prueba de cada plataforma en el admin. Confirmar que cada usuario de prueba solo ve datos ficticios y tiene permisos mínimos (Clínica Sabana Norte maneja datos de salud). Poner HTTPS a 44.214.163.14.
- Configurar en Ajustes → 3 Orillas: correo de contactos y enlace de videollamada (sin enlace, los botones abren WhatsApp).
- Completar en la página "Política de tratamiento de datos" el NIT y la fecha de vigencia (razón social y correo ya están: Valfora Holdings S.A.S., valforaholdings.sas@gmail.com).
- Decidir qué hacer con las propuestas y entregables de clientes del sitio actual antes de apuntar el dominio.
- Resultados marcados como "[Resultado medible]" en el diseño quedaron vacíos y no se muestran: llenarlos en cada proyecto cuando existan.
