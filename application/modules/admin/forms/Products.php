<?php

class Admin_Form_Products extends Business_Form_Products
{
    public function init()
    {
        parent::init();
        $this->setAction('/admin/products/edit/');

        $this->removeElement('shop_id');

        $shop_id = new Zend_Form_Element_Text('shop_id');
        $shop_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Shop-ID')
            ->setAttrib('class', 'span2');
    
        $this->addElements(array($shop_id));
    }
}
