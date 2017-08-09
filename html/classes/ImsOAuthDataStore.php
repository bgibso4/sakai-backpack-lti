<?php

/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 8/1/2017
 * Time: 1:05 PM
 */
class ImsOAuthDataStore extends \IMSGlobal\LTI\OAuth\OAuthDataStore
{
    private $consumer_key = NULL;
    private $consumer_secret = NULL;

    public function __construct($consumer_key, $consumer_secret) {

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;

    }

    function lookup_consumer($consumer_key) {

        return new IMSGlobal\LTI\OAuth\OAuthConsumer($this->consumer_key, $this->consumer_secret);

    }

    function lookup_token($consumer, $token_type, $token) {

        return new IMSGlobal\LTI\OAuth\OAuthToken($consumer, '');

    }

    function lookup_nonce($consumer, $token, $nonce, $timestamp) {

        return FALSE;  // If a persistent store is available nonce values should be retained for a period and checked here

    }

    function new_request_token($consumer, $callback = null) {

        return NULL;

    }

    function new_access_token($token, $consumer, $verifier = null) {

        return NULL;

    }
}