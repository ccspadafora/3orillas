<?php
/**
 * Redirecciones 301 desde las URLs del sitio anterior (Elementor) a las nuevas.
 * Solo actúan cuando la URL vieja ya no existe (404), así nunca tapan una página real.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

final class TOR_Redirecciones {

	public static function init(): void {
		add_action( 'template_redirect', array( __CLASS__, 'redirigir' ), 1 );
	}

	/**
	 * Ruta vieja => ruta nueva (relativas a la raíz del sitio).
	 */
	public static function mapa(): array {
		return apply_filters(
			'tor_redirecciones',
			array(
				'web'                                     => 'servicios/webs-y-ecommerce',
				'sitios-web'                              => 'servicios/webs-y-ecommerce',
				'marketing'                               => 'servicios/marketing-digital',
				'desarrollo-de-software'                  => 'servicios/software',
				'chatbot-con-ia-para-whatsapp'            => 'servicios/chatbot-whatsapp',
				'automatizacion-e-inteligencia-artificial' => 'servicios/automatizacion-ia',
				'conocenos'                               => 'estudio',
				'home-2'                                  => '',
			)
		);
	}

	public static function redirigir(): void {
		if ( ! is_404() ) {
			return;
		}
		$ruta = trim( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ), '/' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- solo se compara contra el mapa.
		$base = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $base && 0 === strpos( $ruta, $base . '/' ) ) {
			$ruta = substr( $ruta, strlen( $base ) + 1 );
		}
		$mapa = self::mapa();
		if ( isset( $mapa[ $ruta ] ) ) {
			wp_safe_redirect( home_url( '/' . ( $mapa[ $ruta ] ? $mapa[ $ruta ] . '/' : '' ) ), 301 );
			exit;
		}
	}
}
