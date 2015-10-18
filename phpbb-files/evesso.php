<?php
/**
 * Created by PhpStorm.
 * User: Korbeil
 * Date: 07/10/15
 * Time: 01:18
 */

    define('IN_PHPBB', true);
    $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);
    include($phpbb_root_path . 'common.' .$phpEx);
    include($phpbb_root_path . 'includes/functions_user.' .$phpEx);
    include($phpbb_root_path . 'includes/auth/auth_apache.' .$phpEx);
    include($phpbb_root_path. '/vendor/autoload.php');

    $user->session_begin();
    $auth->acl($user->data);

    ////////////////////
    // EVESSO Process
    $character          = user::processEveSSO();
    $xml                = simplexml_load_string(file_get_contents('https://api.eveonline.com/eve/CharacterInfo.xml.aspx?&characterId=' .$character['CharacterID']));
    $json               = json_encode($xml);
    $array              = json_decode($json, true);

    // WHITELIST
    $whitelist          = Array(
        'groups'        => Array(
        ),
        'character'     => Array(
        ),
        'corporation'   => Array(
        ),
        'alliance'      => Array(
        )
    );

    // ADDING USER OR RETRIEVE IT
    $data = Array(
        'username'          => $array['result']['characterName'],
        'password'          => md5(pow($character['CharacterID'], 3)),
        'email'             => $array['result']['characterName']. '@[[YOUR_DOMAIN]]'
    );

    $user_id    = 0;
    $result     = search_user_evesso($data['username']);

    if(!$result) {
        // he don't exists create him :)
        $user_id = add_user_evesso($data, $array['result'], $whitelist);
    } else {
        $user_id = $result['user_id'];

        // check if user is still in whitelist ?
        $check = evesso_check_whitelist($whitelist, $result['user_id'], $array['result'], 'add_user_evesso_group_user_add');

        if(!$check) {
            // if no, we need to reset all his groups
            foreach($whitelist['groups'] as $groupId) {
                group_user_del($groupId, $user_id);
            }
        }
    }

    $result = $auth->login($data['username'], $data['password'], true);

    header('Location: /');