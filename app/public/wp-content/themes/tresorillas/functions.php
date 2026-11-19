<?php
/**
 * Tema 3 Orillas.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

define( 'TOR_TEMA_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/plantilla.php';

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'responsive-embeds' );
	}
);

/**
 * El tema necesita el plugin tresorillas-core. Si falta, se avisa en el admin.
 */
function tor_core_activo(): bool {
	return class_exists( 'TOR_Proyectos' );
}

add_action(
	'admin_notices',
	static function () {
		if ( ! tor_core_activo() && current_user_can( 'activate_plugins' ) ) {
			echo '<div class="notice notice-error"><p>' . esc_html__( 'El tema 3 Orillas necesita el plugin «3 Orillas — Núcleo». Actívalo en Plugins.', 'tresorillas' ) . '</p></div>';
		}
	}
);

/**
 * Datos que el JavaScript necesita (proyectos, servicios, URLs y ajustes).
 */
function tor_datos_js(): array {
	$aj = class_exists( 'TOR_Ajustes' ) ? TOR_Ajustes::get() : array();
	return array(
		'cats'     => tor_core_activo() ? TOR_Proyectos::categorias() : array(),
		'projects' => tor_core_activo() ? TOR_Proyectos::datos() : array(),
		'lines'    => function_exists( 'tor_lineas' ) ? tor_lineas() : array(),
		'servs'    => array_values(
			array_map(
				static fn( $s ) => array(
					'name' => $s['name'],
					'url'  => $s['url'],
					'slug'  => $s['slug'],
					'ref'   => $s['ref'],
					'o'     => $s['o'],
					'corto' => $s['corto'],
				),
				tor_lista_servicios()
			)
		),
		'asistente' => function_exists( 'tor_asistente' ) ? tor_asistente() : array(),
		'phases'   => function_exists( 'tor_fases' ) ? tor_fases() : array(),
		'plans'    => function_exists( 'tor_planes' ) ? tor_planes() : array(),
		'urls'     => array(
			'home'      => home_url( '/' ),
			'trabajo'   => tor_url( 'trabajo' ),
			'servicios' => tor_url( 'servicios' ),
			'demoLab'   => tor_url( 'demo-lab' ),
			'metodo'    => tor_url( 'metodo' ),
			'estudio'   => tor_url( 'estudio' ),
			'contacto'  => tor_url( 'contacto' ),
			'politica'  => class_exists( 'TOR_Ajustes' ) ? TOR_Ajustes::url_politica() : '',
			'demo'      => get_template_directory_uri() . '/assets/demo/demo.html',
			'media'     => get_template_directory_uri() . '/assets/media/',
		),
		'ajax'     => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'tor_contacto_nonce' ),
		'ajustes'  => array(
			'wa'       => (string) ( $aj['whatsapp'] ?? '573112218385' ),
			'waTxt'    => (string) ( $aj['whatsapp_txt'] ?? '311 221 8385' ),
			'llamada'  => (string) ( $aj['llamada_url'] ?? '' ),
			'embebida' => ! empty( $aj['llamada_embebida'] ),
			'hIni'     => (int) ( $aj['hora_inicio'] ?? 8 ),
			'hFin'     => (int) ( $aj['hora_fin'] ?? 18 ),
			'zoom'     => class_exists( 'TOR_Zoom' ) && TOR_Zoom::activo(),
			'chat'     => class_exists( 'TOR_Chat' ) && TOR_Chat::activo() ? array(
				'api'       => esc_url_raw( rest_url( 'tor/v1/chat/sesiones' ) ),
				'productos' => TOR_Chat::productos(),
			) : false,
		),
	);
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		$uri = get_template_directory_uri() . '/assets';
		$dir = get_template_directory() . '/assets';
		$ver = static fn( string $f ) => TOR_TEMA_VERSION . '.' . (string) @filemtime( $dir . $f ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		wp_enqueue_style( 'tor-fuentes', $uri . '/fonts/fonts.css', array(), TOR_TEMA_VERSION );
		wp_enqueue_style( 'tor-main', $uri . '/css/main.css', array( 'tor-fuentes' ), $ver( '/css/main.css' ) );

		wp_enqueue_script( 'tor-gsap', $uri . '/js/vendor/gsap.min.js', array(), '3.12.5', array( 'in_footer' => true ) );
		wp_enqueue_script( 'tor-gsap-st', $uri . '/js/vendor/ScrollTrigger.min.js', array( 'tor-gsap' ), '3.12.5', array( 'in_footer' => true ) );
		wp_enqueue_script( 'tor-gsap-flip', $uri . '/js/vendor/Flip.min.js', array( 'tor-gsap' ), '3.12.5', array( 'in_footer' => true ) );
		wp_enqueue_script( 'tor-main', $uri . '/js/main.js', array( 'tor-gsap', 'tor-gsap-st', 'tor-gsap-flip' ), $ver( '/js/main.js' ), array( 'in_footer' => true ) );
		wp_add_inline_script( 'tor-main', 'window.TOR = ' . wp_json_encode( tor_datos_js() ) . ';', 'before' );

		// Las páginas del tema no usan bloques salvo las de texto.
		if ( 'texto' !== tor_vista() ) {
			wp_dequeue_style( 'wp-block-library' );
			wp_dequeue_style( 'classic-theme-styles' );
		}
	},
	20
);

add_action(
	'wp_head',
	static function () {
		$dir = get_template_directory_uri() . '/assets';
		echo '<link rel="preload" href="' . esc_url( $dir . '/fonts/InstrumentSerif-400-latin.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		echo '<link rel="preload" href="' . esc_url( $dir . '/fonts/InterTight-400-latin.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
		echo '<link rel="icon" href="' . esc_url( $dir . '/media/iso-tinta.png' ) . '">' . "\n";
	},
	1
);

// Quitar lo que el diseño no usa.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Si el plugin no está activo, el sitio no se rompe: las listas quedan vacías.
if ( ! function_exists( 'tor_fases' ) ) {
	function tor_fases(): array { return array(); }
}
if ( ! function_exists( 'tor_clientes' ) ) {
	function tor_clientes(): array { return array(); }
}
if ( ! function_exists( 'tor_lineas' ) ) {
	function tor_lineas(): array { return array(); }
}
if ( ! function_exists( 'tor_planes' ) ) {
	function tor_planes(): array { return array(); }
}
if ( ! function_exists( 'tor_necesidades' ) ) {
	function tor_necesidades(): array { return array(); }
}

/**
 * Título de la pestaña en /trabajo/ y sus categorías.
 */
add_filter(
	'document_title_parts',
	static function ( array $t ): array {
		if ( is_post_type_archive( 'tor_proyecto' ) ) {
			$t['title'] = 'Trabajo';
		} elseif ( is_tax( 'tor_categoria' ) ) {
			$t['title'] = 'Trabajo: ' . single_term_title( '', false );
		}
		return $t;
	}
);

/**
 * Las páginas de servicio (/servicios/<slug>/ y /aurea/) usan la plantilla servicio.php.
 */
add_filter(
	'template_include',
	static function ( $plantilla ) {
		if ( function_exists( 'tor_servicio_actual' ) && tor_servicio_actual() ) {
			$p = locate_template( 'servicio.php' );
			return $p ? $p : $plantilla;
		}
		return $plantilla;
	}
);
