<?php

class Admin_SalesController extends Zend_Controller_Action
{
    public function init(){
 
    }

    public function indexAction()
    {
        $this->_redirect('/admin/');
    }

    public function inprocessAction(){
        $this->view->headTitle('Nicht versendet | Epelia');
        $this->view->orders = Model_Order::findByStatus('in_process');
    }

    public function completeAction(){
        $this->view->headTitle("Abgeschlossen | Epelia");
        $this->view->orders= Model_Order::findByStatus('complete');
    }

    public function csvAction(){
        $this->view->minYear = Model_Order::getMinYearSend();
        $this->view->maxYear = Model_Order::getMaxYearSend();

        $request = $this->getRequest();
        $orderContent = '';
        if($request->getPost('year') && $request->getPost('month')){
            $return = array('suc' => false, 'data' => '', 'can_create_invoice' => true);
            $month = $request->getPost('month');
            $year = $request->getPost('year');

            if(Model_Invoice::getByDate($month, $year)){
                $return['can_create_invoice'] = false;
            }


            $shops = Model_Shop::getShopsWithOrders($month, $year);
            foreach($shops as $shop){
                $orderContent .= '<tr><td>' . $shop->id . '</td><td>' . $shop->company . '</td><td>' . $shop->representative . '</td><td>';
                $orders = $shop->getOrdersByDate($month, $year);
                foreach($orders as $order){
                    $orderContent .= $order->order_number . '<br />';
                }
                $salesData = $shop->getSalesData($month, $year);
                $orderContent .= '</td><td>' . number_format($salesData['total'], 2, ',', '') . ' EUR</td><td>' . number_format($salesData['shipping'], 2, ',', '') . ' EUR</td><td>' . number_format($salesData['total'] + $salesData['shipping'], 2, ',', '') . ' EUR</td><td>' . number_format($salesData['provision_netto'], 2, ',', '') . ' EUR<br />(' . number_format($shop->provision, 2, ',', '') . ' %)</td><td>' . number_format($salesData['provision_mwst'], 2, ',', '') . ' EUR</td><td>' . number_format($salesData['provision_brutto'], 2, ',', '') . ' EUR</td><td>' . number_format($salesData['payout'], 2, ',', '') . ' EUR</td></tr>';

            }
            $return['data'] = $orderContent;
            exit(json_encode($return));       
        }
    }


    public function invoicesAction(){
        $this->view->minYear = Model_Order::getMinYearSend();
        $this->view->maxYear = Model_Order::getMaxYearSend();

        $request = $this->getRequest();
        $orderContent = '';
        if($request->getPost('year') && $request->getPost('month')){
            $month = $request->getPost('month');
            $year = $request->getPost('year');
            $invoices = Model_Invoice::getByDate($month, $year);
            foreach($invoices as $invoice){
                $orderContent .= '<tr><td><input type="checkbox" class="send_check" name="invoices[]" value="' . $invoice->id . '" /></td><td>' . 'EP' . $invoice->id . '</td><td>' . $invoice->getShop()->name . ' </td><td>' . $invoice->getShop()->getUser()->email . '</td><td>' . number_format($invoice->invoice_amount, 2, ',', '') . ' EUR</td><td>' . number_format($invoice->payout, 2, ',', '') . ' EUR</td><td><a href="/admin/sales/showinvoice/id/' . $invoice->id . '/">' . $invoice->file . '</a></td><td>' . (($invoice->last_sent) ? date('d.m.Y', strtotime($invoice->last_sent)) : 'nie' ) . '</td></tr>'; 
            }
            exit($orderContent);       
        }
    }

