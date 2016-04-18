<?php

/**
 * Include users class
 *
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 */
require_once 'func.func.php';

$users = &$objBipConfigManager->getUsersNames();

switch (@$_REQUEST['action']) {
    case 'channel':
        $fields = &$objBipConfigManager->getchannelconfigtypes();
        $pid = explode('.', $_REQUEST['pid']);
        $objBipConfigManager->fill_if_needed_channel($objBipConfigManager->vars['user'][$pid[0]]['connection'][$pid[1]]['channel'][$_REQUEST['item_id']]);
        show_form($fields, $objBipConfigManager->vars['user'][$pid[0]]['connection'][$pid[1]]['channel'][$_REQUEST['item_id']], 'Save', 'savechannel');
        break;
    case 'savechannel':
        $pid = explode('.', $_REQUEST['pid']);
        $objBipConfigManager->vars['user'][$pid[0]]['connection'][$pid[1]]['channel'][$_REQUEST['item_id']] = array(
            'key' => @$_REQUEST['key'],
            'name' => @$_REQUEST['name'],
            'backlog' => @$_REQUEST['backlog'] == 'true',
        );
        $objBipConfigManager->save();
        $url = '?site=users&action=channel&item_id=' . $_REQUEST['item_id'] . '&pid=' . $_REQUEST['pid'];
        header('location: ' . $url);
        break;
    case 'addchannel':
        $fields = &$objBipConfigManager->getchannelconfigtypes();
        $data = array();
        $objBipConfigManager->fill_if_needed_channel($data);
        show_form($fields, $data, 'Add channel', 'addchannel2');
        break;
    case 'addchannel2':
        $objBipConfigManager->magic_create_value($objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection'][$_REQUEST['item_id']]['channel'], array(
            'key' => @$_REQUEST['key'],
            'name' => @$_REQUEST['name'],
            'backlog' => @$_REQUEST['backlog'] == 'true',
                )
        );
        $objBipConfigManager->save();
        $url = '?site=users&action=connection&item_id=' . $_REQUEST['item_id'] . '&pid=' . $_REQUEST['pid'];
        header('location: ' . $url);
        break;
    case 'saveconnection':
        $fields = &$objBipConfigManager->getconnectionconfigtypes();
        $data = array();
        foreach ($fields as $fname => $ftype) {
            switch ($ftype) {
                case 'bool':
                    $data[$fname] = isset($_POST[$fname]);
                    break;
                case 'password':
                    if ($_POST[$fname][0] == $_POST[$fname][1]) {
                        $data[$fname] = $_POST[$fname][0];
                    }
                    break;
                default:
                    $data[$fname] = $_POST[$fname];
                    break;
            }
        }
        foreach ($data as $key => $value) {
            $objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection'][$_REQUEST['item_id']][$key] = $value;
        }
        $objBipConfigManager->save();
        $url = '?site=users&action=connection&item_id=' . $_REQUEST['item_id'] . '&pid=' . $_REQUEST['pid'];
        header('location: ' . $url);
        break;
    case 'addconnection':
        $fields = &$objBipConfigManager->getconnectionconfigtypes();
        $data = array();
        $objBipConfigManager->fill_if_needed_connection($data);
        show_form($fields, $data, 'Add connection', 'addconnection2');
        break;
    case 'addconnection2':
        $fields = &$objBipConfigManager->getconnectionconfigtypes();
        $data = array();
        foreach ($fields as $fname => $ftype) {
            switch ($ftype) {
                case 'bool':
                    $data[$fname] = isset($_POST[$fname]);
                    break;
                case 'password':
                    if ($_POST[$fname][0] == $_POST[$fname][1]) {
                        $data[$fname] = $_POST[$fname][0];
                    }
                    break;
                default:
                    $data[$fname] = $_POST[$fname];
                    break;
            }
        }

        $objBipConfigManager->magic_create_value($objBipConfigManager->vars['user'][$_REQUEST['item_id']]['connection'], $data);
        $objBipConfigManager->save();
        $url = '?site=users&action=user&item_id=' . $_REQUEST['item_id'];
        header('location: ' . $url);
        break;
    case 'connection':
        $fields = &$objBipConfigManager->getconnectionconfigtypes();
        $objBipConfigManager->fill_if_needed_connection($objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection'][$_REQUEST['item_id']]);
        show_form($fields, $objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection'][$_REQUEST['item_id']], 'Save', 'saveconnection');
        br(2);
        $servers = $objBipConfigManager->getChannelsNames($_REQUEST['pid'], $_REQUEST['item_id']);
        echo '<h5 class="list">Channels:</h5>';
        show_list('?site=users&action=channel&pid=' . $_REQUEST['pid'] . '.' . $_REQUEST['item_id'], $servers, '?site=users&action=delete&type=channel&pid=' . $_REQUEST['pid'] . '.' . $_REQUEST['item_id']);
        br(2);
        action_button('addchannel', 'Add channel');
        break;
    case 'user':
        $fields = &$objBipConfigManager->getuserconfigtypes();
        $objBipConfigManager->fill_if_needed_user($objBipConfigManager->vars['user'][$_REQUEST['item_id']]);
        show_form($fields, $objBipConfigManager->vars['user'][$_REQUEST['item_id']], 'Save', 'saveuser');
        br(2);
        $servers = $objBipConfigManager->getConnectionsNames($_REQUEST['item_id']);
        echo '<h5 class="list">Connections:</h5>';
        show_list('?site=users&action=connection&pid=' . $_REQUEST['item_id'], $servers, '?site=users&action=delete&type=connection&pid=' . $_REQUEST['item_id']);
        br(2);
        action_button('addconnection', 'Add connection');
        break;
    case 'adduser':
        if (auto_access_denied())
            return;
        $fields = &$objBipConfigManager->getuserconfigtypes();
        $data = array();
        $objBipConfigManager->fill_if_needed_user($data);
        show_form($fields, $data, 'Add user', 'adduser2');
        break;
    case 'saveuser':
        $fields = &$objBipConfigManager->getuserconfigtypes();
        $data = array();
        foreach ($fields as $fname => $ftype) {
            switch ($ftype) {
                case 'bool':
                    $data[$fname] = isset($_POST[$fname]);
                    break;
                case 'password_sys':
                    if (!isset($_POST[$fname]))
                        break;
                    if ($_POST[$fname][0] == $_POST[$fname][1] && (strlen($_POST[$fname][2]) > 5)) {
                        if (strlen($_POST[$fname][0]) > 4) {
                            $data[$fname] = $_POST[$fname][2];
                            $ps = true;
                        }
                    }
                    break;
                default:
                    $data[$fname] = $_POST[$fname];
                    break;
            }
        }
        foreach ($data as $key => $value) {
            $objBipConfigManager->vars['user'][$_REQUEST['item_id']][$key] = $value;
        }
        if (isset($ps) && $ps == true) {
            require 'users.dat.php';
            $users[$data['name']] = sha1($_POST['password'][0]);
            file_put_contents('users.dat.php', '<' . '?php $users = ' . var_export($users, true) . ' ; ?' . '>');
        }
        $objBipConfigManager->save();
        $url = '?site=users&item_id=' . $_REQUEST['item_id'];
        header('location: ' . $url);
        break;
    case 'adduser2':
        if (auto_access_denied())
            return;
        $fields = &$objBipConfigManager->getuserconfigtypes();
        $data = array();
        foreach ($fields as $fname => $ftype) {
            switch ($ftype) {
                case 'bool':
                    $data[$fname] = isset($_POST[$fname]);
                    break;
                case 'password_sys':
                    if ($_POST[$fname][0] == $_POST[$fname][1] && (strlen($_POST[$fname][2]) > 5)) {
                        if (strlen($_POST[$fname][0]) > 4) {
                            $data[$fname] = $_POST[$fname][2];
                            $ps = true;
                        }
                    }
                    break;
                default:
                    $data[$fname] = $_POST[$fname];
                    break;
            }
        }
        $objBipConfigManager->magic_create_value($objBipConfigManager->vars['user'], $data);
        if (isset($ps) && $ps == true) {
            require 'users.dat.php';
            $users[$data['name']] = sha1($_POST['password'][0]);
            file_put_contents('users.dat.php', '<' . '?php $users = ' . var_export($users, true) . ' ; ?' . '>');
        }
        $objBipConfigManager->save();
        $url = '?site=users';
        header('location: ' . $url);
        break;
    case 'delete':
        if (auto_access_denied())
            return;
        switch ($_REQUEST['type']) {
            case 'channel':
                $pid = explode('.', $_REQUEST['pid']);
                unset($objBipConfigManager->vars['user'][$pid[0]]['connection'][$pid[1]]['channel'][$_REQUEST['item_id']]);
                $objBipConfigManager->vars['user'][$pid[0]]['connection'][$pid[1]]['channel'] = array_values($objBipConfigManager->vars['user'][$pid[0]]['connection'][$pid[1]]['channel']);
                $url = '?site=users&action=connection&item_id=' . $pid[1] . '&pid=' . $pid[0];
                break;
            case 'user':
                unset($objBipConfigManager->vars['user'][$_REQUEST['item_id']]);
                $objBipConfigManager->vars['user'] = array_values($objBipConfigManager->vars['user']);
                $url = '?site=users';
                break;
            case 'connection':
                unset($objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection'][$_REQUEST['item_id']]);
                $objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection'] = array_values($objBipConfigManager->vars['user'][$_REQUEST['pid']]['connection']);
                $url = '?site=users&action=user&item_id=' . $_REQUEST['pid'];
                break;
        }
        $objBipConfigManager->save();
        header('location: ' . $url);
        break;
    default:
        if (!$_SESSION['is_admin']) {
//				var_dump($_SESSION['user_id']);
            header('location: ?site=users&action=user&item_id=' . $_SESSION['user_id']);
            exit;
        }
        echo '<h5 class="list">Users:</h5>';
        show_list('?site=users&action=user', $users, (!$_SESSION['is_admin']) ? '' : '?site=users&action=delete&type=user' );
        br(2);
        action_button('adduser', 'Add user');
        break;
}
