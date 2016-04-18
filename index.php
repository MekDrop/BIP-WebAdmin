<?php

/**
 * Main file
 *
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 */
// Report all PHP errors
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$package = array(
    'version' => '0.1.1',
    'author' => 'MekDrop <bipwebadmin@mekdrop.name>',
    'years' => '2009'
);

require_once 'config.dat.php';
require_once 'template.class.php';

session_start();

if (isset($_GET['cheat'])) {
    switch ($_GET['cheat']) {
        case 'restart':
            session_destroy();
            break;
        case 'genpass':
            echo sha1($_GET['param']);
            exit;
            break;
    }
}

require_once 'bip.conf.reader.class.php';

if (!isset($_SESSION['user']) || true) {
    require 'users.dat.php';
    if ((!isset($_SERVER['PHP_AUTH_USER'])) || (!isset($users[$_SERVER['PHP_AUTH_USER']])) || ($users[$_SERVER['PHP_AUTH_USER']] != sha1($_SERVER['PHP_AUTH_PW']))) {
        $realm = 'BIP Web Admin'; // . $_SERVER['PHP_AUTH_USER'] . ' ' . $_SERVER['PHP_AUTH_PW'] . ' | ' . sha1($_SERVER['PHP_AUTH_PW']) . ' = ' . $users[$_SERVER['PHP_AUTH_USER']];
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: basic realm="' . $realm . '",qop="auth",nonce="' . uniqid() . '",opaque="' . md5($realm) . '"');
        exit;
    }
    $_SESSION['user'] = $_SERVER['PHP_AUTH_USER'];
    $users = &$objBipConfigManager->getUsersNames();
    $user_id = array_search($_SESSION['user'], $users);
    if ($user_id === false) {
        $_SESSION['is_admin'] = false;
        $_SESSION['user_id'] = -1;
    } else {
        if (!isset($objBipConfigManager->vars['user'][$user_id]['admin'])) {
            $objBipConfigManager->vars['user'][$user_id]['admin'] = false;
        }
        $_SESSION['is_admin'] = (bool) $objBipConfigManager->vars['user'][$user_id]['admin'];
        $_SESSION['user_id'] = $user_id;
    }
}

$posible_areas = array('about', 'users', 'servers', 'system', 'state');

if (!isset($SESSION['current_place'])) {
    if (isset($_GET['site']) && in_array($_GET['site'], $posible_areas)) {
        $SESSION['current_place'] = $_GET['site'];
    } else {
        $SESSION['current_place'] = 'about';
    }
}

foreach ($posible_areas as $posible_area) {
    $objTemplate->logicAssign("current_menu_$posible_area", $SESSION['current_place'] == $posible_area, ' class="selected"');
}

ob_start();
if ($_SESSION['user_id'] > -1) {
    include $SESSION['current_place'] . '.inc.php';
} else {
    require_once 'func.func.php';
    auto_access_denied();
}
$objTemplate->assign('content', ob_get_contents());
ob_end_clean();

$objTemplate->assign('title', ' :: ' . ucfirst($SESSION['current_place']) . ( isset($title) ? ' :: ' . ucfirst($title) : ''));

$objTemplate->render($config['path'] . 'template.tpl.php');