    public function downloadcsvAction(){
        $request = $this->getRequest();
        if($request->getParam('year') && $request->getParam('month')){
            $month = $request->getParam('month');
            $year = $request->getParam('year');
            $orderContent = '"' . implode('","', array('Bestell ID','Firma','Bank','Shop ID','Kontaktperson','Strasse','PLZ','Ort','Umsatz','Versand','Summe','Provision netto','MwSt 19%','Provision Brutto','Auszahlung')) . '"' . "\n";
            $shops = Model_Shop::getShopsWithOrders($month, $year);
            foreach($shops as $shop){
                $orders = $shop->getOrdersByDate($month, $year);
                $orderIds = array();
                foreach($orders as $order){
                    $orderIds[] = $order->order_number;
                }
                $orderContent .= '"' . implode(' / ', $orderIds) . '",';
                $salesData = $shop->getSalesData($month, $year);
                $orderContent .= '"' . implode('","', array($shop->company, $shop->bank_account_holder . ' / ' . $shop->bank_account_number . ' / ' . $shop->bank_name . ' / ' . $shop->bank_id . ' / ' . $shop->bank_swift . ' / ' . $shop->bank_iban, $shop->id, $shop->representative, $shop->street, $shop->zip, $shop->city, number_format($salesData['total'], 2, ',', '') . ' EUR', number_format($salesData['shipping'], 2, ',', '') . ' EUR', number_format($salesData['total'] + $salesData['shipping'], 2, ',', '') . ' EUR', number_format($salesData['provision_netto'], 2, ',', '') . ' EUR (' . number_format($shop->provision, 2, ',', '') . ' %)', number_format($salesData['provision_mwst'], 2, ',', '') . ' EUR', number_format($salesData['provision_brutto'], 2, ',', '') . ' EUR', number_format($salesData['payout'], 2, ',', '') . ' EUR')) . '"' . "\n";
            }
            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=file.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            exit($orderContent);
        }
        else{
            $this->_redirect('index');
        }
    }      

