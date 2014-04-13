<?php

class Admin_Form_ContentTypes extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/contenttypes/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'contentTypeForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Name')
            ->setAttrib('class', 'span2');

        $baseUnit = new Zend_Form_Element_Text('base_unit');
        $baseUnit->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel('Grundpreis-Einheit')
            ->setAttrib('class', 'span2');

        $baseFactor = new Zend_Form_Element_Text('base_factor');
        $baseFactor
            ->setLabel('Grundpreis-Faktor')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $name, $baseUnit, $baseFactor, $submit));
    }
}
