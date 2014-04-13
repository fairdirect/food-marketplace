<?php

class Admin_Form_Newsletters extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/newsletters/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'newsletterForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $subject = new Zend_Form_Element_Text('subject');
        $subject->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Betreff')
            ->setAttrib('class', 'span2');

        $content = new Zend_Form_Element_Textarea('content');
        $content->setRequired(true)
            ->setLabel('Inhalt (Verfügbare Variablen: ' . implode(' ', Model_Newsletter::$vars) . ')')
            ->setAttrib('class', 'span2');


        $options = array('text' => 'Text', 'html' => 'HTML', 'both' => 'Text & HTML');
        $type = new Zend_Form_Element_Select('type');
        $type->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel('Typ');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $subject, $content, $type, $submit));
    }
}
