<?php

namespace Sezzle\HttpClient;

interface HttpClientInterface
{
    /**
     * Send a GET request
     *
     * @param string $url     URL
     * @param array  $headers array of request options to apply
     *
     * @throws RequestException When an error is encountered
     *
     * @return Response
     */
    public function get($url = null, $headers = []);

    /**
     * Send a HEAD request
     *
     * @param string   $url     URL
     * @param string[] $headers Array of request headers
     *
     * @throws RequestException When an error is encountered
     *
     * @return Response
     */
    public function head($url = null, array $headers = []);

    /**
     * Send a DELETE request
     *
     * @param string   $url     URL
     * @param string[] $headers Array of request headers
     *
     * @throws RequestException When an error is encountered
     *
     * @return Response
     */
    public function delete($url = null, array $headers = []);

    /**
     * Send a PUT request
     *
     * @param string       $url     URL
     * @param string[]     $headers Array of request headers
     * @param string|array $content
     *
     * @throws RequestException When an error is encountered
     *
     * @return Response
     */
    public function put($url = null, array $headers = [], $content = null);

    /**
     * Send a PATCH request
     *
     * @param string       $url     URL
     * @param string[]     $headers Array of request headers
     * @param string|array $content
     *
     * @throws RequestException When an error is encountered
     *
     * @return Response
     */
    public function patch($url = null, array $headers = [], $content = null);

    /**
     * Send a POST request
     *
     * @param string       $url     URL
     * @param string[]     $headers Array of request headers
     * @param string|array $content
     *
     * @throws RequestException When an error is encountered
     *
     * @return Response
     */
    public function post($url = null, array $headers = [], $content = null);
}
