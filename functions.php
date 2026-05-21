<?php

/* =============================================
   CASE STUDY — PER-POST BOJE KATEGORIJA
   Svaki loop item ima .post-{ID} klasu (Elementor generiše).
   PHP čita ACF cs_category_color i ispisuje inline CSS.
   Fallback: mapa naziv_kategorije → boja.
   ============================================= */

add_action( 'wp_head', 'combo_cs_loop_colors', 20 );
function combo_cs_loop_colors() {
	// Mapa kategorija → boja (ako ACF polje nije popunjeno)
	$cat_color_map = [
		'azure'      => '#c4b5fd',
		'cloud'      => '#93c5fd',
		'mreže'      => '#67e8f9',
		'mreze'      => '#67e8f9',
		'microsoft 365' => '#93c5fd',
		'm365'       => '#93c5fd',
		'security'   => '#fca5a5',
		'intune'     => '#86efac',
		'intune mdm' => '#86efac',
		'it setup'   => '#fcd34d',
		'default'    => '#60A5FA',
	];

	$posts = get_posts( [
		'post_type'   => 'case-study',
		'numberposts' => -1,
		'fields'      => 'all',
	] );

	if ( empty( $posts ) ) return;

	$css = '<style id="combo-cs-colors">' . "\n";

	foreach ( $posts as $p ) {
		$post_id = $p->ID;

		// Pokušaj ACF polje
		$color = get_post_meta( $post_id, 'cs_category_color', true );

		// Fallback: mapa
		if ( ! $color ) {
			$cat_raw = strtolower( trim( get_post_meta( $post_id, 'cs_category', true ) ) );
			$color   = $cat_color_map[ $cat_raw ] ?? $cat_color_map['default'];
		}

		// Hex → RGB za rgba pozadinu
		$hex = ltrim( $color, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		// Badge pill boja
		$css .= ".post-{$post_id} .elementor-element-8b7426a {"
		      . "color:{$color}!important;"
		      . "border-color:rgba({$r},{$g},{$b},0.45)!important;"
		      . "background:rgba({$r},{$g},{$b},0.10)!important;"
		      . "}\n";

		// KPI brojevi boja
		$css .= ".post-{$post_id} .elementor-element-f67cf47,"
		      . ".post-{$post_id} .elementor-element-7b5acd9,"
		      . ".post-{$post_id} .elementor-element-d4de6f9 {"
		      . "color:{$color}!important;"
		      . "}\n";
	}

	$css .= '</style>' . "\n";
	echo $css;
}

/* =============================================
   TESTIMONIAL — BOJA AVATARA PO POSTU
   Čita testi_avatar_color ACF polje i override-uje
   hardkodovanu #1E40AF boju u loop templatu (ID 3afb846).
   ============================================= */

add_action( 'wp_head', 'combo_testi_avatar_colors', 20 );
function combo_testi_avatar_colors() {
	$posts = get_posts( [
		'post_type'   => 'testimonial',
		'numberposts' => -1,
		'fields'      => 'all',
	] );
	if ( empty( $posts ) ) return;

	$css = '<style id="combo-testi-colors">' . "\n";
	foreach ( $posts as $p ) {
		$color = get_post_meta( $p->ID, 'testi_avatar_color', true );
		if ( ! $color ) $color = '#1E40AF'; // fallback
		$css .= ".post-{$p->ID} .elementor-element-3afb846 .elementor-widget-container{"
		      . "background:{$color}!important;"
		      . "}\n";
	}
	$css .= '</style>' . "\n";
	echo $css;
}

/* =============================================
   ACF FIELD GROUPS — CASE STUDY & TESTIMONIAL
   Klijent popunjava formu u adminu, nema koda.
   ============================================= */

add_action( 'acf/init', 'combo_register_acf_fields' );
function combo_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

	// ── CASE STUDY ──────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_combo_case_study',
		'title'    => 'Case Study podaci',
		'fields'   => [
			[
				'key'           => 'field_cs_category',
				'label'         => 'Kategorija',
				'name'          => 'cs_category',
				'type'          => 'text',
				'instructions'  => 'Npr: Azure, Cloud, Mreže, Security, Intune, IT Setup',
				'required'      => 1,
				'placeholder'   => 'Azure',
			],
			[
				'key'           => 'field_cs_category_color',
				'label'         => 'Boja kategorije',
				'name'          => 'cs_category_color',
				'type'          => 'color_picker',
				'instructions'  => 'Boja badge-a i KPI brojeva. Ostavi prazno za automatsku boju prema kategoriji.',
				'required'      => 0,
			],
			[
				'key'           => 'field_cs_client',
				'label'         => 'Klijent (firma)',
				'name'          => 'cs_client',
				'type'          => 'text',
				'required'      => 1,
				'placeholder'   => 'EyeSee Research',
			],
			[
				'key'           => 'field_cs_kpi1_value',
				'label'         => 'KPI 1 — vrednost',
				'name'          => 'cs_kpi1_value',
				'type'          => 'text',
				'required'      => 1,
				'placeholder'   => '99.9%',
			],
			[
				'key'           => 'field_cs_kpi1_label',
				'label'         => 'KPI 1 — opis',
				'name'          => 'cs_kpi1_label',
				'type'          => 'text',
				'required'      => 1,
				'placeholder'   => 'uptime',
			],
			[
				'key'           => 'field_cs_kpi2_value',
				'label'         => 'KPI 2 — vrednost',
				'name'          => 'cs_kpi2_value',
				'type'          => 'text',
				'placeholder'   => '3×',
			],
			[
				'key'           => 'field_cs_kpi2_label',
				'label'         => 'KPI 2 — opis',
				'name'          => 'cs_kpi2_label',
				'type'          => 'text',
				'placeholder'   => 'brži cloud',
			],
			[
				'key'           => 'field_cs_kpi3_value',
				'label'         => 'KPI 3 — vrednost',
				'name'          => 'cs_kpi3_value',
				'type'          => 'text',
				'placeholder'   => '40%',
			],
			[
				'key'           => 'field_cs_kpi3_label',
				'label'         => 'KPI 3 — opis',
				'name'          => 'cs_kpi3_label',
				'type'          => 'text',
				'placeholder'   => 'ušteda troškova',
			],
		],
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'case-study' ] ] ],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
	] );

	// ── TESTIMONIAL ─────────────────────────────
	acf_add_local_field_group( [
		'key'      => 'group_combo_testimonial',
		'title'    => 'Testimonial podaci',
		'fields'   => [
			[
				'key'          => 'field_testi_quote',
				'label'        => 'Citat',
				'name'         => 'testi_quote',
				'type'         => 'textarea',
				'required'     => 1,
				'rows'         => 3,
				'instructions' => 'Što kraće i konkretnije — 1-2 rečenice.',
			],
			[
				'key'          => 'field_testi_author',
				'label'        => 'Ime i prezime',
				'name'         => 'testi_author',
				'type'         => 'text',
				'required'     => 1,
				'placeholder'  => 'Novak Marinković',
			],
			[
				'key'         => 'field_testi_role',
				'label'       => 'Pozicija',
				'name'        => 'testi_role',
				'type'        => 'text',
				'placeholder' => 'Innovation Director',
			],
			[
				'key'         => 'field_testi_company',
				'label'       => 'Firma',
				'name'        => 'testi_company',
				'type'        => 'text',
				'placeholder' => 'EyeSee Research',
			],
			[
				'key'          => 'field_testi_stars',
				'label'        => 'Ocena (1–5)',
				'name'         => 'testi_stars',
				'type'         => 'select',
				'choices'      => [ '5' => '★★★★★', '4' => '★★★★', '3' => '★★★' ],
				'default_value'=> '5',
				'required'     => 1,
			],
			[
				'key'          => 'field_testi_avatar_initials',
				'label'        => 'Avatar — inicijali',
				'name'         => 'testi_avatar_initials',
				'type'         => 'text',
				'instructions' => 'Dva slova, npr: NM',
				'placeholder'  => 'NM',
			],
			[
				'key'          => 'field_testi_avatar_color',
				'label'        => 'Avatar — boja pozadine',
				'name'         => 'testi_avatar_color',
				'type'         => 'color_picker',
			],
		],
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'testimonial' ] ] ],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
	] );
}

