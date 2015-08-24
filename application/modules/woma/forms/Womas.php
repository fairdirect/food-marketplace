<?php

class Woma_Form_Womas extends Zend_Form
{
    public function init()
    {
        $this->setAction('/business/womas/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'womaForm'));

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('woma_woma_name'))
            ->setAttrib('class', 'span2')
            ->setOrder(30);

        $url = new Zend_Form_Element_Text('url');
        $url->addFilters(array('StripTags', 'StripNewlines'))
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('business_shop_url'))
            ->setOrder(40);

        $this->addDisplayGroup(array($name, $url), 'shop_data', array('legend' => $this->getTranslator()->translate('business_shop_infos')));

        $options = array('AG' => $this->getTranslator()->translate('business_shop_ag'), 'e.G. (eingetragene Gesellschaft)' => $this->getTranslator()->translate('business_shop_eg'), 'e.K. (eingetragener Kaufmann)' => $this->getTranslator()->translate('business_shop_ek'), 'Einzelunternehmen' => $this->getTranslator()->translate('business_shop_single'), 'GbR' => $this->getTranslator()->translate('business_shop_gbr'), 'GmbH' => $this->getTranslator()->translate('business_shop_gmbh'), 'GmbH & Co. KG' => $this->getTranslator()->translate('business_shop_gmbhco'), 'KG' => $this->getTranslator()->translate('business_shop_kg'), 'Ltd.' => $this->getTranslator()->translate('business_shop_ltd'), 'Ltd. und Co. KG.' => $this->getTranslator()->translate('business_shop_ltdkg'), 'OHG' => $this->getTranslator()->translate('business_shop_ohg'), 'PartG.' => $this->getTranslator()->translate('business_shop_partg'), 'e.V.' => $this->getTranslator()->translate('business_shop_ev'), 'andere' => $this->getTranslator()->translate('business_shop_other'));
        $type = new Zend_Form_Element_Select('type');
        $type->addMultiOptions($options)
            ->setLabel($this->getTranslator()->translate('business_shop_company_type'))
            ->setAttrib('class', 'span2')
            ->setOrder(51);

        $company = new Zend_Form_Element_Text('company');
        $company->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_company'))
            ->setAttrib('class', 'span2')
            ->setOrder(70);

        $street = new Zend_Form_Element_Text('street');
        $street->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_street'))
            ->setAttrib('class', 'span2')
            ->setOrder(80);

        $house = new Zend_Form_Element_Text('house');
        $house->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_housenumber'))
            ->setAttrib('class', 'span2')
            ->setOrder(90);

        $zip = new Zend_Form_Element_Text('zip');
        $zip->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_zip'))
            ->setAttrib('class', 'span2')
            ->setOrder(100);

        $city = new Zend_Form_Element_Text('city');
        $city->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_city'))
            ->setAttrib('class', 'span2')
            ->setOrder(110);

        $options = array('DE' => $this->getTranslator()->translate('areasearch_germany'), 'AT' => $this->getTranslator()->translate('areasearch_austria'), 'CH' => $this->getTranslator()->translate('areasearch_switzerland'));
        $country = new Zend_Form_Element_Select('country');
        $country->addMultiOptions($options)
            ->setLabel($this->getTranslator()->translate('misc_country'))
            ->setAttrib('class', 'span2')
            ->setOrder(130);

        $phone = new Zend_Form_Element_Text('phone');
        $phone->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_phone'))
            ->setAttrib('class', 'span2')
            ->setOrder(140);

        $fax = new Zend_Form_Element_Text('fax');
        $fax->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_fax'))
            ->setAttrib('class', 'span2')
            ->setOrder(150);

        $this->addDisplayGroup(array($type, $company, $street, $house, $zip, $city, $country, $phone, $fax), 'shop_address', array('legend' => $this->getTranslator()->translate('business_shop_address')));

        $description = new Zend_Form_Element_Textarea('description');
        $description
            ->setLabel($this->getTranslator()->translate('business_shop_description'))
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 5)
            ->setOrder(290);

        $history = new Zend_Form_Element_Textarea('history');
        $history
            ->setLabel($this->getTranslator()->translate('business_shop_history'))
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 5)
            ->setOrder(300);

        $philosophy = new Zend_Form_Element_Textarea('philosophy');
        $philosophy
            ->setLabel($this->getTranslator()->translate('business_shop_philosophy'))
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 5)
            ->setOrder(310);

        $procedure = new Zend_Form_Element_Textarea('procedure');
        $procedure
            ->setLabel($this->getTranslator()->translate('business_shop_procedure'))
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 5)
            ->setOrder(320);

        $additional = new Zend_Form_Element_Textarea('additional');
        $additional
            ->setLabel($this->getTranslator()->translate('woma_business_hours'))
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 5)
            ->setOrder(330);

        $this->addDisplayGroup(array($description, $history, $philosophy, $procedure, $additional), 'shop_infos', array('legend' => $this->getTranslator()->translate('business_shop_infos')));

        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('misc_save'));
        $submit->setOrder(350);

        $this->addElements(array($submit));
    }
}
