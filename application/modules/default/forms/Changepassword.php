<?php

class Form_Changepassword extends Zend_Form
{
    public function init()
    {
        $this->setAction('/login/resetpasswordconfirm');
        $this->setMethod('post');
        $this->setName('changepasswordform');

        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('DtDdWrapper')->removeDecorator('label');

        $passwordResetToken = new Zend_Form_Element_Hidden('password_reset_token');
        $passwordResetToken->removeDecorator('DtDdWrapper')->removeDecorator('label');

        $password1 = new Zend_Form_Element_Password('password1');
        $password1->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_password'))
            ->setAttrib('class', 'span2');

        $password2 = new Zend_Form_Element_Password('password2');
        $password2->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_repeat_password'))
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('changepassword');
        $submit->setLabel($this->getTranslator()->translate('login_reset_password'));

        $this->addElements(array($id, $passwordResetToken, $password1, $password2, $submit));
    }
}
