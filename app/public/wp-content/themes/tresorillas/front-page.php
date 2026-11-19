<?php
/**
 * Inicio.
 *
 * @package TresOrillas
 */

defined( 'ABSPATH' ) || exit;
get_header();

$tor_seeds = tor_proyecto( 'seeds' );
$tor_crm   = tor_proyecto( 'crm' );
?>
<div class="stage" id="stage">
	<div class="wrap stage-grid">
		<div class="stage-copy">
			<p class="etq">Estudio digital · Bogotá</p>
			<h1 class="h-xl" id="h-inicio">Estrategia, <em>creatividad</em><br>y tecnología.</h1>
			<p class="body" id="stageLead">Creamos marcas, plataformas y campañas que impulsan negocios. Cuéntanos tu idea y te mostramos cómo la haríamos.</p>
			<form class="idea cristal" id="ideaForm">
				<div class="idea-row">
					<label for="idea" class="sr">¿Qué quieres construir?</label>
					<input id="idea" placeholder="¿Qué quieres construir?" autocomplete="off" maxlength="200">
					<button class="btn btn-tinta" type="submit">Ver propuesta</button>
				</div>
				<div class="ideas-ej" id="ideasEj">
					<button type="button" class="chip">Un ERP para mi clínica</button>
					<button type="button" class="chip">Un chatbot de WhatsApp</button>
					<button type="button" class="chip">Una tienda online</button>
					<button type="button" class="chip">Una app de domicilios</button>
				</div>
			</form>
			<button class="reset" id="reset" type="button">Probar con otra idea</button>
		</div>

		<div class="stage-scene" id="scene" aria-live="polite">
			<div class="halo" id="halo"></div>
			<svg class="orbita-svg" viewBox="0 0 200 200" aria-hidden="true"><circle id="orb1" cx="100" cy="100" r="99"/><circle id="orb2" cx="100" cy="100" r="70" style="stroke-dasharray:2 4;"/><circle class="viajero" id="via1" r="2.2" cx="100" cy="1"/><circle class="viajero" id="via2" r="1.6" cx="100" cy="30" style="fill:var(--cobalto)"/></svg>
			<div class="retrato"><img src="<?php echo esc_url( tor_m( 'retrato-3.jpg' ) ); ?>" alt="Retrato en blanco y negro de una mujer mirando hacia arriba" fetchpriority="high"></div>

			<a href="<?php echo esc_url( $tor_seeds ? $tor_seeds['url'] : tor_url( 'trabajo' ) ); ?>" class="card cristal c-ini" style="left: 2%; top: 14%; width: 220px; --d: 14px;">
				<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;"><span class="t">Rendimiento</span><span style="font-size:12px;color:#5f9a57;font-weight:600;"><span class="roll" data-to="+20×"></span></span></div>
				<div class="spark"><i style="height:22%"></i><i style="height:30%"></i><i style="height:28%"></i><i style="height:44%"></i><i style="height:52%"></i><i style="height:66%"></i><i style="height:78%"></i><i style="height:100%"></i></div>
				<div class="s" style="margin-top:10px;">Seeds: de 2M a 40M al mes</div>
			</a>
			<div class="card cristal c-ini c-canal" style="left: 4%; top: 58%; width: 210px; --d: 22px;">
				<span class="t" style="display:block;margin-bottom:6px;">Campañas</span>
				<div class="canal"><b>Meta Ads</b><span>Activa</span></div>
				<div class="canal"><b>Google Ads</b><span>Activa</span></div>
				<div class="canal"><b>TikTok Ads</b><span>Activa</span></div>
			</div>
			<a href="<?php echo esc_url( add_query_arg( 'app', 'crm', tor_url( 'demo-lab' ) ) ); ?>" id="cicloLink" class="card cristal c-ini" style="left: 66%; top: 6%; width: 250px; --d: 18px;">
				<div class="mini-shot" id="ciclo"><img src="<?php echo esc_url( $tor_crm ? $tor_crm['shot'] : tor_m( 's-crm-0.jpg' ) ); ?>" alt="Tablero del CRM de campo"></div>
				<div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;gap:8px;"><span class="t" id="cicloNom">CRM de Campo</span><span class="s">Pruébalo en vivo</span></div><div class="ciclo-bar"><i id="cicloBar"></i></div>
			</a>
			<a href="<?php echo esc_url( tor_url( 'servicios' ) ); ?>" class="card cristal c-ini c-code" style="left: 74%; top: 52%; width: 150px; --d: 26px; text-align:center;">
				<span class="icono" style="margin: 0 auto 8px;"><?php tor_e_icono( 'codigo' ); ?></span>
				<span class="t" style="display:block;">Software a la medida</span>
			</a>
			<div class="sello c-ini" aria-hidden="true">
				<svg viewBox="0 0 120 120" width="112" height="112"><defs><path id="circ" d="M60,60 m-44,0 a44,44 0 1,1 88,0 a44,44 0 1,1 -88,0"/></defs><circle cx="60" cy="60" r="58" fill="rgba(255,255,255,.6)" stroke="rgba(14,14,16,.15)"/><text font-family="Inter Tight, sans-serif" font-size="9.5" letter-spacing="2.2" fill="#0E0E10"><textPath href="#circ">3 ORILLAS STUDIO · BOGOTÁ · COLOMBIA ·</textPath></text></svg>
				<svg viewBox="0 0 24 24" width="22" height="22" style="position:absolute;left:45px;top:45px;animation:none;" aria-hidden="true"><path d="M12 2l1.8 8.2L22 12l-8.2 1.8L12 22l-1.8-8.2L2 12l8.2-1.8z" fill="#0E0E10"/></svg>
			</div>

			<div class="card cristal c-res hide" id="rPlan" style="left: 5%; top: 8%; width: 300px; --d: 12px;">
				<span class="tag" id="rLine"></span>
				<h3 id="rTitle" style="font-size: 24px; line-height: 1.1; margin: 8px 0 12px;"></h3>
				<ol id="rSteps" style="margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:8px;font-size:13.5px;color:var(--cuerpo);"></ol>
			</div>
			<a class="card cristal c-res hide" id="rRef" href="<?php echo esc_url( tor_url( 'trabajo' ) ); ?>" style="left: 64%; top: 4%; width: 280px; --d: 18px;">
				<div class="mini-shot"><img id="rRefImg" src="<?php echo esc_url( $tor_seeds ? $tor_seeds['shot'] : tor_m( 's-seeds-0.jpg' ) ); ?>" alt=""></div>
				<div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;gap:8px;"><span class="t" id="rRefName"></span><span class="s">Ver caso</span></div>
			</a>
			<div class="card cristal c-res hide" id="rTime" style="left: 74%; top: 46%; width: 190px; --d: 24px;">
				<span class="s">Tiempo estimado</span>
				<div class="k-big" id="rTimeV" style="margin-top:6px;font-size:26px;"></div>
			</div>
			<form class="card cristal c-res hide" id="rCap" data-origen="propuesta" novalidate style="left: 2%; top: 62%; width: 300px; --d: 16px; display:flex; flex-direction:column; gap:10px;">
				<label for="capTel" class="t">Recibe la propuesta por WhatsApp</label>
				<div class="cap-fila" style="display:flex;gap:6px;"><input class="input" id="capTel" name="telefono" type="tel" inputmode="tel" placeholder="+57 300 000 0000" autocomplete="tel" maxlength="40" style="height:42px;"><button class="btn btn-tinta btn-sm" type="submit" style="min-height:42px;">Enviar</button></div>
				<?php echo wp_kses( tor_acepto( 'capOk' ), array( 'label' => array( 'class' => true, 'for' => true ), 'input' => array( 'type' => true, 'id' => true, 'name' => true, 'value' => true, 'required' => true ), 'span' => array(), 'a' => array( 'href' => true, 'target' => true, 'rel' => true ) ) ); ?>
				<?php echo wp_kses( tor_trampa(), array( 'span' => array( 'class' => true, 'aria-hidden' => true ), 'label' => array(), 'input' => array( 'type' => true, 'name' => true, 'tabindex' => true, 'autocomplete' => true ) ) ); ?>
				<span class="s" id="capMsg">Un asesor te escribe por WhatsApp en horario hábil.</span>
			</form>
		</div>
	</div>
