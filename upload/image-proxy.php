<?php
if (!isset($_GET['src'])) {
    http_response_code(400);
    exit('No image source specified.');
}

$src = $_GET['src'];

// Basic security: block local file paths and certain protocols
if (preg_match('#^(file|php|data|zip|glob|phar)://#i', $src)) {
    http_response_code(403);
    exit('Invalid protocol.');
}

// Fetch the image
$ch = curl_init($src);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // allow self-signed
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$data = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

if ($data === false) {
    http_response_code(404);
    exit('Image not found.');
}

// Output with correct content type
header("Content-Type: " . ($contentType ?: 'image/jpeg'));
echo $data;
