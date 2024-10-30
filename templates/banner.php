<?php

if ( ! defined( 'ABSPATH' ) ) exit;

do_action( 'custom_banner_before' );

echo sprintf('<div id="banner-main" class="%s" style="%s">', 
	esc_attr(!empty($banner_class) ? $banner_class : ''), 
	esc_attr($banner_style)
);

	do_action( 'custom_banner_before_content' );

	if (!empty($banner_content)) {

		if (count($banner_content) > 1) {

			echo '<div class="swiper bannerSwiper ' . esc_attr($banner_width) . '">';

				echo '<div class="swiper-wrapper" aria-live="polite">';
	            	foreach ($banner_content as $message) {
	            		echo '<div class="swiper-slide" role="group" aria-label="">';
	            			include plugin_dir_path(__FILE__) . 'message.php';
						echo '</div>';
	            	}
		        echo '</div>';

		        echo sprintf('<span class="banner-prev" style="%s">%s</span>', 
		        	esc_attr('left:0; fill: ' . $banner_text_color),
		        	wp_kses(custom_banner_output_svg($banner_arrows), custom_banner_esc_svg())
		        );

		        echo sprintf('<span class="banner-next" style="%s">%s</span>', 
		        	esc_attr('right:0; fill: ' . $banner_text_color),
		        	wp_kses(custom_banner_output_svg($banner_arrows), custom_banner_esc_svg())
		        );

				echo '<span class="banner-overlay" style="' . esc_attr($overlay_style) . '"></span>';
			echo '</div>';

		} else {
			$message = $banner_content[0];
			echo '<div style="display: inline-flex;">';
				include plugin_dir_path(__FILE__) . 'message.php';
			echo '</div>';
		}
	}

	do_action( 'custom_banner_after_content' );
	
echo '</div>';

do_action( 'custom_banner_after' );