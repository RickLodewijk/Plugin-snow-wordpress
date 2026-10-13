<?php
/**
 * Admin functionaliteit voor Rick Seizoens Thema's (Winter Sneeuw & Herfst Bladeren).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Beschikbare thema's gegroepeerd per seizoen (Winter & Herfst).
 */
function rick_winter_snow_get_all_themes() {
    return array(
        'winter' => array(
            'winter-dark'   => array(
                'label'       => '🌌 Donkere Winternacht',
                'name'        => 'Donkere Winternacht',
                'season'      => 'winter',
                'description' => 'Diepe nachtblauwe achtergrond (#080d1a), frosted glass header, donkere kaarten en pure witte sneeuw.',
                'bg_preview'  => '#080d1a',
                'accent'      => '#38bdf8',
            ),
            'winter-frost'  => array(
                'label'       => '❄️ Frosted Winterdag',
                'name'        => 'Frosted Winterdag',
                'season'      => 'winter',
                'description' => 'Frisse ijsblauwe dagsetting (#edf5fa), heldere frosted elementen en vallende sneeuw.',
                'bg_preview'  => '#e0f2fe',
                'accent'      => '#0284c7',
            ),
            'winter-aurora' => array(
                'label'       => '🌌 Noorderlicht (Aurora)',
                'name'        => 'Noorderlicht (Aurora)',
                'season'      => 'winter',
                'description' => 'Magische polarnacht met smaragdgroene en paarse aurora-gloed en vallende sneeuw.',
                'bg_preview'  => '#061118',
                'accent'      => '#34d399',
            ),
            'snow-only'     => array(
                'label'       => '❄️ Alleen Sneeuweffect',
                'name'        => 'Alleen Sneeuweffect',
                'season'      => 'winter',
                'description' => 'Geen layoutwijzigingen; uitsluitend vallende sneeuw over het bestaande thema.',
                'bg_preview'  => '#ffffff',
                'accent'      => '#64748b',
            ),
        ),
        'autumn' => array(
            'autumn-gold'    => array(
                'label'       => '🍁 Gouden Herfstzon (Warm Amber)',
                'name'        => 'Gouden Herfstzon (Amber)',
                'season'      => 'autumn',
                'description' => 'Warme gouden en amberkleurige herfstsfeer (#1a1008), sfeervolle kaarten en vallende bladeren.',
                'bg_preview'  => '#24160c',
                'accent'      => '#f59e0b',
            ),
            'autumn-dark'    => array(
                'label'       => '🍂 Donker Herfstbos & Koper',
                'name'        => 'Donker Herfstbos & Koper',
                'season'      => 'autumn',
                'description' => 'Diep kastanjebruin en koper nachtbos (#130a05), warme accenten en vallende herfstbladeren.',
                'bg_preview'  => '#140c06',
                'accent'      => '#ea580c',
            ),
            'autumn-pumpkin' => array(
                'label'       => '🎃 Pompoen & Kaneel',
                'name'        => 'Pompoen & Kaneel',
                'season'      => 'autumn',
                'description' => 'Sfeervolle pompoenoranje en kaneelbruine gloed (#170b04) met dwarrelende herfstbladeren.',
                'bg_preview'  => '#231106',
                'accent'      => '#f97316',
            ),
            'leaves-only'    => array(
                'label'       => '🍂 Alleen Vallende Bladeren',
                'name'        => 'Alleen Vallende Bladeren',
                'season'      => 'autumn',
                'description' => 'Geen layoutwijzigingen; uitsluitend vallende herfstbladeren over de bestaande site.',
                'bg_preview'  => '#ffffff',
                'accent'      => '#d97706',
            ),
        ),
    );
}

/**
 * Platte lijst van alle thema's (key => info).
 */
function rick_winter_snow_get_flat_themes() {
    $grouped = rick_winter_snow_get_all_themes();
    return array_merge( $grouped['winter'], $grouped['autumn'] );
}

/**
 * 2. Plugin instellingen ophalen met veilige defaults.
 * Zowel winter als herfst kunnen gelijktijdig actief zijn op verschillende pagina's!
 */
function rick_winter_snow_get_options() {
    $defaults = array(
        // Winter instellingen
        'winter_enabled'        => '1',
        'winter_scope'          => 'specific', // 'all' of 'specific'
        'winter_default_theme'  => 'winter-dark',
        'winter_post_types'     => array( 'oliebol' ),
        'winter_pages'          => array(),
        'snow_flakes_amount'    => 'normal',
        'snow_follow_mouse'     => '1',

        // Herfst instellingen
        'autumn_enabled'        => '1',
        'autumn_scope'          => 'specific', // 'all' of 'specific'
        'autumn_default_theme'  => 'autumn-gold',
        'autumn_post_types'     => array( 'pepernoot' ),
        'autumn_pages'          => array(),
        'leaves_amount'         => 'normal',
        'leaves_follow_mouse'   => '1',

        // Toewijzing per berichttype en per pagina
        'cpt_themes'            => array(
            'pepernoot' => 'autumn-gold',
            'oliebol'   => 'winter-dark',
        ),
        'page_themes'           => array(),
    );

    $options = get_option( 'rick_winter_snow_options', array() );
    if ( ! is_array( $options ) ) {
        $options = array();
    }

    // Migratie van eerdere structuur
    if ( isset( $options['enabled_post_types'] ) && ! isset( $options['winter_post_types'] ) ) {
        $options['winter_post_types'] = array_diff( (array) $options['enabled_post_types'], array( 'pepernoot' ) );
        $options['autumn_post_types'] = array( 'pepernoot' );
    }

    return wp_parse_args( $options, $defaults );
}

