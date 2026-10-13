<?php
/**
 * Frontend weergave & script inladen voor Rick Winter Snow plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Controleer of het sneeuweffect & winterthema actief is voor de huidige pagina / view.
 */
function rick_is_snow_enabled() {
    $post_id = 0;

    if ( is_singular() ) {
        $post_id = get_the_ID();
    } elseif ( is_front_page() ) {
        $post_id = get_option( 'page_on_front' );
    }

    if ( $post_id ) {
        $page_snow = function_exists( 'get_field' ) 
            ? get_field( 'rick_enable_snow', $post_id ) 
            : get_post_meta( $post_id, 'rick_enable_snow', true );

        if ( ! empty( $page_snow ) && ( $page_snow === '1' || $page_snow === 1 || $page_snow === true || $page_snow === 'yes' ) ) {
            return true;
        }
    }

    // Globale optie via plugin settings of thema settings
    $global_snow = get_option( 'rick_snow_global_enable', '0' );
    if ( $global_snow === '1' || $global_snow === 1 || $global_snow === true ) {
        return true;
    }

    if ( function_exists( 'rick_get_general_setting' ) ) {
        $theme_global = rick_get_general_setting( 'snow_global_enable', '0' );
        if ( ! empty( $theme_global ) && ( $theme_global === '1' || $theme_global === 1 || $theme_global === true ) ) {
            return true;
        }
    }

    return false;
}

/**
 * 2. Voeg de winter-theme body classes toe wanneer de optie actief is.
 */
function rick_winter_snow_body_classes( $classes ) {
    if ( rick_is_snow_enabled() ) {
        $classes[] = 'winter-theme';
        $classes[] = 'snow-active';
    }
    return $classes;
}
add_filter( 'body_class', 'rick_winter_snow_body_classes' );

/**
 * 3. Laad het winter stylesheet en het authentieke Snow Falling JS script in.
 */
function rick_winter_snow_enqueue_assets() {
    if ( ! rick_is_snow_enabled() ) {
        return;
    }

    // 1. Donkere winterse achtergrond & layout stylesheet
    $winter_css_path = RICK_WINTER_SNOW_DIR . 'assets/css/winter-theme.css';
    if ( file_exists( $winter_css_path ) ) {
        wp_enqueue_style(
            'rick-winter-theme',
            RICK_WINTER_SNOW_URL . 'assets/css/winter-theme.css',
            array(),
            filemtime( $winter_css_path )
        );
    }

    // 2. Snow Falling JS script (Scott Schiller's Snowstorm - CodePen OJMbXz)
    $snow_js_path = RICK_WINTER_SNOW_DIR . 'assets/js/snowstorm.min.js';
    if ( ! file_exists( $snow_js_path ) ) {
        return;
    }

    wp_enqueue_script(
        'rick-snowstorm',
        RICK_WINTER_SNOW_URL . 'assets/js/snowstorm.min.js',
        array(),
        filemtime( $snow_js_path ),
        true
    );

    // Authentiek CodePen gedrag: pure witte sneeuw over de donkere achtergrond
    wp_add_inline_script(
        'rick-snowstorm',
        'if (typeof snowStorm !== "undefined") {
            snowStorm.snowColor = "#ffffff";
            snowStorm.snowCharacter = "&bull;";
            snowStorm.className = "rick-snowflake";
            snowStorm.useGPU = true;
            snowStorm.zIndex = 99999;
            snowStorm.flakeWidth = 14;
            snowStorm.flakeHeight = 14;
            snowStorm.flakesMax = 128;
            snowStorm.flakesMaxActive = 64;
            snowStorm.followMouse = true;
            snowStorm.snowStick = false;
            snowStorm.usePositionFixed = true;
            snowStorm.excludeMobile = false;
        }'
    );
}
add_action( 'wp_enqueue_scripts', 'rick_winter_snow_enqueue_assets', 20 );
