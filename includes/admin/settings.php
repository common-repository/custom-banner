<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// Add menu page
add_action('admin_menu', 'custom_banner_admin_menu');
function custom_banner_admin_menu() {
    $hook_suffix = add_menu_page(
        'Banner Settings', 
        'Banner', 
        'manage_options', 
        'custom-banner', 
        'custom_banner_settings_page',
    );

    add_action('load-' . $hook_suffix, 'custom_banner_admin_enqueue_scripts');
}

// Menu page content
function custom_banner_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="custom-banner-settings-wrap">
        <h1>Banner Settings</h1>

        <?php custom_banner_output_preview(); ?>

        <form action="options.php" method="post">
            <?php
            settings_fields('custom_banner_options_group');

            echo '<h2 style="border-top: none;">Appearance</h2>';
            echo '<div class="custom-banner-appearance-settings">';
                do_settings_sections('custom-banner-appearance');
                do_settings_sections('custom-banner-appearance-1');
            echo '</div>';

            echo '<div class="custom-banner-content-settings">';
                do_settings_sections('custom-banner-content');
            echo '</div>';

            echo '<div class="custom-banner-behavior-settings">';
                do_settings_sections('custom-banner-behavior');
            echo '</div>';

            echo '<div class="custom-banner-adv-settings">';
                do_settings_sections('custom-banner-adv');
            echo '</div>';

            submit_button();
            ?>
        </form>
    </div>
    <?php 
}

// Add settings to the menu page
add_action('admin_init', 'custom_banner_register_settings');
function custom_banner_register_settings() {

    // Register new settings
    register_setting('custom_banner_options_group', 'custom_banner_enable');
    register_setting('custom_banner_options_group', 'custom_banner_text_color');
    register_setting('custom_banner_options_group', 'custom_banner_background_color');
    register_setting('custom_banner_options_group', 'custom_banner_banner_text');
    register_setting('custom_banner_options_group', 'custom_banner_width');
    register_setting('custom_banner_options_group', 'custom_banner_arrows');
    register_setting('custom_banner_options_group', 'custom_banner_autoplay');
    register_setting('custom_banner_options_group', 'custom_banner_delay');

    // Advanced settings
    register_setting('custom_banner_options_group', 'custom_banner_action_hook');
    register_setting('custom_banner_options_group', 'custom_banner_css_class');

    // Add a new section to the "Banner" page
    add_settings_section(
        'custom_banner_settings_section',
        'Appearance',
        '',
        'custom-banner-appearance'
    );

    add_settings_field(
        'custom_banner_enable',
        'Enable',
        'custom_banner_enable_cb',
        'custom-banner-appearance',
        'custom_banner_settings_section'
    );

    add_settings_field(
        'custom_banner_text_color',
        'Text Color',
        'custom_banner_text_color_cb',
        'custom-banner-appearance',
        'custom_banner_settings_section'
    );

    add_settings_field(
        'custom_banner_background_color',
        'Background Color',
        'custom_banner_background_color_cb',
        'custom-banner-appearance',
        'custom_banner_settings_section'
    );

    // Add a new section to the "Banner" page
    add_settings_section(
        'custom_banner_settings_section_1',
        'Appearance',
        '',
        'custom-banner-appearance-1'
    );

    add_settings_field(
        'custom_banner_width',
        'Width',
        'custom_banner_width_cb',
        'custom-banner-appearance-1',
        'custom_banner_settings_section_1'
    );

    add_settings_field(
        'custom_banner_arrows',
        'Arrows',
        'custom_banner_arrows_cb',
        'custom-banner-appearance-1',
        'custom_banner_settings_section_1'
    );

    // Add a new section to the "Banner" page
    add_settings_section(
        'custom_banner_settings_section_2',
        'Content',
        '',
        'custom-banner-content'
    );

    add_settings_field(
        'custom_banner_banner_text',
        'Messages',
        'custom_banner_banner_text_cb',
        'custom-banner-content',
        'custom_banner_settings_section_2'
    );

     // Add a new section to the "Banner" page
    add_settings_section(
        'custom_banner_settings_section_3',
        'Behavior',
        '',
        'custom-banner-behavior'
    );

    add_settings_field(
        'custom_banner_autoplay',
        'Autoplay',
        'custom_banner_autoplay_cb',
        'custom-banner-behavior',
        'custom_banner_settings_section_3'
    );

    add_settings_field(
        'custom_banner_delay',
        'Delay (Seconds)',
        'custom_banner_delay_cb',
        'custom-banner-behavior',
        'custom_banner_settings_section_3'
    );

    // Add a new section to the "Banner" page
    add_settings_section(
        'custom_banner_settings_section_adv',
        'Advanced',
        '',
        'custom-banner-adv'
    );

    add_settings_field(
        'custom_banner_css_class',
        'Additional CSS Class',
        'custom_banner_css_class_cb',
        'custom-banner-adv',
        'custom_banner_settings_section_adv'
    );

    add_settings_field(
        'custom_banner_action_hook',
        'Action Hook',
        'custom_banner_action_hook_cb',
        'custom-banner-adv',
        'custom_banner_settings_section_adv'
    );
}

