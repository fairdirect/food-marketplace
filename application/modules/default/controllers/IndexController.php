<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->headTitle("Lebensmittel online bestellen | Epelia");

        Model_User::refreshAuth(); // make sure our data is up to date
        $this->auth = Zend_Auth::getInstance();
        $this->user = $this->auth->getIdentity();

        $session = new Zend_Session_Namespace('Default');

//        $shop = Model_Shop::getRandomFeaturedHomeShop();
        $homeGroups = Model_Homegroup::findAll();
        $this->view->frontpageContent = Model_Setting::find('frontpage_content')->value;
        //$this->view->randomShop = $shop;
        $this->view->randomWoma = Model_Woma::getRandomWoma();
        $this->view->homegroups = $homeGroups;
        $this->view->mainCategories = Model_MainCategory::getAll();

        if(Model_Region::getCurrentRegion()) { // region selected
            $this->_redirect('/categories/'); // for now just redirect to categories
        } else {
            $this->_redirect('/regions/');
        }
	}

}
