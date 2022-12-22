<?php
if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente
/**
 * This class is responsible for having the functions to handle order data
 * 
 * @since 04/10/2022
 */
class SpringBootOrder {
    public function __construct() {
        add_action('woocommerce_order_status_changed', [$this, 'generateQrCodeAndShippingStatus'], 10, 4);
    }

    /**
     * When the order is approved/paid, we generate a unique qrCode for it AND set Shipping Status.
     * The QRCode will be used to confirm the order.
     * 
     * @since 01/10/2022
     */
    function generateQrCodeAndShippingStatus($order_id, $old_status, $new_status){
        $wooAPIControll = new SpringBootControll();
        $wooAPIControll->__api_authentication();
        if($new_status == "processing" && ($wooAPIControll->__get_status_api() == 200 || $wooAPIControll->__get_status_api() == 201)){
            update_post_meta($order_id, 'order_shipping_status', 'PEDIDO_APROVADO');
            $params = [
                'typeCurl' => "GET",
                'URL' => $wooAPIControll->__get_BASE_API_URL_SPRING()
                            .$wooAPIControll->__get_API_VERSION_SPRING()
                            .$wooAPIControll->__get_ENDPOINT_API_GENERATEQRCODE_SPRING(),
                'postData' => json_encode([
                    "orderId" => $order_id,
                    "originOrder" => "ecommerce"
                ]),
                'header' => array('Content-Type: application/json', 'Authorization: Bearer ' . $wooAPIControll->__get_authentication_jwt())
            ];
            $wooAPIControll->__cur_api($params);
    
            $resultQR = json_decode($wooAPIControll->__get_result_api(), true);
            if($resultQR['type'] == "success"){
                update_post_meta($order_id, 'qr_code_to_confirm_order', $resultQR['resultObject']);
            }else{
                error_log(print_r($resultQR, true));
            }
        }

        //verify if post meta exists, if not, create new meta key
        $order_priority = get_post_meta($order_id, 'order_priority');
        if($order_priority == null){
            update_post_meta($order_id, 'order_priority', 'NORMAL');
        }
    }

}

new SpringBootOrder();