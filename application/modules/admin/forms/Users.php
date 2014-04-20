<?php

class Admin_Form_Users extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/users/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'userForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $email = new Zend_Form_Element_Text('email');
        $email->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('E-Mail')
            ->setAttrib('class', 'span2');

        $options = array('' => '', 'customer' => 'Kunde', 'shop' => 'Shop');
        $type = new Zend_Form_Element_Select('type');
        $type->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel('Typ');

        $main_delivery_address = new Zend_Form_Element_Select('main_delivery_address_id');
        $main_delivery_address->setAttrib('class', 'span2')
            ->setLabel('Standard Lieferadresse')
            ->setRegisterInArrayValidator(false);

        $main_billing_address = new Zend_Form_Element_Select('main_billing_address_id');
        $main_billing_address->setAttrib('class', 'span2')
            ->setLabel('Standard Rechnungsadresse')
            ->setRegisterInArrayValidator(false);

        $is_admin = new Zend_Form_Element_Checkbox('is_admin');
        $is_admin->setAttrib('class', 'span2')
            ->setLabel('Admin?');

        $is_wholesale = new Zend_Form_Element_Checkbox('is_wholesale');
        $is_wholesale->setAttrib('class', 'span2')
            ->setLabel('Großhändler?');

        $newsletter = new Zend_Form_Element_Checkbox('newsletter');
        $newsletter->setAttrib('class', 'span2')
            ->setLabel('Newsletter?');

        $options = array('' => '', 'new' => 'Neu', 'accepted' => 'Bestätigt');
        $status = new Zend_Form_Element_Select('status');
        $status->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel('Status');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $email, $type, $main_delivery_address, $main_billing_address, $is_admin, $is_wholesale, $newsletter, $status, $submit));
    }
}