/* =============================================
   CUSTOM HEADER — injektuje se odmah posle <body>
   ============================================= */

add_action( 'wp_body_open', 'combo_render_header', 5 );
function combo_render_header() {
	?>
	<header id="combo-header" role="banner">
	  <div class="combo-nav-inner">
	    <a class="combo-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
	      <div class="combo-logo-box">
	        <svg viewBox="0 0 20 20" fill="none" width="20" height="20">
	          <rect x="2" y="2" width="7" height="7" rx="2" fill="white"/>
	          <rect x="11" y="2" width="7" height="7" rx="2" fill="rgba(255,255,255,0.45)"/>
	          <rect x="2" y="11" width="7" height="7" rx="2" fill="rgba(255,255,255,0.45)"/>
	          <rect x="11" y="11" width="7" height="7" rx="2" fill="white"/>
	        </svg>
	      </div>
	      <span class="combo-logo-text">Combo<span>.</span></span>
	    </a>
	    <nav class="combo-nav-menu" aria-label="Glavna navigacija">
	      <a href="<?php echo esc_url( home_url( '/#usluge' ) ); ?>">Usluge</a>
	      <a href="#">O nama</a>
	      <a href="<?php echo esc_url( home_url( '/?post_type=case-study' ) ); ?>">Case studies</a>
	      <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a>
	      <a href="#">Kontakt</a>
	      <a href="#">Klijenti</a>
	    </nav>
	    <div class="combo-nav-right">
	      <a href="#" class="combo-nav-btn combo-nav-btn--outline">Prijavite problem</a>
	      <a href="#" class="combo-nav-btn combo-nav-btn--solid">Zakažite poziv</a>
	    </div>
	  </div>
	</header>
	<?php
}

