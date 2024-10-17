<?php


use PhpJsonResp\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{
    public function testIsValidPasswordLight()
    {
        $resp = new PasswordValidator([]);
        $this->assertTrue('light'===$resp->getCurrentConfigName(), 'config level should be light');

        $isValid = $resp->isValidPassword('');
        $this->assertFalse($isValid, 'password is empty');

        $isValid = $resp->isValidPassword('short');
        $this->assertFalse($isValid, 'password too short');

        $isValid = $resp->isValidPassword('nodigit');
        $this->assertFalse($isValid, 'password without digits');

        $isValid = $resp->isValidPassword('1digit');
        $this->assertTrue($isValid, 'password is valid with minimum criteria');
    }

    public function testIsValidPasswordMedium()
    {
        $resp = new PasswordValidator([]);
        $resp->setMediumConfig();
        $this->assertTrue('medium'===$resp->getCurrentConfigName(), 'config level should be medium');

        $isValid = $resp->isValidPassword('');
        $this->assertFalse($isValid, 'password is empty');

        $isValid = $resp->isValidPassword('justshort');
        $this->assertFalse($isValid, 'password too short');

        $isValid = $resp->isValidPassword('nodigitagain');
        $this->assertFalse($isValid, 'password without digits');

        $isValid = $resp->isValidPassword('1digitvalid');
        $this->assertFalse($isValid, 'password with 1 digit but without uppercase');

        $isValid = $resp->isValidPassword('1_Medium_Pass');
        $this->assertTrue($isValid, 'password with 1 digit and 1 uppercase and 1 symbol more than 10 characters');
    }

    public function testIsValidPasswordHard()
    {
        $resp = new PasswordValidator([]);
        $resp->setHardConfig();
        $this->assertTrue('hard'===$resp->getCurrentConfigName(), 'config level should be hard');

        $isValid = $resp->isValidPassword('');
        $this->assertFalse($isValid, 'password is empty');

        $isValid = $resp->isValidPassword('justshortagainandaga');
        $this->assertFalse($isValid, 'password too short');

        $isValid = $resp->isValidPassword('nodigitagain');
        $this->assertFalse($isValid, 'password without digits');

        $isValid = $resp->isValidPassword('1digitvalid');
        $this->assertFalse($isValid, 'password with 1 digit but without uppercase');

        $isValid = $resp->isValidPassword('My_p4$$word_!$V3ryL0ng');
        $this->assertTrue($isValid, 'password with 1 digit and 1 uppercase and 1 symbol more than 20 characters');
    }

    public function testIsValidPasswordCustom()
    {
        $resp = new PasswordValidator([]);
        $resp->setCustomConfig(4, 4, true, true, true);
        $this->assertTrue('custom'===$resp->getCurrentConfigName(), 'config level should be custom');

        $isValid = $resp->isValidPassword('');
        $this->assertFalse($isValid, 'password is empty');

        $isValid = $resp->isValidPassword('toolong');
        $this->assertFalse($isValid, 'password is too long');

        $isValid = $resp->isValidPassword('A$1e');
        $this->assertTrue($isValid, 'password with 1 digit and 1 uppercase and 1 symbol more than 20 characters');
    }
}
