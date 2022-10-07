<?php
if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente
/**
 * This class controls the api request like authentication and sending data via Curl
 * 
 * @author Gabriel Filippi
 * @since 04/10/2022
 */
class SpringBootAPIControll {
    private const LOG_API_SPRING = true;
    private const BASE_API_URL_SPRING = "http://localhost:8080/api/";
    private const API_VERSION_SPRING = "v1/";
    private const ENDPOINT_API_AUTHENTICATION_SPRING = "auth/signin/";
    private const ENDPOINT_API_GENERATEQRCODE_SPRING = "orderUpdates/generateQrCode/";
    private const ENDPOINT_API_SAVEORDERCOPY_SPRING = "orderUpdates/saveOrderCopy/";
    private const AUTH_CRON_TO_UPDATE_ORDER_IN_SPRING = "ADko3ie12em9daslda9MF93mrl3c034krfsa0dasdk";

    private $_authentication_jwt;
    private $_result_api;
    private $_status_api;

    public function __construct() {
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
            'URL' => $this->__get_BASE_API_URL_SPRING()
                        .$this->__get_API_VERSION_SPRING()
                        .$this->__get_ENDPOINT_API_AUTHENTICATION_SPRING(),
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
    
        if($this->__get_LOG_API_SPRING()){
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

    /**
     * getter LOG_API_SPRING
     */
    public function __get_LOG_API_SPRING(){
        return self::LOG_API_SPRING;
    }

    /**
     * getter BASE_API_URL_SPRING
     */
    public function __get_BASE_API_URL_SPRING(){
        return self::BASE_API_URL_SPRING;
    }

    /**
     * getter API_VERSION_SPRING
     */
    public function __get_API_VERSION_SPRING(){
        return self::API_VERSION_SPRING;
    }

    /**
     * getter API_AUTHENTICATION_SPRING
     */
    public function __get_ENDPOINT_API_AUTHENTICATION_SPRING(){
        return self::ENDPOINT_API_AUTHENTICATION_SPRING;
    }

    /**
     * getter ENDPOINT_API_GENERATEQRCODE_SPRING
     */
    public function __get_ENDPOINT_API_GENERATEQRCODE_SPRING(){
        return self::ENDPOINT_API_GENERATEQRCODE_SPRING;
    }

    /**
     * getter ENDPOINT_API_SAVEORDERCOPY_SPRING
     */
    public function __get_ENDPOINT_API_SAVEORDERCOPY_SPRING(){
        return self::ENDPOINT_API_SAVEORDERCOPY_SPRING;
    }

    /**
     * getter AUTH_CRON_TO_UPDATE_ORDER_IN_SPRING
     */
    public function __get_AUTH_CRON_TO_UPDATE_ORDER_IN_SPRING(){
        return self::AUTH_CRON_TO_UPDATE_ORDER_IN_SPRING;
    }

}

new SpringBootAPIControll();