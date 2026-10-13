<?php
/**
 * Frontend weergave & script inladen voor Rick Seizoens Thema's (Winter Sneeuw & Herfst Bladeren).
 * Ondersteunt gelijktijdige activatie van verschillende effecten op verschillende pagina's (bijv. Pepernoten = Herfst, Oliebollen = Winter).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Bepaal de dynamische seizoensstatus voor de huidige view.
 * Retourneert of Winter en/of Herfst actief zijn en met welk thema.
 */
function rick_winter_snow_get_view_state() {
    static $cached_state = null;
    if ( $cached_state !== null ) {
        return $cached_state;
    }

    $options = rick_winter_snow_get_options();

    $post_id = 0;
    if ( is_singular() ) {
        $post_id = get_the_ID();
    } elseif ( is_front_page() ) {
        $post_id = (int) get_option( 'page_on_front' );
    }

    $post_type = '';
    if ( $post_id ) {
        $post_type = get_post_type( $post_id );
    } elseif ( is_post_type_archive() ) {
        $post_type = get_query_var( 'post_type' );
        if ( is_array( $post_type ) ) {
            $post_type = reset( $post_type );
        }
    } elseif ( is_home() ) {
        $post_type = 'post';
    }

    // 1. Meta op de specifieke post/pagina (ACF of native meta)
    $meta_enabled = null;
    $meta_theme   = '';
    if ( $post_id ) {
        $meta_enabled = function_exists( 'get_field' )
            ? get_field( 'rick_enable_snow', $post_id )
            : get_post_meta( $post_id, 'rick_enable_snow', true );

        $meta_theme = function_exists( 'get_field' )
            ? get_field( 'rick_page_theme', $post_id )
            : get_post_meta( $post_id, 'rick_page_theme', true );
    }

    // Expliciet uitgeschakeld voor deze specifieke pagina
    if ( $meta_theme === 'disabled' ) {
        $cached_state = array( 'winter' => false, 'autumn' => false, 'winter_theme' => '', 'autumn_theme' => '', 'options' => $options );
        return $cached_state;
    }

    // 2. Pagina-specifieke toewijzing in dashboard instellingen
    $page_assigned_theme = '';
    if ( $post_id && ! empty( $options['page_themes'][ $post_id ] ) ) {
        $page_assigned_theme = $options['page_themes'][ $post_id ];
        if ( $page_assigned_theme === 'disabled' ) {
            $cached_state = array( 'winter' => false, 'autumn' => false, 'winter_theme' => '', 'autumn_theme' => '', 'options' => $options );
            return $cached_state;
        }
    }

    // 3. Berichttype-specifieke toewijzing in dashboard instellingen
    $cpt_assigned_theme = '';
    if ( $post_type && ! empty( $options['cpt_themes'][ $post_type ] ) ) {
        $cpt_assigned_theme = $options['cpt_themes'][ $post_type ];
        if ( $cpt_assigned_theme === 'disabled' ) {
            $cached_state = array( 'winter' => false, 'autumn' => false, 'winter_theme' => '', 'autumn_theme' => '', 'options' => $options );
            return $cached_state;
        }
    }

    // Bepaal het thema (prioriteit: pagina-meta > pagina dashboard toewijzing > berichttype dashboard toewijzing)
    $chosen_theme = '';
    if ( ! empty( $meta_theme ) && $meta_theme !== 'default' ) {
        $chosen_theme = $meta_theme;
    } elseif ( ! empty( $page_assigned_theme ) && $page_assigned_theme !== 'default' ) {
        $chosen_theme = $page_assigned_theme;
    } elseif ( ! empty( $cpt_assigned_theme ) && $cpt_assigned_theme !== 'default' ) {
        $chosen_theme = $cpt_assigned_theme;
    }

    $autumn_themes = array( 'autumn-gold', 'autumn-dark', 'autumn-pumpkin', 'leaves-only' );
    $winter_themes = array( 'winter-dark', 'winter-frost', 'winter-aurora', 'snow-only' );

    $winter_active       = false;
    $autumn_active       = false;
    $active_winter_theme = '';
    $active_autumn_theme = '';

    // A. Als er een specifiek thema is toegewezen:
    if ( ! empty( $chosen_theme ) ) {
        if ( in_array( $chosen_theme, $autumn_themes, true ) ) {
            $autumn_active       = true;
            $active_autumn_theme = $chosen_theme;
        } elseif ( in_array( $chosen_theme, $winter_themes, true ) ) {
            $winter_active       = true;
            $active_winter_theme = $chosen_theme;
        }
    } else {
        // B. Geen specifiek thema gekozen: controleer de actieve modules per post type / scope

        // Winter controle
        if ( ! empty( $options['winter_enabled'] ) ) {
            $w_scope = $options['winter_scope'] ?? 'specific';
            $w_cpts  = (array) ( $options['winter_post_types'] ?? array() );
            $w_pages = (array) ( $options['winter_pages'] ?? array() );

            if ( $w_scope === 'all' ) {
                $winter_active       = true;
                $active_winter_theme = $options['winter_default_theme'] ?? 'winter-dark';
            } elseif ( ( $post_type && in_array( $post_type, $w_cpts, true ) ) || ( $post_id && in_array( $post_id, $w_pages, true ) ) ) {
                $winter_active       = true;
                $active_winter_theme = $options['winter_default_theme'] ?? 'winter-dark';
            }
        }

        // Herfst controle
        if ( ! empty( $options['autumn_enabled'] ) ) {
            $a_scope = $options['autumn_scope'] ?? 'specific';
            $a_cpts  = (array) ( $options['autumn_post_types'] ?? array() );
            $a_pages = (array) ( $options['autumn_pages'] ?? array() );

            if ( $a_scope === 'all' ) {
                $autumn_active       = true;
                $active_autumn_theme = $options['autumn_default_theme'] ?? 'autumn-gold';
            } elseif ( ( $post_type && in_array( $post_type, $a_cpts, true ) ) || ( $post_id && in_array( $post_id, $a_pages, true ) ) ) {
                $autumn_active       = true;
                $active_autumn_theme = $options['autumn_default_theme'] ?? 'autumn-gold';
            }
        }

        // C. Fallback als de editor checkbox 'rick_enable_snow' aan staat zonder thema-keuze:
        if ( ! $winter_active && ! $autumn_active && ! empty( $meta_enabled ) ) {
            if ( $post_type === 'pepernoot' ) {
                $autumn_active       = true;
                $active_autumn_theme = $options['autumn_default_theme'] ?? 'autumn-gold';
            } else {
                $winter_active       = true;
                $active_winter_theme = $options['winter_default_theme'] ?? 'winter-dark';
            }
        }
    }

    $cached_state = array(
        'winter'        => $winter_active,
        'autumn'        => $autumn_active,
        'winter_theme'  => $active_winter_theme,
        'autumn_theme'  => $active_autumn_theme,
        'options'       => $options,
    );

    return $cached_state;
}