    public function showinvoiceAction(){
        $invoice = Model_Invoice::find($this->getRequest()->getParam('id'));
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/pdf");
        header("Content-Type: application/download");
        header('Content-Disposition: attachment; filename='.$invoice->file);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize(APPLICATION_PATH . Zend_Registry::get('config')->invoices->path . $invoice->file));
        readfile(APPLICATION_PATH . Zend_Registry::get('config')->invoices->path . $invoice->file);
        exit();
    }

    public function createinvoicesAction(){
        $request = $this->getRequest();
        if($request->getParam('year') && $request->getParam('month')){
            $ar_month = array("","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");

            $month = $request->getParam('month');
            $year = $request->getParam('year');

            if(Model_Invoice::getByDate($month, $year)){
                exit('Rechnungen für ' . $ar_month[$month] . ' ' . $year . ' bereits erstellt.');
            }

            $shops = Model_Shop::getShopsWithOrders($month, $year);
            foreach($shops as $shop){
                $orders = $shop->getOrdersByDate($month, $year);
                $orderIds = array();
                foreach($orders as $order){
                    $orderIds[] = $order->order_number;
                }
                $salesData = $shop->getSalesData($month, $year);
                
                $vars = array(
                    'cart_ids' => implode(", ",$orderIds),
                    'shop_name' => $shop->name,
                    'company_name' => $shop->company . ", KtNr: " . $shop->bank_account_number . ", BLZ: " . $shop->bank_id,
                    'producer_addr' => addslashes($shop->street)." ".addslashes($shop->house),
                    'producer_zip' => $shop->zip,
                    'producer_city' => $shop->city,
                    'producer_id' => $shop->id,
                    'sum' => number_format($salesData['total'] + $salesData['shipping'],2,",","."),
                    'provision' => number_format($salesData['provision_netto'],2,",","."),
                    'provision_mwst' => number_format($salesData['provision_mwst'],2,",","."),
                    'provision_brutto' => number_format($salesData['provision_brutto'],2,",","."),
                    'restbetrag' => number_format($salesData['payout'],2,",","."),
                    'month' => $ar_month[intval($month)],
                    'year' => $year
                );

                foreach($vars as $key => $value){ // FPDF can't handle utf8
                    $vars[$key] = utf8_decode($value);
                }

                $invoice = new Model_Invoice(array(
                    'shop_id' => $shop->id,
                    'invoice_amount' => $salesData['provision_brutto'],
                    'month' => $month,
                    'year' => $year,
                    'payout' => $salesData['payout']
                ));
                $invoice->save(); // need to save first for id
                $vars['r_nr'] = 'EP' . $invoice->id;
                $invoice->file = 'EP' . $invoice->id . '.pdf';
                $invoice->save();

                // create file
                require_once('Fpdf/fpdf.php');
                $pdf = new FPDF('P','mm', 'A4');
                self::getHeader($pdf,$vars);
                self::getBody($pdf, $vars);
                self::getFooter($pdf);

                $pdf->Output(APPLICATION_PATH . Zend_Registry::get('config')->invoices->path . $invoice->file, 'F');
            }
  
        }
        else{
            $this->_redirect('invoices');
        }

        $this->_redirect('/admin/sales/invoices/');

    }

    public function invoiceactionsAction(){
        $request = $this->getRequest();
        if(!$request->getParam('invoices')){
            $this->_redirect('/admin/sales/invoices/');
        }
        if($request->getParam('send')){
            $ar_month = array("","Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
            $failed = array();
            foreach($request->getParam('invoices') as $id){
                $invoice = Model_Invoice::find($id);
                $invoiceMail = Model_Email::find('invoiceShop');
                $mail = new Zend_Mail('UTF-8');
                $subject = str_replace(array('#month#', '#year#'), array($ar_month[$invoice->month], $invoice->year), $invoiceMail->subject);
                $content = str_replace(array('#month#', '#year#'), array($ar_month[$invoice->month], $invoice->year), $invoiceMail->content);
                $mail->setBodyText(strip_tags($content));
                $mail->setFrom('mail@epelia.com', 'Epelia');
                $mail->addTo($invoice->getShop()->getUser()->email);
                $mail->setSubject(strip_tags($subject));
                $at = $mail->createAttachment(file_get_contents(APPLICATION_PATH . Zend_Registry::get('config')->invoices->path . $invoice->file));
                $at->filename = $invoice->file;
                $mail->send();
                $invoice->last_sent = date('Y-m-d', time());
                $invoice->last_sent_email = $invoice->getShop()->getUser()->email;
                $invoice->save();

            }
            $this->_redirect('/admin/sales/invoices/');
        }
        if($request->getParam('download')){
            $zip = new ZipArchive();
            $filename = '/tmp/' . "rechnungen.zip";

            if(file_exists($filename)){
                unlink($filename);
            }
            $zip->open($filename, ZIPARCHIVE::CREATE);
            foreach($request->getParam('invoices') as $id){
                $invoice = Model_Invoice::find($id);
                $zip->addFile(APPLICATION_PATH . Zend_Registry::get('config')->invoices->path . $invoice->file, $invoice->file);
            }
            $zip->close();

            header('Content-type: application/zip');
            header('Content-Disposition: filename="rechnungen.zip"');
            readfile($filename);
        }
        exit();
    }

    private function getHeader($pdf, $data){
        $pdf->addPage();
        $pdf->setFont('Arial', '', 10);

        $pdf->Image(APPLICATION_PATH . '/../public/resources/images/admin/Logo.2011-10-09.400x141.ForShopInvoices.png', 130, 10, 60);
        $pdf->setY(40);
        $pdf->setX(170);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 5, "Warenhandel Gattinger", 0, 0, 'R');
        $pdf->Ln();
        $pdf->setX(170);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(20, 5, 'Inh. Micha Gattinger', 0, 0, 'R');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->setX(150);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell(50, 5, "Konrad- Becker- Str. 11\n35102 Lohra\nDeutschland\n\nTelefon: 06426 6041\nE-Mail: mail@epelia.com");
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->setX(130);
        $pdf->Cell(40, 4, "Rechnung Nr.:");
        $pdf->Cell(40, 4, $data['r_nr']);
        $pdf->Ln();
        $pdf->setX(130);
        $pdf->Cell(40, 4, "Rechnungsdatum:");
        $pdf->Cell(40, 4, date('d.m.Y'));
        $pdf->Ln();
        $pdf->setX(130);
        $pdf->Cell(40, 4, "Shop-ID:");
        $pdf->Cell(40, 4, $data['producer_id']);

        $pdf->setY(40);
        $pdf->setX(10);
        $pdf->setFontSize(7);
        $pdf->Multicell(140, 5, "Warenhandel Gattinger\nKonrad-Becker Str. 11, 35102 Lohra, Deutschland");
        $pdf->Ln();
        $pdf->setFont('Arial', 'B', 10);
        $producer_address = (isset($data['shop_name']) && $data['shop_name']) ? $data['shop_name'] : '';
        $producer_address .= "\n";
        $producer_address .= (isset($data['producer_addr']) && $data['producer_addr']) ? $data['producer_addr'] : '';
        $producer_address .= "\n";
        $producer_address .= (isset($data['producer_zip']) && $data['producer_zip']) ? $data['producer_zip'] : '';
        $producer_address .=  ' ';
        $producer_address .= (isset($data['producer_city']) && $data['producer_city']) ? $data['producer_city'] : '';
        $pdf->Multicell(140, 5, $producer_address);
    }

    private function getBody($pdf, $data){
        $pdf->SetY(110);
        $pdf->setFont('Arial', 'B', 12);
        $pdf->Cell(140, 5, 'Rechnung');
        $pdf->Ln();

        $pdf->setFont('Arial', 'B', 10);
        $pdf->Cell(140, 5, utf8_decode('Abrechnung Provision für ' . utf8_decode($data['month']) . ' ' . $data['year'] . ' über Epelia (epelia.com)'));
        $pdf->Ln();

        $pdf->Cell(140, 5, 'Position', 'BR');
        $pdf->Cell(45, 5, '', 'B');
        $pdf->Ln();
        $pdf->setFont('Arial', '', 10);
        $pdf->Cell(140, 5, utf8_decode('Provision aus Verkäufen im ' . utf8_decode($data['month']) . ' ' . $data['year']), 'R');
        $pdf->Ln();
        $pdf->MultiCell(140, 5, utf8_decode('Bestell-IDs: ' . $data['cart_ids']), 'RB');
        $y = $pdf->getY();
        $pdf->setY($y-5);
        $pdf->setX(150);
        $pdf->Cell(45, 5, $data['provision'] . ' EUR', 'B', '', 'R');
        $pdf->Ln();
        $pdf->Cell(140, 5, 'Zwischensumme','','','R');
        $pdf->Cell(45, 5, $data['provision'] . ' EUR','','','R');
        $pdf->Ln();
        $pdf->Cell(140, 5, '+ Umsatzsteuer (19%)','','','R');
        $pdf->Cell(45, 5, $data['provision_mwst'] . ' EUR','','','R');
        $pdf->Ln();
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Cell(140, 5, 'Rechnungsbetrag','','','R');
        $pdf->Cell(45, 5, $data['provision_brutto'] . ' EUR', '','','R');
        $pdf->Ln();
        $pdf->setFont('Arial', '', 10);
        $pdf->Cell(180, 5, utf8_decode('Wir bedanken uns für Ihren Auftrag.'));
        $pdf->Ln();
        $pdf->Cell(180, 5, utf8_decode('Sie brauchen keine Zahlung vorzunehmen. Der Rechnungsbetrag wird mit Ihrem Umsatz wie folgt verrechnet:'));
        $pdf->Ln();
        $pdf->Cell(140, 5, utf8_decode('Umsatz im ' . $data['month'] . ' ' . $data['year']),'','','R');
        $pdf->Cell(45, 5, $data['sum'] . ' EUR','','','R');
        $pdf->Ln();
        $pdf->Cell(140, 5, '- Rechnungsbetrag','','','R');
        $pdf->Cell(45, 5, $data['provision_brutto'] . ' EUR','','','R');
        $pdf->Ln();
        $pdf->setFont('Arial', 'B', 10);
        $pdf->Cell(140, 5, 'Auszahlungsbetrag','','','R');
        $pdf->Cell(45, 5, $data['restbetrag'] . ' EUR','','','R');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->setFont('Arial', '', 10);
        $pdf->Multicell(180, 5, utf8_decode('Der Auszahlungsbetrag wurde auf Ihr Konto überwiesen') . ' (' . $data['company_name'] . ')');
        $pdf->Ln();
    }

    private function getFooter($pdf){
        $pdf->setY(255);
        $pdf->Cell(30, 5, 'Bankverbindung', 'T');
        $pdf->Cell(150, 5, 'Volksbank Heuchelheim, BLZ 513 610 21, Kto 104 601 548', 'T');
        $pdf->Ln();
        $pdf->Cell(30, 5, 'USt-IdNr.');
        $pdf->Cell(120, 5, 'DE-163752546');
        $pdf->Ln();
        $pdf->Cell(30, 5, 'zust. Gericht');
        $pdf->Cell(120, 5, 'Amtsgericht Marburg');
        $pdf->Ln();
        $pdf->Cell(30, 5, 'Lieferdatum');
        $pdf->Cell(120,5, 'gleich Rechnungsdatum');
    }


}
