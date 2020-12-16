<?php

namespace Hug\Text;

use \ForceUTF8\Encoding;

use TextLanguageDetect\TextLanguageDetect;
use TextLanguageDetect\LanguageDetect\TextLanguageDetectException;

/**
 *
 */
class Text
{

    /**
     * Returns a random extract of a text
     *
     * @param string $text
     * @param int $min_extract_length default 5
     * @param int $max_extract_length default 10
     *
     * @return string text_extract
     */
    public static function extract($text, $min_extract_length = 5, $max_extract_length = 10)
    {
        $extract_length = rand($min_extract_length, $max_extract_length);
        
        $text_array = explode(' ', $text);
        $text_length = count($text_array);
        $random_begin = 0;

        # DO NOT TAKE A LONGER EXTRACT THAN TEXT LENGTH ...
        if($extract_length > $text_length)
        {
            $extract_length = $text_length - 1;   
        }

        # GET RANDOM BEGIN DEPENDING ON EXTRACT LENGTH
        if($text_length > $extract_length)
        {
            $random_begin = rand(0, $text_length - $extract_length - 1);
        }
        
        $text_extract = null;
        $text_extract_array = array_slice($text_array, $random_begin, $extract_length);
        $text_extract_array_length = count($text_extract_array);
        if($text_extract_array_length>=$min_extract_length && $text_extract_array_length<=$max_extract_length)
        {
            $text_extract = trim( implode(' ',  $text_extract_array) );
        }

        unset($text_array);
        
        return $text_extract;
    }

    /**
     * Removes non breaking spaces from a string
     *
     * @param string $text
     *
     * @return string $text 
     *
     * @link http://stackoverflow.com/questions/12837682/non-breaking-utf-8-0xc2a0-space-and-preg-replace-strange-behaviour/30101404#30101404
     * @link http://www.asciivalue.com/index.php
     * @link http://www.ascii-code.com/
     *
     */
    public static function remove_non_breaking_spaces($text)
    {
        # Method 1 : regular expression
        //$clean_text = preg_replace('~\x{00a0}~siu', ' ', $some_text_with_non_breaking_spaces);

        # Method 2 : convert to bin -> replace -> convert to hex
        //$clean_text = hex2bin(str_replace('c2a0', '20', bin2hex($some_text_with_non_breaking_spaces)));

        # Method 3 : my favorite
        $text = str_replace("\xc2\xa0", " ", $text);

        return $text;
    }


    /**
     * Cleans bad UTF-8 chars from text
     *
     * @param string $text
     *
     * @return string $text cleaned text
     *
     */
    public static function clean_utf8($text)
    {
        // html_entity_decode ??
        $encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
        //echo $encoding.'<br>';
        if($encoding==='UTF-8')
        {
            $text = Text::fix_windows_encoding(Text::fix_curly($text));
        }
        else
        {
            # LIB
            $text = Text::convert_to_utf8($text);

            $encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
            //echo $encoding.' !!! <br>';
            if($encoding==='UTF-8')
            {
                $text = Text::fix_windows_encoding(Text::fix_curly($text));
            }
        }
        return $text;
    }

    /**
     * Converts a text into UTF-8
     *
     * @param string $text
     *
     * @return string $text
     *
     */
    public static function convert_to_utf8($text)
    {
        try
        {
            $text = \ForceUTF8\Encoding::toUTF8($text);
            //$text = \ForceUTF8\Encoding::fixUTF8($text);
        }
        catch(Exception $e)
        {
            error_log('Text::convert_to_utf8 : ' . $e->getMessage());
        }
        return $text;
    }

