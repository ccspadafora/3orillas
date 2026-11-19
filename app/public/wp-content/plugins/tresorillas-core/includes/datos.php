<?php
/**
 * Datos fijos del sitio: categorías, orillas, líneas de servicio, fases y propuestas.
 * Se pueden modificar con los filtros tor_lineas, tor_fases y tor_planes.
 *
 * @package TresOrillasCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Categorías de trabajo: slug => [nombre, descripción]. El orden es el del filtro.
 */
function tor_categorias_base(): array {
	return array(
		'software'  => array( 'Software a la medida', 'Plataformas hechas para la operación de cada cliente, con demo funcionando cada dos semanas.' ),
		'erp'       => array( 'ERP', 'Sistemas que conectan inventario, ventas, agenda y finanzas en un solo lugar.' ),
		'apps'      => array( 'Apps móviles', 'Apps iOS, Android y PWA con panel web de gestión.' ),
		'web'       => array( 'Páginas web', 'Sitios pensados como activos comerciales, no como catálogos.' ),
		'ecommerce' => array( 'E-commerce', 'Tiendas conectadas a pagos, inventario y envíos.' ),
		'marketing' => array( 'Marketing', 'Pauta y contenido con el costo por cliente siempre a la vista.' ),
		'marca'     => array( 'Diseño y marca', 'Identidad, sistemas visuales y piezas para cada canal.' ),
		'ia'        => array( 'IA y automatización', 'Asistentes y flujos que atienden, califican y venden solos.' ),
	);
}

/**
 * Las tres orillas: clave => nombre.
 */
function tor_orillas(): array {
	return array(
		'e' => 'Estrategia',
		'c' => 'Creatividad',
		't' => 'Tecnología',
	);
}

/**
 * Líneas de servicio (página Servicios). "ref" es el slug del proyecto de ejemplo.
 */
function tor_lineas(): array {
	return apply_filters(
		'tor_lineas',
		array(
			array( 'name' => 'Software a la medida', 'o' => 't', 'desc' => 'ERP, CRM y plataformas que eliminan el trabajo manual y le dan visibilidad a la gerencia.', 'inc' => array( 'Levantamiento de procesos', 'Prototipo navegable', 'Entregas cada dos semanas', 'Hosting y soporte en AWS' ), 'ref' => 'crm' ),
			array( 'name' => 'Apps móviles', 'o' => 't', 'desc' => 'Apps iOS, Android y PWA con panel web de gestión.', 'inc' => array( 'Diseño de flujos con usuarios', 'Publicación en tiendas', 'Analítica de uso' ), 'ref' => 'raii' ),
			array( 'name' => 'Webs y e-commerce', 'o' => 'c', 'desc' => 'Tiendas y sitios pensados para vender, con pagos, inventario y envíos integrados.', 'inc' => array( 'Diseño y desarrollo', 'Pasarela de pagos', 'Medición de ventas' ), 'ref' => 'seeds' ),
			array( 'name' => 'Marketing digital', 'o' => 'e', 'desc' => 'Pauta y contenido en Meta, Google y TikTok con el costo por cliente siempre a la vista.', 'inc' => array( 'Diagnóstico de embudo', 'Campañas y contenido', 'Tablero semanal' ), 'ref' => 'seeds-pauta' ),
			array( 'name' => 'IA y automatización', 'o' => 't', 'desc' => 'Asistentes de WhatsApp con IA que atienden, califican y agendan a cualquier hora.', 'inc' => array( 'Asistente entrenado con tu información', 'Integración con tu CRM', 'Seguimiento automático' ), 'ref' => 'asistente' ),
		)
	);
}

/**
 * Fases del método.
 */
