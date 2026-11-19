<?php
/**
 * Ajustes del sitio (Ajustes → 3 Orillas).
 *
 * @package TresOrillasCore
 */

defined( 'ABSPATH' ) || exit;

final class TOR_Ajustes {

	const OPCION = 'tor_ajustes';

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'registrar' ) );
	}

	/**
	 * Valores por defecto.
	 */
	public static function defecto(): array {
		return array(
			'correo'           => '',
			'whatsapp'         => '573112218385',
			'whatsapp_txt'     => '311 221 8385',
			'horario_txt'      => 'Bogotá, Colombia · lunes a viernes, 8:00 a 18:00',
			'hora_inicio'      => 8,
			'hora_fin'         => 18,
			'llamada_url'      => '',
			'llamada_embebida' => 0,
			'politica_id'      => 0,
		);
	}

	/**
	 * Lee un ajuste (o todos).
	 *
	 * @param string|null $clave Clave del ajuste.
	 * @return mixed
	 */
	public static function get( ?string $clave = null ) {
		$v = wp_parse_args( (array) get_option( self::OPCION, array() ), self::defecto() );
		if ( null === $clave ) {
			return $v;
		}
		return $v[ $clave ] ?? null;
	}

	/**
	 * Correo que recibe los contactos (si no hay uno, el del administrador).
	 */
	public static function correo(): string {
		$c = (string) self::get( 'correo' );
		return is_email( $c ) ? $c : (string) get_option( 'admin_email' );
	}

	/**
	 * URL de la política de tratamiento de datos.
	 */
	public static function url_politica(): string {
		$id = (int) self::get( 'politica_id' );
		if ( $id && get_post( $id ) ) {
			return (string) get_permalink( $id );
		}
		return home_url( '/politica-de-datos/' );
	}

	public static function menu(): void {
		add_options_page( '3 Orillas', '3 Orillas', 'manage_options', 'tor-ajustes', array( __CLASS__, 'pagina' ) );
	}

	public static function registrar(): void {
		register_setting(
			'tor_ajustes_grupo',
			self::OPCION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitizar' ),
				'default'           => self::defecto(),
			)
		);
	}

	/**
	 * Sanitiza los ajustes enviados.
	 *
	 * @param mixed $in Entrada.
	 */
	public static function sanitizar( $in ): array {
		$in  = is_array( $in ) ? $in : array();
		$out = self::defecto();

		$correo        = sanitize_email( $in['correo'] ?? '' );
		$out['correo'] = is_email( $correo ) ? $correo : '';

		$out['whatsapp']     = preg_replace( '/\D/', '', (string) ( $in['whatsapp'] ?? '' ) );
		$out['whatsapp_txt'] = sanitize_text_field( $in['whatsapp_txt'] ?? '' );
		$out['horario_txt']  = sanitize_text_field( $in['horario_txt'] ?? '' );
		$out['hora_inicio']  = min( 23, max( 0, absint( $in['hora_inicio'] ?? 8 ) ) );
		$out['hora_fin']     = min( 24, max( 1, absint( $in['hora_fin'] ?? 18 ) ) );

		$url                     = esc_url_raw( $in['llamada_url'] ?? '', array( 'https' ) );
		$out['llamada_url']      = $url;
		$out['llamada_embebida'] = empty( $in['llamada_embebida'] ) ? 0 : 1;
		$out['politica_id']      = absint( $in['politica_id'] ?? 0 );

		if ( '' === $out['whatsapp'] ) {
			$out['whatsapp'] = self::defecto()['whatsapp'];
		}
		return $out;
	}

	public static function pagina(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$v = self::get();
		?>
		<div class="wrap">
			<h1>3 Orillas</h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'tor_ajustes_grupo' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="tor-correo">Correo que recibe los contactos</label></th>
						<td><input type="email" class="regular-text" id="tor-correo" name="<?php echo esc_attr( self::OPCION ); ?>[correo]" value="<?php echo esc_attr( $v['correo'] ); ?>">
						<p class="description">Si se deja vacío, se usa el correo del administrador (<?php echo esc_html( (string) get_option( 'admin_email' ) ); ?>).</p></td>
					</tr>
					<tr>
						<th scope="row"><label for="tor-wa">WhatsApp (con indicativo)</label></th>
						<td><input type="text" class="regular-text" id="tor-wa" name="<?php echo esc_attr( self::OPCION ); ?>[whatsapp]" value="<?php echo esc_attr( $v['whatsapp'] ); ?>">
						<p class="description">Solo números, por ejemplo 573112218385.</p></td>
					</tr>
					<tr>
						<th scope="row"><label for="tor-wa-txt">WhatsApp como se muestra</label></th>
						<td><input type="text" class="regular-text" id="tor-wa-txt" name="<?php echo esc_attr( self::OPCION ); ?>[whatsapp_txt]" value="<?php echo esc_attr( $v['whatsapp_txt'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="tor-horario">Texto del horario</label></th>
						<td><input type="text" class="large-text" id="tor-horario" name="<?php echo esc_attr( self::OPCION ); ?>[horario_txt]" value="<?php echo esc_attr( $v['horario_txt'] ); ?>"></td>
					</tr>
					<tr>
						<th scope="row">Horario de atención (hora de Bogotá, lunes a viernes)</th>
						<td>
							<label>De <input type="number" min="0" max="23" class="small-text" name="<?php echo esc_attr( self::OPCION ); ?>[hora_inicio]" value="<?php echo esc_attr( (string) $v['hora_inicio'] ); ?>"></label>
							<label>a <input type="number" min="1" max="24" class="small-text" name="<?php echo esc_attr( self::OPCION ); ?>[hora_fin]" value="<?php echo esc_attr( (string) $v['hora_fin'] ); ?>"></label>
							<p class="description">Dentro de este horario el sitio dice "Asesores en línea"; fuera de él, cuándo respondemos.</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="tor-llamada">Enlace de la videollamada</label></th>
						<td><input type="url" class="large-text" id="tor-llamada" name="<?php echo esc_attr( self::OPCION ); ?>[llamada_url]" value="<?php echo esc_attr( $v['llamada_url'] ); ?>" placeholder="https://">
						<p class="description">Sala de Zoom o página de agenda (Calendly, Google Calendar…). Si está vacío, los botones de videollamada abren WhatsApp.</p>
						<label><input type="checkbox" name="<?php echo esc_attr( self::OPCION ); ?>[llamada_embebida]" value="1" <?php checked( 1, (int) $v['llamada_embebida'] ); ?>> Mostrar la agenda dentro del sitio (solo para agendas que permiten insertarse, como Calendly)</label></td>
					</tr>
					<tr>
						<th scope="row"><label for="tor-politica">Política de tratamiento de datos</label></th>
						<td><?php
						wp_dropdown_pages(
							array(
								'name'              => esc_attr( self::OPCION ) . '[politica_id]',
								'id'                => 'tor-politica',
								'selected'          => (int) $v['politica_id'],
								'show_option_none'  => '— Elegir página —',
								'option_none_value' => '0',
								'post_status'       => array( 'publish', 'draft' ),
							)
						);
						?>
						<p class="description">Se enlaza junto a la casilla de autorización (Ley 1581) en todos los formularios.</p></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
