<?php

class Admin_Form_Emails extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/emails/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'emailForm'));

        $vars = new Zend_Form_Element_Hidden('vars');

        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
            ->setAttrib('readonly', true)
            ->setAttrib('class', 'span2');

        $subject = new Zend_Form_Element_Text('subject');
        $subject->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Betreff')
            ->setAttrib('class', 'span2');

        $content = new Zend_Form_Element_Textarea('content');
        $content->setRequired(true)
            ->setLabel('Inhalt')
            ->setAttrib('class', 'span2');


        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($vars, $name, $subject, $content, $submit));
    }
}
