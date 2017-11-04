<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/4/2017
 * Time: 8:19 AM
 */

use Tops\db\TDbTranslator;
use PHPUnit\Framework\TestCase;
use Tops\sys\TPath;
use TwoQuakers\testing\RepositoryTestFixture;

class TDbTranslatorTest extends RepositoryTestFixture
{
    public function setUp()
    {
        $this->runSqlScript('new-translations-table');
    }

    public function testCoreImport()
    {
        $path = TPath::fromFileRoot('vendor/twoquakers/tops/tops/lib/sys/translations.ini');
        $ini = parse_ini_file($path);
        $expected = count($ini);
        $this->runSqlScript('new-translations-table');
        $actual = TDbTranslator::ImportCoreTranslations();
        $this->assertEquals($expected,$actual);

    }

    public function testCoreTranslations() {
        $this->runSqlScript('add-core-translations');
        $translator = new TDbTranslator();
        $translator->setLanguageCode('en-US');

        $actual = $translator->getText('error-unknown-file');
        $expected = 'File not found.';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('error-no-file'), 'filename');
        $expected = 'The file "filename" was not found.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('service-failed');
        $expected = 'Service failed. If the problem persists contact the site administrator.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('service-insecure');
        $expected = 'Your request contains potentially insecure content. HTML tags are not allowed.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('service-invalid-request');
        $expected = 'Invalid request';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('service-no-auth');
        $expected = 'Sorry, you are not authorized to use this service.';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('service-no-request-value'), 'fieldname');
        $expected = 'No "fieldname" value was received.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('service-no-request');
        $expected = 'No request was received';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('session-expired');
        $expected = 'Sorry, your session has expired or is not valid. Please return to home page.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('smtp-warning-1');
        $expected = 'Address is valid for SMTP but has unusual elements';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('smtp-warning-2');
        $expected = 'Address is valid within the message but cannot be used unmodified for the envelope';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('smtp-warning-3');
        $expected = 'Address contains deprecated elements but may still be valid in restricted contexts';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('smtp-warning-4');
        $expected = 'The address is only valid according to the broad definition of RFC 5322. It is otherwise invalid.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('title-key-words');
        $expected = 'the,a,of,an,in,and';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('validation-code-blank');
        $expected = 'The code field cannot be blank.';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('validation-email-req');
        $expected = 'A valid email address is required';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('validation-field-invalid2'), 'entry', 'field');
        $expected = 'The entry "entry" is not valid for "field".';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('validation-field-invalid'), 'entry');
        $expected = 'The entry "entry" is not valid".';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('validation-field-req2'), 'entry', 'field');
        $expected = 'An entry "entry" is required for "field".';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('validation-field-req'), 'field');
        $expected = 'An entry is required for "field".';
        $this->assertEquals($expected, $actual);
        $actual = $translator->getText('validation-invalid-email2');
        $expected = 'The email address is not valid.';
        $this->assertEquals($expected, $actual);
        $actual = sprintf($translator->getText('validation-invalid-email'), 'email');
        $expected = 'The email address "email" is not valid.';
        $this->assertEquals($expected, $actual);
        $expected = 'No translations, use this';
        $actual = $translator->getText($expected);
        $this->assertEquals($expected,$actual);
        $expected = 'No translations, use this';
        $actual = $translator->getText('no-valid-key',$expected);
        $this->assertEquals($expected,$actual);
    }
}
