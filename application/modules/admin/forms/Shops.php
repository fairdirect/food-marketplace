<?php

class Admin_Form_Shops extends Business_Form_Shops
{
    public function init()
    {
        parent::init();
        $this->setAction('/admin/shops/edit/');

        $id = new Zend_Form_Element_Hidden('id');
        $id->setOrder(10);

        $user_id = new Zend_Form_Element_Text('user_id');
        $user_id->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Benutzer-ID')
            ->setAttrib('class', 'span2')
            ->setOrder(20);

        $provision = new Zend_Form_Element_Text('provision');
        $provision->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel('Provision')
            ->setAttrib('class', 'span2')
            ->setOrder(41);

        $womaOptions = array('');
        $womas = Model_Woma::getAll();
        foreach($womas as $w){
            $womaOptions[$w->id] = $w->name;
        }

        $this->addElements(array($id, $user_id, $provision));
    }
}
