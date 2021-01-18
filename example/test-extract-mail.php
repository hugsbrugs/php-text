<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hug\Text\Text as Text;

$filename = __DIR__ . '/../data/text-emails-u003e.txt'; 
$text_emails = file_get_contents($filename);

$test = Text::extract_emails($text_emails, ['info@figapps.co']);
echo 'Text::extract_emails' . "\n";
echo print_r($test, true) . "\n";
