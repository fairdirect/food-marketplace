<?php

class Admin_Form_WomaShippingCosts extends Woma_Form_WomaShippingCosts
{
    public function init()
    {
      parent::init();

        $woma_id = new Zend_Form_Element_Hidden('woma_id');
    
        $this->addElements(array($woma_id));
    }
}
