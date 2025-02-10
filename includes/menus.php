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
                                <input type="text" name="zara_options[pinecone_key_completion]" value="<?php echo isset( $options['pinecone_key_completion'] ) ? esc_attr( $options['pinecone_key_completion'] ) : ''; ?>" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">URL</th>
                            <td>
                                <input type="text" name="zara_options[pinecone_index_completion]" value="<?php echo isset( $options['pinecone_index_completion'] ) ? esc_attr( $options['pinecone_index_completion'] ) : ''; ?>" />
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
        function toggleFields() {
            var selected = $('#zara-dropdown').val();
            if (selected === 'option1') {
                $('#zara-option1-fields').show();
                $('#zara-option2-fields').hide();
            } else if (selected === 'option2') {
                $('#zara-option2-fields').show();
                $('#zara-option1-fields').hide();
            } else {
                $('#zara-option1-fields, #zara-option2-fields').hide();
            }
        }

        toggleFields();

        $('#zara-dropdown').change(function() {
            toggleFields();
        });

        // toggle for completion engine
        function toggleCompletionFields() {
            var selected = $('#zara-completion-dropdown').val();
            if (selected === 'completion_option1') {
                $('#zara-completion-option1-fields').show();
                $('#zara-completion-option2-fields').hide();
            } else if (selected === 'completion_option2') {
                $('#zara-completion-option2-fields').show();
                $('#zara-completion-option1-fields').hide();
            } else {
                $('#zara-completion-option1-fields, #zara-completion-option2-fields').hide();
            }
        }

        toggleCompletionFields();

        $('#zara-completion-dropdown').change(function() {
            toggleCompletionFields();
        });

        // toggle for vector engine
        function toggleVectorFields() {
            var selected = $('#zara-vector-dropdown').val();
            if (selected === 'vector_option1') {
                $('#zara-vector-option1-fields').show();
                $('#zara-vector-option2-fields').hide();
            } else if (selected === 'vector_option2') {
                $('#zara-vector-option2-fields').show();
                $('#zara-vector-option1-fields').hide();
            } else {
                $('#zara-vector-option1-fields, #zara-vector-option2-fields').hide();
            }
        }

        toggleVectorFields();

        $('#zara-vector-dropdown').change(function() {
            toggleVectorFields();
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
    $pinecone_key = isset( $options['pinecone_key'] ) ? $options['pinecone_key'] : '';
    $openai_key = isset( $options['openai_key'] ) ? $options['openai_key'] : '';
    $pinecone_index = isset( $options['pinecone_index'] ) ? $options['pinecone_index'] : '';

    // If the key is empty, display a warning notice
    if ( empty( $pinecone_key ) ) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( 'Warning: Your Pinecone key is missing. Please update your plugin settings.', 'your-text-domain' ); ?></p>
        </div>
        <?php
    }
    if ( empty( $openai_key ) ) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( 'Warning: Your Openai key is missing. Please update your plugin settings.', 'your-text-domain' ); ?></p>
        </div>
        <?php
    }
    if ( empty( $pinecone_index ) ) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e( 'Warning: Your Pinecone URL key is missing. Please update your plugin settings.', 'your-text-domain' ); ?></p>
        </div>
        <?php
    }
}