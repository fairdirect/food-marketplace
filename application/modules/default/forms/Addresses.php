<?php

class Form_Addresses extends Zend_Form
{
    public function init()
    {
        $this->setAction('/meinepelia/addressesedit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'addressForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $company = new Zend_Form_Element_Text('company');
        $company->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_company'))
            ->setAttrib('class', 'span2');

        $options = array('Herr' => 'Herr', 'Frau' => 'Frau');
        $gender = new Zend_Form_Element_Select('gender');
        $gender->addMultiOptions($options)
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_salutation'))
            ->setAttrib('class', 'span2');

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

        $street = new Zend_Form_Element_Text('street');
        $street->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_street'))
            ->setAttrib('class', 'span2');

        $house = new Zend_Form_Element_Text('house');
        $house->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_housenumber'))
            ->setAttrib('class', 'span2');

        $zip = new Zend_Form_Element_Text('zip');
        $zip->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_zip'))
            ->setAttrib('class', 'span2');

        $city = new Zend_Form_Element_Text('city');
        $city->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_city'))
            ->setAttrib('class', 'span2');

        $options = array('' => '');
        $countries = Model_Region::getCountriesWithRegions();
        foreach($countries as $c){
            $options[$c->id] = $c->name;
        }
        $country = new Zend_Form_Element_Select('country');
        $country->addMultiOptions($options)
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('misc_country'))
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('misc_save'));

        $this->addElements(array($id, $company, $gender, $firstname, $name, $street, $house, $zip, $city, $country, $submit));
    }
}
