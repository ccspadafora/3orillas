<?php
/**
 * «Cotiza en vivo» conectado a la bandeja del ERP (canal web_widget del CRM).
 *
 * Adaptado del plugin «Orillas Cotiza en vivo» (VLF_ERP/wordpress/orillas-cotiza-en-vivo):
 * mismo contrato con el ERP (POST /api/v1/chat/web-widget/sessions/, GET/POST …/sessions/{token}/messages/,
 * cabecera X-Lead-Ingest-Key), mismas reglas (nombre y WhatsApp obligatorios, producto de interés, sesión
 * reanudable). La interfaz es el panel «Cotiza en vivo» del tema.
 *
 * Credenciales SOLO en wp-config.php (o variables de entorno):
 *   define( 'TOR_CHAT_ERP_URL', 'https://erp.ejemplo.com' );   // sin barra final
 *   define( 'TOR_CHAT_ERP_KEY', '…' );                         // Lead Engine → Public ingest API key
 *
 * Añadidos frente al plugin original: autorización Ley 1581 obligatoria, nonce, campo trampa, límites por IP,
 * IP real del visitante hacia el ERP (su límite es por IP) y caché corta del sondeo.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

final class TOR_Chat {

	const NS        = 'tor/v1';
	const TOKEN_RE  = '[A-Za-z0-9_-]{8,64}';
	const MAX_MSG   = 1500; // Caracteres por mensaje del visitante.
	const LIM_MSG   = 40;   // Mensajes por IP…
	const LIM_POLL  = 900;  // Consultas de sondeo por IP…
	const VENTANA   = HOUR_IN_SECONDS; // …por hora.

	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'rutas' ) );
	}

	private static function cfg( string $k ): string {
		$c = 'TOR_CHAT_ERP_' . $k;
		if ( defined( $c ) ) {
			return (string) constant( $c );
		}
		$e = getenv( $c );
		return false === $e ? '' : (string) $e;
	}

	public static function activo(): bool {
		return '' !== self::cfg( 'URL' ) && '' !== self::cfg( 'KEY' );
	}

	/**
	 * Productos que se ofrecen como primera respuesta (como en el plugin original).
	 */
	public static function productos(): array {
		return apply_filters( 'tor_chat_productos', array( 'Software a la medida', 'App móvil', 'Web o e-commerce', 'Marketing digital', 'Chatbot o IA' ) );
	}

	public static function rutas(): void {
		register_rest_route(
			self::NS,
			'/chat/sesiones',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'crear_sesion' ),
				'permission_callback' => array( __CLASS__, 'permiso' ),
			)
		);
		register_rest_route(
			self::NS,
			'/chat/sesiones/(?P<token>' . self::TOKEN_RE . ')/mensajes',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'leer' ),
					'permission_callback' => array( __CLASS__, 'permiso' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'enviar' ),
					'permission_callback' => array( __CLASS__, 'permiso' ),
				),
			)
		);
	}

	/**
	 * Público, pero con el nonce del sitio (el mismo de los formularios) y solo si está configurado.
	 */
	public static function permiso( WP_REST_Request $r ) {
		if ( ! self::activo() ) {
			return new WP_Error( 'tor_chat_off', 'El chat en vivo no está disponible.', array( 'status' => 503 ) );
		}
		if ( ! wp_verify_nonce( (string) $r->get_header( 'x_tor_nonce' ), TOR_Contactos::NONCE ) ) {
			return new WP_Error( 'tor_chat_nonce', 'La página estuvo abierta mucho tiempo. Recárgala e inténtalo de nuevo.', array( 'status' => 403 ) );
		}
		return true;
	}

	private static function ip(): string {
		return isset( $_SERVER['REMOTE_ADDR'] ) ? (string) filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validado como IP.
	}

	/**
	 * Contador por IP en una ventana. Devuelve false si se superó el límite.
	 */
	private static function cupo( string $tipo, int $max ): bool {
		$clave = 'tor_chat_' . $tipo . '_' . md5( self::ip() . wp_salt( 'nonce' ) );
		$n     = (int) get_transient( $clave );
		if ( $n >= $max ) {
			return false;
		}
		set_transient( $clave, $n + 1, self::VENTANA );
		return true;
	}

	/**
	 * Llamada al ERP. La clave nunca sale del servidor.
	 */
	private static function erp( string $metodo, string $ruta, array $cuerpo = array(), array $query = array() ) {
		$url = rtrim( self::cfg( 'URL' ), '/' ) . '/api/v1/chat/web-widget/' . ltrim( $ruta, '/' );
		if ( $query ) {
			$url = add_query_arg( array_map( 'rawurlencode', $query ), $url );
		}
		$args = array(
			'method'  => $metodo,
			'timeout' => 20,
			'headers' => array(
				'Content-Type'     => 'application/json',
				'Accept'           => 'application/json',
				'X-Lead-Ingest-Key' => self::cfg( 'KEY' ),
				// El ERP limita por IP (sin NUM_PROXIES usa X-Forwarded-For): así cada visitante tiene su propio cupo.
				'X-Forwarded-For'  => self::ip(),
			),
		);
		if ( 'GET' !== $metodo ) {
			$args['body'] = wp_json_encode( $cuerpo );
		}
		$res = wp_remote_request( $url, $args );
		if ( is_wp_error( $res ) ) {
			return new WP_Error( 'tor_chat_erp', 'No pudimos conectar con el chat. Escríbenos por WhatsApp.', array( 'status' => 502 ) );
		}
		$code = (int) wp_remote_retrieve_response_code( $res );
		$data = json_decode( (string) wp_remote_retrieve_body( $res ), true );
		if ( 429 === $code ) {
			return new WP_Error( 'tor_chat_limite', 'Hay mucho tráfico en el chat. Inténtalo en un momento o escríbenos por WhatsApp.', array( 'status' => 429 ) );
		}
		if ( 404 === $code ) {
			return new WP_Error( 'tor_chat_sesion', 'La conversación expiró. Empieza una nueva.', array( 'status' => 404 ) );
		}
		if ( $code >= 400 || ! is_array( $data ) ) {
			return new WP_Error( 'tor_chat_erp', 'El chat no respondió como esperábamos. Escríbenos por WhatsApp.', array( 'status' => $code >= 400 ? $code : 502 ) );
		}
		return $data;
	}

	/**
	 * Solo lo que el visitante necesita ver de cada mensaje.
	 */
	private static function limpiar( array $m ): array {
		$tipo = (string) ( $m['sender_type'] ?? '' );
		return array(
			'id'    => sanitize_text_field( (string) ( $m['id'] ?? '' ) ),
			'quien' => in_array( $tipo, array( 'contact', 'user', 'ai_bot' ), true ) ? $tipo : 'ai_bot',
			'texto' => (string) ( $m['content'] ?? '' ),
			'ia'    => ! empty( $m['is_ai_generated'] ),
			'fecha' => sanitize_text_field( (string) ( $m['created_at'] ?? '' ) ),
		);
	}

	private static function respuesta( array $data, int $status = 200 ): WP_REST_Response {
		$lista = array();
		foreach ( (array) ( $data['messages'] ?? array() ) as $m ) {
			if ( is_array( $m ) ) {
				$lista[] = self::limpiar( $m );
			}
		}
		$out = array(
			'token'    => sanitize_text_field( (string) ( $data['session_token'] ?? '' ) ),
			'mensajes' => $lista,
		);
		if ( isset( $data['message'] ) && is_array( $data['message'] ) ) {
			$out['mensaje'] = self::limpiar( $data['message'] );
		}
		$r = new WP_REST_Response( $out, $status );
		$r->header( 'Cache-Control', 'no-store' );
		return $r;
	}

	/**
	 * POST /chat/sesiones — nombre + WhatsApp (+ producto) con autorización de datos.
	 */
	public static function crear_sesion( WP_REST_Request $r ) {
		$p = (array) $r->get_json_params();
		if ( ! empty( $p['sitio'] ) ) { // Campo trampa.
			return new WP_Error( 'tor_chat_invalido', 'No pudimos iniciar el chat.', array( 'status' => 400 ) );
		}
		if ( empty( $p['consent'] ) ) {
			return new WP_Error( 'tor_chat_consent', 'Para chatear necesitamos tu autorización de tratamiento de datos.', array( 'status' => 400 ) );
		}
		$nombre = mb_substr( sanitize_text_field( (string) ( $p['nombre'] ?? '' ) ), 0, 120 );
		$wa     = mb_substr( sanitize_text_field( (string) ( $p['whatsapp'] ?? '' ) ), 0, 40 );
		$prod   = mb_substr( sanitize_text_field( (string) ( $p['producto'] ?? '' ) ), 0, 120 );
		$token  = preg_match( '/^' . self::TOKEN_RE . '$/', (string) ( $p['token'] ?? '' ) ) ? (string) $p['token'] : '';
		$pagina = esc_url_raw( (string) ( $p['pagina'] ?? '' ) );
		if ( $pagina && wp_parse_url( $pagina, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
			$pagina = '';
		}
		if ( '' === $nombre || strlen( preg_replace( '/\D/', '', $wa ) ) < 7 ) {
			return new WP_Error( 'tor_chat_invalido', 'Nombre y WhatsApp (mínimo 7 dígitos) son obligatorios.', array( 'status' => 400 ) );
		}
		if ( ! TOR_Contactos::dentro_del_limite() ) {
			return new WP_Error( 'tor_chat_limite', 'Recibimos varias solicitudes seguidas. Espera unos minutos o escríbenos por WhatsApp.', array( 'status' => 429 ) );
		}
		$data = self::erp(
			'POST',
			'sessions/',
			array(
				'name'             => $nombre,
				'whatsapp'         => $wa,
				'page_url'         => $pagina,
				'product_interest' => $prod,
				'session_token'    => $token,
			)
		);
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		// Constancia de la autorización (Ley 1581) en Contactos; la conversación vive en el ERP. Sin correo: el equipo la ve en la bandeja.
		if ( ! empty( $data['is_new_session'] ) ) {
			TOR_Contactos::guardar(
				array(
					'origen'   => 'Chat en vivo (ERP)',
					'nombre'   => $nombre,
					'telefono' => $wa,
					'linea'    => $prod,
					'pagina'   => $pagina,
					'consent'  => sprintf( 'Aceptada el %s (política: %s)', wp_date( 'Y-m-d H:i' ), TOR_Ajustes::url_politica() ),
				),
				false
			);
		}
		return self::respuesta( $data, 201 );
	}

	/**
	 * GET /chat/sesiones/{token}/mensajes?after=ID — sondeo (con caché de 2 s por sesión).
	 */
	public static function leer( WP_REST_Request $r ) {
		$token = (string) $r['token'];
		$after = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $r->get_param( 'after' ) );
		$clave = 'tor_chat_m_' . md5( $token . '|' . $after );
		$cache = get_transient( $clave );
		if ( is_array( $cache ) ) {
			return self::respuesta( $cache );
		}
		if ( ! self::cupo( 'poll', self::LIM_POLL ) ) {
			return new WP_Error( 'tor_chat_limite', 'Pausamos la actualización un momento.', array( 'status' => 429 ) );
		}
		$data = self::erp( 'GET', 'sessions/' . rawurlencode( $token ) . '/messages/', array(), $after ? array( 'after' => $after ) : array() );
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		set_transient( $clave, $data, 2 );
		return self::respuesta( $data );
	}

	/**
	 * POST /chat/sesiones/{token}/mensajes — mensaje del visitante.
	 */
	public static function enviar( WP_REST_Request $r ) {
		$p     = (array) $r->get_json_params();
		$texto = mb_substr( sanitize_textarea_field( (string) ( $p['texto'] ?? '' ) ), 0, self::MAX_MSG );
		if ( '' === trim( $texto ) ) {
			return new WP_Error( 'tor_chat_vacio', 'El mensaje no puede estar vacío.', array( 'status' => 400 ) );
		}
		if ( ! self::cupo( 'msg', self::LIM_MSG ) ) {
			return new WP_Error( 'tor_chat_limite', 'Enviaste muchos mensajes seguidos. Espera un momento o escríbenos por WhatsApp.', array( 'status' => 429 ) );
		}
		$data = self::erp(
			'POST',
			'sessions/' . rawurlencode( (string) $r['token'] ) . '/messages/',
			array(
				'content'             => $texto,
				'external_message_id' => mb_substr( preg_replace( '/[^A-Za-z0-9_-]/', '', (string) ( $p['id'] ?? '' ) ), 0, 120 ),
				'product_interest'    => mb_substr( sanitize_text_field( (string) ( $p['producto'] ?? '' ) ), 0, 120 ),
			)
		);
		if ( is_wp_error( $data ) ) {
			return $data;
		}
		return self::respuesta( $data, 201 );
	}
}
