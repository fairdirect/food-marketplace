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
        $mail->setFrom('mail@epelia.com', 'Epelia');
        $mail->addTo('hoesel@derhoesel.de', 'Epelia');
        $mail->addTo('mail@epelia.com', 'Epelia');
        $mail->setSubject('Genossenschaft Anmeldung');
        $mail->send();

    }

}