/**
 * 2. Helper functie voor thema & backwards compatibility.
 */
function rick_is_snow_enabled() {
    $state = rick_winter_snow_get_view_state();
    return ! empty( $state['winter'] ) || ! empty( $state['autumn'] );
}

/**
 * 3. Voeg de juiste body classes toe op basis van het gekozen effect op deze pagina.
 */
function rick_winter_snow_body_classes( $classes ) {
    $state = rick_winter_snow_get_view_state();

    // Herfst styling
    if ( ! empty( $state['autumn'] ) ) {
        $classes[] = 'leaves-active';
        $theme     = $state['autumn_theme'] ?: 'autumn-gold';
        if ( $theme !== 'leaves-only' ) {
            $classes[] = 'autumn-theme';
            $classes[] = 'theme-' . sanitize_html_class( $theme );
        } else {
            $classes[] = 'theme-leaves-only';
        }
    }

    // Winter styling
    if ( ! empty( $state['winter'] ) ) {
        $classes[] = 'snow-active';
        $theme     = $state['winter_theme'] ?: 'winter-dark';
        if ( $theme !== 'snow-only' ) {
            $classes[] = 'winter-theme';
            $classes[] = 'theme-' . sanitize_html_class( $theme );
        } else {
            $classes[] = 'theme-snow-only';
        }
    }

    return $classes;
}
add_filter( 'body_class', 'rick_winter_snow_body_classes' );

