<?php
/**
 * Plugin Name: Connection Woocommerce to Spring Boot - Custom System
 * Plugin URI: https://github.com/gabrielfilippi/spring-boot-api-connection-to-woocommerce
 * Description: This plugin connects woocommerce with Floricultura Filippi's custom system using Spring Boot.
 * Author: Gabriel Filippi
 * Author URI: #
 * Version: 0.1.0
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 */

if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente

define("LOG_API_SPRING", true);
define("BASE_API_URL_SPRING", "http://localhost:8080/api/");
define("API_VERSION_SPRING", "v1/");
define("ENDPOINT_API_AUTHENTICATION_SPRING", "auth/signin/");
define("ENDPOINT_API_GENERATEQRCODE_SPRING", "orderUpdates/generateQrCode/");
define("ENDPOINT_API_SAVEORDERCOPY_SPRING", "orderUpdates/saveOrderCopy/");
define("AUTH_CRON_TO_UPDATE_ORDER_IN_SPRING", "ADko3ie12em9daslda9MF93mrl3c034krfsa0dasdk");

require ('spring-boot-api-controll.php');
require ('spring-boot-api-order.php');