// Callback functions for each setting
function custom_banner_enable_cb() {
	$option = get_option( 'custom_banner_enable' );
 	echo '<input name="custom_banner_enable" type="checkbox"' . checked( 'on', $option, false ) . ' />';
}

function custom_banner_text_color_cb() {
    $option = get_option('custom_banner_text_color');
    echo '<input type="text" name="custom_banner_text_color" value="' . esc_attr($option) . '" class="custom-banner-color-field" data-default-color="#333333" />';
}

function custom_banner_background_color_cb() {
    $option = get_option('custom_banner_background_color');
    echo '<input type="text" name="custom_banner_background_color" value="' . esc_attr($option) . '" class="custom-banner-color-field" data-default-color="#f5f5f5" />';
}

function custom_banner_width_cb() {
    $option = get_option('custom_banner_width', 'medium');

    $options = array(
        'narrow' => 'Narrow',
        'medium' => 'Medium',
        'wide' => 'Wide'
    );

    // Output the radio buttons
    echo '<div class="custom-banner-radio-field">';
    foreach ($options as $value => $label) {
        echo sprintf(
            '<label>
                <input type="radio" name="custom_banner_width" value="%s" %s> %s
            </label>',
            esc_attr($value),
            checked($option, $value, false),
            esc_html($label)
        );
    }
    echo '</div>';
}

function custom_banner_arrows_cb() {
    $option = get_option('custom_banner_arrows', 'none');

    $options = array(
        'none' => 'None',
        'square' => 'Square',
        'round' => 'Round',
        'chevron' => 'Chevron'
    );

    // Output the radio buttons
    echo '<div class="custom-banner-radio-field arrows">';
    foreach ($options as $value => $label) {
        echo sprintf(
            '<label>
                <input type="radio" name="custom_banner_arrows" value="%s" %s> %s %s
            </label>',
            esc_attr($value),
            checked($option, $value, false),
            wp_kses(custom_banner_output_svg($value), custom_banner_esc_svg()),
            wp_kses(custom_banner_output_svg($value), custom_banner_esc_svg()),
        );
    }
    echo '</div>';
}

