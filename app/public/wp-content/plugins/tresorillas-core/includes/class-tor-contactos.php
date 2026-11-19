<?php
/**
 * Contactos: los formularios del sitio los guardan aquí y avisan por correo.
 *
 * @package TresOrillasCore
 */

defined( 'ABSPATH' ) || exit;

final class TOR_Contactos {

	const CPT    = 'tor_contacto';
	const ACCION = 'tor_contacto';
	const NONCE  = 'tor_contacto_nonce';
	const LIMITE = 5;   // Envíos permitidos por IP…
	const VENTANA = 600; // …en esta cantidad de segundos.

	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'registrar' ) );
		add_action( 'wp_ajax_' . self::ACCION, array( __CLASS__, 'recibir' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACCION, array( __CLASS__, 'recibir' ) );
		add_action( 'wp_ajax_tor_nonce', array( __CLASS__, 'nonce_fresco' ) );
		add_action( 'wp_ajax_nopriv_tor_nonce', array( __CLASS__, 'nonce_fresco' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
		add_filter( 'manage_' . self::CPT . '_posts_columns', array( __CLASS__, 'columnas' ) );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', array( __CLASS__, 'columna' ), 10, 2 );
	}

	public static function registrar(): void {
		register_post_type(
			self::CPT,
			array(
				'labels'          => array(
					'name'          => 'Contactos',
					'singular_name' => 'Contacto',
					'all_items'     => 'Todos los contactos',
					'menu_name'     => 'Contactos',
				),
				'public'          => false,
				'show_ui'         => true,
				'menu_icon'       => 'dashicons-email-alt',
				'supports'        => array( 'title' ),
				'capability_type' => 'post',
				'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'    => true,
			)
		);
	}

	/**
	 * Campos que se guardan: clave => etiqueta.
	 */
	public static function campos(): array {
		return array(
			'origen'   => 'Formulario',
			'nombre'   => 'Nombre',
			'telefono' => 'WhatsApp',
			'correo'   => 'Correo',
			'plataforma' => 'Plataforma',
			'linea'    => '¿Qué necesita?',
			'idea'     => 'Idea escrita en el inicio',
			'mensaje'  => 'Mensaje',
			'reunion'  => 'Videollamada',
			'pagina'   => 'Página',
			'consent'  => 'Autorización de datos',
		);
	}

	/**
	 * Entrega un nonce nuevo (para páginas guardadas en caché con un nonce vencido).
	 */
	public static function nonce_fresco(): void {
		wp_send_json_success( array( 'nonce' => wp_create_nonce( self::NONCE ) ) );
	}

	/**
	 * Recibe un formulario por admin-ajax.
	 */
	public static function recibir(): void {
		if ( ! check_ajax_referer( self::NONCE, 'nonce', false ) ) {
			wp_send_json_error( array( 'codigo' => 'nonce', 'msg' => 'La página estuvo abierta mucho tiempo. Inténtalo de nuevo.' ), 403 );
		}

		// Trampa para bots: un campo oculto que una persona no llena.
		if ( ! empty( $_POST['sitio'] ) ) {
			wp_send_json_success( array( 'msg' => 'ok' ) );
		}

		if ( ! self::dentro_del_limite() ) {
			wp_send_json_error( array( 'codigo' => 'limite', 'msg' => 'Recibimos varios mensajes seguidos. Espera unos minutos o escríbenos por WhatsApp.' ), 429 );
		}

		$origenes = array( 'propuesta' => 'Propuesta del inicio', 'contacto' => 'Formulario de contacto', 'chat' => 'Chat Cotiza en vivo', 'datos' => 'Solicitud sobre datos personales' );
		$origen   = sanitize_key( wp_unslash( $_POST['origen'] ?? '' ) );
		if ( ! isset( $origenes[ $origen ] ) ) {
			wp_send_json_error( array( 'codigo' => 'origen', 'msg' => 'Formulario no válido.' ), 400 );
		}

		$d = array(
			'origen'   => $origenes[ $origen ],
			'nombre'   => mb_substr( sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) ), 0, 120 ),
			'telefono' => mb_substr( sanitize_text_field( wp_unslash( $_POST['telefono'] ?? '' ) ), 0, 40 ),
			'correo'   => mb_substr( sanitize_email( wp_unslash( $_POST['correo'] ?? '' ) ), 0, 120 ),
			'plataforma' => mb_substr( sanitize_text_field( wp_unslash( $_POST['plataforma'] ?? '' ) ), 0, 60 ),
			'linea'    => mb_substr( sanitize_text_field( wp_unslash( $_POST['linea'] ?? '' ) ), 0, 120 ),
			'idea'     => mb_substr( sanitize_text_field( wp_unslash( $_POST['idea'] ?? '' ) ), 0, 300 ),
			'mensaje'  => mb_substr( sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ?? '' ) ), 0, 3000 ),
			'pagina'   => esc_url_raw( wp_unslash( $_POST['pagina'] ?? '' ) ),
			'consent'  => '',
		);

		if ( empty( $_POST['consent'] ) ) {
			wp_send_json_error( array( 'codigo' => 'consent', 'msg' => 'Para escribirte necesitamos tu autorización de tratamiento de datos.' ), 400 );
		}
		$d['consent'] = sprintf( 'Aceptada el %s (política: %s)', wp_date( 'Y-m-d H:i' ), TOR_Ajustes::url_politica() );

		if ( 'datos' === $origen ) {
			if ( '' === $d['nombre'] ) {
				wp_send_json_error( array( 'codigo' => 'nombre', 'msg' => 'Escribe tu nombre completo.' ), 400 );
			}
			if ( ! is_email( $d['correo'] ) && strlen( preg_replace( '/\D/', '', $d['telefono'] ) ) < 7 ) {
				wp_send_json_error( array( 'codigo' => 'correo', 'msg' => 'Déjanos un correo o un teléfono para confirmarte la eliminación.' ), 400 );
			}
			if ( '' === trim( $d['mensaje'] ) ) {
				wp_send_json_error( array( 'codigo' => 'mensaje', 'msg' => 'Cuéntanos qué datos quieres eliminar o corregir.' ), 400 );
			}
		} elseif ( strlen( preg_replace( '/\D/', '', $d['telefono'] ) ) < 7 ) {
			wp_send_json_error( array( 'codigo' => 'telefono', 'msg' => 'Escribe un número de al menos 7 dígitos.' ), 400 );
		}
		if ( 'contacto' === $origen && '' === $d['nombre'] ) {
			wp_send_json_error( array( 'codigo' => 'nombre', 'msg' => 'Escribe tu nombre.' ), 400 );
		}
		if ( $d['pagina'] && wp_parse_url( $d['pagina'], PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
			$d['pagina'] = '';
		}

		if ( ! self::guardar( $d ) ) {
			wp_send_json_error( array( 'codigo' => 'guardar', 'msg' => 'No pudimos guardar tu mensaje. Escríbenos por WhatsApp.' ), 500 );
		}

		wp_send_json_success( array( 'msg' => 'ok' ) );
	}

	/**
	 * Límite de envíos por IP. La IP solo se usa en forma de hash y no se guarda en el contacto.
	 */
	/**
	 * Guarda un contacto ya sanitizado y avisa por correo. Devuelve el ID o 0.
	 */
	public static function guardar( array $d ): int {
		$titulo = trim( ( $d['nombre'] ?: $d['telefono'] ) . ' — ' . $d['origen'] );
		$id     = wp_insert_post(
			array(
				'post_type'   => self::CPT,
				'post_status' => 'private',
				'post_title'  => $titulo,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return 0;
		}
		foreach ( $d as $k => $v ) {
			if ( '' !== $v ) {
				update_post_meta( $id, '_tor_' . $k, $v );
			}
		}
		self::avisar( (int) $id, $d );
		return (int) $id;
	}

	public static function dentro_del_limite(): bool {
		$ip    = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		$clave = 'tor_lim_' . md5( $ip . wp_salt( 'nonce' ) );
		$n     = (int) get_transient( $clave );
		if ( $n >= self::LIMITE ) {
			return false;
		}
		set_transient( $clave, $n + 1, self::VENTANA );
		return true;
	}

	/**
	 * Envía el aviso por correo al equipo.
	 */
	private static function avisar( int $id, array $d ): void {
		$lineas = array();
		foreach ( self::campos() as $k => $etq ) {
			if ( '' !== ( $d[ $k ] ?? '' ) ) {
				$lineas[] = $etq . ': ' . $d[ $k ];
			}
		}
		$lineas[] = '';
		$lineas[] = 'Ver en WordPress: ' . admin_url( 'post.php?post=' . $id . '&action=edit' );

		if ( ! empty( $d['reunion'] ) ) {
			$asunto = sprintf( '[3 Orillas] Videollamada agendada: %s · %s', $d['nombre'], strtok( $d['reunion'], '·' ) );
		} else {
			$asunto = sprintf( '[3 Orillas] %s: %s', 'Solicitud sobre datos personales' === $d['origen'] ? 'Solicitud de datos (responder en máx. 10 días hábiles)' : 'Nuevo contacto', $d['nombre'] ?: $d['telefono'] );
		}
		wp_mail( TOR_Ajustes::correo(), wp_specialchars_decode( $asunto ), implode( "\n", $lineas ) );
	}

	public static function meta_boxes(): void {
		add_meta_box( 'tor_contacto_datos', 'Datos del contacto', array( __CLASS__, 'caja' ), self::CPT, 'normal', 'high' );
	}

	public static function caja( WP_Post $post ): void {
		echo '<table class="widefat striped"><tbody>';
		foreach ( self::campos() as $k => $etq ) {
			$v = (string) get_post_meta( $post->ID, '_tor_' . $k, true );
			if ( '' === $v ) {
				continue;
			}
			if ( 'telefono' === $k ) {
				$wa = preg_replace( '/\D/', '', $v );
				$wa = strlen( $wa ) === 10 ? '57' . $wa : $wa;
				$v  = '<a href="' . esc_url( 'https://wa.me/' . $wa ) . '" target="_blank" rel="noopener">' . esc_html( $v ) . '</a>';
			} else {
				$v = nl2br( esc_html( $v ) );
			}
			echo '<tr><th style="width:200px">' . esc_html( $etq ) . '</th><td>' . wp_kses_post( $v ) . '</td></tr>';
		}
		echo '<tr><th>Recibido</th><td>' . esc_html( get_the_date( 'Y-m-d H:i', $post ) ) . '</td></tr>';
		echo '</tbody></table>';
	}

	public static function columnas( array $c ): array {
		return array(
			'cb'           => $c['cb'] ?? '',
			'title'        => 'Contacto',
			'tor_telefono' => 'WhatsApp',
			'tor_origen'   => 'Formulario',
			'date'         => 'Fecha',
		);
	}

	public static function columna( string $col, int $id ): void {
		if ( 'tor_telefono' === $col ) {
			echo esc_html( (string) get_post_meta( $id, '_tor_telefono', true ) );
		} elseif ( 'tor_origen' === $col ) {
			echo esc_html( (string) get_post_meta( $id, '_tor_origen', true ) );
		}
	}
}
