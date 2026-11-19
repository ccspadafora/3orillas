<?php
/**
 * Botones de contacto: videollamada, chat y WhatsApp.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
$tor_flechas = ! empty( $args['flechas'] );
$tor_f       = $tor_flechas ? tor_icono( 'flecha' ) : '';
$tor_svg     = tor_kses_svg();
?>
<button class="opcion prin" type="button" data-llamar><span class="live" data-live></span><span><b>Videollamada por Zoom</b><small>Elige el horario que te sirva</small></span><?php echo wp_kses( $tor_f, $tor_svg ); ?></button>
<button class="opcion" type="button" data-chat><span><b>Cotizar por chat</b><small>Cuéntanos tu proyecto en 3 preguntas</small></span><?php echo wp_kses( $tor_f, $tor_svg ); ?></button>
<a class="opcion" href="<?php echo esc_url( tor_wa( 'Hola, quiero hablar de un proyecto con 3 Orillas.' ) ); ?>"><span><b>WhatsApp</b><small><?php echo esc_html( tor_wa_txt() ); ?></small></span><?php echo wp_kses( $tor_f, $tor_svg ); ?></a>
