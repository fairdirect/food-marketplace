<?php

class Admin_Form_Womas extends Woma_Form_Womas
{
    public function init()
    {
        parent::init();
        $this->setAction('/admin/womas/edit/');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setOrder(10);

        $this->addElements(array($id));
    }
}
