<?php
/**
 * Contenido de las páginas de servicio (/servicios/<slug>/ y /aurea/).
 *
 * Cada servicio es una lista de bloques que pinta la plantilla servicio.php del tema.
 * Los campos h1, h2 y frase admiten solo <em> y <br> (se imprimen con wp_kses).
 * Textos tomados de 3orillas.com, resumidos al tono del manual de marca. Sin cifras de industria
 * ni testimonios de ejemplo.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cifras del estudio confirmadas por 3 Orillas (2026-11-19).
 */
function tor_cifras(): array {
	return apply_filters(
		'tor_cifras',
		array(
			array( '+10 años', 'de experiencia' ),
			array( '+1.000', 'clientes acompañados' ),
			array( '+$700M', 'vendidos por e-commerce' ),
		)
	);
}

/**
 * Servicios: slug => datos. 'padre' indica la página madre ('' = raíz del sitio).
 */
function tor_servicios(): array {
	$fases = function_exists( 'tor_fases' ) ? array_map( static fn( $f ) => array( $f['t'], $f['d'], $f['time'] ), tor_fases() ) : array();

	return apply_filters(
		'tor_servicios',
		array(
			'software'            => array(
				'padre'   => 'servicios',
				'name'    => 'Software a la medida',
				'corto'   => 'Plataformas, ERP, portales y apps con precio fijo y dos meses de cambios ilimitados.',
				'o'       => 't',
				'eyebrow' => 'Software a la medida · Precio fijo',
				'h1'      => 'El software que tu negocio <em>necesita.</em>',
				'intro'   => 'Plataformas, apps, tiendas, portales y sistemas internos. Precio cerrado antes de empezar y dos meses de cambios ilimitados después de entregar.',
				'chips'   => array( 'Plataformas web', 'Apps móviles', 'Tiendas y marketplaces', 'Portales de clientes', 'ERP y sistemas internos', 'Dashboards', 'Integraciones' ),
				'retrato' => 'retrato-1.jpg',
				'claves'  => 'erp crm plataforma sistema inventario app aplicacion movil portal clinica gestion facturacion dashboard tablero software',
				'ref'     => 'crm',
				'flotantes' => array( array( 'Precio fijo', 'cero horas facturables' ), array( '2 meses', 'de cambios ilimitados' ) ),
				'wa'      => 'Hola, quiero cotizar un software a la medida.',
				'bloques' => array(
					array(
						'tipo'    => 'modelo',
						'etq'     => 'Nuestra forma de trabajar',
						'h2'      => 'Un precio. Un pago. <em>Cambios ilimitados.</em>',
						'intro'   => 'Sin horas facturables ni sorpresas a mitad de camino: cerramos alcance y precio antes de escribir la primera línea de código.',
						'items'   => array(
							array( 'Precio fijo', 'El valor queda cerrado en la propuesta. Si nos toma más tiempo, lo asumimos nosotros.', 'Cero horas facturables' ),
							array( 'Un solo pago', 'Pagas una vez. Sin mensualidades obligatorias ni licencias para usar lo que ya es tuyo.', 'Cero mensualidades' ),
							array( '2 meses de cambios', 'Durante 60 días después de la entrega ajustamos lo que haga falta, sin límite de solicitudes.', 'Cero costo extra' ),
						),
						'comparar' => array(
							'Con una agencia por horas' => array( 'Cada ajuste se cotiza y se factura aparte', 'El presupuesto crece mientras avanza el proyecto', 'Mensualidades y licencias para seguir usándolo', 'El proveedor se queda con el código' ),
							'Con 3 Orillas'             => array( 'Precio cerrado antes de programar', 'Un solo pago por el proyecto', '60 días de ajustes incluidos', 'Código y datos tuyos, con documentación' ),
						),
					),
					array(
						'tipo'  => 'trabajos',
						'etq'   => 'Trabajos recientes',
						'h2'    => 'Sistemas que ya están <em>funcionando.</em>',
						'intro' => 'Cada uno es un sistema en producción. Varios los puedes recorrer en el Demo Lab.',
						'cats'  => array( 'software', 'erp', 'apps' ),
						'lab'   => true,
					),
					array(
						'tipo'  => 'fases',
						'etq'   => 'Cómo lo hacemos',
						'h2'    => 'Cuatro etapas. <em>Cero improvisación.</em>',
						'items' => array(
							array( 'Diagnóstico', 'Procesos, usuarios y alcance por escrito. Ahí queda el precio.', '1 semana' ),
							array( 'Prototipo', 'Pantallas navegables para validar el flujo antes de programar.', 'Antes de programar' ),
							array( 'Construcción', 'Ves y pruebas las primeras piezas mucho antes de terminar.', 'Demo cada 2 semanas' ),
							array( 'Entrega y ajustes', 'Salimos a producción y ajustamos sin costo extra.', '60 días de cambios' ),
						),
					),
					array(
						'tipo'  => 'faq',
						'etq'   => 'Preguntas',
						'h2'    => 'Antes de <em>empezar.</em>',
						'items' => array(
							array( '¿Precio fijo de verdad?', 'Sí. Definimos el alcance, lo escribimos y ahí queda el precio. Si el desarrollo nos toma más de lo previsto, el costo lo asumimos nosotros.' ),
							array( '¿Qué cubren los cambios ilimitados?', 'Todo ajuste sobre lo acordado: textos, diseño, flujos y correcciones, durante dos meses después de la entrega. Las funciones nuevas fuera del alcance se cotizan aparte, también con precio fijo.' ),
							array( '¿Cuánto tarda?', 'Entre 4 y 16 semanas según el alcance. Trabajamos por iteraciones: ves y pruebas las primeras piezas mucho antes de terminar.' ),
							array( '¿El código es mío?', 'Sí. Código, datos y propiedad intelectual son tuyos, con repositorio y documentación. No quedas atado a nosotros para seguir evolucionándolo.' ),
							array( '¿Solo hacen automatización?', 'No. Construimos plataformas web, apps, tiendas, portales de clientes, sistemas internos y tableros. La automatización es una pieza más.' ),
						),
					),
				),
			),
			'webs-y-ecommerce'    => array(
				'padre'   => 'servicios',
				'name'    => 'Webs y e-commerce',
				'corto'   => 'Sitios y tiendas pensados para vender, con pagos, inventario y envíos integrados.',
				'o'       => 'c',
				'eyebrow' => 'Diseño UX/UI · Webs · E-commerce',
				'h1'      => 'Tu web no es un catálogo. Es tu <em>mejor vendedor.</em>',
				'intro'   => 'Estrategia comercial, diseño y tecnología para convertir visitas en clientes. Con sistema, no con suerte.',
				'chips'   => array( 'Diseño UX/UI', 'Sitios web', 'Landing pages', 'E-commerce', 'Pagos e inventario', 'Analítica' ),
				'retrato' => 'retrato-2.jpg',
				'claves'  => 'pagina web sitio tienda ecommerce online landing catalogo vender internet diseño ux ui',
				'ref'     => 'seeds',
				'flotantes' => array( array( '+$700M', 'vendidos por e-commerce' ), array( '4–8 sem.', 'del diagnóstico al lanzamiento' ) ),
				'cifras'  => true,
				'wa'      => 'Hola, quiero cotizar una web o tienda virtual.',
				'bloques' => array(
					array(
						'tipo'  => 'cartas',
						'etq'   => 'Qué hacemos',
						'h2'    => 'Cada pantalla, pensada <em>para vender.</em>',
						'tono'  => 'oscuro',
						'intro' => 'Cada proyecto parte de una pregunta: ¿cómo hace esto que tu negocio venda más?',
						'cols'  => 4,
						'items' => array(
							array( 'Diseño UX/UI', 'Interfaces claras para que el usuario entienda, confíe y avance hacia la compra.', array() ),
							array( 'Sitios web', 'Rápidos, medibles y construidos para convertir tráfico en clientes.', array() ),
							array( 'E-commerce', 'Catálogo, pagos, logística y analítica integrados para vender todo el día.', array() ),
							array( 'Landing pages', 'Una página por campaña, hecha para convertir desde el primer clic.', array() ),
						),
					),
					array(
						'tipo'  => 'frase',
						'etq'   => 'Nuestra filosofía',
						'frase' => 'Arquitectura comercial, <em>no decoración.</em>',
						'texto' => 'Mapeamos tu embudo, encontramos dónde se pierde la venta y diseñamos la pieza exacta para cerrarla. Estrategia, diseño y desarrollo bajo un mismo techo.',
					),
					array(
						'tipo'  => 'trabajos',
						'etq'   => 'Portafolio',
						'h2'    => 'Proyectos que <em>venden.</em>',
						'cats'  => array( 'web', 'ecommerce' ),
					),
					array(
						'tipo'  => 'fases',
						'etq'   => 'Cómo lo hacemos',
						'h2'    => 'Tres etapas. <em>Cero improvisación.</em>',
						'items' => array(
							array( 'Diagnóstico', 'Tu negocio, tu cliente y tu embudo. Definimos qué vamos a mover y cómo se mide.', '1 semana' ),
							array( 'Diseño y construcción', 'UX/UI, web o tienda: la pieza exacta que tu venta necesita.', '4 a 8 semanas' ),
							array( 'Lanzamiento y optimización', 'Salimos a producción, medimos y mejoramos en cada ciclo.', 'Continuo' ),
						),
					),
				),
			),
			'marketing-digital'   => array(
				'padre'   => 'servicios',
				'name'    => 'Marketing digital',
				'corto'   => 'Pauta en Google, Meta y TikTok con el costo por cliente siempre a la vista.',
				'o'       => 'e',
				'eyebrow' => 'Google · Meta · Instagram · TikTok',
				'h1'      => 'Campañas que traen <em>clientes,</em> no solo clics.',
				'intro'   => 'Diseñamos y operamos tu pauta con métricas reales, hasta que el costo de conseguir un cliente sea predecible.',
				'chips'   => array( 'Google Ads', 'Meta Ads', 'TikTok Ads', 'Landing pages', 'Remarketing', 'ROAS · CAC · CPL' ),
				'retrato' => 'retrato-3.jpg',
				'claves'  => 'pauta anuncios publicidad redes sociales clientes ventas leads campañas facebook instagram google tiktok meta ads',
				'ref'     => 'seeds-pauta',
				'flotantes' => array( array( 'CAC', 'a la vista cada semana' ), array( '4 canales', 'Google · Meta · Instagram · TikTok' ) ),
				'cifras'  => true,
				'wa'      => 'Hola, quiero campañas que generen clientes.',
				'bloques' => array(
					array(
						'tipo'  => 'cartas',
						'etq'   => '¿Para quién es?',
						'h2'    => 'Si te reconoces aquí, <em>hablemos.</em>',
						'tono'  => 'oscuro',
						'intro' => 'Es para negocios que ya funcionan y quieren un canal de clientes medible y predecible.',
						'num'   => 'Síntoma',
						'items' => array(
							array( 'Dependes del voz a voz', 'Buen producto, pero los clientes llegan solo por referidos o por tu red.', array() ),
							array( 'Pautas, pero no puedes medir', 'Ves clics e impresiones, pero no sabes cuántos clientes te dejó la inversión.', array() ),
							array( 'Quieres escalar sin perder el control', 'Subir el presupuesto da miedo si no tienes claro qué está funcionando.', array() ),
						),
					),
					array(
						'tipo'  => 'cartas',
						'etq'   => 'Canales que operamos',
						'h2'    => 'El canal depende de tu cliente, <em>no de la moda.</em>',
						'cols'  => 4,
						'items' => array(
							array( 'Google Ads', 'Captura a quien ya está buscando lo que ofreces.', array( 'Search', 'Shopping', 'YouTube' ) ),
							array( 'Meta Ads', 'Llega a tu cliente antes de que empiece a buscar.', array( 'Conversiones', 'Leads', 'Remarketing' ) ),
							array( 'Instagram Ads', 'Cuando lo visual acelera la decisión de compra.', array( 'Stories', 'Reels', 'Branding' ) ),
							array( 'TikTok Ads', 'Bajo costo por atención, si tienes un ángulo de contenido claro.', array( 'In-feed', 'Spark Ads', 'UGC' ) ),
						),
					),
					array(
						'tipo'  => 'frase',
						'etq'   => 'Por qué funciona',
						'frase' => 'Empezamos por tu negocio, <em>no por el anuncio.</em>',
						'texto' => 'Antes de pautar definimos la oferta, el mensaje y la métrica. Si el problema está antes del anuncio, lo trabajamos ahí: ningún presupuesto arregla una oferta que no convence.',
					),
					array(
						'tipo'  => 'fases',
						'etq'   => 'Cómo lo hacemos',
						'h2'    => 'Tres etapas. <em>Cero improvisación.</em>',
						'items' => array(
							array( 'Diagnóstico', 'Oferta, mensaje, cliente y canal. Definimos cuánto invertir y cómo medir.', '1 semana' ),
							array( 'Estructura y lanzamiento', 'Campañas, creatividades, píxeles, eventos y landing pages.', '30 días' ),
							array( 'Medición y escala', 'Tablero en tiempo real y optimización continua. Escalamos cuando los datos lo confirman.', 'Continuo' ),
						),
					),
					array(
						'tipo'  => 'lista',
						'etq'   => 'Qué incluye',
						'h2'    => 'Entregables concretos. <em>Sin sorpresas.</em>',
						'items' => array(
							array( 'Estrategia de canales y presupuesto', 'Dónde invertir, cuánto y con qué objetivo.' ),
							array( 'Arquitectura de campañas', 'Campañas, conjuntos y audiencias listos para escalar.' ),
							array( 'Creatividades', 'Piezas, videos cortos y textos pensados para convertir.' ),
							array( 'Configuración técnica', 'Píxeles, eventos y públicos medidos desde el día uno.' ),
							array( 'Landing pages', 'Una página por campaña para convertir el tráfico pagado.' ),
							array( 'Tablero de métricas', 'CAC, ROAS, CPL y conversión por canal, en tiempo real.' ),
							array( 'Reportes con contexto', 'Qué pasó, por qué y qué hacemos ahora. Cada semana y cada mes.' ),
							array( 'Optimización continua', 'Rotación de creatividades y ajuste de pujas, mes a mes.' ),
						),
					),
					array(
						'tipo' => 'trabajos',
						'etq'  => 'Casos',
						'h2'   => 'Marketing que <em>se mide.</em>',
						'cats' => array( 'marketing' ),
					),
				),
			),
			'chatbot-whatsapp'    => array(
				'padre'   => 'servicios',
				'name'    => 'Chatbot con IA para WhatsApp',
				'corto'   => 'Un asistente que atiende, califica y agenda en tu WhatsApp a cualquier hora.',
				'o'       => 't',
				'eyebrow' => 'Chatbot con IA · WhatsApp',
				'h1'      => 'Tu WhatsApp atendiendo y vendiendo <em>las 24 horas.</em>',
				'intro'   => 'Un asistente con IA que responde al instante, califica prospectos, resuelve dudas y agenda citas, sin que nadie del equipo tenga que estar conectado.',
				'chips'   => array( 'API oficial de WhatsApp', 'Entrenado con tu información', 'CRM y agenda', 'Paso a un humano' ),
				'retrato' => 'retrato-2.jpg',
				'claves'  => 'whatsapp bot chatbot atencion mensajes respuestas automaticas agenda citas ia inteligencia artificial soporte',
				'ref'     => 'asistente',
				'flotantes' => array( array( '24/7', 'atención en tu WhatsApp' ), array( '2–4 sem.', 'para salir en vivo' ) ),
				'wa'      => 'Hola, quiero un chatbot con IA para mi WhatsApp.',
				'bloques' => array(
					array(
						'tipo'  => 'chat',
						'etq'   => 'Conversación de ejemplo',
						'h2'    => 'Así habla tu asistente <em>con tus clientes.</em>',
						'intro' => 'Toca una opción para ver una conversación de muestra. Tu asistente se entrena con el tono, los productos y las reglas de tu negocio.',
					),
					array(
						'tipo'  => 'cartas',
						'etq'   => 'Para qué sirve',
						'h2'    => 'No solo para ventas. <em>Para toda la experiencia.</em>',
						'items' => array(
							array( 'Ventas y prospección', 'Califica, presenta la oferta y pasa al humano cuando el cliente está listo para comprar.', array( 'Catálogo interactivo', 'Calificación de leads', 'Seguimiento post-consulta', 'Cierre de ventas simples' ) ),
							array( 'Atención al cliente', 'Preguntas frecuentes, estado de pedidos y citas, todos los días.', array( 'Respuestas frecuentes', 'Estado de pedidos', 'Agenda y recordatorios', 'Envío de documentos' ) ),
							array( 'Soporte y postventa', 'Abre tickets, escala casos complejos y hace seguimiento hasta resolver.', array( 'Tickets', 'Escalación por prioridad', 'Encuestas de satisfacción', 'Recordatorios de pago' ) ),
						),
					),
					array(
						'tipo'  => 'fases',
						'etq'   => 'El proceso',
						'h2'    => 'Funcionando en <em>2 a 4 semanas.</em>',
						'items' => array(
							array( 'Diagnóstico de flujos', 'Las conversaciones más frecuentes y los momentos críticos de tu cliente.', '' ),
							array( 'Diseño de la IA', 'Flujos y entrenamiento con el tono y la información de tu negocio.', '' ),
							array( 'Conexión', 'Tu número oficial de WhatsApp, tu CRM, tu agenda o tus pedidos.', '' ),
							array( 'Ajuste continuo', 'Revisamos conversaciones reales y mejoramos las respuestas.', 'Primeros 15 días' ),
						),
					),
					array(
						'tipo'  => 'faq',
						'etq'   => 'Preguntas',
						'h2'    => 'Lo que siempre <em>nos preguntan.</em>',
						'items' => array(
							array( '¿Funciona con mi WhatsApp Business actual?', 'Sí. Usamos la API oficial de WhatsApp Business: tu número sigue siendo el mismo y conectamos el asistente a esa línea.' ),
							array( '¿Qué pasa cuando el cliente necesita a una persona?', 'El asistente detecta cuándo hace falta un humano y transfiere la conversación al agente disponible, con todo el contexto registrado.' ),
							array( '¿Cuánto tarda la implementación?', 'Entre 2 y 4 semanas desde el diagnóstico hasta el lanzamiento. Los primeros 15 días los dedicamos a ajustarlo con conversaciones reales.' ),
							array( '¿Se conecta con mi CRM o mis pedidos?', 'Sí. Lo conectamos con tu CRM, ERP, calendario, tienda o cualquier sistema con API, para consultar pedidos, crear tickets y actualizar prospectos.' ),
							array( '¿Suena robótico?', 'No. Lo entrenamos con el tono y el vocabulario de tu marca, para que la conversación se sienta natural.' ),
						),
					),
					array(
						'tipo' => 'trabajos',
						'etq'  => 'Casos',
						'h2'   => 'IA que ya <em>está trabajando.</em>',
						'cats' => array( 'ia' ),
					),
				),
			),
			'automatizacion-ia'   => array(
				'padre'   => 'servicios',
				'name'    => 'Automatización e IA',
				'corto'   => 'Procesos repetitivos que pasan a hacerse solos, con IA, RPA y sistemas a la medida.',
				'o'       => 't',
				'eyebrow' => 'IA · RPA · Sistemas a la medida',
				'h1'      => 'Tu operación, <em>sin fricción.</em>',
				'intro'   => 'Automatizamos procesos repetitivos, flujos de datos y tareas que consumen el tiempo de tu equipo, con inteligencia artificial, robots de software (RPA) y sistemas hechos para tu negocio.',
				'chips'   => array( 'IA aplicada', 'RPA', 'Integraciones', 'Tableros', 'Reportes automáticos' ),
				'retrato' => 'retrato-1.jpg',
				'claves'  => 'automatizar automatizacion procesos excel tareas manuales reportes rpa inteligencia artificial ia integraciones nomina facturas conciliacion',
				'ref'     => 'insivo',
				'flotantes' => array( array( '5 áreas', 'de comercial a logística' ), array( 'IA + RPA', 'y sistemas a la medida' ) ),
				'wa'      => 'Hola, quiero automatizar procesos de mi empresa.',
				'bloques' => array(
					array(
						'tipo'  => 'cartas',
						'etq'   => 'Qué construimos',
						'h2'    => 'Tres herramientas, <em>un objetivo.</em>',
						'tono'  => 'oscuro',
						'intro' => 'Elegimos la tecnología según el problema, no al revés.',
						'items' => array(
							array( 'IA aplicada', 'Sistemas que clasifican, predicen y deciden con tus datos.', array( 'Clasificación de documentos', 'Predicción de demanda', 'Detección de anomalías', 'Calificación de leads' ) ),
							array( 'Automatización de flujos', 'Robots de software que hacen tareas repetitivas en cualquier sistema, sin errores.', array( 'Extracción de datos', 'Carga en el ERP', 'Reportes', 'Conciliaciones' ) ),
							array( 'Sistemas a la medida', 'Portales, ERP y tableros para cuando lo estándar no encaja.', array( 'Portales de clientes', 'ERP sectorial', 'Tableros de operación', 'APIs de integración' ) ),
						),
					),
					array(
						'tipo'  => 'tabs',
						'etq'   => 'Por área',
						'h2'    => 'Lo automatizamos <em>en cualquier área.</em>',
						'items' => array(
							'Comercial'     => array(
								array( 'Calificación de leads', 'El CRM prioriza prospectos según su comportamiento y perfil.' ),
								array( 'Seguimiento post-cotización', 'WhatsApp y correo automáticos para quien no ha respondido.' ),
								array( 'Pipeline en tiempo real', 'Conversiones, cuellos de botella y pronóstico del mes.' ),
								array( 'Propuestas automáticas', 'Del formulario de diagnóstico a un PDF listo para enviar.' ),
							),
							'Operaciones'   => array(
								array( 'Procesamiento de órdenes', 'Del pedido a la notificación al cliente, sin pasos manuales.' ),
								array( 'Control de calidad con IA', 'Detecta anomalías con modelos entrenados con tu historial.' ),
								array( 'Asignación de tareas', 'Reparte la carga según disponibilidad, prioridad y habilidades.' ),
								array( 'Reporte diario', 'Indicadores, alertas y pendientes cada mañana.' ),
							),
							'Finanzas'      => array(
								array( 'Conciliación bancaria', 'Cruza extractos con la contabilidad en minutos, no en días.' ),
								array( 'Facturación electrónica', 'Conectada a las ventas, sin digitar.' ),
								array( 'Alertas de flujo de caja', 'Avisos cuando aparece un riesgo de liquidez.' ),
								array( 'Cobranza automática', 'Recordatorios por WhatsApp, correo y SMS según los días vencidos.' ),
							),
							'Talento humano' => array(
								array( 'Ingreso digital', 'Documentos, accesos y firma digital para cada persona nueva.' ),
								array( 'Nómina y liquidaciones', 'Cálculo automático conectado a la asistencia.' ),
								array( 'Vacaciones y permisos', 'Solicitudes y aprobaciones con el calendario al día.' ),
								array( 'Evaluación de desempeño', 'Encuestas, recordatorios y resultados consolidados.' ),
							),
							'Logística'     => array(
								array( 'Seguimiento de envíos', 'Avisos al cliente en cada hito del recorrido.' ),
								array( 'Rutas con IA', 'Las rutas más eficientes según tráfico, capacidad y prioridad.' ),
								array( 'Inventario predictivo', 'Órdenes de compra antes de llegar al mínimo.' ),
								array( 'Proveedores', 'Portal con documentos, órdenes de compra y cumplimiento.' ),
							),
						),
					),
					array(
						'tipo'  => 'fases',
						'etq'   => 'Cómo lo hacemos',
						'h2'    => 'Primero entender. <em>Después automatizar.</em>',
						'items' => $fases,
					),
					array(
						'tipo' => 'trabajos',
						'etq'  => 'Casos',
						'h2'   => 'Operaciones que ya <em>corren solas.</em>',
						'cats' => array( 'ia', 'erp' ),
					),
				),
			),
			'aurea'               => array(
				'padre'   => '',
				'name'    => 'Áurea · Wellness',
				'corto'   => 'Estrategia y sistemas comerciales para marcas de bienestar.',
				'o'       => 'c',
				'eyebrow' => 'Áurea · Wellness growth studio',
				'h1'      => 'Marcas wellness que crecen <em>sin sonar como todas.</em>',
				'intro'   => 'Estrategia, narrativa, activos digitales y sistemas comerciales para marcas de bienestar que quieren atraer, convertir y retener sin perder su esencia.',
				'chips'   => array( 'Clínicas estéticas', 'Spas y bienestar', 'Skincare consciente', 'Yoga · pilates · movimiento', 'Coaching y salud emocional', 'Retiros wellness', 'Salud femenina', 'E-commerce wellness' ),
				'retrato' => 'retrato-3.jpg',
				'claves'  => 'wellness bienestar spa clinica estetica yoga pilates skincare salud belleza coaching retiros',
				'ref'     => 'tatem',
				'flotantes' => array( array( 'Wellness', 'estrategia con sensibilidad' ), array( '4 etapas', 'del diagnóstico a escalar' ) ),
				'wa'      => 'Hola, quiero conocer Áurea para mi marca wellness.',
				'bloques' => array(
					array(
						'tipo'  => 'cartas',
						'etq'   => 'El síntoma',
						'h2'    => 'Valor real, <em>sin un sistema</em> para llevarlo al mercado.',
						'tono'  => 'oscuro',
						'num'   => '',
						'cols'  => 4,
						'items' => array(
							array( 'Mensaje difuso', 'El cliente no entiende rápido qué tienes para ofrecerle.', array() ),
							array( 'Ventas inconsistentes', 'Meses buenos y meses lentos, sin una estructura para vender siempre.', array() ),
							array( 'Comunidad sin conversión', 'Hay contenido e interacción, pero pocas compras.', array() ),
							array( 'Campañas sin norte', 'Inviertes en pauta y diseño sin saber qué está funcionando.', array() ),
						),
					),
					array(
						'tipo'  => 'cartas',
						'etq'   => 'Qué hacemos',
						'h2'    => 'Del primer contacto <em>a la recompra.</em>',
						'intro' => 'Diseñamos cómo una persona descubre tu marca, confía, compra, vuelve y recomienda.',
						'items' => array(
							array( 'Estrategia', 'Una propuesta de valor que se pueda comunicar, vender y sostener.', array() ),
							array( 'Conversión', 'Landing pages, tienda y materiales que convierten interés en acción.', array() ),
							array( 'Adquisición', 'Pauta, contenido y embudos que atraen prospectos con intención de compra.', array() ),
							array( 'Seguimiento', 'CRM, WhatsApp y automatizaciones para que cada lead tenga un camino claro.', array() ),
							array( 'Retención', 'Bienvenida, recompra y comunidad para crear vínculos de largo plazo.', array() ),
							array( 'Optimización', 'Medimos, encontramos fugas y ajustamos mensaje, oferta o canal.', array() ),
						),
					),
					array(
						'tipo'   => 'manifiesto',
						'etq'    => 'Manifiesto',
						'frase'  => 'Creemos en un crecimiento que también <em>se sienta bien.</em>',
						'lineas' => array(
							'Una marca wellness puede vender sin presionar.',
							'Puede crecer sin perder humanidad.',
							'Puede usar datos sin volverse fría.',
							'Puede ser estética, profunda y comercial a la vez.',
						),
					),
					array(
						'tipo'  => 'fases',
						'tono'  => 'claro',
						'etq'   => 'Metodología',
						'h2'    => 'Tres orillas. <em>Un sistema.</em>',
						'items' => array(
							array( 'Diagnóstico profundo', 'Producto, oferta, canales, contenido y métricas actuales.', 'Un mapa de prioridades' ),
							array( 'Diseño estratégico', 'Propuesta de valor, narrativa, oferta y recorrido del cliente.', 'Estrategia antes de ejecutar' ),
							array( 'Construcción', 'Landing pages, contenidos, campañas, CRM y secuencias.', 'Un ecosistema conectado' ),
							array( 'Validación', 'Medimos la respuesta real, registramos objeciones y ajustamos.', 'Un modelo que escala' ),
						),
					),
					array(
						'tipo' => 'trabajos',
						'etq'  => 'Casos',
						'h2'   => 'Marcas que cuidan, <em>creciendo con sistema.</em>',
						'ids'  => array( 'tatem' ),
					),
				),
			),
		)
	);
}

