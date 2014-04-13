<?php

class Admin_Form_Homegroups extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/productshome/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'productshomeForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Name')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $name, $submit));
    }
}
