<?php

# For PHP7
// declare(strict_types=1);

// namespace Hug\Tests\Text;

use PHPUnit\Framework\TestCase;

use Hug\Text\Text as Text;

/**
 *
 */
final class TextTest extends TestCase
{
    public $text;
    public $text_invalid_utf8;
    public $text_invalid_curly;
    public $text_invalid_windows;
    public $text_emails;
    public $text_bom;
    public $text_spaces;

    function setUp(): void
    {
        $data = __DIR__ . '/../../../data/';
        
        $filename = $data . 'text-1.txt'; 
        $this->text = file_get_contents($filename);

        $filename = $data . 'text-invalid-utf8.txt'; 
        $this->text_invalid_utf8 = file_get_contents($filename);

        $filename = $data . 'text-invalid-curly.txt'; 
        $this->text_invalid_curly = file_get_contents($filename);

        $filename = $data . 'text-invalid-windows.txt'; 
        $this->text_invalid_windows = file_get_contents($filename);

        $filename = $data . 'text-emails.txt'; 
        $this->text_emails = file_get_contents($filename);

        $filename = $data . 'text-with-BOM.txt'; 
        $this->text_bom = file_get_contents($filename);

        $filename = $data . 'text-multiple-spaces.txt'; 
        $this->text_spaces = file_get_contents($filename);

    }

    /* ************************************************* */
    /* ***************** Text::extract ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanExtract()
    {
    	$test = Text::extract($this->text, $min_extract_length = 5, $max_extract_length = 10);
        $this->assertIsString($test);
    }
    
    /* ************************************************* */
    /* ******** Text::remove_non_breaking_spaces ******* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRemoveNonBreakingSpaces()
    {
    	$test = Text::remove_non_breaking_spaces($this->text);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* **************** Text::clean_utf8 *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanCleanUtf8()
    {
    	$test = Text::clean_utf8($this->text_invalid_utf8);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ************* Text::convert_to_utf8 ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanConvertToUtf8()
    {
    	$test = Text::convert_to_utf8($this->text_invalid_utf8);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* **************** Text::fix_curly **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanFixCurly()
    {
    	$test = Text::fix_curly($this->text_invalid_curly);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* *********** Text::fix_windows_encoding ********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanFixWindowsEncoding()
    {
    	$test = Text::fix_windows_encoding($this->text_invalid_windows);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ************* Text::remove_sentences ************ */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRemoveSentences()
    {
    	$test = Text::remove_sentences($this->text, $min_phrase_words = 12, $separator = "\n");
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ************** Text::extract_emails ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanExtractEmails()
    {
    	$test = Text::extract_emails($this->text_emails);
        $this->assertIsArray($test);
    }

    /* ************************************************* */
    /* *********** Text::extract_first_email *********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanExtractFirstEmail()
    {
    	$test = Text::extract_first_email($this->text_emails);
        $this->assertIsString($test);
    }
    
    /* ************************************************* */
    /* **************** Text::html_diff **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanHtmlDiff()
    {
    	$test = Text::html_diff($this->text, $this->text_emails);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ***************** Text::get_lang **************** */
    /* ************************************************* */

   /**
     *
     */
    public function testCanGetLang()
    {
        $test = Text::get_lang($this->text, '2');
        $this->assertIsString($test);
        $this->assertEquals('fr', $test);
    }

    /* ************************************************* */
    /* ************** Text::get_languages ************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetLanguages()
    {
        $test = Text::get_languages();
        $this->assertIsArray($test);
        $this->assertEquals(52, count($test));   
    }

    /* ************************************************* */
    /* ************* Text::remove_utf8_bom ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRemoveUtf8Bom()
    {    
        $length_before =strlen($this->text_bom);
        $test = Text::remove_utf8_bom($this->text_bom);
        $length_after = strlen($test);
        $this->assertIsString($test);
        $this->assertTrue(($length_before-$length_after)===3);   

    }

    /* ************************************************* */
    /* ********** Text::remove_multiple_spaces ********* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRemoveMultipleSpaces()
    {
        $length_before =strlen($this->text_spaces);
        $test = Text::remove_multiple_spaces($this->text_spaces);
        $length_after =strlen($test);
        $this->assertIsString($test);
        $this->assertTrue($length_after<$length_before);   
    }

}
