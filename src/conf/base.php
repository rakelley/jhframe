<?php
/**
 * All configuration values
 * 
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

/**
 * Base framework config file, should be called before app-specific config
 */

$confDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;

$appConfig = require($confDir . '_app.php');

$classes = json_decode(file_get_contents($confDir . '_classes.json'), true);

$envConfig = require($confDir . '_env.php');

$phpConfig = [
    'timezone'   => 'America/Chicago',
    'log_errors' => 1,
    'error_log'  => $envConfig['log_dir'] . $envConfig['log_default'],
];

return [
    'APP'     => $appConfig,
    'CLASSES' => $classes,
    'ENV'     => $envConfig,
    'PHP'     => $phpConfig,
];
