<?php
/**
 * ENV configuration values
 * 
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

$rootDir = JHFRAME_ROOTDIR;

return [
    'root_dir'     => $rootDir,
    'public_dir'   => $rootDir . 'public_html/',
    'cache_dir'    => $rootDir . 'cache/',
    'log_dir'      => $rootDir . 'logs/',
    'log_default'  => 'default.txt',
    'log_critical' => 'critical.txt',
    'log_info'     => 'info.txt',
    'log_user'     => 'user.txt',
    'is_ajax'      => $isAjax,
    'type'         => 'development',
];
