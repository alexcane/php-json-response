<?php

namespace PhpJsonResp;

class PasswordValidator extends JsonResp
{
    private int $_min;
    private int $_max;
    private bool $_uppercase;
    private bool $_digits;
    private bool $_symbols;
    private string $_language;

    /**
     * Force light config by default
     * @param array $data
     * @param string $lang [optional]
     */
    public function __construct(array $data, string $lang='fr_FR'){
        parent::__construct($data);
        $this->_language = $lang;
        $this->setLightConfig();
    }

    /**
     * Set config with :
     * - at least 6 characters long
     * - digits required
     * - uppercase optional
     * - symbols optional
     * @return $this
     */
    public function setLightConfig(): self
    {
        $this->_min = 6;
        $this->_max = 100;
        $this->_uppercase = false;
        $this->_digits = true;
        $this->_symbols = false;
        return $this;
    }

    /**
     * Set config with :
     * - at least 10 characters long
     * - digits required
     * - uppercase required
     * - symbols required
     * @return $this
     */
    public function setMediumConfig(): self
    {
        $this->_min = 10;
        $this->_max = 100;
        $this->_uppercase = true;
        $this->_digits = true;
        $this->_symbols = true;
        return $this;
    }

    /**
     * Set config with :
     * - at least 20 characters long
     * - digits required
     * - uppercase required
     * - symbols required
     * @return $this
     */
    public function setHardConfig(): self
    {
        $this->_min = 20;
        $this->_max = 100;
        $this->_uppercase = true;
        $this->_digits = true;
        $this->_symbols = true;
        return $this;
    }

    /**
     * Set custom config with :
     * - min length
     * - max length
     * - digits if true : required
     * - uppercase if true : required
     * - symbols if true : required
     * @return $this
     */
    public function setCustomConfig(int $min, int $max, bool $uppercase, bool $digits, bool $symbols): self
    {
        $this->_min = $min;
        $this->_max = $max;
        $this->_uppercase = $uppercase;
        $this->_digits = $digits;
        $this->_symbols = $symbols;
        return $this;
    }

    /**
     * @param $password
     * @return bool
     */
    public function isValidPassword($password): bool
    {
        $isValid = preg_match($this->_regex(), $password);
        if(!$isValid) $this->_addErrMsg();
        return (bool)$isValid;
    }

    /**
     * Regex constructor
     * @return string
     */
    private function _regex(): string
    {
        $regex = '/^'; // start
        if($this->_uppercase) $regex.= '(?=.*[A-Z])'; // required uppercase characters
        if($this->_digits) $regex.= '(?=.*\d)'; // required digit characters
        if($this->_symbols) $regex.= '(?=.*[!@#$%^&*(),.?":{}|<>])'; // required symbols characters
        $regex.= '[A-Za-z\d!@#$%^&*(),.?":{}|<>]'; // authorized characters
        $regex.= '{'. $this->_min .','. $this->_max .'}'; // string length
        $regex.= '$/'; // end
        return $regex;
    }

    /**
     * Errors injector
     * @return void
     */
    private function _addErrMsg(): void
    {
        $this->addErrMsg('Mot de passe invalide');
        $this->addErrMsg('-'. $this->_min .' caractères minimum');
        if($this->_uppercase) $this->addErrMsg('- 1 majuscule requise');
        if($this->_digits) $this->addErrMsg('- 1 chiffre requis');
        if($this->_symbols) $this->addErrMsg('- 1 symbole requise');
        if('fr_FR'===$this->_language) return;

        // if the language is different from French, english is set by default
        $this->clearErrMsg();
        $this->addErrMsg('Invalid Password');
        $this->addErrMsg('- at least '. $this->_min .' characters long');
        if($this->_uppercase) $this->addErrMsg('uppercases required');
        if($this->_digits) $this->addErrMsg('digits required');
        if($this->_symbols) $this->addErrMsg('symbols required');
    }
}