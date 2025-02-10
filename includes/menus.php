<?php
//read https://tommcfarlin.com/multiple-sections-on-wordpress-options-pages/
if (!defined('ZARA_URL')) {
    define('ZARA_URL', plugin_dir_url(__FILE__));
}

add_action('admin_menu', 'zara_admin_page');
add_action('admin_init', 'zara_init_settings');
add_action('admin_notices', 'zara_key_warning' );

function zara_admin_page() {
    add_menu_page(
        __('Zara', 'zara-plugin'),//page title
        __('Zara', 'zara-plugin'),//menu title
        'manage_options',//capability
        'zara-menu',//menu slug
        'zara_menu_render_view',//function
        ZARA_URL . 'admin/img/chat.png',//icon url
        10//position
    );
}

function zara_menu_render_view() {
    $options = get_option('zara_options', []);
    $selected_option = isset( $options['dropdown'] ) ? $options['dropdown'] : '';
    $selected_completion_option = isset( $options['completion_dropdown'] ) ? $options['completion_dropdown'] : '';
    $selected_vector_option = isset( $options['vector_dropdown'] ) ? $options['vector_dropdown'] : '';
    
    ?>
    <div class="wrap">
        <h1>Zara Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('zara_settings_group');
            do_settings_sections('zara-settings-page');
            ?>
            <h2>Setting for Embedding Engine</h2>
            <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Select Provider</th>
                        <td>
                            <select id="zara-dropdown" name="zara_options[dropdown]">
                                <option value="">-- Select an Option --</option>
                                <option value="option1" <?php selected( $selected_option, 'option1' ); ?>>Open AI</option>
                                <option value="option2" <?php selected( $selected_option, 'option2' ); ?>>Huggingface</option>
                            </select>
                        </td>
                    </tr>
            </table>

            <!-- Fields for Option 1 -->
            <div id="zara-option1-fields" style="display:none; margin: 15px 0;">
                    <h4>OpenAI Settings</h4>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">API Key</th>
                            <td>
                                <input type="text" name="zara_options[openai_key]" value="<?php echo isset( $options['openai_key'] ) ? esc_attr( $options['openai_key'] ) : ''; ?>" />
                            </td>
                        </tr>
                    </table>
            </div>

            <!-- Fields for Option 2 -->
            <div id="zara-option2-fields" style="display:none; margin: 15px 0;">
                <h3>Huggingface settings available soon</h3>
            </div>

            <!-- setting for completion  -->
            <h2>Setting for Completion Engine</h2>
            <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Select Provider</th>
                        <td>
                            <select id="zara-completion-dropdown" name="zara_options[completion_dropdown]">
                                <option value="">-- Select an Option --</option>
                                <option value="completion_option1" <?php selected( $selected_completion_option, 'completion_option1' ); ?>>Open AI</option>
                                <option value="completion_option2" <?php selected( $selected_completion_option, 'completion_option2' ); ?>>Huggingface</option>
                            </select>
                        </td>
                    </tr>
            </table>

            <!-- Fields for completion Option 1 -->
            <div id="zara-completion-option1-fields" style="display:none; margin: 15px 0;">
                    <h4>OpenAI Settings</h4>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">API Key</th>
                            <td>
                                <input type="text" name="zara_options[openai_key_completion]" value="<?php echo isset( $options['openai_key_completion'] ) ? esc_attr( $options['openai_key_completion'] ) : ''; ?>" />
                            </td>
                        </tr>
                    </table>
            </div>

            <!-- Fields for  completion Option 2 -->
            <div id="zara-completion-option2-fields" style="display:none; margin: 15px 0;">
                <h3>Huggingface settings available soon</h3>
            </div>

            <!-- setting for completion  -->
            <h2>Setting for Vector Storage Engine</h2>
            <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Select Provider</th>
                        <td>
                            <select id="zara-vector-dropdown" name="zara_options[vector_dropdown]">
                                <option value="">-- Select an Option --</option>
                                <option value="vector_option1" <?php selected( $selected_vector_option, 'vector_option1' ); ?>>Pinecone</option>
                                <option value="vector_option2" <?php selected( $selected_vector_option, 'vector_option2' ); ?>>Qdrant</option>
                            </select>
                        </td>
                    </tr>
            </table>

            <!-- Fields for completion Option 1 -->
            <div id="zara-vector-option1-fields" style="display:none; margin: 15px 0;">
                    <h4>Pinecone Settings</h4>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">API Key</th>
                            <td>
                                <input type="text" name="zara_options[pinecone_key]" value="<?php echo isset( $options['pinecone_key'] ) ? esc_attr( $options['pinecone_key'] ) : ''; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">URL</th>
                            <td>
                                <input type="text" name="zara_options[pinecone_index]" value="<?php echo isset( $options['pinecone_index'] ) ? esc_attr( $options['pinecone_index'] ) : ''; ?>" />
                            </td>
                        </tr>
                    </table>
            </div>

            <!-- Fields for  vector Option 2 -->
            <div id="zara-vector-option2-fields" style="display:none; margin: 15px 0;">
                <h3>Qdrant settings available soon</h3>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
            function toggleFields(selector, field1, field2) {
                var selected = $(selector).val();
                if (selected === field1.expected) {
                    $(field1.container).show();
                    $(field2.container).hide();
                } else if (selected === field2.expected) {
                    $(field2.container).show();
                    $(field1.container).hide();
                } else {
                    $(field1.container + ', ' + field2.container).hide();
                }
            }

            toggleFields('#zara-dropdown', { expected: 'option1', container: '#zara-option1-fields' }, { expected: 'option2', container: '#zara-option2-fields' });
            $('#zara-dropdown').change(function() {
                toggleFields('#zara-dropdown', { expected: 'option1', container: '#zara-option1-fields' }, { expected: 'option2', container: '#zara-option2-fields' });
            });

            toggleFields('#zara-completion-dropdown', { expected: 'completion_option1', container: '#zara-completion-option1-fields' }, { expected: 'completion_option2', container: '#zara-completion-option2-fields' });
            $('#zara-completion-dropdown').change(function() {
                toggleFields('#zara-completion-dropdown', { expected: 'completion_option1', container: '#zara-completion-option1-fields' }, { expected: 'completion_option2', container: '#zara-completion-option2-fields' });
            });

            toggleFields('#zara-vector-dropdown', { expected: 'vector_option1', container: '#zara-vector-option1-fields' }, { expected: 'vector_option2', container: '#zara-vector-option2-fields' });
            $('#zara-vector-dropdown').change(function() {
                toggleFields('#zara-vector-dropdown', { expected: 'vector_option1', container: '#zara-vector-option1-fields' }, { expected: 'vector_option2', container: '#zara-vector-option2-fields' });
            });
        });
    </script>
    <?php
}