</div>
<div class="franja"><div class="wrap"><span class="etq">Estrategia</span><span class="sep"></span><span class="etq">Marketing</span><span class="sep"></span><span class="etq">Tecnología</span><span class="sep"></span><span class="etq">Desarrollo digital</span><span class="der"><span class="roll" data-to="+1.000"></span> clientes acompañados desde Bogotá</span></div></div>

<section>
	<div class="wrap">
		<div style="display:flex;justify-content:space-between;align-items:end;gap:32px;flex-wrap:wrap;">
			<h2 class="h-l">Tres orillas,<br><em>un solo destino.</em></h2>
			<p class="body">Cada orilla es una disciplina. El resultado es lo que las une: un negocio que vende más y opera mejor.</p>
		</div>
		<div class="orillas">
			<a class="orilla" href="<?php echo esc_url( tor_url( 'servicios' ) ); ?>"><span class="glow" style="background: var(--champan);"></span><span class="num">Orilla 01</span><h3>Estrategia</h3><p class="body">Analizamos, estructuramos y trazamos el camino para que tu marca crezca.</p><ul><li>Diagnóstico</li><li>Embudo comercial</li><li>Marketing digital</li></ul></a>
			<a class="orilla oscura" href="<?php echo esc_url( tor_url( 'servicios' ) ); ?>"><span class="glow" style="background: #6d7a63;"></span><span class="num" style="color:#9E9A92;">Orilla 02</span><h3><em>Creatividad</em></h3><p class="body">Ideas que conectan con propósito, rendimiento y visión.</p><ul><li>Marca</li><li>Contenido</li><li>Webs y e-commerce</li></ul></a>
			<a class="orilla" href="<?php echo esc_url( tor_url( 'demo-lab' ) ); ?>"><span class="glow" style="background: var(--hielo);"></span><span class="num">Orilla 03</span><h3>Tecnología</h3><p class="body">Soluciones a la medida, de la web a la inteligencia artificial.</p><ul><li>Software y ERP</li><li>Apps móviles</li><li>IA y automatización</li></ul></a>
		</div>
	</div>
