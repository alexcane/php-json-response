<?php


use PhpJsonResp\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{

    public function testIsValidPassword()
    {
        $resp = new PasswordValidator([]);

        //light

        $isValid = $resp->isValidPassword('');
        $this->assertFalse($isValid, 'password is empty');

        $isValid = $resp->isValidPassword('short');
        $this->assertFalse($isValid, 'password too short');

        $isValid = $resp->isValidPassword('nodigit');
        $this->assertFalse($isValid);

        $isValid = $resp->isValidPassword('1digit');
        $this->assertFalse($isValid);

        //medium
        $resp->setMediumConfig();

//        $isValid = $resp->isValidPassword('short');
//        $this->assertFalse($isValid);
//
//        $isValid = $resp->isValidPassword('short');
//        $this->assertFalse($isValid);
    }
}
