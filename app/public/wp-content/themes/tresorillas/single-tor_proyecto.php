<?php
/**
 * Caso (/trabajo/<proyecto>/). Equivale a caso() del diseño.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();

$tor_todos = tor_core_activo() ? TOR_Proyectos::datos() : array();
$tor_slug  = (string) get_post_field( 'post_name', get_queried_object_id() );
$tor_i     = 0;
$tor_p     = null;
foreach ( $tor_todos as $tor_k => $tor_x ) {
	if ( $tor_x['id'] === $tor_slug ) {
		$tor_p = $tor_x;
		$tor_i = $tor_k;
	}
}

if ( ! $tor_p ) :
	?>
	<div class="cab"><div class="wrap"><h1 class="h-xl"><?php the_title(); ?></h1></div></div>
	<?php
else :
	$tor_n   = count( $tor_todos );
	$tor_ant = $tor_todos[ ( $tor_i + $tor_n - 1 ) % $tor_n ];
	$tor_sig = $tor_todos[ ( $tor_i + 1 ) % $tor_n ];
	$tor_rel = array_slice( array_values( array_filter( $tor_todos, static fn( $x ) => $x['id'] !== $tor_p['id'] && array_intersect( $x['cats'], $tor_p['cats'] ) ) ), 0, 3 );
	$tor_dl  = $tor_p['demo'] ? add_query_arg( 'app', $tor_p['demo'], tor_url( 'demo-lab' ) ) : '';
	$tor_k   = tor_kses_tarjeta();
	?>
	<div class="cab"><div class="wrap">
		<div class="migas"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>/</span><a href="<?php echo esc_url( tor_url( 'trabajo' ) ); ?>">Trabajo</a><span>/</span><span id="cMiga"><?php echo esc_html( $tor_p['name'] ); ?></span></div>
		<div style="display:flex;justify-content:space-between;align-items:end;gap:24px;flex-wrap:wrap;">
			<div style="display:flex;flex-direction:column;gap:14px;max-width:760px;"><span class="tag" id="cTag"><span class="dot o-<?php echo esc_attr( $tor_p['o'] ); ?>"></span><?php echo esc_html( $tor_p['client'] . ' · ' . $tor_p['kind'] . ' · ' . $tor_p['year'] ); ?></span><h1 class="h-xl" id="h-caso"><?php echo esc_html( $tor_p['name'] ); ?></h1><p class="body" id="cIntro"><?php echo esc_html( $tor_p['intro'] ); ?></p></div>
			<div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;">
				<div class="cat-chips" id="cCats"><?php foreach ( $tor_p['cats'] as $tor_c ) { echo '<a href="' . esc_url( tor_cat_url( $tor_c ) ) . '">' . esc_html( tor_cat_nombre( $tor_c ) ) . '</a>'; } ?></div>
				<?php if ( $tor_dl ) : ?><a class="btn btn-tinta" id="cDemo" href="<?php echo esc_url( $tor_dl ); ?>">Probar la demo</a><?php endif; ?>
			</div>
		</div>
	</div></div>
	<section style="padding-top: 0;"><div class="wrap">
		<div class="caso-hero">
			<?php if ( ! empty( $tor_p['tipo'] ) ) : ?>
				<div class="tipo" style="position:relative;height:100%;background:<?php echo esc_attr( $tor_p['tipo'][0] ); ?>;color:<?php echo esc_attr( $tor_p['tipo'][1] ); ?>;padding:48px;"><span><?php echo esc_html( $tor_p['client'] ); ?></span><b style="font-size:clamp(64px,9vw,140px);"><?php echo esc_html( $tor_p['name'] ); ?></b><span><?php echo esc_html( $tor_p['estado'] ?: 'Capturas del proyecto en preparación' ); ?></span></div>
			<?php else : ?>
				<img id="cImg" src="<?php echo esc_url( $tor_p['img'] ); ?>" alt="<?php echo esc_attr( $tor_p['name'] . ': ' . $tor_p['kind'] ); ?>" fetchpriority="high">
			<?php endif; ?>
		</div>
		<div class="datos"<?php echo $tor_p['result'] ? '' : ' style="grid-template-columns:repeat(2,minmax(0,1fr));"'; ?>>
			<div><span class="etq">El reto</span><p id="cProb"><?php echo esc_html( $tor_p['problem'] ); ?></p></div>
			<div><span class="etq">Lo que construimos</span><p id="cSol"><?php echo esc_html( $tor_p['solution'] ); ?></p></div>
			<?php if ( $tor_p['result'] ) : ?><div><span class="etq">Resultado</span><h3 class="h-m" id="cRes"><?php echo esc_html( $tor_p['result'] ); ?></h3></div><?php endif; ?>
		</div>
		<?php if ( $tor_p['gal'] ) : ?>
			<div class="galeria-caso" id="cGaleria">
				<?php
				$tor_ng = count( $tor_p['gal'] );
				foreach ( $tor_p['gal'] as $tor_gi => $tor_g ) {
					echo '<figure class="' . esc_attr( ( $tor_ng % 2 && 0 === $tor_gi ) ? 'full' : '' ) . '"><img src="' . esc_url( $tor_g ) . '" alt="' . esc_attr( $tor_p['name'] . ', detalle ' . ( $tor_gi + 1 ) ) . '" loading="lazy"></figure>';
				}
				?>
			</div>
		<?php endif; ?>
		<?php if ( $tor_p['video'] ) : ?>
			<div class="caso-video" id="cVideoWrap"><div style="display:flex;flex-direction:column;gap:14px;"><p class="etq">En funcionamiento</p><h2 class="h-m">Así se ve en el <em>día a día.</em></h2><p class="tenue" id="cStack"><?php echo esc_html( $tor_p['stack'] ); ?></p></div><div id="cVideo">
				<?php if ( $tor_p['mobile'] ) : ?>
					<div class="escena-cel"><div class="celular"><video src="<?php echo esc_url( $tor_p['video'] ); ?>" autoplay muted loop playsinline aria-label="<?php echo esc_attr( 'Video de ' . $tor_p['name'] ); ?>"></video></div></div>
				<?php else : ?>
					<div class="navegador"><div class="barra"><i></i><i></i><i></i><span>demo.3orillas.com/<?php echo esc_html( $tor_p['demo'] ?: $tor_p['id'] ); ?></span></div><video src="<?php echo esc_url( $tor_p['video'] ); ?>" autoplay muted loop playsinline aria-label="<?php echo esc_attr( 'Video de ' . $tor_p['name'] ); ?>"></video></div>
				<?php endif; ?>
			</div></div>
		<?php endif; ?>
		<?php if ( $tor_rel ) : ?>
			<div style="margin-top:96px;"><p class="etq" style="margin-bottom:24px;">Proyectos relacionados</p><div class="obras" id="cRel"><?php foreach ( $tor_rel as $tor_x ) { echo wp_kses( tor_obra( $tor_x ), $tor_k ); } ?></div></div>
		<?php endif; ?>
		<div class="caso-nav"><a id="cPrev" href="<?php echo esc_url( $tor_ant['url'] ); ?>"><span class="tenue small">Anterior</span><b><?php echo esc_html( $tor_ant['name'] ); ?></b></a><a id="cNext" href="<?php echo esc_url( $tor_sig['url'] ); ?>" style="text-align:right;"><span class="tenue small">Siguiente</span><b><?php echo esc_html( $tor_sig['name'] ); ?></b></a></div>
	</div></section>
	<?php
endif;
get_footer();
