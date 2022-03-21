<?php

class Business_SalesController extends Zend_Controller_Action
{
    public function init(){
        Model_User::refreshAuth(); // make sure our data is up to date
        $this->user = Zend_Auth::getInstance()->getIdentity();      
    }

    public function indexAction(){
        $this->_redirect('/business/');
    }

    public function ordersAction(){
        $this->view->headTitle("Bestellungs-Verwaltung (nicht versendet)");          
        $this->view->inProcessOrders = Model_Order::findByShop($this->user->getShop()->id, 'in_process');
    }

    public function archiveAction(){
        $this->view->headTitle("Bestellungs-Verwaltung (abgeschlossen)");          
        $this->view->completeOrders = Model_Order::findByShop($this->user->getShop()->id, 'complete');
    }

    public function setsendAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($id){ 
            $order = Model_Order::find($id);
            if($order->getShop()->user_id != $this->user->id){
                exit('Forbidden!'); // prevent user from writing orders not owned by them
            }
            if($order->status != 'in_process'){
                exit('Already marked as sent');
            }
        }
        else{ 
            $this->_redirect('index');
        }

        try{
            $order->status = 'complete';
            $order->send_date = date('Y-m-d', time());
            $order->save();
        } catch(Exception $e){
            exit('Ein Fehler ist aufgetreten');
        }

        $sentMail = Model_Email::find('ordersent');
        $mail = new Zend_Mail('UTF-8');
        
        $mail->setBodyText(str_replace(
            array(
                '#shopname#',
                '#orderNumber#',
                '#firstname#',
                '#lastname#'
            ), 
            array(
                $order->getShop()->name, 
                $order->order_number,
                $order->getBillingAddress()->firstname,
                $order->getBillingAddress()->name
            ), 
            $sentMail->content
        ));
        $mail->setFrom('mail@fairdirect.org', 'Sachspendenbörse');
        $mail->addTo($order->getUser()->email);
        $mail->setSubject(str_replace(array('#orderNumber#', '#shopname#'), array($order->order_number, $order->getShop()->name), $sentMail->subject));
        $mail->send();


