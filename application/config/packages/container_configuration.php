<?php
/**
 * Created by PhpStorm.
 * User: valkeru
 * Date: 18.08.18
 * Time: 22:45
 */

$container->setParameter('app.system_hostname', gethostname());
$container->setParameter('graylog_debug_level', ($_SERVER['APP_ENV'] === 'dev' || $_SERVER['APP_ENV'] === 'test') ? \Monolog\Logger::DEBUG : \Monolog\Logger::WARNING);
