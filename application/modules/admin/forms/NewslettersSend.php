<?php

class Admin_Form_NewslettersSend extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/newsletters/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'newsletterForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $options = array_merge(array('' => ''), Model_Newsletter::$recipients);
        $recipients = new Zend_Form_Element_Select('recipients');
        $recipients->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel('Empfänger');

        $testmail = new Zend_Form_Element_Text('testmail');
        $testmail->setAttribs(array('style' => 'display:none'))
            ->setValue('test@epelia.com');

        $send = new Zend_Form_Element_Button('Versenden');
        $send->setAttribs(array('class' => 'btn btn-success', 'id' => 'send_button'));

        $this->addElements(array($id, $recipients, $testmail, $send));
    }
}
