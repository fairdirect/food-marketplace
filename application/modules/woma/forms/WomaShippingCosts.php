<?php

class Woma_Form_WomaShippingCosts extends Zend_Form
{
    public function init()
    {
        $this->setAction('/woma/shippingcosts/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'shippingCostForm'));

        $countries = Model_Country::getAll();
        foreach($countries as $country){
            $value = new Zend_Form_Element_Text('value_' . $country->id);
            $value->setLabel($this->getTranslator()->translate('business_shipping_value'));
            $freeFrom = new Zend_Form_Element_Text('free_from_' . $country->id);
            $freeFrom->setLabel($this->getTranslator()->translate('business_shipping_free_from'));
            $this->addDisplayGroup(array($value, $freeFrom), $country->id, array('legend' => $country->name));
        }

        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('misc_save'));

        $this->addElements(array($submit));
    }
}
