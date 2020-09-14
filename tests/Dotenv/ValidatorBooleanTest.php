<?php

use Dotenv\Dotenv;

class ValidatorBooleanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $fixturesFolder;

    public function setUp()
    {
        $this->fixturesFolder = dirname(__DIR__).'/fixtures/env';
    }

    /**
     * List of valid boolean values in fixtures/env/booleans.env
     *
     * @return array
     */
    public function validBooleanValuesDataProvider()
    {
        return [
            ['VALID_EXPLICIT_LOWERCASE_TRUE'],
            ['VALID_EXPLICIT_LOWERCASE_FALSE'],
            ['VALID_EXPLICIT_UPPERCASE_TRUE'],
            ['VALID_EXPLICIT_UPPERCASE_FALSE'],
            ['VALID_EXPLICIT_MIXEDCASE_TRUE'],
            ['VALID_EXPLICIT_MIXEDCASE_FALSE'],

            ['VALID_NUMBER_TRUE'],
            ['VALID_NUMBER_FALSE'],

            ['VALID_ONOFF_LOWERCASE_TRUE'],
            ['VALID_ONOFF_LOWERCASE_FALSE'],
            ['VALID_ONOFF_UPPERCASE_TRUE'],
            ['VALID_ONOFF_UPPERCASE_FALSE'],
            ['VALID_ONOFF_MIXEDCASE_TRUE'],
            ['VALID_ONOFF_MIXEDCASE_FALSE'],

            ['VALID_YESNO_LOWERCASE_TRUE'],
            ['VALID_YESNO_LOWERCASE_FALSE'],
            ['VALID_YESNO_UPPERCASE_TRUE'],
            ['VALID_YESNO_UPPERCASE_FALSE'],
            ['VALID_YESNO_MIXEDCASE_TRUE'],
            ['VALID_YESNO_MIXEDCASE_FALSE'],
        ];
    }

    /**
     * @dataProvider validBooleanValuesDataProvider
     */
    public function testCanValidateBooleans($boolean)
    {
        $dotenv = new Dotenv($this->fixturesFolder, 'booleans.env');
        $dotenv->load();

        $dotenv->required($boolean)->isBoolean();

        $this->assertTrue(true); // anything wrong - an exception will be thrown
    }
}
