<?php
//read https://tommcfarlin.com/multiple-sections-on-wordpress-options-pages/
if (!defined('ZARA_URL')) {
    define('ZARA_URL', plugin_dir_url(__FILE__));
}

add_action('admin_menu', 'zara_admin_page');
add_action('admin_init', 'zara_init_settings');

function zara_admin_page() {
    add_menu_page(
        __('Zara', 'zara-plugin'),
        __('Zara', 'zara-plugin'),
        'manage_options',
        'zara-menu',
        'zara_menu_callback',
        ZARA_URL . 'admin/img/chat.png',
        10
    );
}

function zara_menu_callback() {
    $options = get_option( 'cap_options' );
    $selected_option = isset( $options['dropdown'] ) ? $options['dropdown'] : '';
    ?>
    <div class="wrap">
        <h1>Zara Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('zara_settings_group');
            do_settings_sections('zara-settings-page');
            submit_button();
            ?>
        </form>
        <!-- Add a new form for the custom button -->
        <form method="post">
            <?php wp_nonce_field('zara_sync_action', 'zara_nonce'); ?>
            <input type="submit" name="zara_sync" class="button button-secondary" value="Sync">
        </form>

    
        <div class="wrap">
            <h1>Conditional Admin Page</h1>
            <form method="post" action="options.php">
                <?php 
                    settings_fields( 'cap_options_group' );
                    do_settings_sections( 'cap_options_group' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Select an Option</th>
                        <td>
                            <select id="cap-dropdown" name="cap_options[dropdown]">
                                <option value="">-- Select an Option --</option>
                                <option value="option1" <?php selected( $selected_option, 'option1' ); ?>>Option 1</option>
                                <option value="option2" <?php selected( $selected_option, 'option2' ); ?>>Option 2</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <!-- Fields for Option 1 -->
                <div id="cap-option1-fields" style="display:none; margin: 15px 0;">
                    <h3>Option 1 Settings</h3>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Input for Option 1</th>
                            <td>
                                <input type="text" name="cap_options[option1_input]" value="<?php echo isset( $options['option1_input'] ) ? esc_attr( $options['option1_input'] ) : ''; ?>" />
                            </td>
                        </tr>

                        <tr valign="top">
                            <th scope="row">Input2 for Option 1</th>
                            <td>
                                <input type="text" name="cap_options[option1_input2]" value="<?php echo isset( $options['option1_input2'] ) ? esc_attr( $options['option1_input2'] ) : ''; ?>" />
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Fields for Option 2 -->
                <div id="cap-option2-fields" style="display:none; margin: 15px 0;">
                    <h3>Option 2 Settings</h3>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Input for Option 2</th>
                            <td>
                                <input type="text" name="cap_options[option2_input]" value="<?php echo isset( $options['option2_input'] ) ? esc_attr( $options['option2_input'] ) : ''; ?>" />
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        function toggleFields() {
            var selected = $('#cap-dropdown').val();
            if (selected === 'option1') {
                $('#cap-option1-fields').show();
                $('#cap-option2-fields').hide();
            } else if (selected === 'option2') {
                $('#cap-option2-fields').show();
                $('#cap-option1-fields').hide();
            } else {
                $('#cap-option1-fields, #cap-option2-fields').hide();
            }
        }

        // Run on page load in case an option is already saved
        toggleFields();

        // Bind the change event to the dropdown
        $('#cap-dropdown').change(function() {
            toggleFields();
        });
    });
    </script>
    <?php
}

function zara_init_settings() {
    // Register settings with sanitization callbacks
    register_setting(
        'zara_settings_group',
        'openai_options',
        ['sanitize_callback' => 'zara_sanitize_options']
    );
    register_setting(
        'zara_settings_group',
        'pinecone_options',
        ['sanitize_callback' => 'zara_sanitize_options']
    );

    register_setting(
        'zara_settings_group',
        'zara_message_options',
        ['sanitize_callback' => 'zara_sanitize_options']
    );


    register_setting( 'cap_options_group', 'cap_options' );

    // OpenAPI Settings Section
    add_settings_section(
        'zara_setting_section_openapi',
        'Openai Key Settings',
        'settings_section_openai_callback',
        'zara-settings-page'
    );

    add_settings_field(
        'openai_key_field',
        'API Key',
        'render_openai_key_field',
        'zara-settings-page',
        'zara_setting_section_openapi'
    );

    // Pinecone Settings Section
    add_settings_section(
        'zara_setting_section_pinecone',
        'Pinecone Key Settings',
        'settings_section_pinecone_callback',
        'zara-settings-page'
    );

    add_settings_field(
        'pinecone_key_field',
        'API Key',
        'render_pinecone_key_field',
        'zara-settings-page',
        'zara_setting_section_pinecone'
    );

    add_settings_field(
        'pinecone_index_field',
        'Pinecone Index',
        'render_pinecone_index_field',
        'zara-settings-page',
        'zara_setting_section_pinecone'
    );

    // Message Settings Section
    add_settings_section(
        'zara_setting_section_message',
        'Message Settings',
        'settings_section_message_callback',
        'zara-settings-page'
    );
    add_settings_field(
        'message_setting_field',
        'Welcome Message',
        'render_message_setting_field',
        'zara-settings-page',
        'zara_setting_section_message'
    );
}

function settings_section_openai_callback() {
    echo '<p>Configure OpenAPI settings below:</p>';
}

function settings_section_pinecone_callback() {
    echo '<p>Configure Pinecone settings below:</p>';
}
function settings_section_message_callback() {
    echo '<p>Configure defatult message settings below:</p>';
}


function render_openai_key_field() {
    $options = get_option('openai_options', []);
    echo '<input type="text" name="openai_options[openai_key]" 
          value="' . esc_attr($options['openai_key'] ?? '') . '" 
          class="regular-text">';
}

function render_pinecone_key_field() {
    $options = get_option('pinecone_options', []);
    echo '<input type="text" name="pinecone_options[pinecone_key]" 
          value="' . esc_attr($options['pinecone_key'] ?? '') . '" 
          class="regular-text">';
}

function render_pinecone_index_field() {
    $options = get_option('pinecone_options', []);
    echo '<input type="text" name="pinecone_options[pinecone_index]" 
          value="' . esc_attr($options['pinecone_index'] ?? '') . '" 
          class="regular-text">';
}

function render_message_setting_field() {
    $options = get_option('zara_message_options', []);
    echo '<input type="text" name="zara_message_options[zara_welcome_message]" 
          value="' . esc_attr($options['zara_welcome_message'] ?? '') . '" 
          class="regular-text">';
}

function zara_sanitize_options($input) {
    // Sanitize input here (e.g., trim whitespace, strip tags)
    $sanitized = [];
    if (isset($input['openai_key'])) {
        $sanitized['openai_key'] = sanitize_text_field($input['openai_key']);
    }
    // Sanitize Pinecone values
    if (isset($input['pinecone_key'])) {
        $sanitized['pinecone_key'] = sanitize_text_field($input['pinecone_key']);
    }
    
    if (isset($input['pinecone_index'])) {
        $sanitized['pinecone_index'] = sanitize_text_field($input['pinecone_index']);
    }

    if (isset($input['zara_welcome_message'])) {
        $sanitized['zara_welcome_message'] = sanitize_text_field($input['zara_welcome_message']);
    }
    foreach ($input as $key => $value) {
        $sanitized[$key] = sanitize_text_field($value);
    }
    return $sanitized;
}