<?php

class Admin_Form_Attributes extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/attributes/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'attributeForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $options = array('' => '', 'additive' => 'Zusatz', 'allergen' => 'Allergen', 'event' => 'Anlass', 'flavor' => 'Geschmack', 'manipulation' => 'Veränderung');
        $type = new Zend_Form_Element_Select('type');
        $type->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel('Typ');

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Name')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $type, $name, $submit));
    }
}