/**
 * Servicio de la página actual, o null.
 */
function tor_servicio_actual(): ?array {
	if ( ! is_page() ) {
		return null;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return null;
	}
	$todos = tor_servicios();
	if ( ! isset( $todos[ $post->post_name ] ) ) {
		return null;
	}
	$s     = $todos[ $post->post_name ];
	$padre = $post->post_parent ? (string) get_post_field( 'post_name', $post->post_parent ) : '';
	if ( $padre !== $s['padre'] ) {
		return null;
	}
	$s['slug'] = $post->post_name;
	return $s;
}

/**
 * URL de un servicio.
 */
function tor_servicio_url( string $slug ): string {
	$todos = tor_servicios();
	if ( ! isset( $todos[ $slug ] ) ) {
		return home_url( '/servicios/' );
	}
	$ruta = ( $todos[ $slug ]['padre'] ? $todos[ $slug ]['padre'] . '/' : '' ) . $slug;
	$p    = get_page_by_path( $ruta );
	return $p ? (string) get_permalink( $p ) : home_url( '/' . $ruta . '/' );
}

/**
 * Asistente «Encuentra tu servicio» del índice de Servicios: tres preguntas; cada opción suma puntos a uno o
 * más servicios (slug => puntos). Gana el que más sume; el segundo se ofrece como complemento.
 */