    /**
     * Fixes curly brackets
     * Some windows brackets are beaking encoding
     * "\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"
     *
     * @param string $string
     *
     * @return string $string
     *
     */
    public static function fix_curly($string)
    {
        // First, replace UTF-8 characters.
        $search_utf8 = ["\xe2\x80\x9c", "\xe2\x80\x9d", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x93", "\xe2\x80\x94", "\xe2\x80\xa6"];
        //$search_utf_8 = ['‘','’','“','”'];
        $replace_utf8 = ['"', '"', "'", "'", '-', '--', '...'];
        $string = str_replace($search_utf8, $replace_utf8, $string);

        // Next, replace their Windows-1252 equivalents.
        // $search_win = [chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)];
        // $replace_win = ["'", "'", '"', '"', '-', '--', '...'];
        // $string = str_replace($search_win, $replace_win, $string);

        return $string;
    }


    /**
     * Fixes windows encoding
     * 
     * @param string $text
     *
     * @return string $text
     *
     * @link http://www.i18nqa.com/debug/utf8-debug.html
     * @link http://stackoverflow.com/questions/9210473/how-to-convert-text-with-html-entites-and-invalid-characters-to-its-utf-8-equi
     * @link http://stackoverflow.com/questions/3565713/how-can-i-convert-html-character-references-x5e3-to-regular-utf-8/3566055#3566055
     * 
     */
    public static function fix_windows_encoding($text)
    {
        ## 8 bytes
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xe2\x80\x9c", "–", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xe2\x80\x9d", "—", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xe2\x84\xa2", "’", $text);

        ## 7 bytes

        $text = str_replace("\xc3\xa2\xe2\x80\x9a\xc2\xac", "€", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xc2\xa1", "‡", $text);
        $text = str_replace("\xc3\xa2\xe2\x80\x9e\xc2\xa2", "™", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xc2\xa2", "•", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xc2\xb0", "‰", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xc2\xb9", "‹", $text);// DUPLICATE
        //$text = str_replace("\xc3\xa2\xe2\x82\xac\xc2\xb9", "Š", $text);// DUPLICATE
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xc2\xba", "›", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xcb\x9c", "‘", $text);
        $text = str_replace("\xc3\xa2\xe2\x82\xac\xc5\x93", "“", $text);

        ## 6 bytes (NOT IN OPFFICIAL LIST)
        $text = str_replace("\xc3\xa2\xc2\x82\xc2\xac", "€", $text);
        $text = str_replace("\xc3\xa2\xc2\x80\xc2\xba", "›", $text);
        
        ## 5 bytes

        # \xc3\xa2\xe2\x82\xac
        $text = str_replace("\xc3\xa2\xe2\x82\xac", "”", $text);// DUPLICATE
        //$text = str_replace("\xc3\xa2\xe2\x82\xac", "†", $text);// DUPLICATE
        # \xc3\x83\xe2
        $text = str_replace("\xc3\x83\xe2\x80\x9a", "Â", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x9e", "Ä", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xa0", "Æ", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xa1", "Ç", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xa2", "Õ", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xa6", "Å", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x93", "Ö", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x94", "×", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x98", "Ñ", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x99", "Ò", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x9c", "Ó", $text);
        $text = str_replace("\xc3\x83\xe2\x80\x9d", "Ô", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xb0", "É", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xb9", "Ë", $text);
        $text = str_replace("\xc3\x83\xe2\x80\xba", "Û", $text);
        $text = str_replace("\xc3\x83\xe2\x82\xac", "À", $text);
        $text = str_replace("\xc3\x83\xe2\x84\xa2", "Ù", $text);
        # \xc3\x85\xe2\x80
        $text = str_replace("\xc3\x85\xe2\x80\x99", "Œ", $text);
        $text = str_replace("\xc3\x85\xe2\x80\x9c", "œ", $text);
        # \xc3\x8b
        $text = str_replace("\xc3\x8b\xe2\x80\xa0", "ˆ", $text);

        ## 4 bytes

        # \xc3\x81\xc2
        $text = str_replace("\xc3\x81\xc2\xa9", "é", $text);
        # \xc3\x82\xc2
        $text = str_replace("\xc3\x82\xc2\xa1", "¡", $text);
        $text = str_replace("\xc3\x82\xc2\xa2", "¢", $text);
        $text = str_replace("\xc3\x82\xc2\xa3", "£", $text);
        $text = str_replace("\xc3\x82\xc2\xa4", "¤", $text);
        $text = str_replace("\xc3\x82\xc2\xa5", "¥", $text);
        $text = str_replace("\xc3\x82\xc2\xa6", "¦", $text);
        $text = str_replace("\xc3\x82\xc2\xa7", "§", $text);
        $text = str_replace("\xc3\x82\xc2\xa8", "¨", $text);
        $text = str_replace("\xc3\x82\xc2\xa9", "©", $text);
        $text = str_replace("\xc3\x82\xc2\xaa", "ª", $text);
        $text = str_replace("\xc3\x82\xc2\xab", "«", $text);
        $text = str_replace("\xc3\x82\xc2\xac", "¬", $text);
        $text = str_replace("\xc3\x82\xc2\xad", "", $text);
        $text = str_replace("\xc3\x82\xc2\xae", "®", $text);
        $text = str_replace("\xc3\x82\xc2\xaf", "¯", $text);
        $text = str_replace("\xc3\x82\xc2\xb0", "°", $text);
        $text = str_replace("\xc3\x82\xc2\xb1", "±", $text);
        $text = str_replace("\xc3\x82\xc2\xb2", "²", $text);
        $text = str_replace("\xc3\x82\xc2\xb3", "³", $text);
        $text = str_replace("\xc3\x82\xc2\xb4", "´", $text);
        $text = str_replace("\xc3\x82\xc2\xb5", "µ", $text);
        $text = str_replace("\xc3\x82\xc2\xb6", "¶", $text);
        $text = str_replace("\xc3\x82\xc2\xb7", "·", $text);
        $text = str_replace("\xc3\x82\xc2\xb8", "¸", $text);
        $text = str_replace("\xc3\x82\xc2\xb9", "¹", $text);
        $text = str_replace("\xc3\x82\xc2\xba", "º", $text);
        $text = str_replace("\xc3\x82\xc2\xbb", "»", $text);
        $text = str_replace("\xc3\x82\xc2\xbc", "¼", $text);
        $text = str_replace("\xc3\x82\xc2\xbd", "½", $text);
        $text = str_replace("\xc3\x82\xc2\xbe", "¾", $text);
        $text = str_replace("\xc3\x82\xc2\xbf", "¿", $text);
        # \xc3\x85\xc2
        $text = str_replace("\xc3\x83\xc2\xa1", "á", $text);
        $text = str_replace("\xc3\x83\xc2\xa2", "â", $text);
        $text = str_replace("\xc3\x83\xc2\xa3", "ã", $text);
        $text = str_replace("\xc3\x83\xc2\xa4", "ä", $text);
        $text = str_replace("\xc3\x83\xc2\xa5", "å", $text);
        $text = str_replace("\xc3\x83\xc2\xa6", "æ", $text);
        $text = str_replace("\xc3\x83\xc2\xa7", "ç", $text);
        $text = str_replace("\xc3\x83\xc2\xa8", "è", $text);
        $text = str_replace("\xc3\x83\xc2\xa9", "é", $text);
        $text = str_replace("\xc3\x83\xc2\xaa", "ê", $text);
        $text = str_replace("\xc3\x83\xc2\xab", "ë", $text);
        $text = str_replace("\xc3\x83\xc2\xac", "ì", $text);
        $text = str_replace("\xc3\x83\xc2\xae", "î", $text);
        $text = str_replace("\xc3\x83\xc2\xaf", "ï", $text);
        $text = str_replace("\xc3\x83\xc2\xb0", "ð", $text);
        $text = str_replace("\xc3\x83\xc2\xb1", "ñ", $text);
        $text = str_replace("\xc3\x83\xc2\xb2", "ò", $text);
        $text = str_replace("\xc3\x83\xc2\xb3", "ó", $text);
        $text = str_replace("\xc3\x83\xc2\xb4", "ô", $text);
        $text = str_replace("\xc3\x83\xc2\xb5", "õ", $text);
        $text = str_replace("\xc3\x83\xc2\xb6", "ö", $text);
        $text = str_replace("\xc3\x83\xc2\xb7", "÷", $text);
        $text = str_replace("\xc3\x83\xc2\xb8", "ø", $text);
        $text = str_replace("\xc3\x83\xc2\xb9", "ù", $text);
        $text = str_replace("\xc3\x83\xc2\xba", "ú", $text);
        $text = str_replace("\xc3\x83\xc2\xbb", "û", $text);
        $text = str_replace("\xc3\x83\xc2\xbc", "ü", $text);
        $text = str_replace("\xc3\x83\xc2\xbd", "ý", $text);
        $text = str_replace("\xc3\x83\xc2\xbe", "þ", $text);
        $text = str_replace("\xc3\x83\xc2\xbf", "ÿ", $text);
        # \xc3\x83\xc5
        $text = str_replace("\xc3\x83\xc5\x92", "Ì", $text);
        $text = str_replace("\xc3\x83\xc5\x93", "Ü", $text);
        $text = str_replace("\xc3\x83\xc5\xa0", "Ê", $text);
        $text = str_replace("\xc3\x83\xc5\xa1", "Ú", $text);
        $text = str_replace("\xc3\x83\xc5\xb8", "ß", $text);
        $text = str_replace("\xc3\x83\xc5\xbd", "Î", $text);
        $text = str_replace("\xc3\x83\xc5\xbe", "Þ", $text);
        # \xc3\x83\xc6
        $text = str_replace("\xc3\x83\xc6\x92", "Ã", $text);
        # \xc3\x83\xcb
        $text = str_replace("\xc3\x83\xcb\x86", "È", $text);
        $text = str_replace("\xc3\x83\xcb\x9c", "Ø", $text);
        # \xc3\x85\xc2
        $text = str_replace("\xc3\x85\xc2\xa1", "š", $text);
        $text = str_replace("\xc3\x85\xc2\xb8", "Ÿ", $text);
        $text = str_replace("\xc3\x85\xc2\xbd", "Ž", $text);
        $text = str_replace("\xc3\x85\xc2\xbe", "ž", $text);
        # \xc3\x8b
        $text = str_replace("\xc3\x8b\xc5\x93", "˜", $text);

        ## 4 bytes

        # \xc3\x83
        $text = str_replace("\xc3\x83", "à", $text); // DUPLICATE
        // $text = str_replace("\xc3\x83", "Á", $text); // DUPLICATE
        // $text = str_replace("\xc3\x83", "Í", $text); // DUPLICATE
        // $text = str_replace("\xc3\x83", "Ï", $text); // DUPLICATE
        // $text = str_replace("\xc3\x83", "Ð", $text); // DUPLICATE
        // $text = str_replace("\xc3\x83", "Ý", $text); // DUPLICATE
        // $text = str_replace("\xc3\x83", "í", $text); // DUPLICATE

        ## 2 bytes

        $text = str_replace("\xc3\x82", "", $text);

        return $text;
    }

    /**
     * Cleans a webpage from small sentences 
     * Typically menus, ads etc ...
     *
     * @param string $text
     * @param string $min_phrase_words default : 12
     * @param string $separator default : "\n"
     *
     * @return string $text
     *
     */
    public static function remove_sentences($text, $min_phrase_words = 12, $separator = "\n")
    {
        $lines = explode("\n", $text);
        foreach ($lines as $key => $line)
        {
            # REMOVE MULTIPLE SPACES ?
            
            # SET PHRASES WITH LESS THAN 12 WORDS TO EMPTY
            if(count(explode(" ", $line)) < $min_phrase_words)
            {
                $lines[$key] = '';
            }
        }
        # REMOVE EMPTY LINES
        $lines = array_filter($lines);
        
        # SET DELIMITER BETWEEN PHRASES
        $text = implode("\n", $lines);

        # REPLACE NON-BREAKING SPACES BY REGULAR SPACES
        $text = str_replace("\xc2\xa0", " ", $text);

        return $text;
    }

    /**
     * Extract all emails contained in a text
     * 
     * @param string $text
     *
     * @return array $emails
     *
     */
    public static function extract_emails($text)
    {
        $emails = [];
        // (patrik.petroff@gmail.com) 
        $ats = [
            '@',
            // ' @ ',
            // '(at)',
            // '(AT)',
            // '( at )',
            // '( AT )',
            // '[at]',
            // '[ at ]',
            // '[AT]',
            // '[ AT ]',
            // ' at ',
        ];

        $exp = "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i";
        $exp_pcre = "/[a-z0-9\._%+!$&*=^|~#%'`?{}/\-]+@([a-z0-9\-]+\.){1,}([a-z]{2,6})/";
        $exp_rfc2822 = "/[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/";

        $exp_fill_1 = "/[_a-z0-9-]+(\.[_a-z0-9-]+)*";
        $exp_fill_2 = "[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i";

        foreach ($ats as $at)
        {
            $exp = $exp_fill_1 . $at . $exp_fill_2;
            $matches = [];
            preg_match_all($exp, $text, $matches);
            if(count($matches[0]) > 0)
            {
                $emails = array_merge($emails, $matches[0]);
            }
        }

        # Remove responsive images @2x.jpg
        $images = ['.jpg', '.jpeg', '.gif', '.png', '.webp'];
        foreach ($emails as $key => $email)
        {
            foreach ($images as $key2 => $image)
            {
                if(substr($email, -strlen($image))===$image)
                {
                    unset($emails[$key]);
                    break;
                }
            }
            
        }

        return $emails;
    }

    /**
     * Extract first email found in a text
     * 
     * @param string $text
     *
     * @return array $emails
     *
     */
    public static function extract_first_email($text)
    {
        $emails = [];
        $emails = Text::extract_emails($text);
        if(count($emails)>0)
        {
            return $emails[0];
        }
        else
        {
            return $emails;
        }
    }

    /**
     * Get difference between two texts
     *
     * @param string $old
     * @param string $new
     *
     * @return array $diff
     *
     */
    private static function diff($old, $new)
    {
        $matrix = array();
        $maxlen = 0;

        foreach($old as $oindex => $ovalue)
        {
            $nkeys = array_keys($new, $ovalue);
            foreach($nkeys as $nindex)
            {
                $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                    $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
                if($matrix[$oindex][$nindex] > $maxlen)
                {
                    $maxlen = $matrix[$oindex][$nindex];
                    $omax = $oindex + 1 - $maxlen;
                    $nmax = $nindex + 1 - $maxlen;
                }
            }   
        }

        if($maxlen == 0)
        {
            return array(array('d'=>$old, 'i'=>$new));
        }
        
        return array_merge(
            Text::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
            array_slice($new, $nmax, $maxlen),
            Text::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
    }

    /**
     * Get difference between two texts
     *
     * @param string $old
     * @param string $new
     *
     * @return string $ret
     *
     */
    public static function html_diff($old, $new)
    {
        $ret = '';
        
        # Get diff for text exploded by spaces
        $diff = Text::diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));

        foreach($diff as $k)
        {
            if(is_array($k))
            {
                //$ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                $ret .= (!empty($k['i']) ? " " . implode(' ',$k['i']) . " " : '');
            }
            else
            {
                $ret .= '<span style="background:#82C9FA"> '.$k .'</span>';
            }
        }
        
        return $ret;
    }


    /**
     * Guess most propable language from a text
     *
     * @param string $text Text to guess language from
     * @param string $lang_format the language code to return (can be full, 2 or 3)
     *
     * @return string $lang
     *
     * @link http://pear.php.net/manual/en/package.text.text-languagedetect.php
     */
    public static function get_lang($text, $lang_format = '2')
    {
        $lang = null;
        try
        {
            $l = new TextLanguageDetect();

            # Set the return language format
            switch($lang_format)
            {
                case 'full':
                    # Do nothing : default behavior
                    break;
                case '2':
                    $l->setNameMode(2);
                    break;
                case '3':
                    $l->setNameMode(3);
                    break;
                default:
                    break;
            }

            # Detect Language
            $lang = $l->detectSimple($text);

            unset($l);
        }
        catch (TextLanguageDetectException $e)
        {
            error_log("Text::get_lang : " . $e->getMessage());
        }

        return $lang;  
    }

    /**
     * Get all available languages with Languagedetect Library
     *
     * @return array $languages
     */
    public static function get_languages()
    {
        $languages = [];

        try 
        {
            $l = new TextLanguageDetect();
            $languages = $l->getLanguages();
            sort($languages);
            unset($l);
        } 
        catch(TextLanguageDetectException $e)
        {
            error_log("Text::get_languages : " . $e->getMessage());
        }
        
        return $languages;
    }

    /**
     * Remove UTF-8 BOM from text
     *
     * @param string $text
     * @return string $text cleaned text from BOM
     */
    public static function remove_utf8_bom($text)
    {
        $bom = pack('H*','EFBBBF');
        $text = preg_replace("/^$bom/", '', $text);
        return $text;
    }

    /**
     * Remove multiple spaces, tabs and line breaks from text
     *
     * @param string $text
     * @return string $text cleaned text from multiple spaces, tabs and line breaks
     */
    public static function remove_multiple_spaces($text)
    {
        //$html = preg_replace('~\x{00a0}~','',$html);
        // Ligne qui peut poser problème !
        $text = preg_replace('~\x{00a0}~siu','',$text);
        $text = preg_replace('/\s+/', ' ',$text);
        return $text;
    }
}
