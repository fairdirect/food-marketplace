<?php

class Business_Form_Products extends Zend_Form
{
    public function init()
    {
        $this->setAction('/business/products/edit/');
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'productForm'));

        $id = new Zend_Form_Element_Hidden('id');
        $id->setDecorators(array('ViewHelper'));

        $this->addElement($id);

        $options = array('' => '', 'offer' => 'Angebot', 'request' => 'Gesuch');
        $producttype = new Zend_Form_Element_Select('producttype');
        $producttype->addMultiOptions($options)
            ->setRequired(true)
            ->setAttrib('class', 'span2')
            ->setLabel($this->getTranslator()->translate('misc_producttype'));

        $mainCats = Model_MainCategory::getAll();
        $options = array('' => '');
        foreach($mainCats as $cat){
            $options[$cat->id] = $cat->name;
        }
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

        $this->addDisplayGroup(array($producttype, $type, $group_id, $category_id), 'category', array('legend' => $this->getTranslator()->translate('misc_category')));

        $name = new Zend_Form_Element_Text('name');
        $name->addFilters(array('StripTags', 'StripNewlines'))
            ->setRequired(true)
            ->setLabel($this->getTranslator()->translate('business_products_name'))
            ->setAttrib('class', 'span2');

        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('misc_description'))
            ->setAttrib('rows', 5);


        $ingredients = new Zend_Form_Element_Textarea('ingredients');
        $ingredients->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('business_products_ingredients'))
            ->setAttrib('rows', 5);

        $tags = new Zend_Form_Element_Textarea('tags');
        $tags->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('product_tags'))
            ->setAttrib('rows', 5);

        $this->addDisplayGroup(array($name, $description, $ingredients,  $tags), 'product_data', array('legend' => $this->getTranslator()->translate('misc_general')));

        $attributeTypes = array('additive' => $this->getTranslator()->translate('business_products_additives'), 'allergen' => $this->getTranslator()->translate('business_products_allergenes'));
        foreach($attributeTypes as $aName => $aLabel){
            $attributes = Model_ProductAttribute::getByType($aName);
            if($attributes){
                $aElem = new Zend_Form_Element_MultiCheckbox($aName);
                foreach($attributes as $a){
                    $aElem->addMultiOption($a->id, $a->name);
                }
                $this->addDisplayGroup(array($aElem), $aLabel, array('legend' => $aLabel));
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

        $this->addDisplayGroup(array($normal_amount_1, $normal_unit_1, $normal_content_1, $normal_content_type_1, $normal_euro_1, $normal_cent_1), 'price_normal_1', array('legend' => $this->getTranslator()->translate('misc_request')));

        $unlimitedStock = new Zend_Form_Element_Checkbox('unlimited_stock');
        $unlimitedStock
            ->setLabel($this->getTranslator()->translate('business_products_unlimited'));

        $stock = new Zend_Form_Element_Text('stock');
        $stock->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('business_products_amount'));

        $best_before = new Zend_Form_Element_Text('best_before');
        $best_before->addFilters(array('StripTags'))
            ->setLabel($this->getTranslator()->translate('misc_best_before'));

        $wholesale_amount_1 = new Zend_Form_Element_Text('wholesale_amount_1');
        $wholesale_amount_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_amount'));
        $wholesale_unit_1 = new Zend_Form_Element_Select('wholesale_unit_1');
        $wholesale_unit_1->addMultiOptions($unitOptions)->setLabel($this->getTranslator()->translate('business_products_price_unit'));
        $wholesale_content_1 = new Zend_Form_Element_Text('wholesale_content_1');
        $wholesale_content_1->addFilters(array('StripTags', 'StripNewlines'))->setLabel($this->getTranslator()->translate('business_products_price_content'));
        $wholesale_content_type_1 = new Zend_Form_Element_Select('wholesale_content_type_1');
        $wholesale_content_type_1->addMultiOptions($contentOptions)->setLabel($this->getTranslator()->translate('business_product_price_content_type'));

        $this->addDisplayGroup(array($wholesale_amount_1, $wholesale_unit_1, $wholesale_content_1, $wholesale_content_type_1, $unlimitedStock, $stock, $best_before), 'wholesale_1', array('legend' => $this->getTranslator()->translate('business_product_price_wholesale')));

        $submit = new Zend_Form_Element_Submit($this->getTranslator()->translate('misc_save'));

        $this->addElements(array($submit));
    }
}
