<?php
/**
 * Videollamadas con Zoom (app Server-to-Server OAuth).
 *
 * El visitante elige un horario libre y deja sus datos; el sitio crea la reunión en la cuenta de Zoom del estudio,
 * guarda el contacto y avisa al equipo. Las credenciales NO van en el código: se definen en wp-config.php
 * (o como variables de entorno con el mismo nombre):
 *
 *   define( 'TOR_ZOOM_ACCOUNT_ID', '…' );
 *   define( 'TOR_ZOOM_CLIENT_ID', '…' );
 *   define( 'TOR_ZOOM_CLIENT_SECRET', '…' );
 *   define( 'TOR_ZOOM_ANFITRION', 'correo@del-anfitrion.com' ); // usuario de Zoom que será el anfitrión
 *
 * Alcances de la app: meeting:write:meeting:admin y meeting:read:list_meetings:admin.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

final class TOR_Zoom {

	const DURACION = 30;              // Minutos por reunión.
	const DIAS     = 6;               // Días hábiles que se ofrecen.
	const MARGEN   = 2 * HOUR_IN_SECONDS; // Anticipación mínima.
	const ZONA     = 'America/Bogota';

	public static function init(): void {
		foreach ( array( 'tor_zoom_horarios' => 'horarios', 'tor_zoom_agendar' => 'agendar' ) as $accion => $metodo ) {
			add_action( 'wp_ajax_' . $accion, array( __CLASS__, $metodo ) );
			add_action( 'wp_ajax_nopriv_' . $accion, array( __CLASS__, $metodo ) );
		}
	}

	private static function cfg( string $k ): string {
		$c = 'TOR_ZOOM_' . $k;
		if ( defined( $c ) ) {
			return (string) constant( $c );
		}
		$e = getenv( $c );
		return false === $e ? '' : (string) $e;
	}

	/**
	 * ¿Hay credenciales completas?
	 */
	public static function activo(): bool {
		return '' !== self::cfg( 'ACCOUNT_ID' ) && '' !== self::cfg( 'CLIENT_ID' ) && '' !== self::cfg( 'CLIENT_SECRET' );
	}

	private static function anfitrion(): string {
		$a = self::cfg( 'ANFITRION' );
		return '' !== $a ? rawurlencode( $a ) : 'me';
	}

	/**
	 * Token de acceso (se guarda en caché hasta un minuto antes de vencer).
	 */
	private static function token(): string {
		$t = get_transient( 'tor_zoom_token' );
		if ( is_string( $t ) && '' !== $t ) {
			return $t;
		}
		$r = wp_remote_post(
			'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . rawurlencode( self::cfg( 'ACCOUNT_ID' ) ),
			array(
				'timeout' => 15,
				'headers' => array( 'Authorization' => 'Basic ' . base64_encode( self::cfg( 'CLIENT_ID' ) . ':' . self::cfg( 'CLIENT_SECRET' ) ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- requerido por OAuth.
			)
		);
		$j = json_decode( (string) wp_remote_retrieve_body( $r ), true );
		if ( is_wp_error( $r ) || empty( $j['access_token'] ) ) {
			return '';
		}
		set_transient( 'tor_zoom_token', $j['access_token'], max( 60, (int) ( $j['expires_in'] ?? 3600 ) - 60 ) );
		return (string) $j['access_token'];
	}

	private static function api( string $metodo, string $ruta, ?array $cuerpo = null ) {
		$tok = self::token();
		if ( '' === $tok ) {
			return null;
		}
		$args = array(
			'method'  => $metodo,
			'timeout' => 15,
			'headers' => array(
				'Authorization' => 'Bearer ' . $tok,
				'Content-Type'  => 'application/json',
			),
		);
		if ( null !== $cuerpo ) {
			$args['body'] = wp_json_encode( $cuerpo );
		}
		$r = wp_remote_request( 'https://api.zoom.us/v2' . $ruta, $args );
		if ( is_wp_error( $r ) ) {
			return null;
		}
		$c = (int) wp_remote_retrieve_response_code( $r );
		if ( 401 === $c ) {
			delete_transient( 'tor_zoom_token' );
		}
		return $c >= 200 && $c < 300 ? json_decode( (string) wp_remote_retrieve_body( $r ), true ) : null;
	}

	/**
	 * Intervalos ocupados [inicio, fin] (timestamps) según las reuniones próximas del anfitrión.
	 */
	private static function ocupados(): array {
		$c = get_transient( 'tor_zoom_ocupados' );
		if ( is_array( $c ) ) {
			return $c;
		}
		$j   = self::api( 'GET', '/users/' . self::anfitrion() . '/meetings?type=upcoming&page_size=300' );
		$out = array();
		foreach ( (array) ( $j['meetings'] ?? array() ) as $m ) {
			$ini = strtotime( (string) ( $m['start_time'] ?? '' ) );
			if ( $ini ) {
				$out[] = array( $ini, $ini + 60 * max( 15, (int) ( $m['duration'] ?? self::DURACION ) ) );
			}
		}
		set_transient( 'tor_zoom_ocupados', $out, 2 * MINUTE_IN_SECONDS );
		return $out;
	}

	/**
	 * Horarios libres: días hábiles, dentro del horario de atención, cada media hora.
	 *
	 * @return array<string, array<int, int>> fecha (Y-m-d) => timestamps
	 */
	public static function libres(): array {
		$zona  = new DateTimeZone( self::ZONA );
		$ini_h = (int) TOR_Ajustes::get( 'hora_inicio' );
		$fin_h = (int) TOR_Ajustes::get( 'hora_fin' );
		$ocup  = self::ocupados();
		$desde = time() + self::MARGEN;
		$dia   = new DateTimeImmutable( 'today', $zona );
		$out   = array();
		for ( $i = 0; count( $out ) < self::DIAS && $i < 14; $i++ ) {
			$d = $dia->modify( "+$i day" );
			if ( (int) $d->format( 'N' ) > 5 ) {
				continue;
			}
			$lista = array();
			for ( $m = $ini_h * 60; $m + self::DURACION <= $fin_h * 60; $m += 30 ) {
				$t = $d->setTime( intdiv( $m, 60 ), $m % 60 )->getTimestamp();
				if ( $t < $desde ) {
					continue;
				}
				$fin = $t + self::DURACION * 60;
				foreach ( $ocup as $o ) {
					if ( $t < $o[1] && $fin > $o[0] ) {
						continue 2;
					}
				}
				$lista[] = $t;
			}
			if ( $lista ) {
				$out[ $d->format( 'Y-m-d' ) ] = $lista;
			}
		}
		return $out;
	}

	/**
	 * Fecha en español (no depende del idioma instalado en WordPress).
	 */
	public static function fecha( int $t, bool $larga = false ): string {
		$d    = new DateTimeImmutable( '@' . $t );
		$d    = $d->setTimezone( new DateTimeZone( self::ZONA ) );
		$dias = array( 1 => 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo' );
		$mes  = array( 1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
		$dia  = $dias[ (int) $d->format( 'N' ) ];
		$m    = $mes[ (int) $d->format( 'n' ) ];
		if ( $larga ) {
			return $dia . ' ' . $d->format( 'j' ) . ' de ' . $m . ', ' . self::hora( $t );
		}
		return mb_substr( $dia, 0, 3 ) . ' ' . $d->format( 'j' ) . ' ' . mb_substr( $m, 0, 3 );
	}

	public static function hora( int $t ): string {
		$d = ( new DateTimeImmutable( '@' . $t ) )->setTimezone( new DateTimeZone( self::ZONA ) );
		return $d->format( 'g:i' ) . ( (int) $d->format( 'G' ) < 12 ? ' a. m.' : ' p. m.' );
	}

	/**
	 * AJAX: horarios libres para el selector.
	 */
	public static function horarios(): void {
		if ( ! check_ajax_referer( TOR_Contactos::NONCE, 'nonce', false ) ) {
			wp_send_json_error( array( 'codigo' => 'nonce', 'msg' => 'La página estuvo abierta mucho tiempo. Inténtalo de nuevo.' ), 403 );
		}
		if ( ! self::activo() ) {
			wp_send_json_error( array( 'codigo' => 'zoom', 'msg' => 'La agenda no está disponible en este momento.' ), 503 );
		}
		$dias = array();
		foreach ( self::libres() as $fecha => $lista ) {
			$dias[] = array(
				'fecha' => $fecha,
				'dia'   => self::fecha( $lista[0] ),
				'horas' => array_map( static fn( $t ) => array( 't' => $t, 'h' => self::hora( $t ) ), $lista ),
			);
		}
		wp_send_json_success( array( 'dias' => $dias ) );
	}

	/**
	 * AJAX: agenda la reunión, guarda el contacto y avisa al equipo.
	 */
	public static function agendar(): void {
		if ( ! check_ajax_referer( TOR_Contactos::NONCE, 'nonce', false ) ) {
			wp_send_json_error( array( 'codigo' => 'nonce', 'msg' => 'La página estuvo abierta mucho tiempo. Inténtalo de nuevo.' ), 403 );
		}
		if ( ! empty( $_POST['sitio'] ) ) {
			wp_send_json_success( array( 'msg' => 'ok' ) );
		}
		if ( ! self::activo() ) {
			wp_send_json_error( array( 'codigo' => 'zoom', 'msg' => 'La agenda no está disponible. Escríbenos por WhatsApp.' ), 503 );
		}
		if ( ! TOR_Contactos::dentro_del_limite() ) {
			wp_send_json_error( array( 'codigo' => 'limite', 'msg' => 'Recibimos varias solicitudes seguidas. Espera unos minutos o escríbenos por WhatsApp.' ), 429 );
		}
		if ( empty( $_POST['consent'] ) ) {
			wp_send_json_error( array( 'codigo' => 'consent', 'msg' => 'Para agendar necesitamos tu autorización de tratamiento de datos.' ), 400 );
		}
		$nombre = mb_substr( sanitize_text_field( wp_unslash( $_POST['nombre'] ?? '' ) ), 0, 120 );
		$tel    = mb_substr( sanitize_text_field( wp_unslash( $_POST['telefono'] ?? '' ) ), 0, 40 );
		$linea  = mb_substr( sanitize_text_field( wp_unslash( $_POST['linea'] ?? '' ) ), 0, 120 );
		$t      = absint( $_POST['t'] ?? 0 );
		if ( '' === $nombre ) {
			wp_send_json_error( array( 'codigo' => 'nombre', 'msg' => 'Escribe tu nombre.' ), 400 );
		}
		if ( strlen( preg_replace( '/\D/', '', $tel ) ) < 7 ) {
			wp_send_json_error( array( 'codigo' => 'telefono', 'msg' => 'Escribe un WhatsApp de al menos 7 dígitos.' ), 400 );
		}
		// El horario debe seguir libre (se vuelve a consultar Zoom).
		delete_transient( 'tor_zoom_ocupados' );
		$libre = false;
		foreach ( self::libres() as $lista ) {
			if ( in_array( $t, $lista, true ) ) {
				$libre = true;
				break;
			}
		}
		if ( ! $libre ) {
			wp_send_json_error( array( 'codigo' => 'horario', 'msg' => 'Ese horario ya no está disponible. Elige otro.' ), 409 );
		}

		$zona = new DateTimeZone( self::ZONA );
		$r    = self::api(
			'POST',
			'/users/' . self::anfitrion() . '/meetings',
			array(
				'topic'      => mb_substr( '3 Orillas · ' . $nombre, 0, 190 ),
				'type'       => 2,
				'start_time' => wp_date( 'Y-m-d\TH:i:s', $t, $zona ),
				'timezone'   => self::ZONA,
				'duration'   => self::DURACION,
				'agenda'     => mb_substr( $linea ? 'Interés: ' . $linea : 'Videollamada desde el sitio web', 0, 1900 ),
				'settings'   => array(
					'waiting_room'     => true,
					'join_before_host' => false,
					'mute_upon_entry'  => true,
				),
			)
		);
		if ( empty( $r['join_url'] ) ) {
			wp_send_json_error( array( 'codigo' => 'zoom', 'msg' => 'No pudimos crear la reunión. Escríbenos por WhatsApp y la agendamos contigo.' ), 502 );
		}
		delete_transient( 'tor_zoom_ocupados' );

		$cuando = self::fecha( $t, true );
		$join   = esc_url_raw( (string) $r['join_url'], array( 'https' ) );
		TOR_Contactos::guardar(
			array(
				'origen'   => 'Videollamada por Zoom',
				'nombre'   => $nombre,
				'telefono' => $tel,
				'linea'    => $linea,
				'reunion'  => $cuando . ' (hora de Bogotá) · ' . $join,
				'pagina'   => esc_url_raw( wp_unslash( $_POST['pagina'] ?? '' ) ),
				'consent'  => sprintf( 'Aceptada el %s (política: %s)', wp_date( 'Y-m-d H:i' ), TOR_Ajustes::url_politica() ),
			)
		);

		wp_send_json_success(
			array(
				'cuando' => $cuando,
				'join'   => $join,
				'inicio' => gmdate( 'Ymd\THis\Z', $t ),
				'fin'    => gmdate( 'Ymd\THis\Z', $t + self::DURACION * 60 ),
			)
		);
	}
}