</section>

<section style="padding-top: 0;" id="reelSec">
	<div class="wrap">
		<div class="galeria-head">
			<div style="display:flex;flex-direction:column;gap:16px;"><p class="etq">Trabajo seleccionado</p><h2 class="h-l">Trabajo que <em>habla</em> por nosotros.</h2></div>
			<div class="galeria-ctrl"><button class="redondo" id="rPrev" aria-label="Proyecto anterior"><?php tor_e_icono( 'izq' ); ?></button><button class="redondo" id="rPausa" aria-label="Pausar"><?php tor_e_icono( 'pausa' ); ?></button><button class="redondo" id="rNext" aria-label="Proyecto siguiente"><?php tor_e_icono( 'der' ); ?></button></div>
		</div>
		<div class="reel" id="reel">
			<div class="reel-barras" id="reelBarras" role="tablist" aria-label="Proyectos"></div>
			<a class="reel-escena" id="reelEscena" href="<?php echo esc_url( tor_url( 'trabajo' ) ); ?>" data-cursor="Ver caso"></a>
			<div class="reel-pie"><span class="tag" id="reelTag"></span><a class="btn btn-borde btn-sm" href="<?php echo esc_url( tor_url( 'trabajo' ) ); ?>">Ver todos los proyectos</a></div>
		</div>
	</div>
</section>

<section style="padding-top: 0;">
	<div class="wrap">
		<div class="galeria-head">
			<div style="display:flex;flex-direction:column;gap:16px;"><p class="etq">Antes y después</p><h2 class="h-l">De esto, <em>a esto.</em></h2></div>
			<div class="ab-tabs" role="tablist" aria-label="Comparaciones"><button class="chip" aria-pressed="true" data-ab="0">Ventas de Seeds</button><button class="chip" aria-pressed="false" data-ab="1">Fuerza de ventas en calle</button></div>
		</div>
		<div class="ab" id="ab">
			<img id="abDespues" src="<?php echo esc_url( tor_m( 'p/seeds-2.jpg' ) ); ?>" alt="Después: inventario en el ERP de Seeds" loading="lazy">
			<div class="antes"><img id="abAntes" src="<?php echo esc_url( tor_m( 'antes-excel.jpg' ) ); ?>" alt="Antes: hoja de cálculo de ventas" loading="lazy"></div>
			<span class="linea-ab"></span>
			<span class="et a cristal">Antes</span><span class="et d cristal">Con 3 Orillas</span>
			<input type="range" min="0" max="100" value="50" id="abRange" aria-label="Deslizar para comparar antes y después">
			<span class="tirador cristal"><?php tor_e_icono( 'tirador' ); ?></span>
		</div>
	</div>
