<?php
/**
 * Estudio (nosotros). Incluye el contenido de la antigua página «Conócenos».
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();

$tor_razones = array(
	array( 'Estrategia y ejecución, en un solo equipo', 'No entregamos presentaciones que quedan en un cajón. Diseñamos el sistema y lo implementamos hasta que funciona.' ),
	array( 'Experiencia desde la operación', 'Más de diez años construyendo marcas, tiendas y estrategias dentro de negocios reales.' ),
	array( 'Un sistema completo, no acciones sueltas', 'Marca, web, tecnología, marketing y operación bajo un mismo método y un solo objetivo.' ),
	array( 'Primero comprender, después proponer', 'Ninguna táctica sale sin diagnóstico. Intervenimos solo donde podemos generar un impacto verificable.' ),
);
?>
<div class="tor-serv" style="--o: var(--champan); --o-txt: var(--bronce); --o-suave: #EFE6D3;">
	<?php
	get_template_part(
		'parts/escena',
		null,
		array(
			'migas'     => array( array( 'Inicio', home_url( '/' ) ), array( 'Estudio' ) ),
			'o'         => 'e',
			'tag'       => 'Estudio digital · Bogotá',
			'h1'        => 'Somos <em>3 Orillas.</em>',
			'intro'     => 'Un estudio digital que une estrategia, marketing, tecnología y desarrollo para que las marcas crezcan con propósito. Una firma joven, respaldada por más de una década construyendo negocios desde la operación.',
			'acciones'  => array(
				array( 'Hablemos', tor_url( 'contacto' ), 'marfil' ),
				array( 'Conocer el método', tor_url( 'metodo' ), 'borde' ),
			),
			'retrato'   => 'retrato-1.jpg',
			'flotantes' => function_exists( 'tor_cifras' ) ? tor_cifras() : array(),
			'cinta'     => array( 'Estrategia', 'Creatividad', 'Tecnología', 'Marca', 'Web y e-commerce', 'Software', 'Marketing', 'IA y automatización' ),
		)
	);
	?>

	<div class="tor-rio" aria-hidden="true"><div class="tor-rio-pista" data-rio>Primero entender · <em>Después construir</em> · Estrategia · Creatividad · Tecnología · </div></div>

	<section style="padding-top: 0;"><div class="wrap tor-historia">
		<figure class="tor-retrato"><img src="<?php echo esc_url( tor_m( 'estudio/fundador.webp' ) ); ?>" alt="Fundador de 3 Orillas" loading="lazy"><figcaption class="tor-pie cristal"><span class="small tenue">Fundador · 3 Orillas</span><b>Primero entender. Después construir.</b></figcaption></figure>
		<div style="display:flex;flex-direction:column;gap:20px;">
			<p class="etq">Nuestra historia</p>
			<h2 class="h-l">Más de una década construyendo <em>negocios reales.</em></h2>
			<p class="body">3 Orillas es una firma reciente; la experiencia que la respalda no lo es. Nacimos de una convicción simple: la mayoría de las empresas no tiene un problema de producto, sino de arquitectura comercial.</p>
			<p class="body">Por eso no ejecutamos tareas sueltas. Diseñamos el sistema completo, de la marca a la operación, y lo implementamos hasta que el crecimiento deja de depender de la suerte.</p>
			<a class="btn btn-borde" href="<?php echo esc_url( tor_url( 'metodo' ) ); ?>" style="align-self:flex-start;">Conocer el método</a>
		</div>
	</div></section>

	<section class="tinta grano tor-sec-oscura"><div class="wrap" style="position:relative;">
		<div class="tor-cabeza"><div style="display:flex;flex-direction:column;gap:14px;"><p class="etq">Por qué 3 Orillas</p><h2 class="h-l">Lo que nos hace <em>distintos.</em></h2></div><p class="body">No prometemos lo que no podemos demostrar.</p></div>
		<div class="tor-cartas" style="--cols: 4;">
			<?php foreach ( $tor_razones as $tor_i => $tor_r ) : ?>
				<article class="orilla tor-carta" data-luz><span class="glow" style="background: <?php echo esc_attr( array( 'var(--champan)', 'var(--salvia)', 'var(--hielo)', 'var(--champan)' )[ $tor_i ] ); ?>;"></span><span class="tor-carta-n"><?php echo esc_html( sprintf( '%02d', $tor_i + 1 ) ); ?></span><h3><?php echo esc_html( $tor_r[0] ); ?></h3><p class="body"><?php echo esc_html( $tor_r[1] ); ?></p></article>
			<?php endforeach; ?>
		</div>
	</div></section>

	<section><div class="wrap">
		<div class="tor-cabeza"><div style="display:flex;flex-direction:column;gap:14px;"><p class="etq">Cómo trabajamos</p><h2 class="h-l">Cerca del negocio, <em>dentro del proceso.</em></h2></div><p class="body">Trabajamos en la misma mesa que tu equipo: mapeamos, construimos y medimos juntos.</p></div>
		<div class="tor-galeria">
			<figure class="g1" data-luz><img src="<?php echo esc_url( tor_m( 'estudio/equipo-1.webp' ) ); ?>" alt="Equipo de 3 Orillas trabajando alrededor de una mesa" loading="lazy"><figcaption class="tor-pie cristal"><b>Estrategia en equipo</b></figcaption></figure>
			<figure class="g2" data-luz><img src="<?php echo esc_url( tor_m( 'estudio/equipo-3.webp' ) ); ?>" alt="Oficina abierta de 3 Orillas" loading="lazy"><figcaption class="tor-pie cristal"><b>Ejecución y detalle</b></figcaption></figure>
			<figure class="g3" data-luz><img src="<?php echo esc_url( tor_m( 'estudio/equipo-2.webp' ) ); ?>" alt="Equipo de 3 Orillas en sus puestos de trabajo" loading="lazy"><figcaption class="tor-pie cristal"><b>Resultados medibles</b></figcaption></figure>
		</div>
	</div></section>

	<section class="tor-oficina-sec" style="padding-top:0;"><div class="tor-oficina" data-oficina>
		<img src="<?php echo esc_url( tor_m( 'estudio/oficina-atardecer.webp' ) ); ?>" alt="Oficina de 3 Orillas en Bogotá al atardecer" loading="lazy">
		<div class="wrap tor-oficina-txt"><p class="etq">Nuestra casa · Bogotá</p><h2 class="h-l">Donde las ideas <em>se vuelven proyectos.</em></h2><p class="body">Estrategia, diseño y desarrollo en el mismo espacio, con la ciudad de fondo.</p><a class="btn btn-marfil" href="<?php echo esc_url( tor_url( 'contacto' ) ); ?>">Hablemos <?php tor_e_icono( 'flecha' ); ?></a></div>
	</div></section>

	<section style="padding-top: 0;"><div class="wrap">
		<?php get_template_part( 'parts/logos', null, array( 'titulo' => 'Clientes' ) ); ?>
	</div></section>

	<section style="padding-top: 0;"><div class="wrap">
		<?php get_template_part( 'parts/cartel-contacto' ); ?>
	</div></section>
</div>
<?php
get_footer();
