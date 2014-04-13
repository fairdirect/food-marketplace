<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->headTitle("Lebensmittel online bestellen | Epelia");

        $shop = Model_Shop::getRandomShopWithProducts(2);
        $homeGroups = Model_Homegroup::findAll();
        $this->view->randomShop = $shop;
        $this->view->homegroups = $homeGroups;
	}

}
