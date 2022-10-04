<?php
if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente

/**
 * This class controls the api request like authentication and sending data via Curl
 * 
 * @author Gabriel Filippi
 * @since 04/10/2022
 */
class SpringBootAPIControll {
    private $_authentication_jwt;
    private $_result_api;
    private $_status_api;
    
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

    /**
     * Function authenticates the Spring application and when it succeeds, it returns a Jwt token so that it is possible to query the API
     * 
     * @since 03/10/2022
     */
    public function __api_authentication(){
        $postData = [
            'userName' => 'gabriel',
            'password' => '123123'
        ];
        $params = [
            'typeCurl' => "POST",
            'URL' => BASE_API_URL_SPRING.API_VERSION_SPRING.ENDPOINT_API_AUTHENTICATION_SPRING,
            'postData' => json_encode($postData),
            'header' => array('Content-Type: application/json')
        ];
        $this->__cur_api($params);
        
        $this->__set_authentication_jwt( $this->__get_result_api() );
    }

    /**
     * Function handles all API requests receiving the necessary parameters for it.
     * 
     * @since 03/10/2022
     */
    public function __cur_api($params){
        // Create a new cURL resource
        $ch = curl_init($params['URL']);
    
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params['postData']);
    
        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, $params['header']);
    
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        if($params['typeCurl'] ==  'POST'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        }else if($params['typeCurl'] ==  'GET'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        }else if($params['typeCurl'] ==  'PUT'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        }else if($params['typeCurl'] ==  'DELETE'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }else{
            error_log(print_r("Define type of CURL (POST, GET, PUT or DELETE)", true));
            return null;
        }
    
        // Execute the POST request
        $result = curl_exec($ch);
    
        // Get the POST request header status
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // If header status is not Created or not OK, return error message
        if ( $status > 201 || $status < 200 ) {
            error_log(print_r("Error: failed to make curl API with status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch), true));
        }
    
        if(LOG_API_SPRING){
            error_log(
                print_r("#API LOG: status $status, response $result, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch), true)
            );
        }
    
        // Close cURL resource
        curl_close($ch);

        $this->__set_result_api($result);
        $this->__set_status_api($status);
    }

    /**
     * getter _authentication_jwt
     */
    public function __get_authentication_jwt(){
        return $this->_authentication_jwt;
    }

    /** 
     * setter _authentication_jwt 
     * 
     */
    public function __set_authentication_jwt($_authentication_jwt){
        $this->_authentication_jwt = $_authentication_jwt;
    }

    /**
     * getter _authentication_status
     * 
     */
    public function __get_status_api(){
        return $this->_status_api;
    }

    /**
     * setter _authentication_status
     * 
     */
    public function __set_status_api($_status_api){
        $this->_status_api = $_status_api;
    }

    /**
     * getter _result_api
     * 
     */
    public function __get_result_api(){
        return $this->_result_api;
    }

    /**
     * setter _result_api 
     * 
     */
    public function __set_result_api($_result_api){
        $this->_result_api = $_result_api;
    }

}

new SpringBootAPIControll();