/**
 * 3. ACF Veldengroep registreren voor pagina's en Custom Post Types.
 */
add_action( 'acf/init', 'rick_winter_snow_register_acf_fields' );
function rick_winter_snow_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    $flat_choices = array(
        'default'        => '⚙️ Standaard (instellingen volgen)',
        'winter-dark'    => '🌌 ❄️ Winter: Donkere Winternacht',
        'winter-frost'   => '❄️ ❄️ Winter: Frosted Winterdag',
        'winter-aurora'  => '🌌 ❄️ Winter: Noorderlicht / Aurora',
        'snow-only'      => '❄️ ❄️ Winter: Alleen Sneeuweffect',
        'autumn-gold'    => '🍁 🍂 Herfst: Gouden Herfstzon (Amber)',
        'autumn-dark'    => '🍂 🍂 Herfst: Donker Herfstbos & Koper',
        'autumn-pumpkin' => '🎃 🍂 Herfst: Pompoen & Kaneel',
        'leaves-only'    => '🍂 🍂 Herfst: Alleen Vallende Bladeren',
        'disabled'       => '🚫 Geen seizoenseffect / Uitgeschakeld',
    );

    acf_add_local_field_group( array(
        'key'      => 'group_rick_page_snow',
        'title'    => '❄️🍂 Seizoenseffect & Thema',
        'fields'   => array(
            array(
                'key'           => 'field_rick_enable_snow',
                'label'         => 'Seizoenseffect Inschakelen',
                'name'          => 'rick_enable_snow',
                'type'          => 'true_false',
                'message'       => 'Effect (sneeuw of bladeren) en gekozen seizoens-thema activeren voor deze pagina',
                'default_value' => 0,
                'ui'            => 1,
                'ui_on_text'    => 'Aan',
                'ui_off_text'   => 'Uit',
                'instructions'  => 'Handmatige activatie voor deze specifieke pagina.',
            ),
            array(
                'key'           => 'field_rick_page_theme',
                'label'         => 'Thema / Effect voor deze pagina',
                'name'          => 'rick_page_theme',
                'type'          => 'select',
                'choices'       => $flat_choices,
                'default_value' => 'default',
                'allow_null'    => 0,
                'multiple'      => 0,
                'ui'            => 0,
                'instructions'  => 'Kies welk specifiek winter- of herfstthema op deze pagina getoond moet worden.',
            ),
        ),
        'location' => array(
            array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'page' ) ),
            array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'recept' ) ),
            array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'pepernoot' ) ),
            array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'oliebol' ) ),
            array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'baktip' ) ),
            array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ),
        ),
        'menu_order'      => 15,
        'position'        => 'side',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'          => true,
    ) );
}

/**
 * 4. Native Meta Box fallback wanneer ACF niet aanwezig is.
 */
add_action( 'add_meta_boxes', 'rick_winter_snow_add_meta_box' );
add_action( 'save_post', 'rick_winter_snow_save_meta_box' );

function rick_winter_snow_add_meta_box() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    $screens = array( 'page', 'post', 'recept', 'baktip', 'pepernoot', 'oliebol' );
    foreach ( $screens as $screen ) {
        add_meta_box(
            'rick_snow_meta_box',
            '❄️🍂 Seizoenseffect & Thema',
            'rick_winter_snow_render_meta_box',
            $screen,
            'side',
            'default'
        );
    }
}

function rick_winter_snow_render_meta_box( $post ) {
    wp_nonce_field( 'rick_save_snow_meta_box', 'rick_snow_meta_box_nonce' );
    $is_enabled = get_post_meta( $post->ID, 'rick_enable_snow', true );
    $page_theme = get_post_meta( $post->ID, 'rick_page_theme', true ) ?: 'default';
    ?>
    <div style="padding: 6px 0;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;color:#1e293b;margin-bottom:12px;">
            <input type="checkbox" name="rick_enable_snow" value="1" <?php checked( $is_enabled, '1' ); ?> />
            <span>Effect & Thema Inschakelen</span>
        </label>
        
        <label for="rick_page_theme" style="display:block;font-weight:600;color:#1e293b;margin-bottom:5px;font-size:12px;">
            Thema voor deze pagina:
        </label>
        <select name="rick_page_theme" id="rick_page_theme" style="width:100%;font-size:12px;">
            <option value="default" <?php selected( $page_theme, 'default' ); ?>>⚙️ Standaard (instellingen)</option>
            <optgroup label="❄️ Winter Thema's">
                <option value="winter-dark" <?php selected( $page_theme, 'winter-dark' ); ?>>🌌 Donkere Winternacht</option>
                <option value="winter-frost" <?php selected( $page_theme, 'winter-frost' ); ?>>❄️ Frosted Winterdag</option>
                <option value="winter-aurora" <?php selected( $page_theme, 'winter-aurora' ); ?>>🌌 Noorderlicht / Aurora</option>
                <option value="snow-only" <?php selected( $page_theme, 'snow-only' ); ?>>❄️ Alleen Sneeuweffect</option>
            </optgroup>
            <optgroup label="🍂 Herfst Thema's">
                <option value="autumn-gold" <?php selected( $page_theme, 'autumn-gold' ); ?>>🍁 Gouden Herfstzon (Amber)</option>
                <option value="autumn-dark" <?php selected( $page_theme, 'autumn-dark' ); ?>>🍂 Donker Herfstbos & Koper</option>
                <option value="autumn-pumpkin" <?php selected( $page_theme, 'autumn-pumpkin' ); ?>>🎃 Pompoen & Kaneel</option>
                <option value="leaves-only" <?php selected( $page_theme, 'leaves-only' ); ?>>🍂 Alleen Vallende Bladeren</option>
            </optgroup>
            <option value="disabled" <?php selected( $page_theme, 'disabled' ); ?>>🚫 Geen effect / Uitgeschakeld</option>
        </select>
    </div>
    <?php
}

