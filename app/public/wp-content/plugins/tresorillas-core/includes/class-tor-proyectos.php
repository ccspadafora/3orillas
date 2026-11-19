<?php
/**
 * Tipo de contenido Proyecto (/trabajo/<slug>/) y su taxonomía de categorías.
 *
 * @package TresOrillasCore
 */

defined( 'ABSPATH' ) || exit;

final class TOR_Proyectos {

	const CPT   = 'tor_proyecto';
	const TAX   = 'tor_categoria';
	const NONCE = 'tor_proyecto_meta';

	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'registrar' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'meta_boxes' ) );
		add_action( 'save_post_' . self::CPT, array( __CLASS__, 'guardar' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_assets' ) );
		add_filter( 'manage_' . self::CPT . '_posts_columns', array( __CLASS__, 'columnas' ) );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', array( __CLASS__, 'columna' ), 10, 2 );
		add_action( 'pre_get_posts', array( __CLASS__, 'orden_archivo' ) );
		foreach ( array( 'save_post_' . self::CPT, 'deleted_post', 'set_object_terms', 'edited_' . self::TAX ) as $h ) {
			add_action( $h, array( __CLASS__, 'limpiar_cache' ) );
		}
	}

	public static function limpiar_cache(): void {
		wp_cache_delete( 'tor_proyectos_datos', 'tor' );
	}

	public static function registrar(): void {
		// La taxonomía va primero para que /trabajo/categoria/<cat>/ gane a las reglas del proyecto.
		register_taxonomy(
			self::TAX,
			self::CPT,
			array(
				'labels'            => array(
					'name'          => 'Categorías de trabajo',
					'singular_name' => 'Categoría de trabajo',
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => 'trabajo/categoria', 'with_front' => false ),
			)
		);

		register_post_type(
			self::CPT,
			array(
				'labels'       => array(
					'name'          => 'Proyectos',
					'singular_name' => 'Proyecto',
					'add_new_item'  => 'Añadir proyecto',
					'edit_item'     => 'Editar proyecto',
					'all_items'     => 'Todos los proyectos',
					'menu_name'     => 'Proyectos',
				),
				'public'       => true,
				'has_archive'  => 'trabajo',
				'rewrite'      => array( 'slug' => 'trabajo', 'with_front' => false ),
				'menu_icon'    => 'dashicons-portfolio',
				'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Definición de los campos del proyecto.
	 */
	public static function campos(): array {
		return array(
			'cliente'     => array( 'Cliente', 'text' ),
			'tipo'        => array( 'Qué es (ej.: Tienda online y ERP)', 'text' ),
			'anio'        => array( 'Año', 'number' ),
			'orilla'      => array( 'Orilla', 'select', tor_orillas() ),
			'chip'        => array( 'Frase corta del showreel (ej.: De 2M a 30–40M al mes)', 'text' ),
			'intro'       => array( 'Introducción', 'textarea' ),
			'reto'        => array( 'El reto', 'textarea' ),
			'solucion'    => array( 'Lo que construimos', 'textarea' ),
			'resultado'   => array( 'Resultado (vacío = no se muestra)', 'text' ),
			'stack'       => array( 'Tecnología usada', 'textarea' ),
			'captura_id'  => array( 'Captura pequeña (tarjetas y Demo Lab). Si falta, se usa la imagen destacada.', 'media' ),
			'galeria_ids' => array( 'Galería del caso', 'gallery' ),
			'video_id'    => array( 'Video corto (MP4)', 'video' ),
			'demo_url'     => array( 'Demo Lab · URL de la plataforma (https). Vacío = no aparece en el Demo Lab', 'url' ),
			'demo_usuario' => array( 'Demo Lab · Usuario de prueba (se muestra en público)', 'text' ),
			'demo_clave'   => array( 'Demo Lab · Clave de prueba (se muestra en público: usa un usuario con datos de prueba y permisos mínimos)', 'text' ),
			'demo_nota'    => array( 'Demo Lab · Nota (ej.: Los datos se reinician cada noche)', 'text' ),
			'movil'       => array( 'Es una app móvil (se muestra en un celular)', 'checkbox' ),
			'tamano'      => array( 'Tamaño en la cuadrícula', 'select', array( '' => 'Automático', 'ancha' => 'Ancha', 'media' => 'Media' ) ),
			'estado'      => array( 'Etiqueta de estado (ej.: En desarrollo)', 'text' ),
			'color_fondo' => array( 'Color de fondo (solo si no hay imagen)', 'color' ),
			'color_texto' => array( 'Color del texto (solo si no hay imagen)', 'color' ),
		);
	}

	public static function meta_boxes(): void {
		add_meta_box( 'tor_proyecto_datos', 'Datos del proyecto', array( __CLASS__, 'caja' ), self::CPT, 'normal', 'high' );
	}

	public static function admin_assets( string $hook ): void {
		$pantalla = get_current_screen();
		if ( ! $pantalla || self::CPT !== $pantalla->post_type || ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script( 'tor-admin', TOR_CORE_URL . 'assets/admin.js', array( 'jquery' ), TOR_CORE_VERSION, true );
		wp_enqueue_style( 'tor-admin', TOR_CORE_URL . 'assets/admin.css', array(), TOR_CORE_VERSION );
	}

	/**
	 * Lee un campo del proyecto.
	 */
	public static function meta( int $id, string $clave ) {
		return get_post_meta( $id, '_tor_' . $clave, true );
	}

	public static function caja( WP_Post $post ): void {
		wp_nonce_field( self::NONCE, self::NONCE . '_nonce' );
		echo '<div class="tor-campos">';
		foreach ( self::campos() as $clave => $def ) {
			$valor = self::meta( $post->ID, $clave );
			$name  = 'tor[' . $clave . ']';
			$id    = 'tor-' . $clave;
			$ancho = in_array( $def[1], array( 'textarea', 'gallery' ), true ) ? ' tor-full' : '';
			echo '<p class="tor-campo' . esc_attr( $ancho ) . '"><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $def[0] ) . '</strong></label><br>';
			switch ( $def[1] ) {
				case 'textarea':
					echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" rows="3" class="large-text">' . esc_textarea( (string) $valor ) . '</textarea>';
					break;
				case 'number':
					echo '<input type="number" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $valor ) . '" class="small-text">';
					break;
				case 'select':
					echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">';
					foreach ( $def[2] as $k => $l ) {
						echo '<option value="' . esc_attr( (string) $k ) . '"' . selected( (string) $valor, (string) $k, false ) . '>' . esc_html( $l ) . '</option>';
					}
					echo '</select>';
					break;
				case 'checkbox':
					echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( '1', (string) $valor, false ) . '>';
					break;
				case 'color':
					echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $valor ) . '" placeholder="#0E0E10" class="regular-text">';
					break;
				case 'url':
					echo '<input type="url" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $valor ) . '" placeholder="https://" class="large-text">';
					break;
				case 'media':
				case 'video':
				case 'gallery':
					$multiple = 'gallery' === $def[1];
					$tipo     = 'video' === $def[1] ? 'video' : 'image';
					$ids      = array_filter( array_map( 'absint', explode( ',', (string) $valor ) ) );
					echo '<input type="hidden" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '">';
					echo '<span class="tor-previa" data-for="' . esc_attr( $id ) . '">';
					foreach ( $ids as $aid ) {
						if ( 'video' === $tipo ) {
							echo '<span class="tor-video">' . esc_html( basename( (string) get_attached_file( $aid ) ) ) . '</span>';
						} else {
							echo wp_get_attachment_image( $aid, 'thumbnail' );
						}
					}
					echo '</span> <button type="button" class="button tor-media" data-for="' . esc_attr( $id ) . '" data-multiple="' . ( $multiple ? '1' : '0' ) . '" data-type="' . esc_attr( $tipo ) . '">Elegir</button> ';
					echo '<button type="button" class="button-link tor-quitar" data-for="' . esc_attr( $id ) . '">Quitar</button>';
					break;
				default:
					echo '<input type="text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( (string) $valor ) . '" class="large-text">';
			}
			echo '</p>';
		}
		echo '</div><p class="description">La imagen destacada es la portada del proyecto. El orden se define en "Atributos → Orden" (menor primero).</p>';
	}

	public static function guardar( int $post_id, WP_Post $post ): void {
		if ( ! isset( $_POST[ self::NONCE . '_nonce' ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE . '_nonce' ] ) ), self::NONCE ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$datos = isset( $_POST['tor'] ) && is_array( $_POST['tor'] ) ? wp_unslash( $_POST['tor'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- se sanitiza campo por campo.
		foreach ( self::campos() as $clave => $def ) {
			$crudo = $datos[ $clave ] ?? '';
			switch ( $def[1] ) {
				case 'textarea':
					$v = sanitize_textarea_field( (string) $crudo );
					break;
				case 'number':
					$v = '' === $crudo ? '' : (string) absint( $crudo );
					break;
				case 'select':
					$v = array_key_exists( (string) $crudo, $def[2] ) ? (string) $crudo : '';
					break;
				case 'checkbox':
					$v = empty( $crudo ) ? '' : '1';
					break;
				case 'color':
					$v = (string) sanitize_hex_color( (string) $crudo );
					break;
				case 'url':
					$v = esc_url_raw( trim( (string) $crudo ), array( 'http', 'https' ) );
					break;
				case 'media':
				case 'video':
				case 'gallery':
					$v = implode( ',', array_filter( array_map( 'absint', explode( ',', (string) $crudo ) ) ) );
					break;
				default:
					$v = sanitize_text_field( (string) $crudo );
			}
			if ( '' === $v ) {
				delete_post_meta( $post_id, '_tor_' . $clave );
			} else {
				update_post_meta( $post_id, '_tor_' . $clave, $v );
			}
		}
	}

	public static function columnas( array $c ): array {
		$nuevas = array();
		foreach ( $c as $k => $l ) {
			$nuevas[ $k ] = $l;
			if ( 'title' === $k ) {
				$nuevas['tor_cliente'] = 'Cliente';
				$nuevas['tor_orden']   = 'Orden';
			}
		}
		return $nuevas;
	}

	public static function columna( string $col, int $id ): void {
		if ( 'tor_cliente' === $col ) {
			echo esc_html( (string) self::meta( $id, 'cliente' ) );
		} elseif ( 'tor_orden' === $col ) {
			echo esc_html( (string) get_post_field( 'menu_order', $id ) );
		}
	}

	/**
	 * En /trabajo/ se muestran todos, en el orden definido.
	 */
	public static function orden_archivo( WP_Query $q ): void {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}
		if ( $q->is_post_type_archive( self::CPT ) || $q->is_tax( self::TAX ) ) {
			$q->set( 'posts_per_page', -1 );
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		}
	}

	/**
	 * Todos los proyectos publicados con la estructura que usa el JavaScript del tema.
	 */
	public static function datos(): array {
		$cache = wp_cache_get( 'tor_proyectos_datos', 'tor' );
		if ( is_array( $cache ) ) {
			return $cache;
		}
		$posts = get_posts(
			array(
				'post_type'      => self::CPT,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
			)
		);
		$orillas = tor_orillas();
		$out     = array();
		foreach ( $posts as $p ) {
			$img  = (string) get_the_post_thumbnail_url( $p, 'full' );
			$cap  = (int) self::meta( $p->ID, 'captura_id' );
			$vid  = (int) self::meta( $p->ID, 'video_id' );
			$gal  = array_filter( array_map( 'absint', explode( ',', (string) self::meta( $p->ID, 'galeria_ids' ) ) ) );
			$o    = (string) self::meta( $p->ID, 'orilla' );
			$o    = isset( $orillas[ $o ] ) ? $o : 'e';
			$cats = wp_get_post_terms( $p->ID, self::TAX, array( 'fields' => 'slugs' ) );
			$item = array(
				'id'       => $p->post_name,
				'name'     => get_the_title( $p ),
				'client'   => (string) self::meta( $p->ID, 'cliente' ),
				'kind'     => (string) self::meta( $p->ID, 'tipo' ),
				'year'     => (int) self::meta( $p->ID, 'anio' ) ?: (int) get_the_date( 'Y', $p ),
				'o'        => $o,
				'orilla'   => $orillas[ $o ],
				'cats'     => is_wp_error( $cats ) ? array() : array_values( $cats ),
				'img'      => $img,
				'shot'     => $cap ? (string) wp_get_attachment_image_url( $cap, 'full' ) : $img,
				'gal'      => array_values( array_filter( array_map( static fn( $g ) => (string) wp_get_attachment_image_url( $g, 'full' ), $gal ) ) ),
				'video'    => $vid ? (string) wp_get_attachment_url( $vid ) : '',
				'demo'     => self::meta( $p->ID, 'demo_url' ) ? $p->post_name : '',
				'demoUrl'  => (string) self::meta( $p->ID, 'demo_url' ),
				'demoUser' => (string) self::meta( $p->ID, 'demo_usuario' ),
				'demoPass' => (string) self::meta( $p->ID, 'demo_clave' ),
				'demoNota' => (string) self::meta( $p->ID, 'demo_nota' ),
				'mobile'   => '1' === (string) self::meta( $p->ID, 'movil' ),
				'size'     => (string) self::meta( $p->ID, 'tamano' ),
				'chip'     => (string) self::meta( $p->ID, 'chip' ),
				'intro'    => (string) self::meta( $p->ID, 'intro' ),
				'problem'  => (string) self::meta( $p->ID, 'reto' ),
				'solution' => (string) self::meta( $p->ID, 'solucion' ),
				'result'   => (string) self::meta( $p->ID, 'resultado' ),
				'stack'    => (string) self::meta( $p->ID, 'stack' ),
				'estado'   => (string) self::meta( $p->ID, 'estado' ),
				'url'      => (string) get_permalink( $p ),
			);
			$fondo = (string) self::meta( $p->ID, 'color_fondo' );
			if ( ! $img && $fondo ) {
				$item['tipo'] = array( $fondo, (string) self::meta( $p->ID, 'color_texto' ) ?: '#0E0E10' );
			}
			$out[] = $item;
		}
		wp_cache_set( 'tor_proyectos_datos', $out, 'tor' );
		return $out;
	}

	/**
	 * Categorías con sus nombres, descripciones y URL de filtro, en orden.
	 */
	public static function categorias(): array {
		$base  = tor_categorias_base();
		$terms = get_terms( array( 'taxonomy' => self::TAX, 'hide_empty' => false ) );
		$out   = array( array( 'todo', 'Todo', 'Plataformas, tiendas, campañas y marcas que hoy están en operación.' ) );
		$vistos = array();
		foreach ( array_keys( $base ) as $slug ) {
			$vistos[ $slug ] = array( $slug, $base[ $slug ][0], $base[ $slug ][1] );
		}
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				$vistos[ $t->slug ] = array( $t->slug, $t->name, $t->description ?: ( $base[ $t->slug ][1] ?? '' ) );
			}
		}
		return array_merge( $out, array_values( $vistos ) );
	}
}
