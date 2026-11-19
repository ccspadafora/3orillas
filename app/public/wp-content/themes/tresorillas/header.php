<?php
/**
 * Cabecera.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
$tor_v    = tor_vista();
$tor_menu = array(
	'trabajo'   => 'Trabajo',
	'servicios' => 'Servicios',
	'demo-lab'  => 'Demo Lab',
	'metodo'    => 'Método',
	'nosotros'  => 'Estudio',
);
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="sr" href="#contenido">Saltar al contenido</a>
<header class="top" id="top">
	<div class="wrap">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="3 Orillas, inicio"><img src="<?php echo esc_url( tor_m( 'logo-tinta.png' ) ); ?>" alt="3 Orillas" width="160" height="30"></a>
		<button class="burger" id="burger" aria-label="Abrir menú" aria-expanded="false" aria-controls="menu"><?php tor_e_icono( 'burger' ); ?></button>
		<div style="position:relative;margin:0 auto;" id="menuWrap"><nav class="menu" id="menu" aria-label="Principal" style="margin:0;">
			<?php
			foreach ( $tor_menu as $tor_k => $tor_l ) :
				$tor_href = tor_url( 'nosotros' === $tor_k ? 'estudio' : $tor_k );
				$tor_act  = $tor_v === $tor_k || ( 'caso' === $tor_v && 'trabajo' === $tor_k );
				?>
				<a href="<?php echo esc_url( $tor_href ); ?>" data-nav="<?php echo esc_attr( $tor_k ); ?>"<?php echo $tor_act ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $tor_l ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="mega cristal" id="mega" aria-hidden="true"></div></div>
		<div class="top-cta"><a href="<?php echo esc_url( tor_url( 'contacto' ) ); ?>" class="btn btn-tinta btn-sm">Hablemos <?php tor_e_icono( 'flecha' ); ?></a></div>
	</div>
</header>

<main id="contenido">
<div class="view on" data-view="<?php echo esc_attr( $tor_v ); ?>">
