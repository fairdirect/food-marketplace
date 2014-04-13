<?php

class Admin_Form_Countries extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/countries/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'countryForm'));

        $id = new Zend_Form_Element_Text('id');
        $id->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('ID')
            ->setAttrib('class', 'span2');

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Name')
            ->setAttrib('class', 'span2');

        $phone = new Zend_Form_Element_Text('phone');
        $phone->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Vorwahl')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $name, $phone, $submit));
    }
}
