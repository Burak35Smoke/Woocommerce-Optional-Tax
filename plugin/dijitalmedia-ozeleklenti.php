<?php
/**
 * Plugin name: Dijital Media - Özel Eklenti
 * Description: Dijital Media - Özel Eklenti
 * Author: bur4ksocial
 * Plugin URI: https://bur4k.social
 * Author URI: https://bur4k.social
 * text-domain: om-service-widget
 */

add_action( 'woocommerce_after_checkout_billing_form', 'bur4ksocial_add_vat_cancel_button' );

function bur4ksocial_add_vat_cancel_button( $checkout ) {
    echo '<div id="vat-cancel">'; 
    
    woocommerce_form_field(
        'bur4ksocial_vat_cancel',
        array(
            'label'  => 'Fatura İstemiyorum',
            'class'  => array( 'vat-cancel-button' ),
            'type'   => 'checkbox'
        ),
        $checkout->get_value( 'bur4ksocial_vat_cancel' )
    );
    
    echo '</div>';    
}

add_action( 'wp_footer', 'bur4ksocial_vat_cancel_ajax' );

function bur4ksocial_vat_cancel_ajax() {
    
    if ( is_checkout() ) {
        ?>
        <script type="text/javascript">
            jQuery( document ).ready(
                function($) {
                    $('#bur4ksocial_vat_cancel').click(
                        function() {
                            jQuery('body').trigger('update_checkout');
                        }    
                    );
                }
            );
        </script>
        <?php
    }
}

function lab_pacakge_cost() {
    
    global $woocommerce;
    
    $flat_fee    = get_option( 'bur4ksocial_kdv_pricing_flat_fee' );
    $dynamic_fee = get_option( 'bur4ksocial_kdv_pricing_dynamic_fee' );
    
    if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
        return;
    }
    
    if ( isset( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    } else {
        $post_data = $_POST;
    }
    
    if ( isset( $post_data['bur4ksocial_vat_cancel'] ) ) {
        return;
    }
    $subtotal = $woocommerce->cart->get_subtotal()/27;
    $taxable =($subtotal/$dynamic_fee)*$flat_fee;

    $woocommerce->cart->add_fee( __( 'KDV Ücreti', 'om-service-widget' ), $taxable );
    
}

add_action( 'woocommerce_cart_calculate_fees', 'lab_pacakge_cost');


add_filter( 'woocommerce_settings_tabs_array', 'bur4ksocial_add_vat_pricing', 50 );

function bur4ksocial_add_vat_pricing( $settings_tab ) {
    
    $settings_tab['bur4ksocial_vat_pricing'] = __( 'KDV Ücreti - Dijital.Media', 'om-service-widget' );
    
    return $settings_tab;
}


add_action( 'woocommerce_settings_tabs_bur4ksocial_vat_pricing', 'bur4ksocial_add_vat_pricing_settings' );

function bur4ksocial_add_vat_pricing_settings() {
    woocommerce_admin_fields( get_bur4ksocial_vat_pricing_settings() );
}

add_action( 'woocommerce_update_options_bur4ksocial_vat_pricing', 'bur4ksocial_update_options_vat_pricing_settings' );

function bur4ksocial_update_options_vat_pricing_settings() {
    woocommerce_update_options( get_bur4ksocial_vat_pricing_settings() );
}

function get_bur4ksocial_vat_pricing_settings() {
    
    $settings = array(
        
        'section_title' => array(
            'id'   => 'bur4ksocial_vat_pricing_settings_title',
            'desc' => 'Fatura Ücreti Ayarları',
            'type' => 'title',
            'name' => 'Fatura Ücreti Ayarları',
        ),
        
        'kdv_pricing_flat_fee' => array(
            'id'   => 'bur4ksocial_kdv_pricing_flat_fee',
            'desc' => 'Vergi Yüzdesi Önerilen : 18',
            'type' => 'text',
            'name' => 'Tax Percentage',
        ),
        
        'kdv_pricing_dynamic_fee' => array(
            'id'   => 'bur4ksocial_kdv_pricing_dynamic_fee',
            'desc' => 'Toplam Yüzde Önerilen : 100',
            'type' => 'text',
            'name' => 'Total Percentage',
        ),
        
        'section_end' => array(
            'id'   => 'bur4ksocial_vat_pricing_sectionend',
            'type' => 'sectionend',
        ),
    );
    
    return apply_filters( 'filter_bur4ksocial_vat_pricing_settings', $settings );
}
