<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class To0n1e_P4ym3nt_Acquiring
{
    /** @var string */
    public const GET_PAYMENT_SESSION = "/paymentSession/";

    /**
     * @param string $sessionId
     *
     * @return mixed
     */
    public static function checkSessionID(string $sessionId): mixed
    {
        $tooniePaymentClass = new To0n1eP4ym3nt_Toonie_Payment_Gateway();

        return To0n1e_P4ym3nt_CurlHelper::checkSessionID(
            $tooniePaymentClass->api_environment_url . self::GET_PAYMENT_SESSION,
            $tooniePaymentClass->auth_environment_url,
            $tooniePaymentClass->auth_environment_client_id,
            $tooniePaymentClass->auth_environment_username,
            $tooniePaymentClass->auth_environment_password,
            $sessionId
        );
    }
}