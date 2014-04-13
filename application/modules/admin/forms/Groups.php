<?php

class Admin_Form_Groups extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/categories/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'categoryForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $options = array('' => '', 'groceries' => 'Lebensmittel', 'drugstore' => 'Drogerie');
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

        $description = new Zend_Form_Element_Textarea('description');
        $description
            ->setLabel('Beschreibung')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $type, $name, $description, $submit));
    }
}
