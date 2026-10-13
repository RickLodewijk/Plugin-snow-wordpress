<?php
/**
 * Admin functionaliteit voor Rick Winter Snow plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. ACF Veldengroep registreren voor de zijbalk van elke pagina en CPT
add_action( 'acf/init', 'rick_winter_snow_register_acf_fields' );
function rick_winter_snow_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group( array(
        'key'      => 'group_rick_page_snow',
        'title'    => '❄️ Sneeuw & Winter Layout',
        'fields'   => array(
            array(
                'key'           => 'field_rick_enable_snow',
                'label'         => 'Sneeuw & Winter Thema Inschakelen',
                'name'          => 'rick_enable_snow',
                'type'          => 'true_false',
                'message'       => 'Sneeuw laten vallen & donkere winterse achtergrond inschakelen',
                'default_value' => 0,
                'ui'            => 1,
                'ui_on_text'    => 'Aan',
                'ui_off_text'   => 'Uit',
                'instructions'  => 'Activeert de vallende sneeuw animatie en hult deze pagina in een sfeervolle, donkere winterse setting (CodePen stijl).',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'page',
                ),
            ),
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'recept',
                ),
            ),
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'pepernoot',
                ),
            ),
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'oliebol',
                ),
            ),
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'baktip',
                ),
            ),
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'post',
                ),
            ),
        ),
        'menu_order'      => 15,
        'position'        => 'side',
        'style'           => 'default',
        'label_placement' => 'top',
        'active'          => true,
    ) );
}

// 2. Native Meta Box fallback wanneer ACF niet aanwezig is
add_action( 'add_meta_boxes', 'rick_winter_snow_add_meta_box' );
add_action( 'save_post', 'rick_winter_snow_save_meta_box' );

function rick_winter_snow_add_meta_box() {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
        return; // ACF handelt dit al netjes af
    }

    $screens = array( 'page', 'post', 'recept', 'baktip', 'pepernoot', 'oliebol' );
    foreach ( $screens as $screen ) {
        add_meta_box(
            'rick_snow_meta_box',
            '❄️ Sneeuw & Winter Layout',
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
    ?>
    <div style="padding: 6px 0;">
        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;color:#1e293b;">
            <input type="checkbox" name="rick_enable_snow" value="1" <?php checked( $is_enabled, '1' ); ?> />
            <span>Sneeuw & donkere winter layout inschakelen</span>
        </label>
        <p style="margin: 8px 0 0; font-size: 12px; color: #64748b; line-height: 1.4;">
            Activeert het Snow Falling JS effect en een donkere winterse nacht-layout voor deze pagina.
        </p>
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
}

// 3. Globale instellingenpagina onder Instellingen -> ❄️ Winter Sneeuw
add_action( 'admin_menu', 'rick_winter_snow_register_admin_menu' );
add_action( 'admin_init', 'rick_winter_snow_register_settings' );

function rick_winter_snow_register_admin_menu() {
    add_options_page(
        'Winter Sneeuw & Thema Instellingen',
        '❄️ Winter Sneeuw',
        'manage_options',
        'rick-winter-snow',
        'rick_winter_snow_render_settings_page'
    );
}

function rick_winter_snow_register_settings() {
    register_setting( 'rick_winter_snow_settings_group', 'rick_snow_global_enable' );
}

function rick_winter_snow_render_settings_page() {
    ?>
    <div class="wrap" style="max-width: 800px;">
        <h1 style="display:flex;align-items:center;gap:10px;">
            <span>❄️</span> Winter Sneeuw & Thema Instellingen
        </h1>
        <p style="color:#64748b;font-size:14px;">
            Beheer hier het vallende sneeuweffect en de donkere winterse layout (gebaseerd op <a href="https://codepen.io/massiebn/pen/OJMbXz" target="_blank" rel="noopener">CodePen OJMbXz</a>).
        </p>

        <?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>🎉 Instellingen succesvol opgeslagen!</strong></p>
            </div>
        <?php endif; ?>

        <form method="post" action="options.php" style="background:#fff;padding:24px;border:1px solid #e2e8f0;border-radius:10px;margin-top:20px;">
            <?php settings_fields( 'rick_winter_snow_settings_group' ); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><strong>Globale Activatie</strong></th>
                    <td>
                        <?php $global_val = get_option( 'rick_snow_global_enable', '0' ); ?>
                        <label style="display:inline-flex;align-items:center;gap:8px;font-weight:600;cursor:pointer;">
                            <input type="checkbox" name="rick_snow_global_enable" value="1" <?php checked( $global_val, '1' ); ?> />
                            <span>Laat standaard sneeuw vallen & activeer de donkere winterse layout op álle pagina's</span>
                        </label>
                        <p class="description" style="margin-top:8px;">
                            Wanneer uitgevinkt, kun je het sneeuweffect en de winterse achtergrond per individuele pagina activeren via de zijbalk bij het bewerken van die pagina.
                        </p>
                    </td>
                </tr>
            </table>

            <div style="margin-top:20px;padding-top:15px;border-top:1px solid #e2e8f0;">
                <?php submit_button( 'Instellingen Opslaan' ); ?>
            </div>
        </form>
    </div>
    <?php
}
