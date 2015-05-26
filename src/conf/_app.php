<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

use \rakelley\jhframe\interfaces\services\IExceptionHandler;

if (isset($_SERVER['HTTP_HOST'])) {
    if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443)
    ) {
        $connectionType = 'https://';
    } else {
        $connectionType = 'http://';
    }

    $basePath = $connectionType . $_SERVER['HTTP_HOST'] . '/';
} else { //fallback for HTTP 1.0 and Local
    $basePath = 'unknown';
}

return [
    'name'                => 'unknown',
    'base_path'           => $basePath,
    'exception_log_level' => IExceptionHandler::LOGGING_ALL,
    'error_view'          => 'error.php',
    'force_https'         => false,
    'input_rules'         => [],
];
