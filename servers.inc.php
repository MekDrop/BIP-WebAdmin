<?php

/**
 * Include file for servers
 *
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 */
require_once 'func.func.php';

if (auto_access_denied())
    return;

$networks = &$objBipConfigManager->getNetworksNames();

switch (@$_REQUEST['action']) {
    case 'addnewnetwork':
        $fields = &$objBipConfigManager->getnetworkconfigtypes();
        $data = array();
        $objBipConfigManager->fill_if_needed_network($data);
        show_form($fields, $data, 'Add network', 'addnewnetwork2');
        break;
    case 'addnewnetwork2':
        if (trim(@$_REQUEST['name']) != '') {
            $objBipConfigManager->vars['network'][] = array(
                'name' => $_REQUEST['name'],
                'ssl' => (@$_REQUEST['ssl']) . '' == 'true',
                'server' => array(
                )
            );
            $objBipConfigManager->save();
        }
        header('location: ?site=servers');
        break;
    case 'addserver':
        $fields = &$objBipConfigManager->getserverconfigtypes();
        $data = array();
        $objBipConfigManager->fill_if_needed_server($data);
        show_form($fields, $data, 'Add server', 'addserver2');
        break;
    case 'addserver2':
        if (trim(@$_REQUEST['host']) != '' || trim(@$_REQUEST['port']) != '') {
            $objBipConfigManager->vars['network'][$_REQUEST['item_id']]['server'][] = array(
                'host' => $_REQUEST['host'],
                'port' => intval($_REQUEST['port']),
            );
            $objBipConfigManager->save();
        }
        header('location: ?site=servers&action=show&item_id=' . $_REQUEST['item_id']);
        break;
    case 'save':
        $objBipConfigManager->vars['network'][$_REQUEST['item_id']]['name'] = $_REQUEST['name'];
        $objBipConfigManager->vars['network'][$_REQUEST['item_id']]['ssl'] = (@$_REQUEST['ssl']) . '' == 'true';
        $objBipConfigManager->save();
        header('location: ?site=servers&action=show&item_id=' . $_REQUEST['item_id']);
        break;
    case 'show':
        $fields = &$objBipConfigManager->getnetworkconfigtypes();
        $objBipConfigManager->fill_if_needed_network($objBipConfigManager->vars['network'][$_REQUEST['item_id']]);
        show_form($fields, $objBipConfigManager->vars['network'][$_REQUEST['item_id']]);
        br(2);
        $servers = $objBipConfigManager->getServersNames($_REQUEST['item_id']);
        echo '<h5 class="list">Servers:</h5>';
        show_list('?site=servers&action=servers&pid=' . $_REQUEST['item_id'], $servers, '?site=servers&action=delete&type=server&pid=' . $_REQUEST['item_id']);
        br(2);
        action_button('addserver', 'Add server');
        break;
    case 'saveserver':
        $objBipConfigManager->vars['network'][$_REQUEST['pid']]['server'][$_REQUEST['item_id']]['host'] = $_REQUEST['host'];
        $objBipConfigManager->vars['network'][$_REQUEST['pid']]['server'][$_REQUEST['item_id']]['port'] = intval($_REQUEST['port']);
        $objBipConfigManager->save();
        header('location: ?site=servers&action=servers&item_id=' . $_REQUEST['item_id'] . '&pid=' . $_REQUEST['pid']);
        break;
    case 'servers':
        $fields = &$objBipConfigManager->getserverconfigtypes();
        $objBipConfigManager->fill_if_needed_server($objBipConfigManager->vars['network'][$_REQUEST['pid']]['server'][$_REQUEST['item_id']]);
        show_form($fields, $objBipConfigManager->vars['network'][$_REQUEST['pid']]['server'][$_REQUEST['item_id']], 'Save', 'saveserver');
        break;
    case 'delete':
        switch ($_REQUEST['type']) {
            case 'network':
                unset($objBipConfigManager->vars['network'][$_REQUEST['item_id']]);
                $objBipConfigManager->vars['network'] = array_values($objBipConfigManager->vars['network']);
                $objBipConfigManager->save();
                header('location: ?site=servers&action=default');
                break;
            case 'server':
                unset($objBipConfigManager->vars['network'][$_REQUEST['pid']]['server'][$_REQUEST['item_id']]);
                $objBipConfigManager->vars['network'][$_REQUEST['pid']]['server'] = array_values($objBipConfigManager->vars['network'][$_REQUEST['pid']]['server']);
                $objBipConfigManager->save();
                header('location: ?site=servers&action=show&item_id=' . $_REQUEST['pid']);
                break;
        }
        break;
    default:
        echo '<h5 class="list">Networks:</h5>';
        show_list('?site=servers&action=show', $networks, '?site=servers&action=delete&type=network');
        br(2);
        action_button('addnewnetwork', 'Add network');
        break;
}
