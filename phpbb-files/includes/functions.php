<?php
/**
 * Created by PhpStorm.
 * User: Korbeil
 * Date: 07/10/15
 * Time: 01:28
 */

    ////////////////
    // FIRST PART

    $evesso_params  = Array(
        'response_type'     => 'code',
        'redirect_uri'      => '[[YOUR_FORUM_URL]]/evesso.php',
        'client_id'         => '[[YOUR_EVE_SSO_CLIENT_ID]]',
        'scope'             => '',
        'state'             => uniqid('user_')
    );
    $u_login_evesso = 'https://login.eveonline.com/oauth/authorize/?' .http_build_query($evesso_params);


    ////////////////
    // SECOND PART

    'U_LOGIN_EVESSO'                => $u_login_evesso,