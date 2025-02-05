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