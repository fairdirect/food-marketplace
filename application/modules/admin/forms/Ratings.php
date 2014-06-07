<?php

class Admin_Form_Ratings extends Form_Ratings
{
    public function init()
    {
        parent::init();

        $id = new Zend_Form_Element_Hidden('id');
        $userId = new Zend_Form_Element_Hidden('user_id');

        $this->addElements(array($id, $userId));
    }
}
