<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->headTitle("Lebensmittel online bestellen | Epelia");

        $shop = Model_Shop::getRandomFeaturedHomeShop();
        $homeGroups = Model_Homegroup::findAll();
        $this->view->frontpageContent = Model_Setting::find('frontpage_content')->value;
        $this->view->randomShop = $shop;
        $this->view->homegroups = $homeGroups;
        $this->view->mainCategories = Model_MainCategory::getAll();
	}

}
