<?php

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

	// Inter font
	wp_enqueue_style(
		'combo-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		[],
		null
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
