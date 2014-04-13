<?php

class Form_Login extends Zend_Form
{
    public function init()
    {
        $this->setAction('/login/');
        $this->setMethod('post');
        $this->setName('loginform');

        $email = new Zend_Form_Element_Text('email');
        $email->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_email'))
            ->setAttrib('class', 'span2');

        $password = new Zend_Form_Element_Password('password');
        $password->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_password'))
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Login');

        $this->addElements(array($email, $password, $submit));
    }
}

