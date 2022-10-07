<?php
/**
 * Plugin Name: Connection Woocommerce to Spring Boot - Custom System
 * Plugin URI: https://github.com/gabrielfilippi/spring-boot-api-connection-to-woocommerce
 * Description: This plugin connects woocommerce with Floricultura Filippi's custom system using Spring Boot.
 * Author: Gabriel Filippi
 * Author URI: #
 * Version: 0.1.2
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente
require_once ('spring-boot-api-controll.php');
require_once ('spring-boot-api-order.php');
class MainSpringConnection{

    public function __construct() {
        // This hook will run when the plugin is activated and call the activate function
        register_activation_hook(__FILE__, '__spring_boot_API_controll_db');
    }

    /**
     * Create a table in the database so we can save when the last cron access was and look for products that 
     * have been updated since the last cron access.
     * 
     * @since 04/10/2022
     */
    public function __spring_boot_API_controll_db(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
    
        $table_name = $wpdb->prefix . 'controll_orders_last_cron_runned';
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name` (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            last_time_runned_gmt datetime DEFAULT NOW() NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
    
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}
new MainSpringConnection();