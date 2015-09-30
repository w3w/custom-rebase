<?php

require_once 'touch-commit.php';
$path = getcwd();
$from = $argv[1];
$to = $argv[2];
$scriptPath = dirname($argv[0]);
define('SCRIPT_ROOT', $scriptPath);

$commandShowTree = sprintf('git log --oneline %s..%s', $from, $to);

$output = array();
cmd($commandShowTree, $output);

$revisions = array_map(function ($item) {
    return substr($item, 0, strpos($item, ' '));
}, $output);
$revisions = array_reverse($revisions);

foreach ($revisions as $revision) {
    echo 'processing revision ' . $revision . PHP_EOL;
    touchCommit($revision);
}


