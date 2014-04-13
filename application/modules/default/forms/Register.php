<?php

class Form_Register extends Zend_Form
{
    public function init()
    {
        $this->setAction('/login/');
        $this->setMethod('post');
        $this->setName('registerform');

        $options = array('Herr' => 'Herr', 'Frau' => 'Frau');
        $gender = new Zend_Form_Element_Select('gender');
        $gender->addMultiOptions($options)
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_salutation'));

        $firstname = new Zend_Form_Element_Text('firstname');
        $firstname->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_firstname'))
            ->setAttrib('class', 'span2');

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_lastname'))
            ->setAttrib('class', 'span2');

        $company = new Zend_Form_Element_Text('company');
        $company->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_company'))
            ->setAttrib('class', 'span2');

        $street = new Zend_Form_Element_Text('street');
        $street->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_street'))
            ->setAttrib('class', 'span2');

        $housenumber = new Zend_Form_Element_Text('housenumber');
        $housenumber->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_housenumber'))
            ->setAttrib('class', 'span2');

        $zip_code = new Zend_Form_Element_Text('zip_code');
        $zip_code->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_zip'))
            ->setAttrib('class', 'span2');

        $city = new Zend_Form_Element_Text('city');
        $city->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_city'))
            ->setAttrib('class', 'span2');
 
        $countries = Model_Country::getAll();
        $options = array('' => '');
        foreach($countries as $c){
            $options[$c->id] = $c->name;
        }
        $country = new Zend_Form_Element_Select('country');
        $country->addMultiOptions($options)
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_country'));     

        $email = new Zend_Form_Element_Text('email');
        $email->addFilters(array('StripTags', 'StripNewlines'))
            ->addValidator('EmailAddress')
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_email'))
            ->setAttrib('class', 'span2');


        $password1 = new Zend_Form_Element_Password('password1');
        $password1->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('password'))
            ->setAttrib('class', 'span2');

        $password2 = new Zend_Form_Element_Password('password2');
        $password2->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_repeat_password'))
            ->setAttrib('class', 'span2');

        $agb = new Zend_Form_Element_Checkbox('agb');
        $agb->setRequired(true)
            ->setLabel($this->getTranslator()->translate('login_register_agb'))
            ->setAttrib('class', 'span2');
        $agb->getDecorator('Label')->setOption('escape', false); // prevent links from being escaped

        $newsletter = new Zend_Form_Element_Checkbox('newsletter');
        $newsletter->setLabel($this->getTranslator()->translate('login_register_newsletter'))
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('login_register_register'));

        $this->addElements(array($gender, $firstname, $name, $company, $street, $housenumber, $zip_code, $city, $country, $email, $password1, $password2, $agb, $newsletter, $submit));
    }
}