function tor_asistente(): array {
	return apply_filters(
		'tor_asistente',
		array(
			array(
				'q'    => '¿Qué quieres lograr <em>primero?</em>',
				'opts' => array(
					array( 'Conseguir más clientes', array( 'marketing-digital' => 3, 'webs-y-ecommerce' => 1, 'chatbot-whatsapp' => 1 ) ),
					array( 'Vender en línea', array( 'webs-y-ecommerce' => 3, 'marketing-digital' => 1 ) ),
					array( 'Ordenar mi operación', array( 'software' => 3, 'automatizacion-ia' => 2 ) ),
					array( 'Atender más rápido', array( 'chatbot-whatsapp' => 3, 'automatizacion-ia' => 1 ) ),
					array( 'Hacer crecer mi marca wellness', array( 'aurea' => 4 ) ),
				),
			),
			array(
				'q'    => '¿Qué te quita más <em>tiempo</em> hoy?',
				'opts' => array(
					array( 'Responder los mismos mensajes', array( 'chatbot-whatsapp' => 3 ) ),
					array( 'Hojas de cálculo y tareas manuales', array( 'automatizacion-ia' => 3, 'software' => 1 ) ),
					array( 'Buscar clientes nuevos', array( 'marketing-digital' => 2, 'webs-y-ecommerce' => 1 ) ),
					array( 'Mi web no vende (o no existe)', array( 'webs-y-ecommerce' => 3 ) ),
					array( 'Procesos sin un sistema propio', array( 'software' => 3 ) ),
				),
			),
			array(
				'q'    => '¿Para <em>cuándo</em> lo necesitas?',
				'opts' => array(
					array( 'Este mes', array( 'chatbot-whatsapp' => 1, 'marketing-digital' => 1 ) ),
					array( 'En 1 a 3 meses', array( 'webs-y-ecommerce' => 1, 'software' => 1, 'automatizacion-ia' => 1 ) ),
					array( 'Estoy explorando', array() ),
				),
			),
		)
	);
}
