<?php
/**
 * Funciones de plantilla. Reproducen el marcado del diseño (index.html) en PHP.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;

/**
 * URL de una sección del sitio.
 */
function tor_url( string $slug ): string {
	if ( 'trabajo' === $slug ) {
		$u = post_type_exists( 'tor_proyecto' ) ? get_post_type_archive_link( 'tor_proyecto' ) : false;
		return $u ? (string) $u : home_url( '/trabajo/' );
	}
	$p = get_page_by_path( $slug );
	return $p ? (string) get_permalink( $p ) : home_url( '/' . $slug . '/' );
}

/**
 * URL de un archivo de medios del tema.
 */
function tor_m( string $archivo ): string {
	return get_template_directory_uri() . '/assets/media/' . ltrim( $archivo, '/' );
}

/**
 * Vista actual (equivale a data-view del diseño).
 */
function tor_vista(): string {
	if ( is_front_page() ) {
		return 'inicio';
	}
	if ( is_post_type_archive( 'tor_proyecto' ) || is_tax( 'tor_categoria' ) ) {
		return 'trabajo';
	}
	if ( is_singular( 'tor_proyecto' ) ) {
		return 'caso';
	}
	if ( function_exists( 'tor_servicio_actual' ) && tor_servicio_actual() ) {
		return 'servicios';
	}
	foreach ( array( 'servicios', 'demo-lab', 'metodo', 'estudio', 'contacto' ) as $v ) {
		if ( is_page( $v ) ) {
			return 'estudio' === $v ? 'nosotros' : $v;
		}
	}
	return 'texto';
}

/**
 * Enlace de WhatsApp con mensaje opcional.
 */
function tor_wa( string $texto = '' ): string {
	$n = class_exists( 'TOR_Ajustes' ) ? (string) TOR_Ajustes::get( 'whatsapp' ) : '573112218385';
	return 'https://wa.me/' . rawurlencode( $n ) . ( $texto ? '?text=' . rawurlencode( $texto ) : '' );
}

function tor_wa_txt(): string {
	return class_exists( 'TOR_Ajustes' ) ? (string) TOR_Ajustes::get( 'whatsapp_txt' ) : '311 221 8385';
}

function tor_horario_txt(): string {
	return class_exists( 'TOR_Ajustes' ) ? (string) TOR_Ajustes::get( 'horario_txt' ) : '';
}

/**
 * Etiquetas permitidas para los íconos SVG del tema.
 */
function tor_kses_svg(): array {
	$comun = array( 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'opacity' => true, 'style' => true, 'class' => true, 'id' => true );
	return array(
		'svg'      => $comun + array( 'width' => true, 'height' => true, 'viewbox' => true, 'aria-hidden' => true, 'xmlns' => true ),
		'path'     => $comun + array( 'd' => true ),
		'circle'   => $comun + array( 'cx' => true, 'cy' => true, 'r' => true ),
		'rect'     => $comun + array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true ),
		'defs'     => array(),
		'text'     => $comun + array( 'font-family' => true, 'font-size' => true, 'letter-spacing' => true ),
		'textpath' => array( 'href' => true ),
	);
}

/**
 * Íconos del diseño.
 */
