<?php

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('plugins_loaded', 'to0n1e_p4ym3nt__gateway_class');
function to0n1e_p4ym3nt__gateway_class()
{
    class To0n1eP4ym3nt_Toonie_Payment_Gateway extends WC_Payment_Gateway
    {
        /** @var string */
        public string $api_environment_url;
        /** @var string */
        public string $app_environment_url;
        /** @var string */
        public string $auth_environment_url;
        /** @var string */
        public string $auth_environment_client_id;
        /** @var string */
        public string $auth_environment_username;
        /** @var string */
        public string $auth_environment_password;

        public function __construct()
        {
            $this->id = 'To0n1eP4ym3nt_Toonie_Payment_Gateway';
            $this->icon = '';
            $this->has_fields = true;
            $this->method_title = 'Toonie Payment Gateway';
            $this->method_description = 'Pay with via our super-cool payment gateway.';

            $this->supports = array(
                'products'
            );

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->enabled = $this->get_option('enabled');
            $this->api_environment_url = $this->get_option('api_environment_url') ?? '';
            $this->app_environment_url = $this->get_option('app_environment_url');
            $this->auth_environment_url = $this->get_option('auth_environment_url');
            $this->auth_environment_client_id = $this->get_option('auth_environment_client_id');
            $this->auth_environment_username = $this->get_option('auth_environment_username');
            $this->auth_environment_password = $this->get_option('auth_environment_password');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        /**
         * @return void
         */
        public function init_form_fields(): void
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => 'Enable/Disable',
                    'label' => 'Enable Toonie Payment Gateway',
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => 'Title',
                    'type' => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default' => 'Toonie Wallet',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => 'Description',
                    'type' => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default' => 'Pay with via our super-cool payment gateway.',
                ),
                'api_environment_url' => array(
                    'title' => 'Api environment url',
                    'type' => 'text',
                    'description' => 'This controls the api environment url.',
                    'default' => '',
                ),
                'app_environment_url' => array(
                    'title' => 'App environment url',
                    'type' => 'text',
                    'description' => 'This controls the app environment url.',
                    'default' => '',
                ),
                'auth_environment_url' => array(
                    'title' => 'Auth environment url',
                    'type' => 'text',
                    'description' => 'This controls the auth environment url.',
                    'default' => '',
                ),
                'auth_environment_client_id' => array(
                    'title' => 'Auth environment client_id',
                    'type' => 'text',
                    'description' => '',
                    'default' => 'pay-with-toonie',
                ),
                'auth_environment_username' => array(
                    'title' => 'Auth environment username',
                    'type' => 'text',
                    'description' => '',
                    'default' => 'pay-with-toonie',
                ),
                'auth_environment_password' => array(
                    'title' => 'Auth environment password',
                    'type' => 'password',
                    'description' => '',
                    'default' => '',
                ),
            );
        }

        /**
         * @param $orderId
         *
         * @return array
         */
        public function process_payment($orderId)
        {
            $order = new WC_Order($orderId);
            $orderTotal = $order->get_total();

            $orderTotalRounded = round($orderTotal, 2);
            if ($orderTotalRounded != $orderTotal) {
                write_log( "The total {$orderTotal} of order #{$orderId} was rounded to {$orderTotalRounded}");
                $orderTotal = $orderTotalRounded;
            }

            $returnUrl = $this->get_return_url($order);

            $sessionID = To0n1e_P4ym3nt_CurlHelper::getSessionID(
                $this->api_environment_url,
                $this->auth_environment_url,
                $this->auth_environment_client_id,
                $this->auth_environment_username,
                $this->auth_environment_password,
                $orderTotal,
                "Order #" . $orderId,
                $returnUrl
            );

            if ($sessionID) {
                $order->update_meta_data('toonie_payment_session_id', $sessionID);
                $order->update_status('pending', __('Awaiting payment confirmation', 'toonie-payment-for-woocommerce'));
                $order->add_order_note(__('Toonie payment sessionId: ', 'toonie-payment-for-woocommerce') . $sessionID, false);
                return array(
                    'result' => 'success',
                    'redirect' => $this->app_environment_url . "index.html?orderId=" . $sessionID
                );
            } else {
                return array(
                    'result' => 'failed',
                    'redirect' => ''
                );
            }
        }

        /**
         * @return bool
         */
        public function validate_fields(): bool
        {
            return true;
        }
    }
}