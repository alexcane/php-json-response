<?php


use PhpJsonResp\PasswordValidator;
use PHPUnit\Framework\TestCase;

class PasswordValidatorTest extends TestCase
{
    private PasswordValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new PasswordValidator([]);
    }

    public function testIsValidPasswordLight()
    {
        $this->assertSame('light', $this->validator->getCurrentConfigName(), 'config level should be light');

        $this->assertFalse($this->validator->isValidPassword(''), 'password is empty');
        $this->assertFalse($this->validator->isValidPassword('short'), 'password too short');
        $this->assertFalse($this->validator->isValidPassword('nodigit'), 'password without digits');
        $this->assertTrue($this->validator->isValidPassword('1digit'), 'password is valid with minimum criteria');
    }

    public function testIsValidPasswordMedium()
    {
        $this->validator->setMediumConfig();
        $this->assertSame('medium', $this->validator->getCurrentConfigName(), 'config level should be medium');

        $this->assertFalse($this->validator->isValidPassword(''), 'password is empty');
        $this->assertFalse($this->validator->isValidPassword('justshort'), 'password too short');
        $this->assertFalse($this->validator->isValidPassword('nodigitagain'), 'password without digits');
        $this->assertFalse($this->validator->isValidPassword('1digitvalid'), 'password with 1 digit but without uppercase');
        $this->assertTrue($this->validator->isValidPassword('1_Medium_Pass'), 'password with 1 digit and 1 uppercase and 1 symbol more than 10 characters');
    }

    public function testIsValidPasswordHard()
    {
        $this->validator->setHardConfig();
        $this->assertSame('hard', $this->validator->getCurrentConfigName(), 'config level should be hard');

        $this->assertFalse($this->validator->isValidPassword(''), 'password is empty');
        $this->assertFalse($this->validator->isValidPassword('justshortagainandaga'), 'password too short');
        $this->assertFalse($this->validator->isValidPassword('nodigitagain'), 'password without digits');
        $this->assertFalse($this->validator->isValidPassword('1digitvalid'), 'password with 1 digit but without uppercase');
        $this->assertTrue($this->validator->isValidPassword('My_p4$$word_!$V3ryL0ng'), 'password with 1 digit and 1 uppercase and 1 symbol more than 20 characters');
    }

    public function testIsValidPasswordCustom()
    {
        $this->validator->setCustomConfig(4, 4, true, true, true);
        $this->assertSame('custom', $this->validator->getCurrentConfigName(), 'config level should be custom');

        $this->assertFalse($this->validator->isValidPassword(''), 'password is empty');
        $this->assertFalse($this->validator->isValidPassword('toolong'), 'password is too long');
        $this->assertTrue($this->validator->isValidPassword('A$1e'), 'password with 1 digit and 1 uppercase and 1 symbol exactly 4 characters');
    }

    public function testErrorMessagesInFrench()
    {
        $validator = new PasswordValidator([], 'fr_FR');
        $validator->setLightConfig();

        $validator->isValidPassword('abc');
        $errors = $validator->getErrMsg();

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Mot de passe invalide', $errors[0]);
        $this->assertStringContainsString('6 caractères minimum', $errors[1]);
        $this->assertStringContainsString('1 chiffre requis', $errors[2]);
    }

    public function testErrorMessagesInEnglish()
    {
        $validator = new PasswordValidator([], 'en_US');
        $validator->setLightConfig();

        $validator->isValidPassword('abc');
        $errors = $validator->getErrMsg();

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid Password', $errors[0]);
        $this->assertStringContainsString('at least 6 characters long', $errors[1]);
        $this->assertStringContainsString('digits required', $errors[2]);
    }

    public function testGetRegexForLightConfig()
    {
        $this->validator->setLightConfig();
        $regex = $this->validator->getRegex();

        $this->assertStringContainsString('(?=.*\d)', $regex, 'regex should require digits');
        $this->assertStringNotContainsString('(?=.*[A-Z])', $regex, 'regex should not require uppercase');
        $this->assertStringContainsString('{6,100}', $regex, 'regex should have correct length requirement');
    }

    public function testGetRegexForMediumConfig()
    {
        $this->validator->setMediumConfig();
        $regex = $this->validator->getRegex();

        $this->assertStringContainsString('(?=.*[A-Z])', $regex, 'regex should require uppercase');
        $this->assertStringContainsString('(?=.*\d)', $regex, 'regex should require digits');
        $this->assertStringContainsString('(?=.*[!@#$%^&*(),.?:{}|<>-_])', $regex, 'regex should require symbols');
        $this->assertStringContainsString('{10,100}', $regex, 'regex should have correct length requirement');
    }
}
