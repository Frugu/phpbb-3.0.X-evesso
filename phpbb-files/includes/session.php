<?php
/**
 * Created by PhpStorm.
 * User: Korbeil
 * Date: 07/10/15
 * Time: 01:23
 */

    //////////////////////////////////////////////////////////
    // EvE-SSO

    public static $evesso_more      = Array(
        'client_id'	    => '[[YOUR_EVE_SSO_CLIENT_ID]]',
        'secret_key'    => '[[YOUR_EVE_SSO_SECRET_KEY]]',
        'base_url'      => 'https://login.eveonline.com',
        'auth_url'      => '/oauth/authorize',
        'token_url'     => '/oauth/token',
        'verify_url'    => '/oauth/verify',
        'callback'      => '[[YOUR_FORUM_URL]]/evesso.php'
    );

    public static function processEveSSO() {
        $response   = self::processAuthCode();
        $character  = self::getCharacterId($response);

        return $character;
    }

    public static function processCREST($url, $headers, $post_params = Array()) {
        // curl query
        $ch         = curl_init();

        $opts       = Array(
            CURLOPT_URL             => $url,
            CURLOPT_HTTPHEADER      => $headers,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_USERAGENT       => 'iveeCrest/1.0',
            CURLOPT_SSL_VERIFYPEER  => true,
            CURLOPT_SSL_CIPHER_LIST => 'TLSv1' //prevent protocol negotiation fail
        );
        if(count($post_params) > 0) {
            $opts[CURLOPT_POST]         = true;
            $opts[CURLOPT_POSTFIELDS]   = http_build_query($post_params);
        }

        curl_setopt_array($ch, $opts);

        // curl response
        $resBody    = curl_exec($ch);
        $info       = curl_getinfo($ch);
        $err        = curl_errno($ch);
        $errMsg     = curl_error($ch);

        if ($err != 0) {
            throw new Exception('EvESSO - ' .$errMsg, $err);
        }
        if (!in_array($info['http_code'], array(200, 302))) {
            $errMsg = 'HTTP response not OK: ' . (int)$info['http_code'] . '. Response body: ' . $resBody;
            throw new Exception('EvESSO - ' .$errMsg, $info['http_code']);
        }

        curl_close($ch);
        $response = json_decode($resBody, true);
        return $response;
    }

    public static function processAuthCode() {
        $params             = self::$evesso_more;
        $header             = 'Authorization: Basic ' . base64_encode($params['client_id'] . ':' . $params['secret_key']);
        $url                = $params['base_url'] . $params['token_url'];

        // fields
        $fields = array(
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code']
        );

        return self::processCREST($url, Array($header), $fields);
    }

    public static function getCharacterId($response) {
        $params             = self::$evesso_more;
        $header             = 'Authorization: ' .$response['token_type']. ' ' .$response['access_token'];
        $url                = $params['base_url'] . $params['verify_url'];

        return self::processCREST($url, Array($header));
    }