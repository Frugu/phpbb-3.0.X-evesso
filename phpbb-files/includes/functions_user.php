<?php
/**
 * Created by PhpStorm.
 * User: Korbeil
 * Date: 07/10/15
 * Time: 01:22
 */

    function evesso_check_whitelist($whitelist, $userid, $data, $callback) {
        // PUTTING IT IN RIGHT GROUP
        $oneIsOkay          = false;

        foreach($whitelist as $type => $values) {
            switch($type) {
                case 'character':
                    $index  = $data['characterID'];
                    $isOkay = in_array($data['characterID'], array_keys($values));
                    break;
                case 'corporation':
                    $index  = $data['corporationID'];
                    $isOkay = in_array($data['corporationID'], array_keys($values));
                    break;
                case 'alliance':
                    $index  = $data['allianceID'];
                    $isOkay = in_array($data['allianceID'], array_keys($values));
                    break;
                case 'groups':
                    $index  = NULL;
                    $isOkay = false;
                    break;
            }

            if($isOkay) {
                $oneIsOkay  = true;
                $callback($values[$index], $userid);
            }
        }

        return $oneIsOkay;
    }

    function add_user_evesso(&$data, $character, $whitelist) {
        $data['userid']         = user_add(user_row_apache($data['username'], $data['password']));
        $userid                 = $data['userid'];

        evesso_check_whitelist($whitelist, $userid, $character, 'add_user_evesso_group_user_add');

        return $data['userid'];
    }

    function add_user_evesso_group_user_add($groupid, $userid) {
        group_user_add($groupid, $userid, false, false, true);
    }

    function add_user_evesso_NULL($a, $b) {
    }

    function search_user_evesso($username) {
        global $db;

        $sql = 'SELECT user_id, username, user_password, user_passchg, user_pass_convert, user_email, user_type, user_login_attempts
            FROM ' . USERS_TABLE . "
            WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        return $row;
    }