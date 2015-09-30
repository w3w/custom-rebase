<?php

require_once 'convert.functions.php';
function touchCommit($commit)
{
    $commandMessage = sprintf('git log --format="%%an:::%%ae:::%%ad" --date=raw  -n 1 %s', $commit);
    $output = null;
    cmd($commandMessage, $output);
    list($authorName, $authorEmail, $commitDate) = explode(':::', $output[0]);
    $commandMessage = sprintf('git log --format=%%B -n 1 %s', $commit);
    $output = null;
    cmd($commandMessage, $output);
    $shortCommitMessage = $output[0];
    $commitMessage = trim(implode("\n", $output));
    $commitTemplatePath = SCRIPT_ROOT . '/commit-template';
    file_put_contents($commitTemplatePath, $commitMessage);
    echo $commitMessage . PHP_EOL;

    $phpFiles = getChangedPhpFiles($commit, 'M');
    foreach ($phpFiles as $phpFile) {
        convertFile(getcwd() . '/' . $phpFile);
    }
    $gitCommitCmd = 'git commit -a -q --allow-empty -F %s --date=%s --author=%s';
    $author = sprintf('%s <%s>', $authorName, $authorEmail);
    $commitCommand = sprintf($gitCommitCmd, escapeshellarg($commitTemplatePath), escapeshellarg($commitDate), escapeshellarg($author));
    $output = null;
    cmd($commitCommand, $output); // commit convert

    $commandCherryPick = sprintf('git cherry-pick -n %s', $commit);
    $returnCode = null;
    cmd($commandCherryPick, $output, $returnCode);
    echo $returnCode;
    if ($returnCode) {
        echo "Pausing command..." . PHP_EOL;
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
    } else {
        echo 'Skipping pause...' . PHP_EOL;
    }
    $output = null;
    cmd($commitCommand . ' --amend', $output); //commit cherrypick

    $phpFiles = getChangedPhpFiles($commit, 'ACMR');
    foreach ($phpFiles as $phpFile) {
        revertFile(getcwd() . '/' . $phpFile);
    }
    $output = null;
    cmd($commitCommand . ' --amend', $output); // commit revert
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
