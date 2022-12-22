<?php
/**
 * Plugin Name: Connection Woocommerce to Spring Boot - Custom System
 * Plugin URI: https://github.com/gabrielfilippi/spring-boot-api-connection-to-woocommerce
 * Description: This plugin connects woocommerce with Floricultura Filippi's custom system using Spring Boot.
 * Author: Gabriel Filippi
 * Author URI: #
 * Version: 1.2.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente
require_once ('spring-boot-controll.php');
require_once ('spring-boot-order.php');
class MainSpringConnection{

    public function __construct() {
        add_filter('woocommerce_reports_order_statuses', [$this, 'set_order_statuses_in_sales_reports']);
    }

    /**
    * Set order status to get sales reports FROM API.
    * 
    * @since 20/10/2022
    */
    function set_order_statuses_in_sales_reports( $statuses ){
        return array( 'processing', 'completed' );
    }

}
new MainSpringConnection();