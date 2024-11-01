<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin Name: Payment Toonie for Woocommerce
 * Plugin URI: https://toonieglobal.com/
 * Description: An eCommerce payment provider that helps you sell quickly.
 * Version: 0.2
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Author: PortIT
 * Author URI: https://portit.io/
 * Requires Plugins: woocommerce
 **/

include_once("class/To0n1e_P4ym3nt_Gateway.php");
include_once("class/To0n1e_P4ym3nt_Curl.php");
include_once("class/To0n1e_P4ym3nt_Acquiring.php");

include_once("helper/To0n1e_P4ym3nt_CurlHelper.php");
include_once("helper/to0n1e_p4ym3nt_validation.php");

add_filter('woocommerce_payment_gateways', 'to0n1e_p4ym3nt__add_gateway_class');
function to0n1e_p4ym3nt__add_gateway_class($gateways)
{
    $gateways[] = 'To0n1eP4ym3nt_Toonie_Payment_Gateway'; // your class name is here
    return $gateways;
}

if ( ! function_exists('write_log')) {
    function write_log ( $log )  {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}