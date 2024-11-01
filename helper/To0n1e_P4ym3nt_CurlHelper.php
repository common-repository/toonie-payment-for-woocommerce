<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class To0n1e_P4ym3nt_CurlHelper
{
    /**
     * @param string $authUrl
     * @param string $clientId
     * @param string $username
     * @param string $password
     *
     * @return mixed
     */
    public static function getAccessToken(string $authUrl, string $clientId, string $username, string $password): mixed
    {
        $result = To0n1e_P4ym3nt_Curl::curlExec(
            "POST",
            ["Content-Type: application/x-www-form-urlencoded"],
            $authUrl,
            "grant_type=password&client_id=" . $clientId . "&username=" . $username . "&password=" . $password
        );

        $resultArray = json_decode($result, true);
        if (isset($resultArray['access_token'])) {
            return $resultArray['access_token'];
        }
        return "";
    }

    /**
     * @param string $apiUrl
     * @param string $authUrl
     * @param string $clientId
     * @param string $username
     * @param string $password
     * @param float  $orderTotal
     * @param string $orderName
     * @param string $returnUrl
     *
     * @return mixed|void
     */
    public static function getSessionID(string $apiUrl, string $authUrl, string $clientId, string $username, string $password, float $orderTotal, string $orderName, string $returnUrl)
    {
        $accessToken = self::getAccessToken($authUrl, $clientId, $username, $password);
        if ($accessToken) {
            $data = [
                "amount" => $orderTotal,
                "currency" => get_woocommerce_currency(),
                "reason" => $orderName,
                "successUrl" => $returnUrl,
                "errorUrl" => $returnUrl
            ];
            $resultPayment = To0n1e_P4ym3nt_Curl::curlExec(
                "POST",
                [
                    "Content-Type" => "application/json",
                    "Authorization" => "Bearer " . $accessToken
                ],
                $apiUrl,
                wp_json_encode($data)
            );
            $resultPaymentArray = json_decode($resultPayment, true);
            if (isset($resultPaymentArray['sessionId'])) {
                return $resultPaymentArray['sessionId'];
            }
        }
    }

    /**
     * @param string $apiUrl
     * @param string $authUrl
     * @param string $clientId
     * @param string $username
     * @param string $password
     * @param string $sessionId
     *
     * @return mixed
     */
    public static function checkSessionID(string $apiUrl, string $authUrl, string $clientId, string $username, string $password, string $sessionId): mixed
    {
        $accessToken = self::getAccessToken($authUrl, $clientId, $username, $password);
        if ($accessToken) {
            $resultCheck = To0n1e_P4ym3nt_Curl::curlExec(
                "GET",
                [
                    "Authorization" => "Bearer " . $accessToken
                ],
                $apiUrl . $sessionId,
                ""
            );

            $resultArray = json_decode($resultCheck, true);
            if (isset($resultArray['status'])) {
                return $resultArray['status'];
            }
        }
        return "";
    }
}
