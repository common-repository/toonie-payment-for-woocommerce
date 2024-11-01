<?php

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('woocommerce_thankyou', 'to0n1e_p4ym3nt__payment_check', 10, 2);
function to0n1e_p4ym3nt__payment_check($order_id)
{
    $order = wc_get_order($order_id);

    if ($order->get_payment_method() == "To0n1eP4ym3nt_Toonie_Payment_Gateway") {
        $sessionId = $order->get_meta('toonie_payment_session_id');
        if ($sessionId) {
            $checkStatus = To0n1e_P4ym3nt_Acquiring::checkSessionID($sessionId);
            if ($order->get_status() == "pending") {
                if (in_array($checkStatus, ["SUCCEEDED", "COMPLETED"])) {
                    $order->update_status('processing', __('Payment confirmed.', 'toonie-payment-for-woocommerce'));
                } else {
                    $order->update_status('failed', __('Payment rejected.', 'toonie-payment-for-woocommerce'));
                }
            }
        } else {
            $order->update_status('failed', __('Payment session not valid or generated.', 'toonie-payment-for-woocommerce'));
        }
    }
}
