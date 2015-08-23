<?php

class Admin_Form_Settings extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/settings/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'settingForm'));

        $id = new Zend_Form_Element_Text('id');
        $id->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Name')
            ->setAttrib('class', 'span2')
            ->setAttrib('readonly', true);

        $value = new Zend_Form_Element_Textarea('value');
        $value->setRequired(true)
            ->setLabel('Inhalt')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $value, $submit));
    }
}
