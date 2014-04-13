<?php

class Form_Wholesale extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setName('wholesaleform');
        $this->setAttrib('enctype', 'multipart/form-data');

        $company = new Zend_Form_Element_Text('company');
        $company->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_company'))
            ->setAttrib('class', 'span2');
        
        $contact = new Zend_Form_Element_Text('contact');
        $contact->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_contact_person'))
            ->setAttrib('class', 'span2');

        $trade_certificate = new Zend_Form_Element_File('trade_certificate');
        $trade_certificate
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_trade_certificate'))
            ->setAttrib('class', 'span2');
        
        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('login_register_register'));

        $this->addElements(array($company, $contact, $trade_certificate, $submit));
    }
}

