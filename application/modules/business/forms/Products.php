<?php

class Business_Form_Products extends Zend_Form
{
    public function init()
    {
        $this->setAction('/business/products/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'productForm'));

        $id = new Zend_Form_Element_Hidden('id');

        $this->addElement($id);

        $options = array('' => '', 'groceries' => $this->getTranslator()->translate('misc_groceries'), 'drugstore' => $this->getTranslator()->translate('misc_drugstore'));
        $type = new Zend_Form_Element_Select('type');
        $type->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('misc_type'));

        $options = array('' => '');
        $group_id = new Zend_Form_Element_Select('group_id');
        $group_id->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('misc_group'))
            ->setRegisterInArrayValidator(false);

        $options = array('' => '');
        $category_id = new Zend_Form_Element_Select('category_id');
        $category_id->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('misc_category'))
            ->setRegisterInArrayValidator(false);

        $this->addDisplayGroup(array($type, $group_id, $category_id), 'category', array('legend' => $this->getTranslator()->translate('misc_category')));

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('business_products_name'))
            ->setAttrib('class', 'span2');

        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('misc_description'))
            ->setAttrib('rows', 5);

        $unlimitedStock = new Zend_Form_Element_Checkbox('unlimited_stock');
        $unlimitedStock
            ->setLabel($this->getTranslator()->translate('business_products_unlimited'));

        $stock = new Zend_Form_Element_Text('stock');
        $stock->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('business_products_amount'));

        $ingredients = new Zend_Form_Element_Textarea('ingredients');
        $ingredients->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('business_products_ingredients'))
            ->setAttrib('rows', 5);

        $tags = new Zend_Form_Element_Textarea('tags');
        $tags->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('product_tags'))
            ->setAttrib('rows', 5);

        $is_bio = new Zend_Form_Element_Checkbox('is_bio');
        $is_bio
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('business_products_bio'));

        $is_discount = new Zend_Form_Element_Checkbox('is_discount');
        $is_discount
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('business_products_discount'));

        $tax = new Zend_Form_Element_Select('tax');
        $tax->addMultiOptions(array('19' => '19 %', '7' => '7 %', '0 %'))
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('misc_salestax'));
 
        $this->addDisplayGroup(array($name, $description, $unlimitedStock, $stock, $ingredients,  $tags, $is_bio, $is_discount, $tax), 'product_data', array('legend' => $this->getTranslator()->translate('misc_general')));

        $attributeTypes = array('additive' => $this->getTranslator()->translate('business_products_additives'), 'allergen' => $this->getTranslator()->translate('business_products_allergenes'), 'event' => $this->getTranslator()->translate('business_products_events'), 'flavor' => $this->getTranslator()->translate('business_products_flavor'), 'manipulation' => $this->getTranslator()->translate('business_products_manipulations'));
        foreach($attributeTypes as $aName => $aLabel){
            $attributes = Model_ProductAttribute::getByType($aName);
            if($attributes){
                $aElem = new Zend_Form_Element_MultiCheckbox($aName);
                foreach($attributes as $a){
                    $aElem->addMultiOption($a->id, $a->name);
                }
                if($aName == 'flavor' || $aName == 'allergen'){
                    $this->addDisplayGroup(array($aElem), $aLabel, array('legend' => $aLabel, 'class' => 'groceryOnly'));
                }
                else{
                    $this->addDisplayGroup(array($aElem), $aLabel, array('legend' => $aLabel));
                }
            }
        }     

        $unitOptions = array();
        $all_price_units = Model_Unit::getAll();
        foreach($all_price_units as $unit){
            $unitOptions[$unit->id] = $unit->singular . ' / ' . $unit->plural;
        }

        $contentOptions = array();
        $all_contents = Model_ContentType::getAll();
        foreach($all_contents as $content){
            $contentOptions[$content->id] = $content->name;
        }

        $normal_amount_1 = new Zend_Form_Element_Text('normal_amount_1');
        $normal_amount_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $normal_unit_1 = new Zend_Form_Element_Select('normal_unit_1');
        $normal_unit_1->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $normal_content_1 = new Zend_Form_Element_Text('normal_content_1');
        $normal_content_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $normal_content_type_1 = new Zend_Form_Element_Select('normal_content_type_1');
        $normal_content_type_1->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));
        $normal_euro_1 = new Zend_Form_Element_Text('normal_euro_1');
        $normal_euro_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Euro');
        $normal_cent_1 = new Zend_Form_Element_Text('normal_cent_1');
        $normal_cent_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Cent');

        $normal_amount_2 = new Zend_Form_Element_Text('normal_amount_2');
        $normal_amount_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $normal_unit_2 = new Zend_Form_Element_Select('normal_unit_2');
        $normal_unit_2->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $normal_content_2 = new Zend_Form_Element_Text('normal_content_2');
        $normal_content_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $normal_content_type_2 = new Zend_Form_Element_Select('normal_content_type_2');
        $normal_content_type_2->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));
        $normal_euro_2 = new Zend_Form_Element_Text('normal_euro_2');
        $normal_euro_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Euro');
        $normal_cent_2 = new Zend_Form_Element_Text('normal_cent_2');
        $normal_cent_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Cent');

        $normal_amount_3 = new Zend_Form_Element_Text('normal_amount_3');
        $normal_amount_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $normal_unit_3 = new Zend_Form_Element_Select('normal_unit_3');
        $normal_unit_3->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $normal_content_3 = new Zend_Form_Element_Text('normal_content_3');
        $normal_content_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $normal_content_type_3 = new Zend_Form_Element_Select('normal_content_type_3');
        $normal_content_type_3->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));
        $normal_euro_3 = new Zend_Form_Element_Text('normal_euro_3');
        $normal_euro_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Euro');
        $normal_cent_3 = new Zend_Form_Element_Text('normal_cent_3');
        $normal_cent_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Cent');

        $this->addDisplayGroup(array($normal_amount_1, $normal_unit_1, $normal_content_1, $normal_content_type_1, $normal_euro_1, $normal_cent_1), 'price_normal_1', array('legend' => '1. ' . $this->getTranslator()->translate('business_product_price_default')));
        $this->addDisplayGroup(array($normal_amount_2, $normal_unit_2, $normal_content_2, $normal_content_type_2, $normal_euro_2, $normal_cent_2), 'price_normal_2', array('legend' => '2. ' . $this->getTranslator()->translate('business_product_price_default')));
        $this->addDisplayGroup(array($normal_amount_3, $normal_unit_3, $normal_content_3, $normal_content_type_3, $normal_euro_3, $normal_cent_3), 'price_normal_3', array('legend' => '3. ' . $this->getTranslator()->translate('business_product_price_default')));

        $wholesale_amount_1 = new Zend_Form_Element_Text('wholesale_amount_1');
        $wholesale_amount_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $wholesale_unit_1 = new Zend_Form_Element_Select('wholesale_unit_1');
        $wholesale_unit_1->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $wholesale_content_1 = new Zend_Form_Element_Text('wholesale_content_1');
        $wholesale_content_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $wholesale_content_type_1 = new Zend_Form_Element_Select('wholesale_content_type_1');
        $wholesale_content_type_1->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));
        $wholesale_euro_1 = new Zend_Form_Element_Text('wholesale_euro_1');
        $wholesale_euro_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Euro');
        $wholesale_cent_1 = new Zend_Form_Element_Text('wholesale_cent_1');
        $wholesale_cent_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Cent');

        $wholesale_amount_2 = new Zend_Form_Element_Text('wholesale_amount_2');
        $wholesale_amount_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $wholesale_unit_2 = new Zend_Form_Element_Select('wholesale_unit_2');
        $wholesale_unit_2->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $wholesale_content_2 = new Zend_Form_Element_Text('wholesale_content_2');
        $wholesale_content_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $wholesale_content_type_2 = new Zend_Form_Element_Select('wholesale_content_type_2');
        $wholesale_content_type_2->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));
        $wholesale_euro_2 = new Zend_Form_Element_Text('wholesale_euro_2');
        $wholesale_euro_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Euro');
        $wholesale_cent_2 = new Zend_Form_Element_Text('wholesale_cent_2');
        $wholesale_cent_2->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Cent');

        $wholesale_amount_3 = new Zend_Form_Element_Text('wholesale_amount_3');
        $wholesale_amount_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $wholesale_unit_3 = new Zend_Form_Element_Select('wholesale_unit_3');
        $wholesale_unit_3->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $wholesale_content_3 = new Zend_Form_Element_Text('wholesale_content_3');
        $wholesale_content_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $wholesale_content_type_3 = new Zend_Form_Element_Select('wholesale_content_type_3');
        $wholesale_content_type_3->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));
        $wholesale_euro_3 = new Zend_Form_Element_Text('wholesale_euro_3');
        $wholesale_euro_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Euro');
        $wholesale_cent_3 = new Zend_Form_Element_Text('wholesale_cent_3');
        $wholesale_cent_3->addFilters(array('StripTags', 'StripNewlines'))->setLabel('Cent');

        $this->addDisplayGroup(array($wholesale_amount_1, $wholesale_unit_1, $wholesale_content_1, $wholesale_content_type_1, $wholesale_euro_1, $wholesale_cent_1), 'wholesale_1', array('legend' => '1. ' . $this->getTranslator()->translate('business_product_price_wholesale')));
        $this->addDisplayGroup(array($wholesale_amount_2, $wholesale_unit_2, $wholesale_content_2, $wholesale_content_type_2, $wholesale_euro_2, $wholesale_cent_2), 'wholesale_2', array('legend' => '2. ' . $this->getTranslator()->translate('business_product_price_wholesale')));
        $this->addDisplayGroup(array($wholesale_amount_3, $wholesale_unit_3, $wholesale_content_3, $wholesale_content_type_3, $wholesale_euro_3, $wholesale_cent_3), 'wholesale_3', array('legend' => '3. ' . $this->getTranslator()->translate('business_product_price_wholesale')));
        
        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('misc_save'));

        $this->addElements(array($submit));
    }
}