function zara_init_settings() {
    // Register settings with sanitization callbacks
    register_setting(
        'zara_settings_group',
        'zara_options',
        ['sanitize_callback' => 'zara_sanitize_options']
    );


    register_setting( 'zara_settings_group', 
        'zara_options',
        ['sanitize_callback' => 'zara_sanitize_options'] );

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
    $options = get_option('zara_options', []);
    echo '<input type="text" name="zara_options[openai_key]" 
          value="' . esc_attr($options['openai_key'] ?? '') . '" 
          class="regular-text">';
}

function render_pinecone_key_field() {
    $options = get_option('zara_options', []);
    echo '<input type="text" name="zara_options[pinecone_key]" 
          value="' . esc_attr($options['pinecone_key'] ?? '') . '" 
          class="regular-text">';
}

function render_pinecone_index_field() {
    $options = get_option('zara_options', []);
    echo '<input type="text" name="zara_options[pinecone_index]" 
          value="' . esc_attr($options['pinecone_index'] ?? '') . '" 
          class="regular-text">';
}

function render_message_setting_field() {
    $options = get_option('zara_options', []);
    echo '<input type="text" name="zara_options[zara_welcome_message]" 
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

    if (isset($input['openai_key_completion'])) {
        $sanitized['openai_key_completion'] = sanitize_text_field($input['openai_key_completion']);
    }
    foreach ($input as $key => $value) {
        $sanitized[$key] = sanitize_text_field($value);
    }
    return $sanitized;
}

function zara_key_warning() {
    // Retrieve the options array and check for the pinecone_key
    $options     = get_option( 'zara_options', [] );

    // Check for required keys and display warnings if they are empty.
    display_warning_if_empty(
        $options,
        'openai_key_completion',
        __( 'Warning: Your OpenAI key for completion is missing. Please update your plugin settings.', 'zara-plugin' )
    );
    display_warning_if_empty(
        $options,
        'openai_key',
        __( 'Warning: Your OpenAI key is missing. Please update your plugin settings.', 'zara-plugin' )
    );
    display_warning_if_empty(
        $options,
        'pinecone_index',
        __( 'Warning: Your Pinecone URL key is missing. Please update your plugin settings.', 'zara-plugin' )
    );
    display_warning_if_empty(
        $options,
        'pinecone_key',
        __( 'Warning: Your Pinecone Key key is missing. Please update your plugin settings.', 'zara-plugin' )
    );
}
function display_warning_if_empty( $options, $key, $message ) {
    if ( empty( $options[ $key ] ) ) {
        printf(
            '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
            esc_html( $message )
        );
    }
}