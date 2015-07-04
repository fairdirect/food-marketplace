<?php

class Plugin_Front extends Zend_Controller_Plugin_Abstract{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request){
        switch($request->getModuleName()){
            case 'business':
                if(!Zend_Auth::getInstance()->hasIdentity() || Zend_Auth::getInstance()->getIdentity()->type != 'shop'){
                    $redirect = new Zend_Controller_Action_Helper_Redirector();
                    $redirect->gotoUrl('/')->redirectAndExit();
                    return;
                }
                $layout = Zend_Layout::getMvcInstance();
                $layout->setLayout('layout_business');
                $view = $layout->getView();
                $view->user = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity() : null;
                break;
            case 'woma':
                if(!Zend_Auth::getInstance()->hasIdentity() || Zend_Auth::getInstance()->getIdentity()->type != 'agent'){
                    $redirect = new Zend_Controller_Action_Helper_Redirector();
                    $redirect->gotoUrl('/')->redirectAndExit();
                    return;
                }
                $layout = Zend_Layout::getMvcInstance();
                $layout->setLayout('layout_woma');
                $view = $layout->getView();
                $view->user = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity() : null;
                break;
            case 'admin':
                if(!Zend_Auth::getInstance()->hasIdentity() || !(Zend_Auth::getInstance()->getIdentity()->is_admin)){
                    $redirect = new Zend_Controller_Action_Helper_Redirector();
                    $redirect->gotoUrl('/')->redirectAndExit();
                    return;
                }
                $layout = Zend_Layout::getMvcInstance();
                $layout->setLayout('layout_admin');
                $view = $layout->getView();
                $view->user = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity() : null;
                break;
            default:
                $layout = Zend_Layout::getMvcInstance();
                $layout->setLayout('layout');
                $view = $layout->getView();
                $view->user = (Zend_Auth::getInstance()->hasIdentity()) ? Zend_Auth::getInstance()->getIdentity() : null;
                $view->shoppingCart = Model_ShoppingCart::getRunningShoppingCart();
                $view->mainCategories = Model_MainCategory::getAll();
                return;
        }
    }
}
