<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class To0n1e_P4ym3nt_Curl
{
    /**
     * @param string $type
     * @param array  $headers
     * @param string $url
     * @param string $postFields
     *
     * @return string
     */
    public static function curlExec(string $type, array $headers, string $url, string $postFields): string
    {
        $result = match ($type) {
            "POST" => wp_remote_post($url, [
                "body" => $postFields,
                "headers" => $headers
            ]),
            "GET" => wp_remote_get($url, [
                "headers" => $headers
            ]),
            default => [],
        };
        return $result["body"];
    }
}