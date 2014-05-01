<?php

class Woma_Form_ShippingCosts extends Business_Form_ShippingCosts
{
    public function init()
    {
        $this->setAction('/woma/shippingcosts/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'shippingCostForm'));
    }
}
