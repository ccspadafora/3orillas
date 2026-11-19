<?php
/**
 * Plantilla por defecto.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	?>
	<div class="cab"><div class="wrap">
		<div class="migas"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Inicio</a><span>/</span><span><?php the_title(); ?></span></div>
		<h1 class="h-l"><?php the_title(); ?></h1>
	</div></div>
	<section style="padding-top: 8px;"><div class="wrap"><div class="texto"><?php the_content(); ?></div></div></section>
	<?php
endwhile;
get_footer();