</section>

<section style="padding-top: 0;">
	<div class="wrap" style="display:grid;grid-template-columns:minmax(0,7fr) minmax(0,5fr);gap:48px;align-items:end;">
		<p class="scrub" id="scrub">No vendemos promesas. Diseñamos sistemas que convierten ideas <em>en resultados que puedes medir.</em></p>
		<div style="display:flex;flex-direction:column;gap:18px;"><p class="body">Diagnóstico antes que táctica. Demos funcionando cada dos semanas. Un tablero de resultados que ves con nosotros.</p><a href="<?php echo esc_url( tor_url( 'metodo' ) ); ?>" class="btn btn-tinta" style="align-self:flex-start;">Conocer el método</a></div>
	</div>
</section>

<section class="tinta">
	<svg class="onda dibujar" width="900" height="600" viewBox="0 0 900 600" style="right:-200px;top:-80px;" aria-hidden="true"><path d="M40 420 C 160 120, 360 100, 420 300 S 640 560, 860 220" fill="none" stroke="#C9B38A" stroke-width="1.2"/><path d="M40 470 C 160 170, 360 150, 420 350 S 640 610, 860 270" fill="none" stroke="#C9B38A" stroke-width="1.2" opacity=".5"/></svg>
	<div class="wrap lab-band">
		<div style="display:flex;flex-direction:column;gap:22px;position:relative;">
			<p class="etq">Demo Lab</p>
			<h2 class="h-l">No te lo contamos. <em>Entra y navégalo.</em></h2>
			<p class="body">Plataformas reales que construimos, en producción. Entras con un usuario de prueba y las recorres a tu ritmo, antes de hablar con nosotros.</p>
			<div style="display:flex;gap:10px;flex-wrap:wrap;"><a href="<?php echo esc_url( tor_url( 'demo-lab' ) ); ?>" class="btn btn-marfil">Abrir el Demo Lab</a><button class="btn btn-borde" type="button" data-llamar>Recorrido guiado por Zoom</button></div>
		</div>
		<div class="laptop">
			<div class="pantalla"><video src="<?php echo esc_url( $tor_seeds && $tor_seeds['video'] ? $tor_seeds['video'] : tor_m( 'vid-seeds.mp4' ) ); ?>" autoplay muted loop playsinline aria-label="Recorrido por el ERP de Seeds"></video></div>
			<div class="base"></div>
			<div class="credencial cristal"><span class="t" style="font:600 13.5px/1.2 var(--sans);">Acceso de prueba</span><?php if ( $tor_seeds && $tor_seeds['demoUser'] ) : ?><span class="small">Usuario <code><?php echo esc_html( $tor_seeds['demoUser'] ); ?></code></span><?php if ( $tor_seeds['demoPass'] ) : ?><span class="small">Clave <code><?php echo esc_html( $tor_seeds['demoPass'] ); ?></code></span><?php endif; ?><?php else : ?><span class="small">Usuario de prueba incluido en el Demo Lab.</span><?php endif; ?><a href="<?php echo esc_url( add_query_arg( 'app', 'seeds', tor_url( 'demo-lab' ) ) . '#laboratorio' ); ?>" class="btn btn-tinta btn-sm" style="align-self:flex-start;">Entrar a Seeds</a></div>
		</div>
	</div>
</section>

<section>
	<div class="wrap">
		<div style="display:flex;justify-content:space-between;align-items:end;gap:32px;flex-wrap:wrap;"><h2 class="h-l">Primero entender.<br><em>Después proponer.</em></h2><a href="<?php echo esc_url( tor_url( 'metodo' ) ); ?>" class="btn btn-borde btn-sm">Conocer el método</a></div>
		<div class="fases" id="fasesInicio"><?php foreach ( tor_fases() as $tor_i => $tor_f ) { echo wp_kses_post( tor_fase( $tor_f, $tor_i ) ); } ?></div>
	</div>
</section>

<section style="padding-top: 0;">
	<div class="wrap">
		<?php get_template_part( 'parts/logos' ); ?>
	</div>
</section>

<section style="padding-top: 0;">
	<div class="wrap">
		<?php get_template_part( 'parts/cartel-contacto' ); ?>
	</div>
</section>
<?php
get_footer();
