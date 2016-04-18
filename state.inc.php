<?php

/**
 * Include file for state operations
 *
 * @since       0.1
 * @author		MekDrop <github@mekdrop.name>
 */
require_once 'func.func.php';

switch (@$_REQUEST['action']) {
    case 'start':
        if (auto_access_denied())
            return;
        exec($config['app_command']);
        header('location: ?site=state');
        die();
        break;
    case 'stop':
        if (auto_access_denied())
            return;
        $pid = intval(file_get_contents($objBipConfigManager->vars['pid_file']));
        system('kill ' . $pid, $rez);
        header('location: ?site=state');
        die();
        break;
    case 'restart':
        if (auto_access_denied())
            return;
        $pid = intval(file_get_contents($objBipConfigManager->vars['pid_file']));
        system('kill ' . $pid, $rez);
        header('location: ?site=state&action=start');
        die();
        break;
}

echo "<b>Current state:</b> ";

$is_running = false;

if (file_exists($objBipConfigManager->vars['pid_file'])) {
    ob_start();
    $pid = intval(file_get_contents($objBipConfigManager->vars['pid_file']));
    $lpid = intval(trim(system('ps ' . $pid, $retval)));
    $is_running = $pid == $lpid;
    ob_end_clean();
}

if ($is_running) {
    echo 'running';
    if ($_SESSION['is_admin']) {
        br(2);
        action_button('stop', 'Stop');
        action_button('restart', 'Restart');
    }
} else {
    echo 'stopped';
    if ($_SESSION['is_admin']) {
        br(2);
        action_button('start', 'Start');
    }
}
