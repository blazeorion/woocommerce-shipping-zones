<?php
// Plugin Name: StackOverflow Custom Shipping 
// Plugin URI: https://example.com/woocommerce-custom-shipping-method/
// Description: This plugin adds a custom shipping method to WooCommerce.
// Version: 1.0.0
// Author: Your Name
// Author URI: https://example.com/

// To initialize your new shipping method you have to keep it in function 
function local_shipping_init() {
    if ( ! class_exists( 'Local_Shipping_Method' ) ) {
        class Local_Shipping_Method extends WC_Shipping_Method {

            public function __construct( $instance_id = 0 ) {
                $this->id                 = 'local_shipping';
                $this->instance_id        = absint( $instance_id );
                $this->method_title       = __( 'Pickup', 'text-domain' );
                $this->method_description = __( 'Pickup Location for WooCommerce', 'text-domain' );
                $this->title              = __( 'Pickup Location', 'text-domain' );
                $this->supports           = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );

                // then you have to call this method to initiate your settings
                $this->init();
            }


            public function init() {
                // this method used to initiate your fields on settings 
                $this->init_form_fields();
                // this is settings instance where you can declar your settings field 
                $this->init_instance_settings();
                
                // user defined values goes here, not in construct 
                $this->enabled = $this->get_option( 'enabled' );
                $this->title   = __( 'Pickup Location', 'text-domain' );
                
                // call this action in init() method to save your settings at the backend
                add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
            }

            public function init_instance_settings() {
                // you have to keep all the instance settings field inside the init_instance_settings method 
                $this->instance_form_fields = array(
                    'enabled'    => array(
                        'title'   => __( 'Enable/Disable' ),
                        'type'    => 'checkbox',
                        'label'   => __( 'Enable this shipping method' ),
                        'default' => 'yes',
                    ),
                    'title'      => array(
                        'title'       => __( 'Method Title' ),
                        'type'        => 'text',
                        'description' => __( 'This controls the title which the user sees during checkout.' ),
                        'default'     => __( 'Pickup Location' ),
                        'desc_tip'    => true
                    ),
                    'tax_status' => array(
                        'title'   => __( 'Tax status', 'woocommerce' ),
                        'type'    => 'select',
                        'class'   => 'wc-enhanced-select',
                        'default' => 'taxable',
                        'options' => array(
                            'taxable' => __( 'Taxable', 'woocommerce' ),
                            'none'    => _x( 'None', 'Tax status', 'woocommerce' ),
                        ),
                    ),
                    'cost'       => array(
                        'title'       => __( 'Cost', 'woocommerce' ),
                        'type'        => 'text',
                        'placeholder' => '0',
                        'description' => __( 'Optional cost for pickup.', 'woocommerce' ),
                        'default'     => '',
                        'desc_tip'    => true,
                    ),
                );
            }

            public function calculate_shipping( $package = array() ) {
                $this->add_rate( array(
                    'id'    => $this->id, // you should define only your shipping method id here
                    'label' => $this->title,
                    'cost'  => 0,
                ) );
            }
        }
    }

}
add_action( 'woocommerce_shipping_init', 'local_shipping_init' ); // use this hook to initialize your new custom method 

function add_local_shipping( $methods ) {
    $methods['local_shipping'] = 'Local_Shipping_Method';

    return $methods;
}
add_filter( 'woocommerce_shipping_methods', 'add_local_shipping' );
