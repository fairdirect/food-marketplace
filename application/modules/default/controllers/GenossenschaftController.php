<?php

class GenossenschaftController extends Zend_Controller_Action
{
    public function indexAction(){
    }

    public function registerAction(){
        $content = '';
        foreach($_POST as $key => $val){
            if($key != 'register'){
                $content .= $key . ' : ' . $val . "\n";
            }
        }	
        $mail = new Zend_Mail('UTF-8');
        $mail->setBodyText(strip_tags($content));
        $mail->setFrom('mail@fairdirect.org', 'Sachspendenbörse');
        $mail->addTo('hoesel@derhoesel.de', 'Sachspendenbörse');
        $mail->addTo('mail@epelia.com', 'Sachspendenbörse');
        $mail->setSubject('Entwicklungsrat Anmeldung');
        $mail->send();

    }

}