        $this->_helper->redirector('orders');
    }

    public function showinvoiceAction(){
        $id = $this->getRequest()->getParam('id');
        if($id){ 
            $order = Model_Order::find($id);
            if($order->getShop()->user_id != $this->user->id){
                exit('Forbidden!'); 
            }
        }
        else{ 
            $this->_redirect('index');
        }

        require_once('Fpdf/fpdf.php');
        $pdf = new FPDF('P','mm', 'A4');
        self::getHeader($pdf,$order);
        self::getBody($pdf, $order);
        self::getFooter($pdf, $order);

        $pdf->Output($order->getShop()->id . '-' . $order->order_number . '.pdf', 'D');
        exit();
    }


    private function getHeader($pdf, $order){
        $pdf->addPage();
        $pdf->setFont('Arial', '', 10);
 
        $pdf->setY(20);
        $pdf->setX(150);

        if($order->getShop()->getLogo() && $order->getShop()->getLogo()->filename) : $pdf->Image(APPLICATION_PATH . '/../public/img/shops/' . $order->getShop()->getLogo()->filename ); endif;
        $pdf->setY(40);
        $pdf->setX(10);
        
        $pdf->SetFont('Arial', 'U', 6);
        $pdf->Cell(20, 5, utf8_decode($order->getShop()->company . ' ' . $order->getShop()->street . ' ' . $order->getShop()->house . ' ' . $order->getShop()->zip . ' ' . $order->getShop()->city));
        $pdf->Ln();
        $pdf->setX(10);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Multicell(140, 5, utf8_decode($order->getBillingAddress()->toMailFormatedString()));
        $pdf->SetFont('Arial', '', 10);
        $pdf->setX(130);
        $pdf->Cell(40, 4, "Rechnung Nr.:");
        $pdf->Cell(40, 4, $order->getShop()->id . '-' . $order->order_number);
        $pdf->Ln();
        $pdf->setX(130);
        $pdf->Cell(40, 4, "Rechnungsdatum:");
        $pdf->Cell(40, 4, date('d.m.Y', time()));
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->setX(10);
        $pdf->MultiCell(180, 5, utf8_decode('Sachspendenbörse und ' . $order->getShop()->name . ' begrüßen Sie als Nutzer auf unserem Sachspendenbörse- Netzwerk. Wir bedanken uns herzlich für Ihre Bestellanfrage.'));
        $pdf->Ln();
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, 'Lieferschein / Rechnung');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->setFont('Arial', 'B', 8);
    }

    private function getBody($pdf, $order){
        $pdf->Cell(20, 5, 'Artikel-Nr.', 1, 0, 'C');
        $pdf->Cell(40, 5, 'Artikel-Bezeichnung', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Anzahl', 1, 0, 'C');
        $pdf->Cell(40, 5, utf8_decode('Bestellgröße'), 1, 0, 'C');
        $pdf->Cell(20, 5, utf8_decode('Einzelpreis'), 1, 0, 'C');
        if(!$order->getShop()->small_business){
            $pdf->Cell(20, 5, 'MwSt. (%)', 1, 0, 'C');
        }
        $pdf->Cell(($order->getShop()->small_business) ? 50 : 25, 5, utf8_decode('Gesamtpreis'), 1, 0, 'C');
        $pdf->Ln();
        $pdf->Ln();

        $pdf->setFont('Arial', '', 8);
        foreach($order->getProducts() as $item){
            $pdf->Cell(20, 5, utf8_decode($item['product_id']));
            $pdf->Cell(40, 5, utf8_decode($item['product_name']));
            $pdf->Cell(15, 5, utf8_decode($item['quantity']), 0, 0, 'C');
            $pdf->Cell(40, 5, utf8_decode($item['price_quantity'] . ' ' . $item['unit_type'] . ' à ' . $item['contents'] . ' ' . $item['content_type']));
            $pdf->Cell(20, 5, utf8_decode(number_format($item['value'], 2, ',', '.') . ' EUR'), 0, 0, 'R');
            if(!$order->getShop()->small_business){
                $pdf->Cell(20, 5, utf8_decode($item['tax']), 0, 0, 'R');
            }
            $pdf->Cell(($order->getShop()->small_business) ? 50 : 25, 5, utf8_decode(number_format($item['value'] * $item['quantity'], 2, ',', '.') . ' EUR'), 0, 0, 'R');
            $pdf->Ln();           
        }
        $pdf->Cell(20, 5);
        $pdf->Cell(40, 5, utf8_decode('Versandkosten'));
        $pdf->Cell(15, 5);
        $pdf->Cell(40, 5);
        $pdf->Cell(20, 5);
        if(!$order->getShop()->small_business){
            $pdf->Cell(20, 5, utf8_decode($order->getShippingTax()), 0, 0, 'R');
        }
        $pdf->Cell(($order->getShop()->small_business) ? 50 : 25, 5, utf8_decode(number_format($order->shipping, 2, ',', '.') . ' EUR'), 0, 0, 'R');
        $pdf->Ln();           
        $pdf->Ln();

        $taxes = $order->getTaxes();

        if(!$order->getShop()->small_business){
            foreach($order->getValueForTaxes() as $tax => $value){
                if($tax == 0 || $value == 0){
                    continue;
                }
                $pdf->Cell(155, 5, utf8_decode('Enthaltene MwSt. ' . $tax . '%'), 1);
                $pdf->Cell(25, 5, utf8_decode(number_format($taxes[$tax], 2, ',', '.') . ' EUR'), 1, 0, 'R');
                $pdf->Ln();
            }
            $pdf->Cell(155, 5, utf8_decode('Netto Betrag'), 1);
            $pdf->Cell(25, 5, utf8_decode(number_format($order->getPriceTotal() - $taxes[7] - $taxes[19], 2, ',', '.') . ' EUR'), 1, 0, 'R');
            $pdf->Ln();
        }
        $pdf->Cell(($order->getShop()->small_business) ? 160 : 155, 5, utf8_decode('Gesamtbetrag'), 1);
        $pdf->Cell(25, 5, utf8_decode(number_format($order->getPriceTotal(), 2, ',', '.') . ' EUR'), 1, 0, 'R');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(180, 5, utf8_decode('Zahlungsweise: Die Abwicklung der Zahlung erfolgte über den Sachspendenbörse Treuhandservice.'));
        if($order->getShop()->small_business){
            $pdf->Ln();
            $pdf->Cell(180, 5, utf8_decode('Diese Rechnung ist gemäß §19 UStG. von der Umsatzsteuer befreit.'));
        }
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(180, 5, utf8_decode('Lieferung an:'));
        $pdf->Ln(1);
        $pdf->Multicell(140, 5, utf8_decode($order->getDeliveryAddress()->toMailFormatedString()));
    }

    private function getFooter($pdf, $order){
        $pdf->setFont('Arial', '', 8);
        $pdf->setY(255);
        $pdf->Multicell(65, 5, utf8_decode($order->getShop()->company . "\n" . $order->getShop()->street . ' ' . $order->getShop()->house . "\n" . $order->getShop()->zip . ' ' . $order->getShop()->city), 'T');
        $pdf->setY(255);
        $pdf->setX(65);
        $pdf->Multicell(65, 5, utf8_decode('Tel: ' . $order->getShop()->phone . "\n" . (($order->getShop()->taxnumber) ? 'Steuernummer: ' . $order->getShop()->taxnumber : '') . "\n" . (($order->getShop()->salestax_id) ? 'USt-ID: ' . $order->getShop()->salestax_id : '')), 'T');
        $pdf->setY(255);
        $pdf->setX(130);
        $pdf->Multicell(65, 5, utf8_decode('Kontoinhaber: ' . $order->getShop()->bank_account_holder . "\n" . 'Konto-Nr.: ' . $order->getShop()->bank_account_number . "\n" . 'BLZ: ' . $order->getShop()->bank_id), 'T');

    }


}
