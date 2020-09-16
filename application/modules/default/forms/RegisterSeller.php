<?php

class Form_RegisterSeller extends Zend_Form
{
    public function init()
    {
        $this->setAction('/verkaufen/register/');
        $this->setMethod('post');
        $this->setName('registersellerform');

        $company = new Zend_Form_Element_Text('company');
        $company->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_company'))
            ->setAttrib('class', 'span2');

        $shopname = new Zend_Form_Element_Text('shopname');
        $shopname->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_shopname'))
            ->setAttrib('class', 'span2');
  
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
        
        $phone = new Zend_Form_Element_Text('phone');
        $phone->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_phone'))
            ->setAttrib('class', 'span2');

        $email = new Zend_Form_Element_Text('email');
        $email->addFilters(array('StripTags', 'StripNewlines'))
            ->addValidator('EmailAddress')
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_email'))
            ->setAttrib('class', 'span2');
 
        $countries = Model_Region::getCountriesWithRegions();
        $options = array('' => '');
        foreach($countries as $c){
            $options[$c->id] = $c->name;
        }
        $country = new Zend_Form_Element_Select('country');
        $country->addMultiOptions($options)
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_country'));     

        $website = new Zend_Form_Element_Text('website');
        $website->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_website'))
            ->setAttrib('class', 'span2');

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

        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('login_register_register'));

        $this->addElements(array($shopname, $company, $gender, $firstname, $name, $phone, $email, $country, $website, $password1, $password2, $submit));
    }
}
