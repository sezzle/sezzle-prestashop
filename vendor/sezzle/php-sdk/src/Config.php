<?php


namespace Sezzle;

/**
 * Class Config
 * @package Sezzle
 */
class Config
{
    const SANDBOX = "sandbox";
    const PRODUCTION = "production";

    const SANDBOX_GATEWAY = 'https://sandbox.gateway.sezzle.com/v2/';
    const PRODUCTION_GATEWAY = 'https://gateway.sezzle.com/v2/';

    const API_V1 = "v1";
    const API_V2 = "v2";

    const POST = "POST";
    const GET = "GET";
    const PATCH = "PATCH";
    const DELETE = "DELETE";
    const PUT = "PUT";

    const SESSION_RESOURCE = 'session/%s';
    const AUTHENTICATION_RESOURCE = 'authentication';
    const REFUND_RESOURCE = 'order/%s/refund';
    const RELEASE_RESOURCE = 'order/%s/release';
    const CAPTURE_RESOURCE = 'order/%s/capture';
    const CUSTOMER_ORDER_RESOURCE = 'customer/%s/order';
    const TOKENIZE_RESOURCE = 'token/%s/session';
    const ORDER_RESOURCE = 'order/%s';

    const GATEWAY_URL = "https://%sgateway.%s/%s";
    const SEZZLE_DOMAIN = "%ssezzle.com";
    public static $supportedRegions = ['US', 'EU'];

    /**
     * Get Sezzle Domain
     *
     * @param string $gatewayRegion
     * @return string
     */
    public static function getSezzleDomain($gatewayRegion = '')
    {
        switch ($gatewayRegion) {
            case self::$supportedRegions[1]:
                return sprintf(Config::SEZZLE_DOMAIN, 'eu.');
            case self::$supportedRegions[0]:
            default:
                return sprintf(Config::SEZZLE_DOMAIN, '');
        }
    }

    /**
     * Get Gateway URL
     *
     * @param string $apiMode
     * @param string $apiVersion
     * @param string $gatewayRegion
     * @return string
     */
    public static function getGatewayUrl($apiMode, $apiVersion, $gatewayRegion = '')
    {
        $sezzleDomain = self::getSezzleDomain($gatewayRegion);
        if ($apiMode === Config::SANDBOX) {
            return sprintf(Config::GATEWAY_URL, 'sandbox.', $sezzleDomain, $apiVersion);
        }
        return sprintf(Config::GATEWAY_URL, "", $sezzleDomain, $apiVersion);
    }
}