/* =============================================
   CUSTOM FOOTER
   ============================================= */

add_action( 'wp_footer', 'combo_render_footer', 5 );
function combo_render_footer() {
	?>
	<footer id="combo-footer" role="contentinfo">
	  <div class="combo-footer-inner">
	  <div class="combo-footer-top">
	    <div class="combo-ft-brand">
	      <a class="combo-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
	        <div class="combo-logo-box" style="width:28px;height:28px">
	          <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
	            <rect x="2" y="2" width="7" height="7" rx="2" fill="white"/>
	            <rect x="11" y="2" width="7" height="7" rx="2" fill="rgba(255,255,255,0.45)"/>
	            <rect x="2" y="11" width="7" height="7" rx="2" fill="rgba(255,255,255,0.45)"/>
	            <rect x="11" y="11" width="7" height="7" rx="2" fill="white"/>
	          </svg>
	        </div>
	        <span class="combo-logo-text" style="font-size:15px">Combo<span>.</span></span>
	      </a>
	      <p class="combo-ft-desc">IT usluge za mala, srednja i velika preduzeća u Srbiji od 2016. godine.</p>
	      <div class="combo-ft-contact">
	        <a href="mailto:office@combo.rs">office@combo.rs</a><br>
	        <a href="mailto:support@combo.rs">support@combo.rs</a><br>
	        +381 11 420 9242<br>
	        +381 63 681 403<br>
	        Beograd, Srbija
	      </div>
	    </div>
	    <div class="combo-ft-col">
	      <h4>Usluge</h4>
	      <a href="#">IT administracija</a>
	      <a href="#">Cloud rešenja</a>
	      <a href="#">Mreže</a>
	      <a href="#">Licenciranje</a>
	      <a href="#">IT consulting</a>
	      <a href="#">Web izrada</a>
	    </div>
	    <div class="combo-ft-col">
	      <h4>Kompanija</h4>
	      <a href="#">O nama</a>
	      <a href="#">Tim</a>
	      <a href="<?php echo esc_url( home_url( '/?post_type=case-study' ) ); ?>">Case studies</a>
	      <a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Blog</a>
	      <a href="#">Kontakt</a>
	    </div>
	    <div class="combo-ft-col">
	      <h4>Microsoft</h4>
	      <a href="#">Microsoft 365</a>
	      <a href="#">Azure</a>
	      <a href="#">Teams</a>
	      <a href="#">Intune / MDM</a>
	      <a href="#">SharePoint</a>
	    </div>
	  </div>
	  <div class="combo-footer-bottom">
	    <span class="combo-ft-copy">© <?php echo date( 'Y' ); ?> Combo IT · PIB: 112211110 · MB: 21627917</span>
	    <div class="combo-ft-certs">
	      <span class="combo-ft-cert combo-ft-cert--green">ISO 27001</span>
	      <span class="combo-ft-cert">MS Partner</span>
	      <span class="combo-ft-cert">Sophos</span>
	      <span class="combo-ft-cert">Panda</span>
	      <span class="combo-ft-cert">MikroTik MTCRE</span>
	      <span class="combo-ft-cert">Cisco SMB</span>
	      <span class="combo-ft-cert">HP · Dell · Lenovo</span>
	      <span class="combo-ft-cert">Ubiquiti</span>
	    </div>
	  </div>
	  </div><!-- /.combo-footer-inner -->
	</footer>
	<?php
}

/* =============================================
   ENQUEUE SCRIPTS & STYLES
   ============================================= */

add_action( 'wp_enqueue_scripts', 'combo_child_enqueue' );
function combo_child_enqueue() {

	// Roditeljska tema
	wp_enqueue_style(
		'hello-elementor-style',
		get_template_directory_uri() . '/style.css'
	);

	// Child tema CSS
	wp_enqueue_style(
		'combo-child-style',
		get_stylesheet_uri(),
		[ 'hello-elementor-style' ],
		wp_get_theme()->get( 'Version' )
	);

	// Inter + Montserrat font
	wp_enqueue_style(
		'combo-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Montserrat:wght@600;700;800&display=swap',
		[],
		null
	);

	// Font Awesome (Elementor bundle) — za FA ikonice u tab sadržaju
	wp_enqueue_style(
		'combo-font-awesome',
		plugins_url( 'elementor/assets/lib/font-awesome/css/all.min.css' ),
		[],
		'6.5.1'
	);

	// Combo JS (slider counter, itd.)
	wp_enqueue_script(
		'combo-scripts',
		get_stylesheet_directory_uri() . '/assets/js/combo.js',
		[],
		wp_get_theme()->get( 'Version' ),
		true  // u footer-u, posle Elementor/Swiper
	);
}
