<?php

declare(strict_types=1);

use Symfony\Component\Process\Process;

require dirname(__DIR__) . '/vendor/autoload.php';

$_SERVER += $_ENV;
// $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev'; // not working with value from phpunit.xml.dist.
$_SERVER['APP_ENV'] = 'test';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_ENV'] !== 'prod';
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

//$process = new Process(['php', 'bin/console', 'cache:clear', '--env=test']);
//$process->run();
//if (! $process->isSuccessful()) {
//    throw new ProcessFailedException($process);
//}

/** @var Process[] $processes */
$processes = [
    new Process(['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction']),
    new Process(['php', 'bin/console', 'users:create-test-user']),
];
foreach ($processes as $process) {
    $process->run();
}
