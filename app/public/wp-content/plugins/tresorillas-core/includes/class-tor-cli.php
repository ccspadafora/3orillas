<?php
/**
 * Comandos WP-CLI del sitio.
 *
 * @package TresOrillasCore
 */

defined( 'ABSPATH' ) || exit;

/**
 * Carga inicial del contenido de 3 Orillas.
 */
final class TOR_CLI {

	/**
	 * Crea o actualiza las categorías, los proyectos del diseño (con sus medios) y las páginas.
	 * Se puede ejecutar varias veces: no duplica nada.
	 *
	 * ## OPTIONS
	 *
	 * --media=<carpeta>
	 * : Carpeta "media" del diseño (contiene p/, vid-*.mp4, s-*.jpg).
	 *
	 * ## EXAMPLES
	 *
	 *     wp tor sembrar --media="/Users/camilo/Local Sites/3Orillas/3 Orillas/media"
	 *
	 * @param array $args  Argumentos.
	 * @param array $assoc Opciones.
	 */
	public function sembrar( $args, $assoc ): void {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$dir = rtrim( (string) ( $assoc['media'] ?? '' ), '/' );
		if ( ! $dir || ! is_dir( $dir ) ) {
			WP_CLI::error( 'Indica la carpeta media del diseño con --media=…' );
		}

		// Categorías.
		$orden = 0;
		foreach ( tor_categorias_base() as $slug => $c ) {
			$t = term_exists( $slug, TOR_Proyectos::TAX );
			if ( ! $t ) {
				$t = wp_insert_term( $c[0], TOR_Proyectos::TAX, array( 'slug' => $slug, 'description' => $c[1] ) );
			} else {
				wp_update_term( (int) $t['term_id'], TOR_Proyectos::TAX, array( 'name' => $c[0], 'description' => $c[1] ) );
			}
			if ( ! is_wp_error( $t ) ) {
				update_term_meta( (int) $t['term_id'], '_tor_orden', $orden++ );
			}
		}
		WP_CLI::log( 'Categorías listas.' );

		// Proyectos.
		$json = file_get_contents( TOR_CORE_DIR . 'data/proyectos.json' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- archivo local del plugin.
		$lista = json_decode( (string) $json, true );
		foreach ( (array) $lista as $i => $p ) {
			$existe = get_page_by_path( $p['id'], OBJECT, TOR_Proyectos::CPT );
			$datos  = array(
				'post_type'   => TOR_Proyectos::CPT,
				'post_status' => 'publish',
				'post_title'  => $p['name'],
				'post_name'   => $p['id'],
				'menu_order'  => $i,
			);
			if ( $existe ) {
				$datos['ID'] = $existe->ID;
			}
			$id = wp_insert_post( $datos, true );
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( $p['name'] . ': ' . $id->get_error_message() );
				continue;
			}

			$meta = array(
				'cliente'   => $p['client'],
				'tipo'      => $p['kind'],
				'anio'      => (string) $p['year'],
				'orilla'    => $p['o'],
				'chip'      => $p['chip'] ?? '',
				'intro'     => $p['intro'] ?? '',
				'reto'      => $p['problem'] ?? '',
				'solucion'  => $p['solution'] ?? '',
				'resultado' => $p['result'] ?? '',
				'stack'     => $p['stack'] ?? '',
				'demo_url'  => $p['demo_url'] ?? '',
				'movil'     => empty( $p['mobile'] ) ? '' : '1',
				'tamano'    => $p['size'] ?? '',
				'estado'    => $p['estado'] ?? '',
			);
			if ( ! empty( $p['tipo'] ) ) {
				$meta['color_fondo'] = $p['tipo'][0];
				$meta['color_texto'] = $p['tipo'][1];
			}
			if ( ! empty( $p['img'] ) ) {
				$aid = $this->medio( $dir . '/' . $p['img'], $id );
				if ( $aid ) {
					set_post_thumbnail( $id, $aid );
				}
			}
			if ( ! empty( $p['shot'] ) ) {
				$meta['captura_id'] = (string) $this->medio( $dir . '/' . $p['shot'], $id );
			}
			if ( ! empty( $p['gal'] ) ) {
				$meta['galeria_ids'] = implode( ',', array_filter( array_map( fn( $g ) => $this->medio( $dir . '/' . $g, $id ), $p['gal'] ) ) );
			}
			if ( ! empty( $p['video'] ) ) {
				$meta['video_id'] = (string) $this->medio( $dir . '/' . $p['video'], $id );
			}
			foreach ( $meta as $k => $v ) {
				if ( '' === (string) $v || '0' === (string) $v ) {
					delete_post_meta( $id, '_tor_' . $k );
				} else {
					update_post_meta( $id, '_tor_' . $k, $v );
				}
			}
			wp_set_object_terms( $id, $p['cats'], TOR_Proyectos::TAX );
			WP_CLI::log( 'Proyecto: ' . $p['name'] );
		}

		// Páginas.
		$paginas = array(
			'servicios'         => 'Servicios',
			'demo-lab'          => 'Demo Lab',
			'metodo'            => 'Método',
			'estudio'           => 'Estudio',
			'contacto'          => 'Contacto',
			'politica-de-datos' => 'Política de tratamiento de datos',
		);
		foreach ( $paginas as $slug => $titulo ) {
			$pg = get_page_by_path( $slug );
			if ( $pg ) {
				continue;
			}
			$contenido = 'politica-de-datos' === $slug ? $this->politica() : '';
			$nueva     = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $titulo,
					'post_name'    => $slug,
					'post_content' => $contenido,
				)
			);
			if ( 'politica-de-datos' === $slug && $nueva && ! is_wp_error( $nueva ) ) {
				$aj                = TOR_Ajustes::get();
				$aj['politica_id'] = (int) $nueva;
				update_option( TOR_Ajustes::OPCION, $aj );
			}
			WP_CLI::log( 'Página: ' . $titulo );
		}

		// Páginas de servicio (hijas de Servicios, salvo Áurea) y legales del sitio anterior, con sus mismas URLs.
		foreach ( tor_servicios() as $slug => $s ) {
			$padre = $s['padre'] ? get_page_by_path( $s['padre'] ) : null;
			$ruta  = ( $s['padre'] ? $s['padre'] . '/' : '' ) . $slug;
			if ( get_page_by_path( $ruta ) || ( $s['padre'] && ! $padre ) ) {
				continue;
			}
			wp_insert_post(
				array(
					'post_type'   => 'page',
					'post_status' => 'publish',
					'post_title'  => $s['name'],
					'post_name'   => $slug,
					'post_parent' => $padre ? $padre->ID : 0,
					'menu_order'  => (int) array_search( $slug, array_keys( tor_servicios() ), true ),
				)
			);
			WP_CLI::log( 'Servicio: ' . $s['name'] );
		}
		$legales = array(
			'privacy-policy'                    => array( 'Política de privacidad', $this->privacidad() ),
			'terminos-y-condiciones-de-uso'     => array( 'Términos y condiciones de uso', $this->terminos() ),
			'solicitud-de-eliminacion-de-datos' => array( 'Solicitud de eliminación de datos', $this->eliminacion() ),
		);
		foreach ( $legales as $slug => $l ) {
			$pg = get_page_by_path( $slug );
			// La página «privacy-policy» que trae WordPress nace en borrador: se reemplaza; una publicada no se toca.
			if ( $pg && 'publish' === $pg->post_status ) {
				continue;
			}
			wp_insert_post(
				array(
					'ID'           => $pg ? $pg->ID : 0,
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $l[0],
					'post_name'    => $slug,
					'post_content' => $l[1],
				)
			);
			WP_CLI::log( 'Página legal: ' . $l[0] );
		}

		update_option( 'permalink_structure', '/%postname%/' );
		update_option( 'blogname', '3 Orillas' );
		update_option( 'blogdescription', 'Estrategia, creatividad y tecnología' );
		flush_rewrite_rules();
		WP_CLI::success( 'Contenido cargado.' );
	}

	/**
	 * Importa un archivo a la biblioteca una sola vez y devuelve su ID.
	 */
	private function medio( string $ruta, int $post_id ): int {
		if ( ! is_file( $ruta ) ) {
			WP_CLI::warning( 'No existe: ' . $ruta );
			return 0;
		}
		$clave = 'tor/' . basename( dirname( $ruta ) ) . '/' . basename( $ruta );
		$ya    = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'meta_key'       => '_tor_origen', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- solo en la carga inicial.
				'meta_value'     => $clave, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'fields'         => 'ids',
				'posts_per_page' => 1,
			)
		);
		if ( $ya ) {
			return (int) $ya[0];
		}
		$tmp = wp_tempnam( basename( $ruta ) );
		copy( $ruta, $tmp );
		$id = media_handle_sideload( array( 'name' => basename( $ruta ), 'tmp_name' => $tmp ), $post_id );
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			WP_CLI::warning( basename( $ruta ) . ': ' . $id->get_error_message() );
			return 0;
		}
		update_post_meta( $id, '_tor_origen', $clave );
		return (int) $id;
	}

	/**
	 * Borrador de la política de tratamiento de datos (debe revisarlo el responsable antes de publicar en producción).
	 */
	private function politica(): string {
		return '<!-- wp:paragraph --><p><strong>Borrador para revisión.</strong> Completar los datos marcados entre corchetes antes de publicar en producción.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Responsable del tratamiento</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Valfora Holdings S.A.S. (3 Orillas), NIT [número], con domicilio en Bogotá, Colombia. Correo para asuntos de datos personales: valforaholdings.sas@gmail.com. Teléfono: 311 221 8385.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Datos que recogemos y para qué</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>En los formularios de este sitio pedimos solo tu nombre, tu número de WhatsApp y lo que nos cuentes de tu proyecto. Los usamos para responderte, enviarte la propuesta o cotización que pediste y hacer seguimiento comercial de esa solicitud. No los vendemos ni los compartimos con terceros para fines distintos.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Tus derechos</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>De acuerdo con la Ley 1581 de 2012 y el Decreto 1377 de 2013, puedes conocer, actualizar, rectificar y suprimir tus datos, revocar la autorización y presentar quejas ante la Superintendencia de Industria y Comercio. Para ejercerlos, escríbenos a valforaholdings.sas@gmail.com o usa el <a href="/solicitud-de-eliminacion-de-datos/">formulario de solicitud</a>; respondemos consultas en máximo 10 días hábiles y reclamos en máximo 15 días hábiles.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2 class="wp-block-heading">Conservación</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Conservamos tus datos mientras exista una relación comercial o la solicitud siga activa, y luego por el tiempo que exija la ley.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Vigencia: desde [fecha].</p><!-- /wp:paragraph -->';
	}

	/**
	 * Bloques de texto: título h2 + párrafos o listas. Los textos vienen de 3orillas.com (integración con Meta).
	 */
	private function bloques( array $secciones ): string {
		$h = '';
		foreach ( $secciones as $titulo => $partes ) {
			$h .= '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $titulo ) . '</h2><!-- /wp:heading -->' . "\n";
			foreach ( $partes as $p ) {
				if ( is_array( $p ) ) {
					$h .= '<!-- wp:list --><ul class="wp-block-list">' . implode( '', array_map( static fn( $li ) => '<!-- wp:list-item --><li>' . esc_html( $li ) . '</li><!-- /wp:list-item -->', $p ) ) . '</ul><!-- /wp:list -->' . "\n";
				} else {
					$h .= '<!-- wp:paragraph --><p>' . wp_kses( $p, array( 'a' => array( 'href' => array() ) ) ) . '</p><!-- /wp:paragraph -->' . "\n";
				}
			}
		}
		return $h;
	}

	private function privacidad(): string {
		$c = 'valforaholdings.sas@gmail.com';
		return $this->bloques(
			array(
				'1. Información general'       => array( 'En Valfora Holdings S.A.S. (en adelante, «la Empresa»), nos comprometemos a proteger la privacidad de los usuarios que interactúan con nuestros servicios, incluyendo nuestras integraciones con plataformas de Meta como Facebook Messenger.', 'Esta política describe cómo recopilamos, usamos y protegemos la información personal.' ),
				'2. Información que recopilamos' => array( 'Información que nos das: nombre, correo electrónico, número de teléfono y cualquier otra información que decidas compartir.', 'Información automática: ID de usuario en plataformas de Meta, historial de conversaciones, y fecha y hora de la interacción.' ),
				'3. Cómo usamos la información' => array( array( 'Brindar atención automatizada y soporte', 'Responder solicitudes o consultas', 'Mejorar nuestros servicios y la experiencia del usuario', 'Hacer seguimiento a solicitudes', 'Enviar información relacionada con el servicio, cuando aplique' ) ),
				'4. Plataformas de terceros'   => array( 'Nuestro servicio puede integrarse con Facebook Messenger, Instagram y otros servicios de Meta. El uso de esas plataformas también está sujeto a sus propias políticas de privacidad.' ),
				'5. Compartición de datos'     => array( 'La Empresa no vende, alquila ni comparte datos personales con terceros, excepto cuando sea necesario para prestar el servicio, cuando lo exija la ley o para proteger los derechos legales de la Empresa.' ),
				'6. Almacenamiento y seguridad' => array( 'Aplicamos medidas razonables de seguridad para proteger la información contra acceso no autorizado, pérdida o alteración. Ningún sistema es completamente seguro, por lo que no podemos garantizar seguridad absoluta.' ),
				'7. Derechos del usuario'      => array( 'Puedes acceder a tus datos, pedir su corrección o actualización, solicitar su eliminación y revocar la autorización de uso. Para ejercer estos derechos, escríbenos a los canales indicados en esta política.' ),
				'8. Eliminación de datos'      => array( 'Puedes solicitar la eliminación de tus datos personales en cualquier momento en la <a href="/solicitud-de-eliminacion-de-datos/">página de solicitud de eliminación de datos</a> o escribiendo al correo indicado abajo.' ),
				'9. Retención de datos'        => array( 'Guardamos los datos solo durante el tiempo necesario para cumplir las finalidades de esta política, o el que exija la ley.' ),
				'10. Cambios en esta política' => array( 'La Empresa puede modificar esta política en cualquier momento. Los cambios se publicarán en esta página.' ),
				'11. Contacto'                 => array( 'Si tienes preguntas sobre esta política o sobre el tratamiento de tus datos, escríbenos a <a href="mailto:' . $c . '">' . $c . '</a>.' ),
			)
		);
	}

	private function terminos(): string {
		$c = 'valforaholdings.sas@gmail.com';
		return $this->bloques(
			array(
				'1. Información general'        => array( 'Este documento regula el uso del servicio ofrecido por Valfora Holdings S.A.S. (en adelante, «la Empresa»), a través de su aplicación y canales digitales, incluyendo integraciones con plataformas de Meta como Facebook Messenger.', 'Al interactuar con nuestros servicios, el usuario acepta estos términos y condiciones.' ),
				'2. Descripción del servicio'   => array( 'La Empresa ofrece un sistema automatizado de comunicación (bot) para brindar información, atención al cliente y soporte a través de plataformas como Facebook Messenger. El servicio puede incluir:', array( 'Respuestas automatizadas', 'Recolección de datos básicos de contacto', 'Seguimiento de solicitudes', 'Envío de información relevante' ) ),
				'3. Uso adecuado del servicio'  => array( 'El usuario se compromete a:', array( 'Usar el servicio de manera legal y respetuosa', 'No dar información falsa o engañosa', 'No realizar actividades que afecten el funcionamiento del sistema' ), 'La Empresa puede restringir el acceso en caso de uso indebido.' ),
				'4. Recolección y uso de datos' => array( 'Al usar nuestros servicios, el usuario autoriza la recolección y el tratamiento de su nombre, su identificador de usuario en plataformas de Meta y su información de contacto, si la da. Estos datos se usan solo para brindar el servicio solicitado, mejorar la experiencia y enviar comunicaciones relacionadas con el servicio. Más detalles en la <a href="/privacy-policy/">política de privacidad</a>.' ),
				'5. Integración con terceros'   => array( 'Nuestros servicios pueden integrarse con plataformas de terceros como Meta (Facebook, Instagram, Messenger). El uso de esas plataformas también está sujeto a sus propios términos y políticas.' ),
				'6. Limitación de responsabilidad' => array( 'La Empresa no garantiza la disponibilidad continua del servicio ni la ausencia de errores. En ningún caso será responsable por pérdida de información, fallos en plataformas de terceros o uso indebido por parte del usuario.' ),
				'7. Modificaciones'             => array( 'La Empresa puede modificar estos términos en cualquier momento. Las actualizaciones se publicarán en esta misma página.' ),
				'8. Contacto'                   => array( 'Para cualquier duda o solicitud, escríbenos a <a href="mailto:' . $c . '">' . $c . '</a>.' ),
			)
		);
	}

	private function eliminacion(): string {
		$c = 'valforaholdings.sas@gmail.com';
		return $this->bloques(
			array(
				'1. Tu derecho'                   => array( 'En cumplimiento de las políticas de Meta y de la Ley 1581 de 2012, puedes solicitar la eliminación de tu información personal almacenada en nuestros sistemas.' ),
				'2. Datos que se pueden eliminar' => array( array( 'Datos de contacto que nos diste', 'Historial de interacción con el bot', 'Identificadores asociados a plataformas de Meta' ) ),
				'3. Cómo solicitarla'             => array( 'Usa el formulario de esta página o escribe a <a href="mailto:' . $c . '">' . $c . '</a> con tu nombre completo, la plataforma desde la que interactuaste (Facebook, Instagram, WhatsApp, este sitio) y qué datos quieres eliminar.' ),
				'4. Tiempo de respuesta'          => array( 'Procesamos las solicitudes en un plazo máximo de 10 días hábiles desde que las recibimos.' ),
				'5. Confirmación'                 => array( 'Cuando eliminemos los datos, te enviaremos una confirmación por el mismo medio en que hiciste la solicitud.' ),
				'6. Consideraciones'              => array( 'Algunos datos pueden conservarse temporalmente cuando sea necesario para cumplir obligaciones legales, prevenir fraudes o resolver disputas.' ),
			)
		);
	}
}
