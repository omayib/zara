<?php
//read https://tommcfarlin.com/multiple-sections-on-wordpress-options-pages/
if (!defined('ZARA_URL')) {
    define('ZARA_URL', plugin_dir_url(__FILE__));
}

add_action('admin_menu', 'zara_admin_page');
add_action('admin_init', 'zara_init_settings');
add_action('admin_init', 'zara_handle_sync_buttons');
add_action('admin_init', 'zara_handle_test_question_buttons');

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
        <br>
        <form method="post">
            <?php wp_nonce_field('zara_test_action', 'zara_nonce'); ?>
            <input type="text" name="test_question"/>
            <input type="submit" name="zara_test" class="button button-secondary" value="Test">
        </form>
    </div>
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

    // OpenAPI Settings Section
    add_settings_section(
        'zara_setting_section_openapi',
        'OpenAPI Key Settings',
        'settings_section_openapi_callback',
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
}

function zara_handle_test_question_buttons() {
    if (isset($_POST['zara_test'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['zara_nonce'], 'zara_test_action')) {
            wp_die('Security check failed');
        }

        // Get the value of the test_question field
        $test_question = isset($_POST['test_question']) ? sanitize_text_field($_POST['test_question']) : '';

        // Log the test question value
        // error_log('Test Question: ' . $test_question);
        looking_for_answer($test_question);
    }
}
function zara_handle_sync_buttons() {
    if (isset($_POST['zara_sync'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['zara_nonce'], 'zara_sync_action')) {
            wp_die('Security check failed');
        }

        // Log to error log
        error_log('Hello from Zara plugin!');

        // Call external function
        if (function_exists('read_all_content')) {
            read_all_content(); // Call function from utilities.php
        } else {
            error_log('read_all_content function not found!');
        }
        
        // Redirect back to settings page
        // wp_redirect(admin_url('admin.php?page=zara-menu'));
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

function zara_sanitize_options($input) {
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