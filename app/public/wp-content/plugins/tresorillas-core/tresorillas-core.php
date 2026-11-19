<?php
/**
 * Plugin Name: 3 Orillas — Núcleo
 * Description: Lógica del sitio de 3 Orillas: proyectos, contactos, ajustes y datos que usa el tema.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Author: 3 Orillas
 * Text Domain: tresorillas-core
 *
 * @package TresOrillasCore
 */

defined( 'ABSPATH' ) || exit;

define( 'TOR_CORE_VERSION', '1.0.0' );
define( 'TOR_CORE_FILE', __FILE__ );
define( 'TOR_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'TOR_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once TOR_CORE_DIR . 'includes/datos.php';
require_once TOR_CORE_DIR . 'includes/servicios.php';
require_once TOR_CORE_DIR . 'includes/class-tor-ajustes.php';
require_once TOR_CORE_DIR . 'includes/class-tor-proyectos.php';
require_once TOR_CORE_DIR . 'includes/class-tor-contactos.php';
require_once TOR_CORE_DIR . 'includes/class-tor-redirecciones.php';
require_once TOR_CORE_DIR . 'includes/class-tor-zoom.php';
require_once TOR_CORE_DIR . 'includes/class-tor-chat.php';

TOR_Ajustes::init();
TOR_Proyectos::init();
TOR_Contactos::init();
TOR_Redirecciones::init();
TOR_Zoom::init();
TOR_Chat::init();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once TOR_CORE_DIR . 'includes/class-tor-cli.php';
	WP_CLI::add_command( 'tor', 'TOR_CLI' );
}

register_activation_hook(
	__FILE__,
	static function () {
		TOR_Proyectos::registrar();
		TOR_Contactos::registrar();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