function tor_fases(): array {
	return apply_filters(
		'tor_fases',
		array(
			array( 't' => 'Diagnóstico', 'd' => 'Producto, proceso y resultados: el punto de partida real.', 'time' => '1 semana' ),
			array( 't' => 'Diseño y construcción', 'd' => 'Oferta, embudo y activos listos para salir al mercado.', 'time' => '2 a 6 semanas' ),
			array( 't' => 'Ejecución', 'd' => 'Seguimiento semanal e iteración basada en datos.', 'time' => 'Continuo' ),
			array( 't' => 'Escala', 'd' => 'Más volumen en lo que funciona y nuevos segmentos.', 'time' => 'Trimestral' ),
		)
	);
}

/**
 * Propuestas que arma el escenario del inicio según la idea del visitante.
 * "head" admite <em>; se imprime con wp_kses en el JS a partir de texto controlado aquí.
 */
function tor_planes(): array {
	return apply_filters(
		'tor_planes',
		array(
			'software'  => array( 'line' => 'Software a la medida', 'o' => 't', 'title' => 'Una plataforma hecha para tu operación', 'head' => 'Una plataforma <em>a tu medida.</em>', 'time' => '8–16 semanas', 'steps' => array( 'Diagnóstico de tus procesos en una semana.', 'Demo funcionando cada dos semanas.', 'Conectada a WhatsApp, pagos y facturación.' ), 'ref' => 'crm' ),
			'app'       => array( 'line' => 'Apps móviles', 'o' => 't', 'title' => 'Una app que tu equipo y tus clientes sí usan', 'head' => 'Una app que la gente <em>sí usa.</em>', 'time' => '10–14 semanas', 'steps' => array( 'Flujos clave definidos con usuarios reales.', 'App iOS y Android con panel web.', 'Publicada y medida desde el primer día.' ), 'ref' => 'raii' ),
			'web'       => array( 'line' => 'Webs y e-commerce', 'o' => 'c', 'title' => 'Una tienda que vende, no un catálogo', 'head' => 'Una tienda que <em>vende.</em>', 'time' => '4–8 semanas', 'steps' => array( 'Diseño alrededor de tus productos.', 'Pagos, inventario y envíos integrados.', 'Ventas y campañas medidas desde el lanzamiento.' ), 'ref' => 'seeds' ),
			'marketing' => array( 'line' => 'Marketing digital', 'o' => 'e', 'title' => 'Un sistema de adquisición medible', 'head' => 'Clientes nuevos, <em>cada semana.</em>', 'time' => '30 días', 'steps' => array( 'Revisión de oferta, embudo y canales.', 'Campañas en Meta, Google y TikTok.', 'Costo por cliente a la vista cada semana.' ), 'ref' => 'seeds-pauta' ),
			'ia'        => array( 'line' => 'IA y automatización', 'o' => 't', 'title' => 'Un asistente que atiende y vende a cualquier hora', 'head' => 'Un asistente que <em>vende de noche.</em>', 'time' => '2–4 semanas', 'steps' => array( 'Asistente con IA conectado a tu WhatsApp.', 'Entrenado con tus productos y precios.', 'Seguimiento y agenda automáticos.' ), 'ref' => 'asistente' ),
			'general'   => array( 'line' => 'Diagnóstico de crecimiento', 'o' => 'e', 'title' => 'Empecemos por entender tu negocio', 'head' => 'Primero, <em>entender tu negocio.</em>', 'time' => '1 semana', 'steps' => array( 'Revisamos producto, ventas y resultados.', 'Ubicamos el mayor retorno.', 'Plan por fases con costos claros.' ), 'ref' => 'seeds' ),
		)
	);
}

/**
 * Clientes que se muestran como texto.
 */
function tor_clientes(): array {
	return apply_filters( 'tor_clientes', array( 'Cinépolis', 'Colsubsidio', 'Colsanitas', 'Seeds', 'Tatem', '3 Cordilleras', 'NymeCall', 'OKKE', 'Big House Records', 'RezetMilano' ) );
}

/**
 * Opciones del formulario de contacto (¿Qué necesitas?).
 */
function tor_necesidades(): array {
	return array( 'Software a la medida', 'App móvil', 'Web o e-commerce', 'Marketing digital', 'IA y automatización', 'Aún no lo sé' );
}
