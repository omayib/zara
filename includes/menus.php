<?php
//read https://tommcfarlin.com/multiple-sections-on-wordpress-options-pages/
if (!defined('TALKTOME_URL')) {
    define('TALKTOME_URL', plugin_dir_url(__FILE__));
}

add_action('admin_menu', 'talktome_admin_page');
add_action('admin_init', 'talktome_init_settings');
add_action('admin_init', 'talktome_handle_sync_buttons');
add_action('admin_init', 'talktome_handle_test_question_buttons');

function talktome_admin_page() {
    add_menu_page(
        __('Talktome', 'talktome-plugin'),
        __('Talktome', 'talktome-plugin'),
        'manage_options',
        'talktome-menu',
        'talktome_menu_callback',
        TALKTOME_URL . 'admin/img/chat.png',
        10
    );
}

function talktome_menu_callback() {
    ?>
    <div class="wrap">
        <h1>Talktome Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('talktome_settings_group');
            do_settings_sections('talktome-settings-page');
            submit_button();
            ?>
        </form>
        <!-- Add a new form for the custom button -->
        <form method="post">
            <?php wp_nonce_field('talktome_sync_action', 'talktome_nonce'); ?>
            <input type="submit" name="talktome_sync" class="button button-secondary" value="Sync">
        </form>
        <br>
        <form method="post">
            <?php wp_nonce_field('talktome_test_action', 'talktome_nonce'); ?>
            <input type="text" name="test_question"/>
            <input type="submit" name="talktome_test" class="button button-secondary" value="Test">
        </form>
    </div>
    <?php
}

function talktome_init_settings() {
    // Register settings with sanitization callbacks
    register_setting(
        'talktome_settings_group',
        'openai_options',
        ['sanitize_callback' => 'talktome_sanitize_options']
    );
    register_setting(
        'talktome_settings_group',
        'pinecone_options',
        ['sanitize_callback' => 'talktome_sanitize_options']
    );

    // OpenAPI Settings Section
    add_settings_section(
        'talktome_setting_section_openapi',
        'OpenAPI Key Settings',
        'settings_section_openapi_callback',
        'talktome-settings-page'
    );

    add_settings_field(
        'openai_key_field',
        'API Key',
        'render_openai_key_field',
        'talktome-settings-page',
        'talktome_setting_section_openapi'
    );

    // Pinecone Settings Section
    add_settings_section(
        'talktome_setting_section_pinecone',
        'Pinecone Key Settings',
        'settings_section_pinecone_callback',
        'talktome-settings-page'
    );

    add_settings_field(
        'pinecone_key_field',
        'API Key',
        'render_pinecone_key_field',
        'talktome-settings-page',
        'talktome_setting_section_pinecone'
    );

    add_settings_field(
        'pinecone_index_field',
        'Pinecone Index',
        'render_pinecone_index_field',
        'talktome-settings-page',
        'talktome_setting_section_pinecone'
    );
}

function talktome_handle_test_question_buttons() {
    if (isset($_POST['talktome_test'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['talktome_nonce'], 'talktome_test_action')) {
            wp_die('Security check failed');
        }

        // Get the value of the test_question field
        $test_question = isset($_POST['test_question']) ? sanitize_text_field($_POST['test_question']) : '';

        // Log the test question value
        // error_log('Test Question: ' . $test_question);
        looking_for_answer($test_question);
    }
}
function talktome_handle_sync_buttons() {
    if (isset($_POST['talktome_sync'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['talktome_nonce'], 'talktome_sync_action')) {
            wp_die('Security check failed');
        }

        // Log to error log
        error_log('Hello from Talktome plugin!');

        // Call external function
        if (function_exists('read_all_content')) {
            read_all_content(); // Call function from utilities.php
        } else {
            error_log('read_all_content function not found!');
        }
        
        // Redirect back to settings page
        // wp_redirect(admin_url('admin.php?page=talktome-menu'));
        // exit;
    }
}

function settings_section_openapi_callback() {
    echo '<p>Configure OpenAPI settings below:</p>';
}

function settings_section_pinecone_callback() {
    echo '<p>Configure Pinecone settings below:</p>';
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

function talktome_sanitize_options($input) {
    // Sanitize input here (e.g., trim whitespace, strip tags)
    $sanitized = [];
    if (isset($input['openai_key'])) {
        $sanitized['openai_key'] = sanitize_text_field($input['openai_key']);
        error_log('openai_key : ' . $sanitized['openai_key']);
    }
    // Sanitize Pinecone values
    if (isset($input['pinecone_key'])) {
        $sanitized['pinecone_key'] = sanitize_text_field($input['pinecone_key']);
    }
    
    if (isset($input['pinecone_index'])) {
        $sanitized['pinecone_index'] = sanitize_text_field($input['pinecone_index']);
    }
    foreach ($input as $key => $value) {
        $sanitized[$key] = sanitize_text_field($value);
    }
    return $sanitized;
}