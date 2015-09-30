<?php

require_once 'convert.functions.php';
function touchCommit($commit)
{
    $commandMessage = sprintf('git log --format=%%B -n 1 %s', $commit);
    cmd($commandMessage, $output);
    $commitMessage = $output[0];

    echo $commitMessage . PHP_EOL;

    $phpFiles = getChangedPhpFiles($commit, 'M');
    foreach ($phpFiles as $phpFile) {
        convertFile(getcwd() . '/' . $phpFile);
    }
    $gitCommitCmd = 'git commit -aqm "%s"';
    $commitCommand = sprintf($gitCommitCmd, $commitMessage);
    $output = null;
    cmd($commitCommand, $output);

    $commandCherryPick = sprintf('git cherry-pick -n %s', $commit);
    cmd($commandCherryPick, $output);

    echo "Pausing command..." . PHP_EOL;
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    $commitCommand = sprintf($gitCommitCmd, 'fixup! ' . $commitMessage);
    $output = null;
    cmd($commitCommand, $output);

    $phpFiles = getChangedPhpFiles($commit, 'ACMR');
    foreach ($phpFiles as $phpFile) {
        revertFile(getcwd() . '/' . $phpFile);
    }
    $commitCommand = sprintf($gitCommitCmd, 'fixup! ' . $commitMessage);
    $output = null;
    cmd($commitCommand, $output);
}

function getChangedPhpFiles($commit, $mode = 'ACMR')
{
    $commandDiff = sprintf('git show --name-only %s --diff-filter %s', $commit, $mode);

    $output = [];
    cmd($commandDiff, $output);

    $phpFiles = array_filter($output, function ($file) {
        $info = new SplFileInfo($file);
        return $info->getExtension() == 'php';
    });

    return $phpFiles;
}