function custom_banner_banner_text_cb() {
    $options = get_option('custom_banner_banner_text');
    if (!is_array($options)) {
        $options = [];
    }

    // Script for reizing textarea based on input
    echo '<script type="text/javascript">
        const adjustAllTextAreas = () => {
            document.querySelectorAll("td.html textarea").forEach(textarea => textAreaAdjust(textarea));
        };
        const textAreaAdjust = element => {
            element.style.height = "1px";
            element.style.height = `${2 + element.scrollHeight}px`;
        };
        document.addEventListener("DOMContentLoaded", adjustAllTextAreas);
        window.addEventListener("resize", adjustAllTextAreas);
    </script>';
  
    echo '<table id="custom-banner-content-table">';

    foreach ($options as $index => $option) {
        echo '<tr>';
        echo '<td><label>Type</label><select name="custom_banner_banner_text[' . esc_html($index) . '][type]" class="type">';
        echo '<option value="simple"' . ($option['type'] === 'simple' ? ' selected' : '') . '>Simple</option>';
        echo '<option value="html"' . ($option['type'] === 'html' ? ' selected' : '') . '>HTML</option>';
        echo '</select></td>';

        if ($option['type'] === 'html') {
            echo '<td class="html"><label>Html</label><textarea onInput="textAreaAdjust(this)" onload="textAreaAdjust(this)" name="custom_banner_banner_text[' . esc_html($index) . '][text]">' . esc_attr($option['text']) . '</textarea></td>';
        } else {
            echo '<td class="message"><label>Text</label><input type="text" name="custom_banner_banner_text[' . esc_html($index) . '][text]" value="' . esc_attr($option['text']) . '"></td>';
            echo '<td><label>Link Text</label><input type="text" name="custom_banner_banner_text[' . esc_html($index) . '][link_text]" value="' . esc_attr($option['link_text']) . '"></td>';
            echo '<td><label>URL</label><input type="text" name="custom_banner_banner_text[' . esc_html($index) . '][url]" value="' . esc_attr($option['url']) . '"></td>';
            echo '<td class="switch"><label>Show Link</label><input type="checkbox" name="custom_banner_banner_text[' . esc_html($index) . '][show_link]"' . (isset($option['show_link']) ? ' checked' : '') . '></td>';
        }
        echo '<td><button type="button" class="remove-row"><span class="minus"></span></button></td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<button type="button" id="cbAddMore"><div class="plus"><span></span><span></span></div>New Message</button>';
}

function custom_banner_autoplay_cb() {
    $option = get_option('custom_banner_autoplay');
    echo '<input name="custom_banner_autoplay" type="checkbox"' . checked( 'on', $option, false ) . ' />';
}

function custom_banner_delay_cb() {
    $option = get_option('custom_banner_delay');
    echo '<div class="custom-banner-number-field">';
        echo '<button type="button" class="minus-btn"><span class="minus"></span></button>';
        echo '<input type="number" name="custom_banner_delay" placeholder="3" value="' . esc_attr($option) . '" max="99" min="1">';
        echo '<button type="button" class="plus-btn"><div class="plus"><span></span><span></span></div></button>';
    echo '</div>';
}

function custom_banner_css_class_cb() {
    $option = get_option('custom_banner_css_class');
    echo '<div><label>Class</label><input type="text" name="custom_banner_css_class" value="' . esc_attr($option) . '"></div>';
}

function custom_banner_action_hook_cb() {
    $option = get_option('custom_banner_action_hook', array('name' => '', 'priority' => ''));
    echo '<div><label>Hook Name</label><input type="text" name="custom_banner_action_hook[name]" placeholder="wp_body_open" value="' . esc_attr($option['name']) . '"></div>';
    echo '<div><label>Priority</label><input type="number" name="custom_banner_action_hook[priority]" placeholder="10" value="' . esc_attr($option['priority']) . '"></div>';
}

// Outputs the banner preview
function custom_banner_output_preview() {
    echo '<div class="custom-banner-preview-wrapper">';
        echo '<div class="custom-banner-preview loading">';
            echo '<div id="banner-main"><span class="loader"></span></div>';
        echo '</div>';
    echo '</div>';
}

// Banner preview ajax function
add_action('wp_ajax_custom_banner_update_preview', 'custom_banner_update_preview');
function custom_banner_update_preview() {
    // Check the nonce with sanitization
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'custom_banner_ajax_nonce')) {
        die('Permission denied');
    }

    // Sanitize and Validate input
    $banner_text_color = isset($_POST['custom_banner_text_color']) ? sanitize_hex_color($_POST['custom_banner_text_color']) : '';
    $banner_bg_color = isset($_POST['custom_banner_background_color']) ? sanitize_hex_color($_POST['custom_banner_background_color']) : '';
    $banner_width = isset($_POST['custom_banner_width']) ? sanitize_text_field($_POST['custom_banner_width']) : '';
    $banner_arrows = isset($_POST['custom_banner_arrows']) ? sanitize_text_field($_POST['custom_banner_arrows']) : '';
    $banner_class = isset($_POST['custom_banner_css_class']) ? sanitize_html_class($_POST['custom_banner_css_class']) : '';

    $banner_style = '';
    if ($banner_text_color || $banner_bg_color) {
        $banner_style .= $banner_text_color ? 'color:' . esc_attr($banner_text_color) . ';' : '';
        $banner_style .= $banner_bg_color ? 'background-color:' . esc_attr($banner_bg_color) . ';' : '';
    }

    $overlay_style = '';
    if ($banner_bg_color) {
        $overlay_style = sprintf(
            'background: linear-gradient(90deg, %s 0%%, rgba(0,0,0,0) 10%%, rgba(0,0,0,0) 90%%, %s 100%%);', 
            esc_attr($banner_bg_color), 
            esc_attr($banner_bg_color)
        );
    }

    $raw_banner_content = isset($_POST['custom_banner_content']) ? $_POST['custom_banner_content'] : '';
    $banner_content = [];
    if (!empty($raw_banner_content) && is_array($raw_banner_content)) {
        foreach ($raw_banner_content as $content_item) {
            if (isset($content_item['type'], $content_item['text'])) {
                $sanitized_item = [
                    'type' => sanitize_text_field($content_item['type']),
                    'text' => wp_kses_post($content_item['text']),
                ];

                // Simple Type Fields
                if (isset($content_item['link_text'])) {
                    $sanitized_item['link_text'] = sanitize_text_field($content_item['link_text']);
                }
                if (isset($content_item['url'])) {
                    $sanitized_item['url'] = esc_url_raw($content_item['url']);
                }
                if (isset($content_item['show_link'])) {
                    $sanitized_item['show_link'] = $content_item['show_link'] === 'on' ? 'on' : '';
                }

                $banner_content[] = $sanitized_item;
            }
        }
    } else {
        $banner_content[] = ['type' => 'simple', 'text' => 'Add Some Content'];
    }

    // Template
    echo '<span class="loader"></span>';
    include dirname(dirname(plugin_dir_path(__FILE__))) . '\templates\banner.php';
    
    wp_die();
}



