<?php
//Load WordPress.
require('../../../wp-load.php');

/**
 * This Class is responsible for checking if there were updates in the requests via cron server.
 * We check if since the last time cron ran we haven't had any updates in the requests, if so send the request json to the Spring Boot API
 * 
 * @author Gabriel Filippi
 * @since 04/10/2022
 */
class SpringBootAPICronControllOrders {
    private $_no_errors;
    private $_time_gmt;

    public function __construct() {
        $this->_no_errors = true; 
        $this->_time_gmt = current_time('mysql', 1);
        $this->__run_cron();
    }

    public function __run_cron(){
        if(isset($_GET['auth_cron']) && $_GET['auth_cron'] == AUTH_CRON_TO_UPDATE_ORDER_IN_SPRING){
            global $wpdb;
            
            $wooAPIControll  = new SpringBootAPIControll();
            $wooAPIOrder  = new SpringBootAPIOrder();
        
            $sql = $wpdb->prepare( "SELECT * FROM wp_controll_orders_last_cron_runned ORDER BY id DESC LIMIT 1");
            $resultsArr = $wpdb->get_results( $sql );
        
            $last_time_cron_is_runned = "2022-09-03 01:08:34";
            if(count($resultsArr) > 0 ){
                $last_time_cron_is_runned = $resultsArr[0]->{'last_time_runned_gmt'};
            }
        
            $result_orders = $wpdb->get_results ("
                SELECT * FROM wp_posts
                    WHERE post_type = 'shop_order' AND post_modified_gmt between '$last_time_cron_is_runned' AND '$this->_time_gmt'
            ");
            
            foreach ( $result_orders as $order_post ){
                $order_id = $order_post->ID;
                if($wooAPIControll->__get_authentication_jwt() == ""){
                    $wooAPIControll->__api_authentication();
                }
        
                if($wooAPIControll->__get_status_api() == 200){
                    /**
                     * 
                     * SEND ORDER DATA JSON TO SPRING BOOT
                     * 
                    */
                    $wooAPIOrder->__generate_order_data($order_id);
        
                    $params = [
                        'typeCurl' => "GET",
                        'URL' => BASE_API_URL_SPRING.API_VERSION_SPRING.ENDPOINT_API_SAVEORDERCOPY_SPRING,
                        'postData' => json_encode($wooAPIOrder->__get_order_full_data()),
                        'header' => array('Content-Type: application/json', 'Authorization: Bearer ' . $wooAPIControll->__get_authentication_jwt())
                    ];
        
                    if(LOG_API_SPRING){
                        error_log(
                            print_r($wooAPIOrder->__get_order_full_data(), true)
                        );
                    }
                   
                    $wooAPIControll->__cur_api($params);
                    if($wooAPIControll->__get_status_api() < 200 || $wooAPIControll->__get_status_api() > 201){
                        $this->_no_errors = false;
                    }
                }else{
                    $this->_no_errors = false;
                }
        
            }
        
            if(count($result_orders)>0 && $this->_no_errors){
                if(count($resultsArr) > 0 ){
                    $wpdb->update('wp_controll_orders_last_cron_runned', 
                        array(
                            'last_time_runned_gmt' => $this->_time_gmt
                        ),
                        array(
                            'id' => $resultsArr[0]->{'id'}
                        ),
                        array('%s'),
                        array('%d')
                    );
                }else{
                    $wpdb->insert('wp_controll_orders_last_cron_runned', 
                        array(
                            'last_time_runned_gmt' => $this->_time_gmt
                        ),
                        array(
                            '%s'
                        ) 
                    );
                }
            }
        
            if( !$this->_no_errors ){
                error_log("Não foi possivel atualizar os produtos para o Spring Boot, API Error.");
            }
        }else{
            wp_die("Você não tem permissão para acessar este local.");
        }
    }

}

new SpringBootAPICronControllOrders();