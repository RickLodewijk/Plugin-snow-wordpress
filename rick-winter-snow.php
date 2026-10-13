<?php
/**
 * Plugin Name:       Rick Winter Snow & Theme
 * Plugin URI:        https://example.com/rick-winter-snow
 * Description:       Sneeuweffect (Snow Falling JS) en sfeervolle, donkere winterse achtergrond per pagina of globaal (gebaseerd op CodePen OJMbXz).
 * Version:           1.0.0
 * Author:            Rick
 * Text Domain:       rick-winter-snow
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin Constanten
define( 'RICK_WINTER_SNOW_VERSION', '1.0.0' );
define( 'RICK_WINTER_SNOW_FILE', __FILE__ );
define( 'RICK_WINTER_SNOW_DIR', plugin_dir_path( __FILE__ ) );
define( 'RICK_WINTER_SNOW_URL', plugin_dir_url( __FILE__ ) );

// Laad modulaire onderdelen
require_once RICK_WINTER_SNOW_DIR . 'includes/admin.php';
require_once RICK_WINTER_SNOW_DIR . 'includes/frontend.php';
