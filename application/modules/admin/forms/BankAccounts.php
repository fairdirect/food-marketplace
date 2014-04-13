<?php

class Admin_Form_BankAccounts extends Zend_Form
{
    public function init()
    {
        $this->setAction('/admin/bankaccounts/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'bankAccountForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $user_id = new Zend_Form_Element_Hidden('user_id');

        $account_nr = new Zend_Form_Element_Text('account_nr');
        $account_nr->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Kontonummer')
            ->setAttrib('class', 'span2');

        $account_holder = new Zend_Form_Element_Text('account_holder');
        $account_holder->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Kontoinhaber')
            ->setAttrib('class', 'span2');

        $bank_id = new Zend_Form_Element_Text('bank_id');
        $bank_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('BLZ')
            ->setAttrib('class', 'span2');

        $bank_name = new Zend_Form_Element_Text('bank_name');
        $bank_name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Bank-Name')
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($id, $user_id, $account_nr, $account_holder, $bank_id, $bank_name, $submit));
    }
}
