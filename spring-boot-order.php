<?php
if (! defined ('ABSPATH')) exit; // Saia se acessado diretamente
/**
 * This class is responsible for having the functions to handle order data
 * 
 * @since 04/10/2022
 */
class SpringBootOrder {
    private $_order_full_data;

    public function __construct() {
        add_action('woocommerce_order_status_changed', [$this, 'generateQrCodeAndShippingStatus'], 10, 4);
    }

    /**
     * It generates the order data and also all the products contained in it.
     * 
     * @since 04/10/2022
     */
    public function __generate_order_data($order_id){
        // Get an instance of the WC_Order object
        $order = wc_get_order( $order_id );
    
        // Get the meta data in an unprotected array
        $response['orderData'] = $order->get_data();
        
        // Get and Loop Over Order Items
        $order_items = array();
        foreach ( $order->get_items() as $item_id => $item ) {
            $product = $item->get_product();
            $order_items[] = (object) array(
                'product_id' => $item->get_product_id(),
                'variation_id' => $item->get_variation_id(),
                'product_name' => $item->get_name(),
                'unitary_price' => $product->get_price(),
                'quantity' => $item->get_quantity(),
                'subtotal' => $item->get_subtotal(),
                'total' => $item->get_total(),
                'tax' => $item->get_subtotal_tax(),
                'tax_class' => $item->get_tax_class(),
                'tax_status' => $item->get_tax_status(),
                'allmeta' => $item->get_meta_data(),
                'somemeta' => $item->get_meta( '_whatever', true ),
                'item_type' => $item->get_type() // e.g. "line_item"
            );
        }
        $response['orderItems'] = $order_items;

        $this->__set_order_full_data($response);
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
            update_post_meta($order_id, 'order_shipping_status', 'PEDIDO_PAGO');
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
    }

    /** 
     * getter _order_full_data
     * 
     */
    public function __get_order_full_data(){
        return $this->_order_full_data;
    }

    /**
     * setter _order_full_data
     * 
     */
    public function __set_order_full_data($_order_full_data){
        $this->_order_full_data = $_order_full_data;
    }

}

new SpringBootOrder();