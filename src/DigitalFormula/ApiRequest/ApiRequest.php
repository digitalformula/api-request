<?php
/**
 *
 * Contains the ApiRequest class
 *
 * @author Chris Rasmussen, digitalformula.net
 * @category Development
 * @license MIT
 * @package DigitalFormula
 *
 */

namespace DigitalFormula;

use GuzzleHttp\Client;

class ApiRequest {
    
    /**
     * The username to use during the connection
     */
    private $username;
    
    /**
     * The password to use during the connection
     */
    private $password;
    
    /**
     * The request's base URI, as per RFC 3986
     */
    private $uri;
    
    /**
     * Guzzle and cURL request options
     */
    private $options;
    
    /**
     * Private object for Guzzle-specific details
     */
    private $guzzleClient;
    
    /**
     * ApiRequest constructor.
     * @param $username
     * @param $password
     * @param $uri
     * @return void
     */
    public function __construct( $username, $password, $uri ) {
        try {
            $this->username = $username;
            $this->password = $password;
            $this->uri = $uri;
            $this->options = [
                'auth' => [
                        $this->username,
                        $this->password
                ],
                'verify' => false,
                'defaults' => [
                    'cookies' => true,
                    'config' => [
                            'timeout' => 3,
                            'connect_timeout' => 3,
                            'curl' => [
                                    CURLOPT_SSL_VERIFYHOST => false,
                                    CURLOPT_SSL_VERIFYPEER => false
                            ]
                    ],
                    'headers' => [
                            "Accept" => "application/json",
                            "Content-Type" => "application/json",
                    ],
                ]
            ];
            $this->guzzleClient = new \GuzzleHttp\Client( [ 'base_uri' => $this->uri ] );
        }
        catch( Exception $e ) {
            return( json_encode( [ 'message' => $e->getMessage() ] ) );
        }
    }
    
    /**
     * Perform an API GET request
     * @param $uri
     * @return Array containing API response details
     */
    public function get( $uri ) {
        $response = $this->guzzleClient->request( 'GET', $uri, $this->options );
        return [
            'statusCode' => $response->getStatusCode(),
            'contentType' => $response->getHeader( 'content-type' ),
            'body' => $response->getBody()->getContents()
        ];
    }
    
    /**
     * Perform an API POST request
     * @param $uri
     * @param $parameters
     * @return Array containing API response details
     */
    public function post( $uri, $parameters ) {
        $response = $this->guzzleClient->request( 'POST', $uri, array_merge( $this->options, $parameters ) );
        return [
            'statusCode' => $response->getStatusCode(),
            'contentType' => $response->getHeader( 'content-type' ),
            'body' => $response->getBody()->getContents()
        ];
    }
    
}