function rick_winter_snow_save_meta_box( $post_id ) {
    if ( ! isset( $_POST['rick_snow_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['rick_snow_meta_box_nonce'], 'rick_save_snow_meta_box' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( ! empty( $_POST['rick_enable_snow'] ) ) {
        update_post_meta( $post_id, 'rick_enable_snow', '1' );
    } else {
        delete_post_meta( $post_id, 'rick_enable_snow' );
    }

    if ( isset( $_POST['rick_page_theme'] ) ) {
        $allowed = array(
            'default', 'winter-dark', 'winter-frost', 'winter-aurora', 'snow-only',
            'autumn-gold', 'autumn-dark', 'autumn-pumpkin', 'leaves-only', 'disabled'
        );
        $theme = in_array( $_POST['rick_page_theme'], $allowed, true ) ? sanitize_text_field( $_POST['rick_page_theme'] ) : 'default';
        update_post_meta( $post_id, 'rick_page_theme', $theme );
    }
}

/**
 * 5. Eigen optiepagina in het WordPress admin menu.
 */
add_action( 'admin_menu', 'rick_winter_snow_register_admin_menu' );
add_action( 'admin_init', 'rick_winter_snow_register_settings' );

function rick_winter_snow_register_admin_menu() {
    add_menu_page(
        'Seizoens Thema\'s & Effecten',
        '❄️🍂 Seizoenen',
        'manage_options',
        'rick-winter-snow',
        'rick_winter_snow_render_settings_page',
        'dashicons-palmtree',
        30
    );
}

function rick_winter_snow_register_settings() {
    register_setting(
        'rick_winter_snow_settings_group',
        'rick_winter_snow_options',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'rick_winter_snow_sanitize_options',
        )
    );
}

function rick_winter_snow_sanitize_options( $input ) {
    $clean       = array();
    $flat_themes = array_keys( rick_winter_snow_get_flat_themes() );

    // Winter instellingen
    $clean['winter_enabled']        = ! empty( $input['winter_enabled'] ) ? '1' : '0';
    $clean['winter_scope']          = ( isset( $input['winter_scope'] ) && $input['winter_scope'] === 'all' ) ? 'all' : 'specific';
    $clean['winter_default_theme']  = ( isset( $input['winter_default_theme'] ) && in_array( $input['winter_default_theme'], $flat_themes, true ) )
        ? $input['winter_default_theme']
        : 'winter-dark';
    $clean['winter_post_types']     = array();
    if ( ! empty( $input['winter_post_types'] ) && is_array( $input['winter_post_types'] ) ) {
        foreach ( $input['winter_post_types'] as $pt ) {
            $clean['winter_post_types'][] = sanitize_key( $pt );
        }
    }
    $clean['winter_pages']          = array();
    if ( ! empty( $input['winter_pages'] ) && is_array( $input['winter_pages'] ) ) {
        foreach ( $input['winter_pages'] as $pid ) {
            $clean['winter_pages'][] = absint( $pid );
        }
    }
    $clean['snow_flakes_amount']    = ( isset( $input['snow_flakes_amount'] ) && in_array( $input['snow_flakes_amount'], array( 'light', 'normal', 'heavy' ), true ) )
        ? $input['snow_flakes_amount']
        : 'normal';
    $clean['snow_follow_mouse']     = ! empty( $input['snow_follow_mouse'] ) ? '1' : '0';

    // Herfst instellingen
    $clean['autumn_enabled']        = ! empty( $input['autumn_enabled'] ) ? '1' : '0';
    $clean['autumn_scope']          = ( isset( $input['autumn_scope'] ) && $input['autumn_scope'] === 'all' ) ? 'all' : 'specific';
    $clean['autumn_default_theme']  = ( isset( $input['autumn_default_theme'] ) && in_array( $input['autumn_default_theme'], $flat_themes, true ) )
        ? $input['autumn_default_theme']
        : 'autumn-gold';
    $clean['autumn_post_types']     = array();
    if ( ! empty( $input['autumn_post_types'] ) && is_array( $input['autumn_post_types'] ) ) {
        foreach ( $input['autumn_post_types'] as $pt ) {
            $clean['autumn_post_types'][] = sanitize_key( $pt );
        }
    }
    $clean['autumn_pages']          = array();
    if ( ! empty( $input['autumn_pages'] ) && is_array( $input['autumn_pages'] ) ) {
        foreach ( $input['autumn_pages'] as $pid ) {
            $clean['autumn_pages'][] = absint( $pid );
        }
    }
    $clean['leaves_amount']         = ( isset( $input['leaves_amount'] ) && in_array( $input['leaves_amount'], array( 'light', 'normal', 'heavy' ), true ) )
        ? $input['leaves_amount']
        : 'normal';
    $clean['leaves_follow_mouse']   = ! empty( $input['leaves_follow_mouse'] ) ? '1' : '0';

    // Thema per berichttype
    $clean['cpt_themes'] = array();
    if ( ! empty( $input['cpt_themes'] ) && is_array( $input['cpt_themes'] ) ) {
        foreach ( $input['cpt_themes'] as $pt => $theme ) {
            $pt_clean    = sanitize_key( $pt );
            $theme_clean = sanitize_text_field( $theme );
            if ( in_array( $theme_clean, array_merge( array( 'default', 'disabled' ), $flat_themes ), true ) ) {
                $clean['cpt_themes'][ $pt_clean ] = $theme_clean;
            }
        }
    }

    // Thema per specifieke pagina
    $clean['page_themes'] = array();
    if ( ! empty( $input['page_themes'] ) && is_array( $input['page_themes'] ) ) {
        foreach ( $input['page_themes'] as $page_id => $theme ) {
            $pid_clean   = absint( $page_id );
            $theme_clean = sanitize_text_field( $theme );
            if ( in_array( $theme_clean, array_merge( array( 'default', 'disabled' ), $flat_themes ), true ) ) {
                $clean['page_themes'][ $pid_clean ] = $theme_clean;
            }
        }
    }

    return $clean;
}

/**
 * 6. Weergave van de eigen optiepagina met tabs in het WordPress Dashboard.
 */
function rick_winter_snow_render_settings_page() {
    $options        = rick_winter_snow_get_options();
    $grouped_themes = rick_winter_snow_get_all_themes();
    $winter_themes  = $grouped_themes['winter'];
    $autumn_themes  = $grouped_themes['autumn'];

    $winter_enabled = ! empty( $options['winter_enabled'] );
    $winter_scope   = $options['winter_scope'];
    $winter_default = $options['winter_default_theme'];
    $winter_cpts    = (array) $options['winter_post_types'];
    $winter_pages   = (array) $options['winter_pages'];
    $snow_flakes    = $options['snow_flakes_amount'];
    $snow_mouse     = $options['snow_follow_mouse'];

    $autumn_enabled = ! empty( $options['autumn_enabled'] );
    $autumn_scope   = $options['autumn_scope'];
    $autumn_default = $options['autumn_default_theme'];
    $autumn_cpts    = (array) $options['autumn_post_types'];
    $autumn_pages   = (array) $options['autumn_pages'];
    $leaves_amount  = $options['leaves_amount'];
    $leaves_mouse   = $options['leaves_follow_mouse'];

    $cpt_themes     = (array) $options['cpt_themes'];
    $page_themes    = (array) $options['page_themes'];

    // Haal openbare post types op
    $public_cpts = get_post_types( array( 'public' => true ), 'objects' );
    unset( $public_cpts['attachment'] );

    // Haal gepubliceerde pagina's op
    $all_pages = get_pages( array(
        'post_status' => 'publish',
        'sort_column' => 'post_title',
        'sort_order'  => 'ASC',
    ) );
    ?>
    <div class="wrap rick-seasons-admin-wrap" style="max-width: 1080px; margin: 20px 20px 40px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
        
        <!-- Header Banner -->
        <div style="background: linear-gradient(135deg, #080d1a 0%, #152238 35%, #9a3412 75%, #b45309 100%); color: #ffffff; padding: 26px 32px; border-radius: 14px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3); margin-bottom: 22px; position: relative; overflow: hidden;">
            <div style="position: absolute; right: 25px; top: 12px; font-size: 65px; opacity: 0.2; user-select: none;">❄️🍂</div>
            <h1 style="color: #ffffff; margin: 0 0 8px 0; font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                <span>❄️🍂</span> Seizoens Thema's & Effecten (Winter & Herfst)
            </h1>
            <p style="color: #fed7aa; font-size: 14px; margin: 0; max-width: 720px; line-height: 1.5;">
                Beide seizoenen kunnen <strong>gelijktijdig actief</strong> zijn! Kies bijvoorbeeld de <strong>herfstbladeren voor pepernoten</strong> en <strong>sneeuw voor oliebollen</strong>.
            </p>
        </div>

        <?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
            <div class="notice notice-success is-dismissible" style="border-radius: 8px; border-left-color: #ea580c;">
                <p><strong>🎉 Instellingen succesvol opgeslagen!</strong> De seizoens- en pagina-instellingen zijn direct bijgewerkt.</p>
            </div>
        <?php endif; ?>

        <!-- Navigatietabs -->
        <nav class="nav-tab-wrapper" style="margin-bottom: 22px; border-bottom: 2px solid #cbd5e1;">
            <a href="#tab-matrix" id="tab-btn-matrix" class="nav-tab nav-tab-active" onclick="rickSwitchTab(event, 'matrix');" style="font-size: 14px; font-weight: 600; padding: 8px 16px;">
                📄 Welk Thema op Welke Pagina? (Toewijzing)
            </a>
            <a href="#tab-winter" id="tab-btn-winter" class="nav-tab" onclick="rickSwitchTab(event, 'winter');" style="font-size: 14px; font-weight: 600; padding: 8px 16px;">
                ❄️ Winter Sneeuw & Thema's
            </a>
            <a href="#tab-autumn" id="tab-btn-autumn" class="nav-tab" onclick="rickSwitchTab(event, 'autumn');" style="font-size: 14px; font-weight: 600; padding: 8px 16px;">
                🍂 Herfst Bladeren & Thema's
            </a>
        </nav>

        <form method="post" action="options.php">
            <?php settings_fields( 'rick_winter_snow_settings_group' ); ?>

            <!-- ============================================================ -->
            <!-- TAB 1: TOEWIJZING (DE MATRIX: HERFST EN WINTER GELIJKTIJDIG) -->
            <!-- ============================================================ -->
            <div id="rick-tab-matrix" class="rick-tab-pane">
                
                <!-- Info Banner -->
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:14px 18px; margin-bottom:20px; color:#1e40af; font-size:13px; display:flex; align-items:center; gap:10px;">
                    <span style="font-size:20px;">💡</span>
                    <span>Hieronder stel je in welk effect en thema actief is per berichttype en pagina. Zo kun je <strong>Herfst</strong> aanzetten voor Pepernoten en <strong>Winter</strong> voor Oliebollen!</span>
                </div>

                <!-- Thema per Berichttype -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <h2 style="font-size:18px; margin-top:0; margin-bottom:6px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                        <span>📑</span> 1. Thema per Berichttype toewijzen
                    </h2>
                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Kies direct per berichttype welk seizoen en thema getoond moet worden.
                    </p>

                    <table class="widefat striped" style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th style="padding:12px 16px; font-weight:600;">Berichttype</th>
                                <th style="padding:12px 16px; font-weight:600;">Slug</th>
                                <th style="padding:12px 16px; font-weight:600; width:400px;">Toegewezen Thema / Effect</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $public_cpts as $pt_slug => $pt_obj ) : 
                                $pt_theme = $cpt_themes[ $pt_slug ] ?? 'default';
                            ?>
                                <tr>
                                    <td style="padding:12px 16px; vertical-align:middle; font-weight:600; color:#0f172a;">
                                        <?php echo esc_html( $pt_obj->labels->name ); ?>
                                    </td>
                                    <td style="padding:12px 16px; vertical-align:middle; color:#64748b; font-family:monospace; font-size:12px;">
                                        <code><?php echo esc_html( $pt_slug ); ?></code>
                                    </td>
                                    <td style="padding:12px 16px; vertical-align:middle;">
                                        <select name="rick_winter_snow_options[cpt_themes][<?php echo esc_attr( $pt_slug ); ?>]" style="width:100%; font-weight:500;">
                                            <option value="default" <?php selected( $pt_theme, 'default' ); ?>>⚙️ Standaard (geen specifiek effect)</option>
                                            
                                            <optgroup label="🍂 Herfst Thema's & Vallende Bladeren">
                                                <?php foreach ( $autumn_themes as $t_slug => $t_info ) : ?>
                                                    <option value="<?php echo esc_attr( $t_slug ); ?>" <?php selected( $pt_theme, $t_slug ); ?>>
                                                        🍂 <?php echo esc_html( $t_info['label'] ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>

                                            <optgroup label="❄️ Winter Thema's & Sneeuweffect">
                                                <?php foreach ( $winter_themes as $t_slug => $t_info ) : ?>
                                                    <option value="<?php echo esc_attr( $t_slug ); ?>" <?php selected( $pt_theme, $t_slug ); ?>>
                                                        ❄️ <?php echo esc_html( $t_info['label'] ); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>

                                            <option value="disabled" <?php selected( $pt_theme, 'disabled' ); ?>>🚫 Geen effect / Uitgeschakeld</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Thema per Individuele Pagina toewijzen -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:6px;">
                        <h2 style="font-size:18px; margin:0; color:#0f172a; display:flex; align-items:center; gap:8px;">
                            <span>📄</span> 2. Thema per Specifieke Pagina toewijzen
                        </h2>
                        <input type="text" id="rickPageSearch" placeholder="🔍 Filter pagina's..." style="padding:6px 12px; border:1px solid #cbd5e1; border-radius:6px; font-size:13px; width:220px;" onkeyup="rickFilterPages();" />
                    </div>
                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Wijs hier per pagina direct een specifiek winter- of herfstthema toe.
                    </p>

                    <div style="max-height: 480px; overflow-y: auto; border:1px solid #e2e8f0; border-radius:8px;">
                        <table class="widefat striped" id="rickPagesTable" style="margin:0;">
                            <thead>
                                <tr style="background:#f8fafc; position:sticky; top:0; z-index:2;">
                                    <th style="padding:10px 14px; font-weight:600;">Paginatitel</th>
                                    <th style="padding:10px 14px; font-weight:600; width:120px;">Acties</th>
                                    <th style="padding:10px 14px; font-weight:600; width:380px;">Thema / Effect voor deze pagina</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ( ! empty( $all_pages ) ) : ?>
                                    <?php foreach ( $all_pages as $page ) : 
                                        $p_id      = $page->ID;
                                        $p_theme   = $page_themes[ $p_id ] ?? 'default';
                                        $is_front  = ( (int) get_option( 'page_on_front' ) === $p_id );
                                    ?>
                                        <tr class="rick-page-row" data-title="<?php echo esc_attr( strtolower( $page->post_title ) ); ?>">
                                            <td style="padding:10px 14px; vertical-align:middle;">
                                                <strong style="color:#0f172a;"><?php echo esc_html( $page->post_title ); ?></strong>
                                                <?php if ( $is_front ) : ?>
                                                    <span style="background:#e0f2fe; color:#0369a1; font-size:11px; font-weight:600; padding:2px 6px; border-radius:4px; margin-left:6px;">Homepage</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding:10px 14px; vertical-align:middle; font-size:12px;">
                                                <a href="<?php echo esc_url( get_permalink( $p_id ) ); ?>" target="_blank" style="text-decoration:none; color:#0284c7;">Bekijk ↗</a>
                                                <span style="color:#cbd5e1;"> | </span>
                                                <a href="<?php echo esc_url( get_edit_post_link( $p_id ) ); ?>" target="_blank" style="text-decoration:none; color:#64748b;">Bewerk</a>
                                            </td>
                                            <td style="padding:10px 14px; vertical-align:middle;">
                                                <select name="rick_winter_snow_options[page_themes][<?php echo esc_attr( $p_id ); ?>]" style="width:100%; font-weight:500;">
                                                    <option value="default" <?php selected( $p_theme, 'default' ); ?>>⚙️ Volg berichttype instelling</option>
                                                    
                                                    <optgroup label="🍂 Herfst Thema's">
                                                        <?php foreach ( $autumn_themes as $t_slug => $t_info ) : ?>
                                                            <option value="<?php echo esc_attr( $t_slug ); ?>" <?php selected( $p_theme, $t_slug ); ?>>
                                                                🍂 <?php echo esc_html( $t_info['label'] ); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>

                                                    <optgroup label="❄️ Winter Thema's">
                                                        <?php foreach ( $winter_themes as $t_slug => $t_info ) : ?>
                                                            <option value="<?php echo esc_attr( $t_slug ); ?>" <?php selected( $p_theme, $t_slug ); ?>>
                                                                ❄️ <?php echo esc_html( $t_info['label'] ); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </optgroup>

                                                    <option value="disabled" <?php selected( $p_theme, 'disabled' ); ?>>🚫 Geen effect op deze pagina</option>
                                                </select>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="3" style="padding:16px; text-align:center; color:#64748b;">Geen pagina's gevonden.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ============================================================ -->
            <!-- TAB 2: WINTER SNEEUW & THEMA'S                               -->
            <!-- ============================================================ -->
            <div id="rick-tab-winter" class="rick-tab-pane" style="display:none;">
                
                <!-- Winter Activatie & Scope -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <h2 style="font-size:18px; margin:0; color:#0f172a; display:flex; align-items:center; gap:8px;">
                            <span>❄️</span> Winter Sneeuw Module
                        </h2>
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:700; color:#0284c7;">
                            <input type="checkbox" name="rick_winter_snow_options[winter_enabled]" value="1" <?php checked( $winter_enabled ); ?> />
                            <span>Winter Module Actief</span>
                        </label>
                    </div>

                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Kies waar het wintereffect van toepassing is als er geen specifieke pagina-instelling is.
                    </p>

                    <div style="display:flex; gap:20px; flex-wrap:wrap;">
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                            <input type="radio" name="rick_winter_snow_options[winter_scope]" value="specific" <?php checked( $winter_scope, 'specific' ); ?> />
                            <span>Specifieke berichttypen of pagina's (Aanbevolen)</span>
                        </label>
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                            <input type="radio" name="rick_winter_snow_options[winter_scope]" value="all" <?php checked( $winter_scope, 'all' ); ?> />
                            <span>Hele website (standaard op alle pagina's)</span>
                        </label>
                    </div>

                    <div style="margin-top:16px; padding-top:16px; border-top:1px solid #e2e8f0;">
                        <strong style="display:block; font-size:13px; margin-bottom:8px; color:#0f172a;">Berichttypen die standaard Winter krijgen:</strong>
                        <div style="display:flex; gap:16px; flex-wrap:wrap;">
                            <?php foreach ( $public_cpts as $pt_slug => $pt_obj ) : ?>
                                <label style="display:inline-flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                    <input type="checkbox" name="rick_winter_snow_options[winter_post_types][]" value="<?php echo esc_attr( $pt_slug ); ?>" <?php checked( in_array( $pt_slug, $winter_cpts, true ) ); ?> />
                                    <span><?php echo esc_html( $pt_obj->labels->name ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Winter Thema Keuze -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <h2 style="font-size:18px; margin-top:0; margin-bottom:6px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                        <span>🎨</span> Standaard Winterthema
                    </h2>
                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Kies het winterthema dat gebruikt wordt voor pagina's met het wintereffect.
                    </p>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <?php foreach ( $winter_themes as $t_slug => $t_info ) : 
                            $is_checked = ( $winter_default === $t_slug );
                        ?>
                            <label style="display:block; padding:16px; border:2px solid <?php echo $is_checked ? '#0284c7' : '#e2e8f0'; ?>; border-radius:10px; cursor:pointer; background:<?php echo $is_checked ? '#f0f9ff' : '#fafafa'; ?>; transition:all 0.2s ease;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <input type="radio" name="rick_winter_snow_options[winter_default_theme]" value="<?php echo esc_attr( $t_slug ); ?>" <?php checked( $is_checked ); ?> />
                                        <strong style="font-size:13px; color:#0f172a;"><?php echo esc_html( $t_info['name'] ); ?></strong>
                                    </div>
                                    <span style="display:inline-block; width:16px; height:16px; border-radius:50%; background:<?php echo esc_attr( $t_info['bg_preview'] ); ?>; border:1px solid #cbd5e1;"></span>
                                </div>
                                <p style="font-size:12px; color:#64748b; margin:0; line-height:1.4;">
                                    <?php echo esc_html( $t_info['description'] ); ?>
                                </p>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Winter Sneeuweffect Instellingen -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <h2 style="font-size:18px; margin-top:0; margin-bottom:6px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                        <span>⚙️</span> Sneeuw Effect Instellingen (Snow Falling JS)
                    </h2>
                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Snelheid en hoeveelheid van de vallende sneeuwvlokken.
                    </p>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                        <div>
                            <label for="rick_snow_amount" style="display:block; font-weight:600; color:#0f172a; margin-bottom:6px; font-size:13px;">
                                Sneeuwval Intensiteit:
                            </label>
                            <select name="rick_winter_snow_options[snow_flakes_amount]" id="rick_snow_amount" style="width:100%; max-width:320px;">
                                <option value="light" <?php selected( $snow_flakes, 'light' ); ?>>❄️ Rustig (48 vlokken)</option>
                                <option value="normal" <?php selected( $snow_flakes, 'normal' ); ?>>❄️❄️ Normaal (96 vlokken)</option>
                                <option value="heavy" <?php selected( $snow_flakes, 'heavy' ); ?>>❄️❄️❄️ Hevig (160 vlokken)</option>
                            </select>
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; color:#0f172a; margin-bottom:6px; font-size:13px;">
                                Muis Interactie:
                            </label>
                            <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:#334155; margin-top:6px;">
                                <input type="checkbox" name="rick_winter_snow_options[snow_follow_mouse]" value="1" <?php checked( $snow_mouse, '1' ); ?> />
                                <span>Sneeuwvlokken waaien mee met de muiscursor (follow mouse)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- TAB 3: HERFST BLADEREN & THEMA'S                            -->
            <!-- ============================================================ -->
            <div id="rick-tab-autumn" class="rick-tab-pane" style="display:none;">
                
                <!-- Herfst Activatie & Scope -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                        <h2 style="font-size:18px; margin:0; color:#0f172a; display:flex; align-items:center; gap:8px;">
                            <span>🍂</span> Herfst Bladeren Module
                        </h2>
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:700; color:#ea580c;">
                            <input type="checkbox" name="rick_winter_snow_options[autumn_enabled]" value="1" <?php checked( $autumn_enabled ); ?> />
                            <span>Herfst Module Actief</span>
                        </label>
                    </div>

                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Kies waar het herfsteffect van toepassing is als er geen specifieke pagina-instelling is.
                    </p>

                    <div style="display:flex; gap:20px; flex-wrap:wrap;">
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                            <input type="radio" name="rick_winter_snow_options[autumn_scope]" value="specific" <?php checked( $autumn_scope, 'specific' ); ?> />
                            <span>Specifieke berichttypen of pagina's (Aanbevolen)</span>
                        </label>
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                            <input type="radio" name="rick_winter_snow_options[autumn_scope]" value="all" <?php checked( $autumn_scope, 'all' ); ?> />
                            <span>Hele website (standaard op alle pagina's)</span>
                        </label>
                    </div>

                    <div style="margin-top:16px; padding-top:16px; border-top:1px solid #e2e8f0;">
                        <strong style="display:block; font-size:13px; margin-bottom:8px; color:#0f172a;">Berichttypen die standaard Herfst krijgen:</strong>
                        <div style="display:flex; gap:16px; flex-wrap:wrap;">
                            <?php foreach ( $public_cpts as $pt_slug => $pt_obj ) : ?>
                                <label style="display:inline-flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                                    <input type="checkbox" name="rick_winter_snow_options[autumn_post_types][]" value="<?php echo esc_attr( $pt_slug ); ?>" <?php checked( in_array( $pt_slug, $autumn_cpts, true ) ); ?> />
                                    <span><?php echo esc_html( $pt_obj->labels->name ); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Herfst Thema Keuze -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <h2 style="font-size:18px; margin-top:0; margin-bottom:6px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                        <span>🎨</span> Standaard Herfstthema
                    </h2>
                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Kies het herfstthema dat getoond wordt voor pagina's met het herfsteffect.
                    </p>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                        <?php foreach ( $autumn_themes as $t_slug => $t_info ) : 
                            $is_checked = ( $autumn_default === $t_slug );
                        ?>
                            <label style="display:block; padding:16px; border:2px solid <?php echo $is_checked ? '#ea580c' : '#e2e8f0'; ?>; border-radius:10px; cursor:pointer; background:<?php echo $is_checked ? '#fff7ed' : '#fafafa'; ?>; transition:all 0.2s ease;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:10px;">
                                    <div style="display:flex; align-items:center; gap:8px;">
                                        <input type="radio" name="rick_winter_snow_options[autumn_default_theme]" value="<?php echo esc_attr( $t_slug ); ?>" <?php checked( $is_checked ); ?> />
                                        <strong style="font-size:13px; color:#0f172a;"><?php echo esc_html( $t_info['name'] ); ?></strong>
                                    </div>
                                    <span style="display:inline-block; width:16px; height:16px; border-radius:50%; background:<?php echo esc_attr( $t_info['bg_preview'] ); ?>; border:1px solid #cbd5e1;"></span>
                                </div>
                                <p style="font-size:12px; color:#64748b; margin:0; line-height:1.4;">
                                    <?php echo esc_html( $t_info['description'] ); ?>
                                </p>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Vallende Bladeren Instellingen -->
                <div class="rick-admin-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:24px 28px; margin-bottom:24px; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <h2 style="font-size:18px; margin-top:0; margin-bottom:6px; color:#0f172a; display:flex; align-items:center; gap:8px;">
                        <span>🍁</span> Vallende Bladeren Effect Instellingen
                    </h2>
                    <p style="color:#64748b; font-size:13px; margin-top:0; margin-bottom:18px;">
                        Gedetailleerde esdoorn-, eiken- en beukenbladeren in warme herfsttinten (rood, oranje, goud en kastanje) die natuurgetrouw dwarrelen over de pagina.
                    </p>

                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                        <div>
                            <label for="rick_leaves_amount" style="display:block; font-weight:600; color:#0f172a; margin-bottom:6px; font-size:13px;">
                                Bladeren Dichtheid:
                            </label>
                            <select name="rick_winter_snow_options[leaves_amount]" id="rick_leaves_amount" style="width:100%; max-width:320px;">
                                <option value="light" <?php selected( $leaves_amount, 'light' ); ?>>🍂 Rustig (16 dwarrelende bladeren)</option>
                                <option value="normal" <?php selected( $leaves_amount, 'normal' ); ?>>🍂🍂 Normaal (32 dwarrelende bladeren)</option>
                                <option value="heavy" <?php selected( $leaves_amount, 'heavy' ); ?>>🍂🍂🍂 Herfststorm (50 dwarrelende bladeren)</option>
                            </select>
                        </div>

                        <div>
                            <label style="display:block; font-weight:600; color:#0f172a; margin-bottom:6px; font-size:13px;">
                                Muis Interactie:
                            </label>
                            <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:#334155; margin-top:6px;">
                                <input type="checkbox" name="rick_winter_snow_options[leaves_follow_mouse]" value="1" <?php checked( $leaves_mouse, '1' ); ?> />
                                <span>Bladeren wapperen zachtjes weg bij beweging van de muiscursor</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                <span style="color:#64748b; font-size:13px;">Vergeet niet je wijzigingen op te slaan:</span>
                <?php submit_button( 'Instellingen Opslaan', 'primary large', 'submit', false ); ?>
            </div>
        </form>

        <script>
        function rickSwitchTab(e, tabId) {
            e.preventDefault();
            document.querySelectorAll('.rick-tab-pane').forEach(function(pane) {
                pane.style.display = 'none';
            });
            document.querySelectorAll('.nav-tab').forEach(function(tab) {
                tab.classList.remove('nav-tab-active');
            });
            var targetPane = document.getElementById('rick-tab-' + tabId);
            var targetTab  = document.getElementById('tab-btn-' + tabId);
            if (targetPane) targetPane.style.display = 'block';
            if (targetTab)  targetTab.classList.add('nav-tab-active');

            try {
                localStorage.setItem('rick_active_season_tab', tabId);
            } catch(err) {}
        }

        document.addEventListener('DOMContentLoaded', function() {
            try {
                var savedTab = localStorage.getItem('rick_active_season_tab');
                if (savedTab && document.getElementById('rick-tab-' + savedTab)) {
                    var tabBtn = document.getElementById('tab-btn-' + savedTab);
                    if (tabBtn) tabBtn.click();
                }
            } catch(err) {}
        });

        function rickFilterPages() {
            var input  = document.getElementById('rickPageSearch');
            var filter = input.value.toLowerCase();
            var rows   = document.querySelectorAll('.rick-page-row');
            rows.forEach(function(row) {
                var title = row.getAttribute('data-title');
                if (title && title.indexOf(filter) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        </script>
    </div>
    <?php
}
