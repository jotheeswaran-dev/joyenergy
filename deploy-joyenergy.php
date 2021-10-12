#!/usr/bin/env php
<?php
/**
 * SETUP THE JOYENERGY APPLICATION AND BOOTSTRAP IT
 */

function is_executable_available(string $filename): bool
{
    if (is_executable($filename)) {
        return true;
    }
    if ($filename !== basename($filename)) {
        return false;
    }
    $paths = explode(PATH_SEPARATOR, getenv("PATH"));
    foreach ($paths as $path) {
        if (is_executable($path . DIRECTORY_SEPARATOR . $filename)) {
            return true;
        }
    }
    return false;
}
function isEnvValueSet($envKey, $fname): bool
{
    $str = file_get_contents($fname);
    $str .= "\n"; // In case the searched variable is in the last line without \n
    $keyPosition = strpos($str, "{$envKey}=");
    $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
    $line = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
    $value = substr($line, strpos($line, "=") + 1, strlen($line));
    if ($value === '') {
        return False;
    } else return True;
}

if (is_executable_available("composer")) {
    echo "Installing Laravel, related packages and setting things up...\n";
    exec("composer install", $output, $res);
    if (!file_exists(".env")) {
        exec("cp .env.example .env", $output, $res);
        if ($res !== 0) {
            echo "Error copying 'env' file...\n";
            exit;
        }
    }
    if (!isEnvValueSet("APP_KEY", ".env")) {
        echo "Generating the APP KEY...\n";
        exec("php artisan key:generate", $output1, $res1);
        if ($res1 !== 0) {
            echo "Error generating app key...\n";
            exit;
        }
    }
    system("php artisan serve");
} else {
    echo "'PHP Composer' not found....\n";
    echo "Please install 'Composer' and re-run this script...\n";
}