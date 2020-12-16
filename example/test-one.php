<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hug\Text\Text as Text;

$filename = __DIR__ . '/../data/text-1.txt'; 
$text = file_get_contents($filename);

$filename = __DIR__ . '/../data/text-invalid-utf8.txt'; 
$text_invalid_utf8 = file_get_contents($filename);

$filename = __DIR__ . '/../data/text-invalid-curly.txt'; 
$text_invalid_curly = file_get_contents($filename);

$filename = __DIR__ . '/../data/text-invalid-windows.txt'; 
$text_invalid_windows = file_get_contents($filename);

$filename = __DIR__ . '/../data/text-emails.txt'; 
$text_emails = file_get_contents($filename);

$filename = __DIR__ . '/../data/text-with-BOM.txt'; 
$text_bom = file_get_contents($filename);

$filename = __DIR__ . '/../data/text-multiple-spaces.txt'; 
$text_spaces = file_get_contents($filename);


/* ************************************************* */
/* ************** Text::extract_emails ************* */
/* ************************************************* */

$test = Text::extract_emails($text_emails);
echo 'Text::extract_emails' . "\n";
echo print_r($test, true) . "\n";

/* ************************************************* */
/* *********** Text::extract_first_email *********** */
/* ************************************************* */

$test = Text::extract_first_email($text_emails);
echo 'Text::extract_first_email' . "\n";
echo $test . "\n";

