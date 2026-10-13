<?php
/**
 * Plugin Name:       Rick Seizoens Thema's (Winter Sneeuw & Herfst Bladeren)
 * Plugin URI:        https://example.com/rick-winter-snow
 * Description:       Sneeuweffect (Snow Falling JS), dwarrelende herfstbladeren (Autumn Leaves JS), tabbladen in het dashboard en keuze uit winter- en herfstthema's per pagina of site-breed.
 * Version:           1.2.0
 * Author:            Rick
 * Text Domain:       rick-winter-snow
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin Constanten
define( 'RICK_WINTER_SNOW_VERSION', '1.2.0' );
define( 'RICK_WINTER_SNOW_FILE', __FILE__ );
define( 'RICK_WINTER_SNOW_DIR', plugin_dir_path( __FILE__ ) );
define( 'RICK_WINTER_SNOW_URL', plugin_dir_url( __FILE__ ) );

// Laad modulaire onderdelen
require_once RICK_WINTER_SNOW_DIR . 'includes/admin.php';
require_once RICK_WINTER_SNOW_DIR . 'includes/frontend.php';