/**
 * 4. Laad de juiste seizoens scripts en stylesheets in op basis van wat actief is op deze pagina.
 */
function rick_winter_snow_enqueue_assets() {
    $state   = rick_winter_snow_get_view_state();
    $options = $state['options'];

    // ==========================================
    // 1. HERFST BLADEREN & THEMA
    // ==========================================
    if ( ! empty( $state['autumn'] ) ) {
        $theme = $state['autumn_theme'];

        // Herfst Stylesheet
        if ( $theme !== 'leaves-only' ) {
            $autumn_css = RICK_WINTER_SNOW_DIR . 'assets/css/autumn-theme.css';
            if ( file_exists( $autumn_css ) ) {
                wp_enqueue_style(
                    'rick-autumn-theme',
                    RICK_WINTER_SNOW_URL . 'assets/css/autumn-theme.css',
                    array(),
                    filemtime( $autumn_css )
                );
            }
        }

        // Vallende Bladeren JS Script
        $leaves_js = RICK_WINTER_SNOW_DIR . 'assets/js/autumn-leaves.min.js';
        if ( file_exists( $leaves_js ) ) {
            wp_enqueue_script(
                'rick-autumn-leaves',
                RICK_WINTER_SNOW_URL . 'assets/js/autumn-leaves.min.js',
                array(),
                filemtime( $leaves_js ),
                true
            );

            $leaves_setting = $options['leaves_amount'] ?? 'normal';
            $leaf_count     = ( $leaves_setting === 'light' ) ? 16 : ( ( $leaves_setting === 'heavy' ) ? 50 : 32 );
            $follow_mouse   = ! empty( $options['leaves_follow_mouse'] );

            wp_add_inline_script(
                'rick-autumn-leaves',
                'window.rickAutumnConfig = {
                    leafCount: ' . intval( $leaf_count ) . ',
                    followMouse: ' . ( $follow_mouse ? 'true' : 'false' ) . '
                };',
                'before'
            );
        }
    }

    // ==========================================
    // 2. WINTER SNEEUW & THEMA
    // ==========================================
    if ( ! empty( $state['winter'] ) ) {
        $theme = $state['winter_theme'];

        // Winter Stylesheet
        if ( $theme !== 'snow-only' ) {
            $winter_css = RICK_WINTER_SNOW_DIR . 'assets/css/winter-theme.css';
            if ( file_exists( $winter_css ) ) {
                wp_enqueue_style(
                    'rick-winter-theme',
                    RICK_WINTER_SNOW_URL . 'assets/css/winter-theme.css',
                    array(),
                    filemtime( $winter_css )
                );
            }
        }

        // Snow Falling JS Script
        $snow_js = RICK_WINTER_SNOW_DIR . 'assets/js/snowstorm.min.js';
        if ( file_exists( $snow_js ) ) {
            wp_enqueue_script(
                'rick-snowstorm',
                RICK_WINTER_SNOW_URL . 'assets/js/snowstorm.min.js',
                array(),
                filemtime( $snow_js ),
                true
            );

            $flakes_setting = $options['snow_flakes_amount'] ?? 'normal';
            $flakes_max     = ( $flakes_setting === 'light' ) ? 48 : ( ( $flakes_setting === 'heavy' ) ? 160 : 96 );
            $flakes_active  = intval( $flakes_max / 2 );
            $follow_mouse   = ! empty( $options['snow_follow_mouse'] ) ? 'true' : 'false';

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
                    snowStorm.flakesMax = ' . intval( $flakes_max ) . ';
                    snowStorm.flakesMaxActive = ' . intval( $flakes_active ) . ';
                    snowStorm.followMouse = ' . $follow_mouse . ';
                    snowStorm.snowStick = false;
                    snowStorm.usePositionFixed = true;
                    snowStorm.excludeMobile = false;
                }'
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'rick_winter_snow_enqueue_assets', 20 );
