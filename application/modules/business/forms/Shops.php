<?php

class Business_Form_Shops extends Zend_Form
{
    public function init()
    {
        $this->setAction('/business/shops/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'shopForm'));

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('business_shop_name'))
            ->setAttrib('class', 'span2')
            ->setOrder(30);

        $url = new Zend_Form_Element_Text('url');
        $url->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
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

        $countries = Model_Region::getCountriesWithRegions();
        $options = array('' => '');
        foreach($countries as $c){
            $options[$c->id] = $c->name;
        }
        $country = new Zend_Form_Element_Select('country');
        $country->addMultiOptions($options)
            ->setRequired(true)
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

        $taxnumber = new Zend_Form_Element_Text('taxnumber');
        $taxnumber->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_taxnumber'))
            ->setAttrib('class', 'span2')
            ->setOrder(50);

        $salestax_id = new Zend_Form_Element_Text('salestax_id');
        $salestax_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('business_shop_salestax_id'))
            ->setOrder(60);

        $small_business = new Zend_Form_Element_Checkbox('small_business');
        $small_business
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('business_shop_small_business'))
            ->setOrder(160)
            ->setCheckedValue('t')
            ->setUnCheckedValue('f');

        $register_id = new Zend_Form_Element_Text('register_id');
        $register_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_register_id'))
            ->setAttrib('class', 'span2')
            ->setOrder(170);

        $register_court = new Zend_Form_Element_Text('register_court');
        $register_court->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_register_court'))
            ->setAttrib('class', 'span2')
            ->setOrder(180);

        $office = new Zend_Form_Element_Text('office');
        $office->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_office'))
            ->setAttrib('class', 'span2')
            ->setOrder(190);

        $representative = new Zend_Form_Element_Text('representative');
        $representative->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_representative'))
            ->setAttrib('class', 'span2')
            ->setOrder(200);

        $eco_control_board = new Zend_Form_Element_Text('eco_control_board');
        $eco_control_board->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_eco_control_board'))
            ->setAttrib('class', 'span2')
            ->setOrder(210);

        $eco_control_id = new Zend_Form_Element_Text('eco_control_id');
        $eco_control_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_eco_control_id'))
            ->setAttrib('class', 'span2')
            ->setOrder(220);

        $this->addDisplayGroup(array($taxnumber, $salestax_id, $small_business, $register_id, $register_court, $office, $representative, $eco_control_board, $eco_control_id), 'company_data', array('legend' => $this->getTranslator()->translate('business_shop_company_data')));

        $bank_account_holder = new Zend_Form_Element_Text('bank_account_holder');
        $bank_account_holder->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_bank_holder'))
            ->setAttrib('class', 'span2')
            ->setOrder(230);

        $bank_account_number = new Zend_Form_Element_Text('bank_account_number');
        $bank_account_number->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_bank_account_number'))
            ->setAttrib('class', 'span2')
            ->setOrder(240);

        $bank_id = new Zend_Form_Element_Text('bank_id');
        $bank_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_bank_number'))
            ->setAttrib('class', 'span2')
            ->setOrder(250);

        $bank_name = new Zend_Form_Element_Text('bank_name');
        $bank_name->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_bank_name'))
            ->setAttrib('class', 'span2')
            ->setOrder(260);

        $bank_swift = new Zend_Form_Element_Text('bank_swift');
        $bank_swift->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_bank_swift'))
            ->setAttrib('class', 'span2')
            ->setOrder(270);

        $bank_iban = new Zend_Form_Element_Text('bank_iban');
        $bank_iban->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('business_shop_bank_iban'))
            ->setAttrib('class', 'span2')
            ->setOrder(280);

        $this->addDisplayGroup(array($bank_account_holder, $bank_account_number, $bank_id, $bank_name, $bank_swift, $bank_iban), 'bank', array('legend' => $this->getTranslator()->translate('business_shop_bank')));

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
            ->setLabel($this->getTranslator()->translate('business_shop_additional'))
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 5)
            ->setOrder(330);

        $this->addDisplayGroup(array($description, $history, $philosophy, $procedure, $additional), 'shop_infos', array('legend' => $this->getTranslator()->translate('business_shop_infos')));

        $useStandardAgb = new Zend_Form_Element_Checkbox('standardagb');
        $useStandardAgb
            ->setLabel('Eigene AGBs verwenden?');

        $agb = new Zend_Form_Element_Textarea('agb');
        $agb
            ->setAttrib('class', 'span2')
            ->setAttrib('rows', 50)
            ->setAttrib('style', 'width:750px')
            ->setOrder(340);

        $this->addDisplayGroup(array($useStandardAgb, $agb), 'agb_group', array('legend' => $this->getTranslator()->translate('footer_agb')));


        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('misc_save'));
        $submit->setOrder(350);

        $this->addElements(array($submit));
    }
}
