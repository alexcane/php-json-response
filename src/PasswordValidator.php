<?php

namespace PhpJsonResp;

class PasswordValidator extends JsonResp
{
    private int $_min;
    private int $_max;
    private bool $_uppercase;
    private bool $_lowercase;
    private bool $_digits;
    private bool $_symbols;
    private string $_currentConfig;

    /**
     * Force light config by default
     * @param array $data
     */
    public function __construct(array $data){
        parent::__construct($data);
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
        $this->_lowercase = true;
        $this->_digits = true;
        $this->_symbols = false;
        $this->_currentConfig = 'light';
        return $this;
    }

    /**
     * Set config with :
     * - at least 10 characters long
     * - digits required
     * - lowercase and uppercase required
     * - symbols required
     * @return $this
     */
    public function setMediumConfig(): self
    {
        $this->_min = 10;
        $this->_max = 100;
        $this->_uppercase = true;
        $this->_lowercase = true;
        $this->_digits = true;
        $this->_symbols = true;
        $this->_currentConfig = 'medium';
        return $this;
    }

    /**
     * Set config with :
     * - at least 20 characters long
     * - digits required
     * - lowercase and uppercase required
     * - symbols required
     * @return $this
     */
    public function setHardConfig(): self
    {
        $this->_min = 20;
        $this->_max = 100;
        $this->_uppercase = true;
        $this->_lowercase = true;
        $this->_digits = true;
        $this->_symbols = true;
        $this->_currentConfig = 'hard';
        return $this;
    }

    //TODO: set method setCustomConfig(int $min, int $max, ...)

    /**
     * @param $password
     * @param string $lang [optional]
     * @return bool
     */
    public function isValidPassword($password, string $lang='fr_FR'): bool
    {
        $isValid = preg_match($this->_regex(), $password);
        if(!$isValid) $this->_addErrMsg($lang);
        return (bool)$isValid;
    }

    /**
     * Regex constructor
     * @return string
     */
    private function _regex(): string
    {
        $regex = '/^'; // start
        if($this->_uppercase) $regex.= '(?=.*[A-Z])';
        if($this->_digits) $regex.= '(?=.*\d)';
        if($this->_symbols) $regex.= '(?=.*[!@#$%^&*(),.?":{}|<>])';
        $regex.= '[A-Za-z\d!@#$%^&*(),.?":{}|<>]'; // authorized characters
        $regex.= '{'. $this->_min .','. $this->_max .'}'; // string length
        $regex.= '$/'; // end
        return $regex;
    }

    /**
     * Errors injector
     * @param string $lang
     * @return void
     */
    private function _addErrMsg(string $lang): void
    {
        $this->addErrMsg('Mot de passe invalide');
        $this->addErrMsg('-'. $this->_min .' caractères minimum');
        if($this->_uppercase) $this->addErrMsg('- 1 majuscule requise');
        if($this->_digits) $this->addErrMsg('- 1 chiffre requis');
        if($this->_symbols) $this->addErrMsg('- 1 symbole requise');
        if('fr_FR'===$lang) return;

        // if the language is different from French, english is set by default
        $this->clearErrMsg();
        $this->addErrMsg('Invalid Password');
        $this->addErrMsg('- at least '. $this->_min .' characters long');
        if($this->_uppercase) $this->addErrMsg('uppercases required');
        if($this->_digits) $this->addErrMsg('digits required');
        if($this->_symbols) $this->addErrMsg('symbols required');
    }
}