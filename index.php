<?php

$timestamp = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: $timestamp");
header("Last-Modified: $timestamp");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

header("Content-type: image/svg+xml");

function incrementFile($filename): int
{
    if (file_exists($filename)) {
        $fp = fopen($filename, "r+") or die("Failed to open the file.");
        flock($fp, LOCK_EX);
        $count = fread($fp, filesize($filename)) + 1;
        ftruncate($fp, 0);
        fseek($fp, 0);
        fwrite($fp, $count);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    else {
        $count = 1;
        file_put_contents($filename, $count);
    }
    return $count;
}

// short numbers from https://stackoverflow.com/a/52490452/11608064
function shortNumber($num) 
{
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}

$message = incrementFile("views.txt");

$params = [
    "label" => "Profile Views",
    "logo" => "github",
    "message" => shortNumber($message),
    "color" => "purple",
    "style" => "for-the-badge"
];

$url = "https://img.shields.io/static/v1?" . http_build_query($params);

echo file_get_contents($url);
