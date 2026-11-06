<?php
$root = dirname(__DIR__) . '/.local/wordpress';
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$resolved = realpath($root . $path);
if ($resolved && str_starts_with($resolved, realpath($root) . DIRECTORY_SEPARATOR) && is_file($resolved)) return false;
$_SERVER['SCRIPT_NAME'] = '/index.php';
require $root . '/index.php';
