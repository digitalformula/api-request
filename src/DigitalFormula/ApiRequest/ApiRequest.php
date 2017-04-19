<?php
/**
 *
 * Contains the ApiRequest class
 *
 * @author Chris Rasmussen, digitalformula.net
 * @category Development
 * @license MIT
 * @package DigitalFormula\ApiRequest
 *
 */

namespace DigitalFormula\ApiRequest;

/**
 *
 * The ApiRequest class
 *
 * @package DigitalFormula\ApiRequest
 *
 */
class ApiRequest
{

    /**
     * The username to use during the connection
     */
    var $username;

    /**
     * The password to use during the connection
     */
    var $password;

    /**
     * The path for the actual API request
     */
    var $requestPath;

    /**
     * The IP address of the CVM
     */
    var $cvmAddress;

    /**
     * The port to connect on
     */
    var $cvmPort;

    /**
     * The timeout period i.e. how long to wait before the request is considered failed
     */
    var $connectionTimeout;

    /**
     * Is this a GET or POST request?
     */
    var $method;

    /**
     * @var \GuzzleHttp\Client
     */
    var $client;

    /**
     * @var version
     *
     * The version that will be used in the API request (for future use)
     *
     */
    var $version;

    /**
     * apiRequest constructor.
     * @param $username
     * @param $password
     * @param $cvmAddress
     * @param string $cvmPort
     * @param int $connectionTimeout
     * @param string $requestPath
     * @param string $method
     * @param string $version
     */
    public function __construct( $username, $password, $cvmAddress, $cvmPort, $connectionTimeout, $requestPath, $method, $version = 'v3' )
    {
        $this->username          = $username;
        $this->password          = $password;
        $this->requestPath       = $requestPath;
        $this->cvmAddress        = $cvmAddress;
        $this->cvmPort           = $cvmPort;
        $this->connectionTimeout = $connectionTimeout;
        $this->method            = $method;

        $credentials = base64_encode( $this->username . ':' . $this->password );

        $this->client = new \GuzzleHttp\Client( [
            'base_url' => sprintf( "https://%s:%s%s",
                $this->cvmAddress,
                $this->cvmPort,
                $this->requestPath
            ),
            'defaults' => [
                'auth' => [
                    $this->username,
                    $this->password,
                ],
                'cookies' => true,
                'config' => [
                    'timeout' => $this->connectionTimeout,
                    'connect_timeout' => $this->connectionTimeout,
                    'curl' => [
                        CURLOPT_SSL_VERIFYHOST => false,
                        CURLOPT_SSL_VERIFYPEER => false
                    ]
                ],
                'headers' => [
                    "Accept" => "application/json",
                    "Content-Type" => "application/json",
                    "Authorization" => "Basic $credentials"
                ],
            ]
        ] );

    }

    /**
     * Perform an API GET request
     *
     * @param $requestPath
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function get( $endPoint = null )
    {
    	return $this->client->get( $this->requestPath . '/' . $endPoint );
    }

    /**
     * Perform an API POST request i.e. make changes
     *
     * @param $requestPath
     * @param $body
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function post( $endPoint, $body, $encode = true )
    {
    	if( $encode ) {
    		return $this->client->post( $this->requestPath . '/' . $endPoint, [ 'body' => json_encode( $body ) ] );
    	}
    	else {
    		return $this->client->post( $this->requestPath . '/' . $endPoint, [ 'body' => $body ] );
    	}
    	
    }

}
/* ApiRequest */

?>