function tor_icono( string $n ): string {
	$i = array(
		'flecha'  => '<svg class="flecha" viewBox="0 0 14 14" aria-hidden="true"><path d="M4 10L10 4M5 4h5v5" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>',
		'ir'      => '<svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 7h8M7 3l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>',
		'izq'     => '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M10 3L5 8l5 5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
		'der'     => '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M6 3l5 5-5 5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
		'pausa'   => '<svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" id="rPausaIco"><path d="M4 2v10M10 2v10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'burger'  => '<svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true"><path d="M2 5h14M2 9h14M2 13h14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
		'check'   => '<svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 7.5l2.5 2.5L11 4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'codigo'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M8 7l-5 5 5 5M16 7l5 5-5 5"/></svg>',
		'buscar'  => '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><circle cx="7" cy="7" r="5" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M11 11l3 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>',
		'grid'    => '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><rect x="2" y="2" width="5" height="5" rx="1.2" fill="currentColor"/><rect x="9" y="2" width="5" height="5" rx="1.2" fill="currentColor"/><rect x="2" y="9" width="5" height="5" rx="1.2" fill="currentColor"/><rect x="9" y="9" width="5" height="5" rx="1.2" fill="currentColor"/></svg>',
		'lista'   => '<svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 4h12M2 8h12M2 12h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'tirador' => '<svg width="22" height="22" viewBox="0 0 22 22" aria-hidden="true"><path d="M8 6l-4 5 4 5M14 6l4 5-4 5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
	);
	return $i[ $n ] ?? '';
}

function tor_e_icono( string $n ): void {
	echo wp_kses( tor_icono( $n ), tor_kses_svg() );
}

/**
 * Proyecto por slug, desde los datos del plugin.
 */
function tor_proyecto( string $slug ): ?array {
	if ( ! tor_core_activo() ) {
		return null;
	}
	foreach ( TOR_Proyectos::datos() as $p ) {
		if ( $p['id'] === $slug ) {
			return $p;
		}
	}
	return null;
}

/**
 * Nombre de una categoría.
 */
function tor_cat_nombre( string $slug ): string {
	if ( tor_core_activo() ) {
		foreach ( TOR_Proyectos::categorias() as $c ) {
			if ( $c[0] === $slug ) {
				return $c[1];
			}
		}
	}
	return $slug;
}

function tor_cat_url( string $slug ): string {
	return 'todo' === $slug ? tor_url( 'trabajo' ) : add_query_arg( 'cat', $slug, tor_url( 'trabajo' ) );
}

/**
 * Media de una tarjeta de proyecto (equivale a media() del diseño).
 */
function tor_obra_media( array $p ): string {
	$estado = $p['estado'] ? '<span class="estado cristal">' . esc_html( $p['estado'] ) . '</span>' : '';
	if ( ! empty( $p['tipo'] ) ) {
		return '<div class="obra-media" style="background:' . esc_attr( $p['tipo'][0] ) . ';color:' . esc_attr( $p['tipo'][1] ) . ';"><div class="tipo"><span>' . esc_html( $p['client'] ) . '</span><b>' . esc_html( $p['name'] ) . '</b><span>' . esc_html( $p['kind'] ) . '</span></div>' . $estado . '</div>';
	}
	$video = ( $p['video'] && ! $p['mobile'] ) ? '<video data-src="' . esc_url( $p['video'] ) . '" muted loop playsinline preload="none" aria-hidden="true"></video>' : '';
	return '<div class="obra-media"><img src="' . esc_url( $p['img'] ) . '" alt="' . esc_attr( $p['name'] . ': ' . $p['kind'] ) . '" loading="lazy">' . $video . $estado . '</div>';
}

function tor_chips( array $p ): string {
	$h = '';
	foreach ( $p['cats'] as $c ) {
		$h .= '<span>' . esc_html( tor_cat_nombre( $c ) ) . '</span>';
	}
	return '<div class="cat-chips">' . $h . '</div>';
}

/**
 * Tarjeta de proyecto (equivale a obra() del diseño).
 */
function tor_obra( array $p, string $tam = '' ): string {
	return '<a class="obra ' . esc_attr( $tam ) . '" href="' . esc_url( $p['url'] ) . '" data-id="' . esc_attr( $p['id'] ) . '" data-cursor="Ver caso">' . tor_obra_media( $p ) . '<div class="obra-info"><div><h3>' . esc_html( $p['name'] ) . '</h3><p>' . esc_html( $p['client'] . ' · ' . $p['kind'] ) . '</p></div><span class="anio">' . esc_html( (string) $p['year'] ) . '</span></div>' . tor_chips( $p ) . '</a>';
}

