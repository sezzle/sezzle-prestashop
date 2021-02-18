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
}
