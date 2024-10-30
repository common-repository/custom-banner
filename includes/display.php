<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Determines the hook from which to output the banner
add_action('init', 'custom_banner_hook');
function custom_banner_hook() {
	$custom_action_hook = get_option('custom_banner_action_hook');
	$hook_name = 'wp_body_open';
	$hook_priority = 10;

	if (is_array($custom_action_hook)) {
		if (isset($custom_action_hook['name']) && $custom_action_hook['name'] !== '') {
			$hook_name = trim( $custom_action_hook['name'] );
		}
		if (isset($custom_action_hook['priority']) && is_numeric($custom_action_hook['priority'])) {
			$hook_priority = (int)trim( $custom_action_hook['priority'] );
		}
	}

	$hook_name = apply_filters( 'custom_banner_output_hook', $hook_name);

	// Add action to hook
	add_action($hook_name, 'custom_banner_output', $hook_priority);
}

// Outputs the banner
function custom_banner_output() {
	if (!custom_banner_is_enabled()) {
		return;
	}

	// Settings
	$banner_text_color = get_option('custom_banner_text_color');
	$banner_bg_color = get_option('custom_banner_background_color');
	$banner_style = '';
	if ($banner_text_color || $banner_bg_color) {
		$banner_style .= $banner_text_color ? 'color: ' . $banner_text_color . '; ' : '';
		$banner_style .= $banner_bg_color ? 'background-color: ' . $banner_bg_color . ';' : '';
	}
	$overlay_style = '';
	if ($banner_bg_color) {
		$overlay_style = sprintf(
			'background: linear-gradient(90deg, %s 0%%, rgba(0,0,0,0) 10%%, rgba(0,0,0,0) 90%%, %s 100%%);', 
			$banner_bg_color, 
			$banner_bg_color
		);
	}
	$banner_arrows = get_option('custom_banner_arrows');
	$banner_width = get_option('custom_banner_width');
	$banner_class = trim(get_option('custom_banner_css_class'));
	$banner_content = get_option('custom_banner_banner_text');
	if (empty($banner_content) || $banner_content === '') {
		$banner_content = array(
			array(
				'type' => 'simple',
				'text' => 'Add Some Content',
			),
		);
	} 

	// Template
	include dirname(plugin_dir_path(__FILE__)) . '\templates\banner.php';
}

// Returns true if banner is enabled and there are messages
function custom_banner_is_enabled() {
	$is_enabled = false;

	// Check enable setting
	$banner_enable = get_option( 'custom_banner_enable' );
	if ($banner_enable === 'on') {

		// Check for content
		$banner_content = get_option('custom_banner_banner_text');
		if ($banner_content && is_array($banner_content)) {
		    $is_enabled = true;
		}
	}

	$is_enabled = apply_filters( 'custom_banner_is_enabled', $is_enabled );

    return $is_enabled;
}

// Returns an arrow svg
function custom_banner_output_svg($arrow) {
	$svg = '';
	switch ($arrow) {
		case 'square':
			$svg = '<svg height="24" viewBox="0 -960 960 960" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m 321,-80 -71,-71 329,-329 -329,-329 71,-71 400,400 z" /></svg>';
			break;

		case 'round':
			$svg = '<svg height="24" viewBox="0 -960 960 960" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m 321.01562,-859.21875 c -13.32883,-0.017 -26.11742,5.26711 -35.54687,14.6875 -19.61649,19.60786 -19.61649,51.40775 0,71.01563 L 578.98438,-480 285.46875,-186.48438 a 50.210106,50.210106 0 0 0 0,70.9375 50.210106,50.210106 0 0 0 70.9375,0 h 0.0781 l 328.98437,-328.98437 a 50.215626,50.215626 0 0 0 10.9375,-16.25 A 50.215626,50.215626 0 0 0 700.23438,-480 50.215626,50.215626 0 0 0 700,-484.375 a 50.215626,50.215626 0 0 0 -0.54688,-4.375 50.215626,50.215626 0 0 0 -0.9375,-4.21875 50.215626,50.215626 0 0 0 -1.32812,-4.21875 50.215626,50.215626 0 0 0 -1.64062,-4.0625 50.215626,50.215626 0 0 0 -2.03126,-3.82812 50.215626,50.215626 0 0 0 -2.34374,-3.75 50.215626,50.215626 0 0 0 -2.73438,-3.4375 50.215626,50.215626 0 0 0 -2.96875,-3.20313 l -328.98437,-329.0625 c -9.41032,-9.40125 -22.16697,-14.68375 -35.46876,-14.6875 z" /></svg>';
			break;

		case 'chevron':
			$svg = '<svg height="24" viewBox="0 -960 960 960" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M 356.52343,-115.50781 H 214.49219 L 579,-480 214.5,-844.5 h 142 L 721,-480 Z"/></svg>';
			break;
	}

	$svg = apply_filters( 'custom_banner_output_svg', $svg, $arrow );
    return $svg;
}

// Reutrns the allowed html element names for escaping svg using wp_kses()
function custom_banner_esc_svg() {
    return array(
	    'svg'   => array(
	        'class'           => true,
	        'aria-hidden'     => true,
	        'xmlns'           => true,
	        'width'           => true,
	        'height'          => true,
	        'viewbox'         => true
	    ),
	    'path'  => array( 
	        'd'               => true, 
	    )
	);
}
