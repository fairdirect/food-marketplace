<?php

class Admin_Form_Units extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/units/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'unitForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $singular = new Zend_Form_Element_Text('singular');
        $singular->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Einzahl')
            ->setAttrib('class', 'span2');

        $plural = new Zend_Form_Element_Text('plural');
        $plural->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Mehrzahl')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $singular, $plural, $submit));
    }
}
