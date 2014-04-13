<?php

class Admin_Form_Addresses extends Form_Addresses
{
    public function init()
    {
        parent::init();
        $this->setAction('/admin/addresses/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'addressForm'));

        $user_id = new Zend_Form_Element_Hidden('user_id');
 
        $this->addElements(array($user_id));
    }
}
