<?php

/**
* Retrieve the full custom table name for the Zara plugin.
*
* @return string The table name.
*/
function zara_get_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'zara_sync_data';
}
function zara_get_conversations_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'zara_conversations';
}
/**
 * Create or update the custom zara_sync_data table in the database.
 */
function zara_init_database(){
    global $wpdb;
    $table_name = zara_get_table_name();
    $charset_collate = $wpdb->get_charset_collate();
    // SQL for creating table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) UNSIGNED NOT NULL,
        last_synced datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        chunk_total INT(10) UNSIGNED NOT NULL DEFAULT 0,
        PRIMARY KEY  (id),
        UNIQUE KEY post_id (post_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
/**
 * Insert or update a row in zara_sync_data table based on post_id uniqueness.
 *
 * @param int $post_id     The post ID to reference.
 * @param int $chunk_total The chunk total to store for this post ID.
 */
function zara_insert_or_update($post_id,$chunk_total=0){
    global $wpdb;
    $table_name = zara_get_table_name();
    $now = current_time( 'mysql' );

    // This assumes 'post_id' is a UNIQUE key in the table schema
    $wpdb->replace(
        $table_name,
        array(
            'post_id'     => $post_id,
            'last_synced' => $now,
            'chunk_total' => $chunk_total,
        ),
        array(
            '%d', // post_id is an integer
            '%s', // last_synced is a string (datetime)
            '%d' // chunk_total is an integer
        )
    );
}

/**
 * Delete row in zara_sync_data table based on post_id.
 *
 * @param int $post_id The post ID whose record to delete.
 */
function zara_delete_by_post($post_id){
    global $wpdb;
    $table_name = zara_get_table_name();

    $wpdb->delete(
        $table_name,
        array( 'post_id' => $post_id ),
        array( '%d' )
    );
}

/**
 * Retrieve data from the zara_sync_data table for a specific post.
 *
 * @param int $post_id The post ID to look up.
 * @return object|null Returns the row as an object if found, or null if not found.
 */
function zara_get_data_by_post( $post_id ) {
    global $wpdb;
    $table_name = zara_get_table_name();

    // Prepare the query to avoid SQL injection
    $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE post_id = %d LIMIT 1", $post_id ) );

    return $result;
}

function zara_init_conversations_database() {
    global $wpdb;
    $table_name = zara_get_conversations_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        session_id VARCHAR(255) NOT NULL,
        question TEXT NOT NULL,
        retrieved_docs TEXT NOT NULL,
        generated_answer TEXT NOT NULL,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
function zara_store_conversation($session_id, $question, $retrieved_docs, $generated_answer) {
    global $wpdb;
    $table_name = zara_get_conversations_table_name();

    $wpdb->insert(
        $table_name,
        [
            'session_id' => $session_id,
            'question'    => $question,
            'retrieved_docs'    => $retrieved_docs,
            'generated_answer'     => $generated_answer
        ],
        ['%s', '%s', '%s']
    );
}

function zara_conversation_admin_page() {
    global $wpdb;
    $table_name = zara_get_conversations_table_name();
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");
    return $results;
}