<?php

class Form_Ratings extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');
        $this->setAttribs(array('id' => 'ratingForm'));

        $productid = new Zend_Form_Element_Hidden('product_id');
        $productid->setRequired(true);

        $rating = new Zend_Form_Element_Hidden('rating');
        $rating->addValidator(new Zend_Validate_Between(array('min' => 1, 'max' => 5)))
            ->addValidator(new Zend_Validate_Int())
            ->setRequired(true);

        $comment = new Zend_Form_Element_Text('comment');
        $comment->addFilters(array('StripTags', 'StripNewlines'))
            ->setLabel($this->getTranslator()->translate('misc_comment'))
            ->setRequired(true)
            ->setAttrib('class', 'span2');

        $submit = new Zend_Form_Element_Submit('Speichern');

        $this->addElements(array($productid, $rating, $comment, $submit));
    }
}
