<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if (!empty($message)) {
	switch ($message['type']) {
		case 'html':
			$text = $message['text'];
			echo '<div class="banner-message html">';
				echo wp_kses_post(stripcslashes($text));
			echo '</div>';
			break;
		
		case 'simple':
			$text = $message['text'];
			$url = ( isset($message['url']) && $message['url'] !== '' ) ? $message['url'] : null;
			$link_text = ( isset($message['link_text']) && $message['link_text'] !== '' ) ? $message['link_text'] : null;
			$link_show = ( isset($message['show_link']) && $message['show_link'] !== '' ) ? $message['show_link'] === 'on' : false;

			if ($link_show && isset($url) && isset($link_text)) {
				echo '<div class="banner-message">';
					echo '<div>' . esc_html($text) . '</div>';
					echo '<a href="' . esc_url($url) . '" alt="' . esc_html($link_text) . '">';
						echo esc_html($link_text);
					echo '</a>';
				echo '</div>';
			} else if (isset($url)) {
				echo '<a class="banner-message" href="' . esc_url($url) . '" alt="' . esc_html($link_text) . '">';
					echo '<div>' . esc_html($text) . '</div>';
				echo '</a>';
			} else {
				echo '<div class="banner-message">';
					echo '<div>' . esc_html($text) . '</div>';
				echo '</div>';
			}
			break;
	}
}

