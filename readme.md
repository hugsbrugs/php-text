# php-text

This librairy provides utilities function to ease text manipulation

[![Build Status](https://travis-ci.org/hugsbrugs/php-text.svg?branch=master)](https://travis-ci.org/hugsbrugs/php-text)
[![Coverage Status](https://coveralls.io/repos/github/hugsbrugs/php-text/badge.svg?branch=master)](https://coveralls.io/github/hugsbrugs/php-text?branch=master)

## Install

Install package with composer
```
composer require hugsbrugs/php-text
```

In your PHP code, load library
```php
require_once __DIR__ . '/../vendor/autoload.php';
use Hug\Text\Text as Text;
```

## Usage

Returns a random extract of a text
```php
Text::extract($text, $min_extract_length = 5, $max_extract_length = 10);
```

Removes non breaking spaces from a string
```php
Text::remove_non_breaking_spaces($text);
```

Cleans bad UTF-8 chars from text
```php
Text::clean_utf8($text);
```

Converts a text into UTF-8
```php
Text::convert_to_utf8($text);
```

Fixes curly brackets
```php
Text::fix_curly($text);
```

Fixes windows encoding
```php
Text::fix_windows_encoding($text);
```

Cleans a text from small sentences
```php
Text::remove_sentences($text, $min_phrase_words = 12, $separator = "\n");
```

Extract all emails contained in a text
```php
Text::extract_emails($text);
```

Extract first email found in a text
```php
Text::extract_first_email($text);
```

Get difference between two texts
```php
Text::diff($old, $new);
```

Get difference between two texts in HTML
```php
Text::html_diff($old, $new);
```

Guess most propable language from a text using PEAR DetectLanguage
```php
Text::get_lang($text, '2');
```

Get all available languages with PEAR DetectLanguage Library
```php
$test = Text::get_languages();
```

Remove UTF-8 BOM from text
```php
$text_without_bom = Text::remove_utf8_bom($text_with_bom);
```

Remove multiple spaces, tabs and line breaks from text
```php
$text_without_spaces = Text::remove_multiple_spaces($text_with_spaces);
```




## Author

Hugo Maugey [Webmaster](https://hugo.maugey.fr/webmaster) | [Consultant SEO](https://hugo.maugey.fr/consultant-seo) | [Fullstack developer](https://hugo.maugey.fr/developpeur-web)