/**
 * Fase del método (equivale a fase() del diseño).
 */
function tor_fase( array $f, int $i ): string {
	return '<div class="fase"><span class="n">Fase ' . esc_html( (string) ( $i + 1 ) ) . '</span><h3>' . esc_html( $f['t'] ) . '</h3><p>' . esc_html( $f['d'] ) . '</p><span class="t">' . esc_html( $f['time'] ) . '</span></div>';
}

/**
 * Casilla de autorización de tratamiento de datos (Ley 1581).
 */
function tor_acepto( string $id ): string {
	$url = class_exists( 'TOR_Ajustes' ) ? TOR_Ajustes::url_politica() : home_url( '/politica-de-datos/' );
	return '<label class="acepto" for="' . esc_attr( $id ) . '"><input type="checkbox" id="' . esc_attr( $id ) . '" name="consent" value="1" required><span>Autorizo el tratamiento de mis datos según la <a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">política de datos</a>.</span></label>';
}

/**
 * Campo trampa para bots.
 */
function tor_trampa(): string {
	return '<span class="trampa" aria-hidden="true"><label>Sitio web<input type="text" name="sitio" tabindex="-1" autocomplete="off"></label></span>';
}

/**
 * HTML permitido para tarjetas generadas en PHP (marcado propio ya escapado).
 */
function tor_kses_tarjeta(): array {
	$a = wp_kses_allowed_html( 'post' );
	$a['video'] = array( 'data-src' => true, 'src' => true, 'muted' => true, 'loop' => true, 'playsinline' => true, 'preload' => true, 'aria-hidden' => true, 'autoplay' => true, 'aria-label' => true, 'controls' => true );
	$a['a']['data-id'] = true;
	$a['a']['data-cursor'] = true;
	$a['img']['loading'] = true;
	return $a;
}

/**
 * Etiquetas permitidas en titulares que traen datos: solo itálica y salto de línea.
 */
function tor_kses_em(): array {
	return array(
		'em' => array(),
		'br' => array(),
	);
}

/**
 * Imprime un titular con su única itálica.
 */
function tor_e_titular( string $html ): void {
	echo wp_kses( $html, tor_kses_em() );
}

/**
 * Número y nombre de cada orilla, para las etiquetas.
 */
function tor_orilla_etq( string $o ): string {
	$n = array(
		'e' => 'Orilla 01 · Estrategia',
		'c' => 'Orilla 02 · Creatividad',
		't' => 'Orilla 03 · Tecnología',
	);
	return $n[ $o ] ?? '';
}

/**
 * Servicios en el orden de navegación, con su URL. Vacío si el plugin no está activo.
 */
function tor_lista_servicios(): array {
	if ( ! function_exists( 'tor_servicios' ) ) {
		return array();
	}
	$out = array();
	foreach ( tor_servicios() as $slug => $s ) {
		$out[ $slug ] = $s + array(
			'slug' => $slug,
			'url'  => tor_servicio_url( $slug ),
		);
	}
	return $out;
}

/**
 * Logos de clientes (assets/media/clientes/<archivo>.png, tinta sobre transparente). Tomados del sitio anterior.
 */
function tor_logos(): array {
	return apply_filters(
		'tor_logos',
		array(
			array( 'cinepolis', 'Cinépolis' ),
			array( 'colsubsidio', 'Colsubsidio' ),
			array( 'colsanitas', 'Colsanitas' ),
			array( 'seeds', 'Seeds' ),
			array( 'tatem', 'Tatem' ),
			array( 'okke', 'OKKE' ),
			array( '3-cordilleras', '3 Cordilleras' ),
			array( 'nymecall', 'NymeCall' ),
			array( 'big-house-records', 'Big House Records' ),
			array( 'rezet-milano', 'RezetMilano' ),
			array( 'laura-calderon', 'Laura Calderón' ),
			array( 'par-de-manos', 'Par de manos' ),
		)
	);
}
