<?php

add_action( 'wp_enqueue_scripts', 'combo_child_enqueue' );
function combo_child_enqueue() {
	wp_enqueue_style(
		'hello-elementor-style',
		get_template_directory_uri() . '/style.css'
	);
	wp_enqueue_style(
		'combo-child-style',
		get_stylesheet_uri(),
		[ 'hello-elementor-style' ],
		wp_get_theme()->get( 'Version' )
	);
	wp_enqueue_style(
		'combo-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
		[],
		null
	);
}
