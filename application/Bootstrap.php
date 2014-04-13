<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initHelper()
    {
    }

    /**
     * Load the classes automatically
     */
    protected function _initAutoload()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Plugins');
        $this->bootstrap('modules'); // required for usage of default-models

        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH . '/modules/default'
        ));
        $autoloader->pushAutoloader($moduleLoader);

        return $moduleLoader;
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);

        return $config;
    }

    protected function _initTranslation(){
        $translator = new Zend_Translate(
            array(
                'adapter' => 'Zend_Translate_Adapter_Tmx',
                'content' => APPLICATION_PATH . '/configs/languages.tmx',
                'locale'  => 'de'
            )
        );
        Zend_Form::setDefaultTranslator($translator);
        Zend_Registry::set('Zend_Translate', $translator);

        $formTranslator = new Zend_Translate(
            array(
                'adapter' => 'array',
                'content' => '/var/www/epelia/resources/languages',
                'locale'  => 'de',
                'scan' => Zend_Translate::LOCALE_DIRECTORY
            )
        );
        Zend_Validate_Abstract::setTranslator($formTranslator);
    }

    protected function _initDatabase()
    {
        $resource = $this->getPluginResource('multidb');
        $resource->init();

        $db1 = $resource->getDb('db1');
        Zend_Db_Table::setDefaultAdapter($db1);
    }

    protected function _initPlugins()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Plugin_Front());
    }

    protected function _initRoutes(){
/*        $front = Zend_Controller_Front::getInstance();       
        $router = $front->getRouter();
        $router->addRoute('bla', new Zend_Controller_Router_Route('bla/', array('controller' => 'products', 'action' => 'index'))); */
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routes');
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $router->addConfig( $config, 'routes' );
    }

}
