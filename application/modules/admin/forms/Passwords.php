<?php

class Admin_Form_Passwords extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setName('passwordform');

        $user_id = new Zend_Form_Element_Hidden('id');

        $password1 = new Zend_Form_Element_Password('password1');
        $password1->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Passwort')
            ->setAttrib('class', 'span2');

        $password2 = new Zend_Form_Element_Password('password2');
        $password2->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Passwort wiederholen')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($user_id, $password1, $password2, $submit));
    }
}
