<?php
$currency_symbol = " Eur";

function drawInvPage(&$pdf,$invoice,$order,$client,$terms,$taxes,$payment_date){
    global $company_address,$currency_symbol;

    /**
    * PAGE HEADER
    **/
        //Logo
        $pdf->ezSetY(580);
        $pdf->ezImage('../../custom/logo_invoices.jpg',0,400,'none','center');
        //Titre du document
        //$pdf->setColor(0.137,0.122,0.126);
        //$pdf->ezSetY(777);
        //$pdf->ezText($company_address,9,array('left'=>185));

		$pdf->setColor(0,0,0);
        $pdf->ezSetY(520);
        $pdf->ezText("<b>"._("Facture")." #".($invoice->refund=="Y"?"R":"").utf8_decode(id_format($invoice->id))."</b>",14,array('left'=>10));
        $pdf->ezSetY(520);
        $pdf->ezText("<i>"._("Date").": ".utf8_decode($invoice->invoice_date)."   </i>",12,array('justification'=>'right'));
        $pdf->ezSetY(500);
        //Custumer information
        $tableDate = array();
        $invoice_address=unhtmlentities(utf8_decode($invoice->billing_address));
        $order_address=unhtmlentities(utf8_decode($order->mailing_address));
        $tableData[]=array("invoice_address"=>"<b>"._("Adresse de facturation")."</b>","order_address"=>"<b>"._("Adresse de livraison")."</b>");
        $tableData[]=array("invoice_address"=>$invoice_address,"order_address"=>$order_address);
        $tableHeader = array("invoice_address"=>_("Adresse de facturation"),"order_address"=>_("Adresse de livraison"));
        //$pdf->setColor(0,0,0,1);
        $pdf->setStrokeColor(0,0,0);
        $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "shaded"=>2,
                                        "shadeCol"=>array(1,1,1),
                                        "shadeCol2"=>array(0.89,0.65,0.2),
                                        //"lineCol"=>array(0,0.196,0.4),
                                        //"textCol"=>array(0,0.196,0.4),
                                        "lineCol"=>array(0.55,0,0),
                                        "textCol"=>array(0,0,0),
                                        "xPos"=>216,
                                        "width"=>400,
                                        'fontSize' => 8)
                                );
        $tableData=array();
        $tableData[]=array("id_client"=>"<b>".utf8_decode(_("Votre N° Client"))."</b>","order_date"=>"<b>"._("Date de commande")."</b>","order_num"=>"<b>".utf8_decode(_("N° de Commande"))."</b>","terms"=>"<b>"._("Termes")."</b>","delay"=>"<b>"._("Payer avant le")."</b>");
        $tableData[]=array("id_client"=>id_format($client->id),"order_date"=>utf8_decode($order->order_date),"order_num"=>id_format($invoice->id_order),"terms"=>utf8_decode($terms->name),"delay"=>utf8_decode($invoice->due_date));
        $tableHeader = array("id_client"=>"<b>".utf8_decode(_("Votre N° Client"))."</b>","order_date"=>"<b>"._("Date de commande")."</b>","order_num"=>"<b>".utf8_decode(_("N° de Commande"))."</b>","terms"=>"<b>"._("Termes")."</b>","delay"=>"<b>"._("Payer avant le")."</b>");
        $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "shaded"=>2,
                                        "shadeCol"=>array(1,1,1),
                                        "shadeCol2"=>array(0.89,0.65,0.2),
                                        "lineCol"=>array(0.55,0,0),
                                        "textCol"=>array(0.137,0.122,0.126),
                                        "xPos"=>216,
                                        "width"=>400,
                                        'fontSize' => 8
                                        )
                                );


    /**
    * DRAW THE INVOICE TABLE
    **/
        //table header
        $pdf->setStrokeColor(0.55,0,0);
        $pdf->rectangle(11,395,30,12); //qty box
        $pdf->rectangle(41,395,230,12); //description box
        $pdf->rectangle(271,395,70,12); //price box
        $pdf->rectangle(341,395,70,12); //total box

        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("QTÉ"))."</b>",8,array('left'=>15));
        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("DESCRIPTION"))."</b>",8,array('left'=>115));
        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("PRIX"))."</b>",8,array('left'=>295));
        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("TOTAL"))."</b>",8,array('left'=>362));

        //table
        $pdf->rectangle(11,125,30,270); //qty
        $pdf->rectangle(41,125,230,270); //description
        $pdf->rectangle(271,125,70,270); //price
        $pdf->setColor(0.89,0.65,0.2);
        $pdf->rectangle(341,125,70,270); //total
        $pdf->filledRectangle(342,126,68,268); //total filled<br>

        //total
        $pdf->setColor(0.89,0.65,0.2);
        $pdf->rectangle(341,50,70,75);
        $pdf->filledRectangle(342,51,68,73);
        $pdf->setColor(0,0,0);
        $pdf->rectangle(341,50,70,20);  //To pay amount


    /**
    * WRITE SUBTOTAL TABLE
    **/
        $tableData = array();
        $tableData[]=array("title"=>_("Total HT"),"amount"=>number_format($invoice->subtotal,2,","," ").$currency_symbol);
        foreach($taxes as $tax){
            $tableData[]=array("title"=>utf8_decode($tax["name"]),"amount"=>number_format($tax["amount"],2,","," ").$currency_symbol);
        }
        $nbTaxes = count($taxes);
        if($nbTaxes<4) for($i=0;$i<4-$nbTaxes;$i++){
            $tableData[]=array("title"=>"","amount"=>"");
        }
        $tableData[]=array("title"=>"<b>".utf8_decode(_("MONTANT DÛ"))."</b>","amount"=>"<b>".number_format($invoice->grandtotal,2,","," ").$currency_symbol."</b>");
        $tableHeader = array("title"=>"","amount"=>"");
        $pdf->ezSetY(123);
        $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "showLines"=>0,
                                        "shaded"=>0,
                                        "lineCol"=>array(0.145,0.255,0.145),
                                        "textCol"=>array(0,0,0),
                                        "rowGap"=>1,
                                        "xPos"=>318,
                                        "width"=>200,
                                        'fontSize' => 8,
                                        "cols"=>array(
                                                        "title"=>array("justification"=>"right"),
                                                        "amount"=>array("width"=>85,"justification"=>"right"))
                                        )
                                );
        //Write perceived amount
        if($payment_date->last_payment!=NULL){
        	$balance = $invoice->grandtotal-$invoice->payed_amount;
            $pdf->ezSetY(123);
            	$pdf->ezText(utf8_decode(_("Montant perçu au"))." ".$payment_date->last_payment.": ".$invoice->payed_amount.$currency_symbol."\nBalance:".number_format($balance,2,","," ").$currency_symbol.($invoice->refund=="Y"?"\n<b>CECI EST UNE FACTURE DE REMBOURSEMENT\nDE LA FACTURE #".$invoice->id_refunded."</b>":""),10,array('left'=>20));
        }

    /**
    * WRITE FOOTER
    **/
	$pdf->setStrokeColor(0.55,0,0);
	$pdf->line(11,45,411,45);
	$pdf->setColor(0,0,0);
	$pdf->ezSetY(44);
	$pdf->ezText(
		utf8_decode("SARL au capital de 50 000 euros - N° Siret 501 032 015 00015 - CODE NAF 524Z\nN° TVA Intracom. FR55 493 510 374 - RCS PARIS 501 032 015"),
		6,
		array('left'=>11,'justification'=>'center')
	);
}

function drawOrdPage(&$pdf,$order,$client,$payment_date){
    global $company_address,$currency_symbol;

    /**
    * PAGE HEADER
    **/
        //Logo
        $pdf->ezSetY(580);
        $pdf->ezImage('../../custom/logo_invoices.jpg',0,400,'none','center');
        //Titre du document
        //$pdf->setColor(0.137,0.122,0.126);
        //$pdf->ezSetY(777);
        //$pdf->ezText($company_address,9,array('left'=>185));

		$pdf->setColor(0,0,0);
        $pdf->ezSetY(520);
        $pdf->ezText("<b>"._("Commande")." #".utf8_decode(id_format($order->id))."</b>",14,array('left'=>10));
        $pdf->ezSetY(520);
        $pdf->ezText("<i>"._("Date").": ".utf8_decode($order->order_date)."   </i>",12,array('justification'=>'right'));
        $pdf->ezSetY(500);
        //Custumer information
        $tableDate = array();
        $invoice_address=unhtmlentities(utf8_decode($order->billing_address));
        $order_address=unhtmlentities(utf8_decode($order->mailing_address));
        $tableData[]=array("invoice_address"=>"<b>"._("Adresse de facturation")."</b>","order_address"=>"<b>"._("Adresse de livraison")."</b>");
        $tableData[]=array("invoice_address"=>$invoice_address,"order_address"=>$order_address);
        $tableHeader = array("invoice_address"=>_("Adresse de facturation"),"order_address"=>_("Adresse de livraison"));
        //$pdf->setColor(0,0,0,1);
        $pdf->setStrokeColor(0,0,0);
        $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "shaded"=>2,
                                        "shadeCol"=>array(1,1,1),
                                        "shadeCol2"=>array(0.89,0.65,0.2),
                                        //"lineCol"=>array(0,0.196,0.4),
                                        //"textCol"=>array(0,0.196,0.4),
                                        "lineCol"=>array(0.55,0,0),
                                        "textCol"=>array(0,0,0),
                                        "xPos"=>216,
                                        "width"=>400,
                                        'fontSize' => 8)
                                );
         $tableData=array();
        $tableData[]=array("id_client"=>"<b>"._("Votre No Client")."</b>","delivery_date"=>"<b>"._("Date de livraison")."</b>","deposit"=>"<b>"._("Accompte requis")."</b>");
        $tableData[]=array("id_client"=>id_format($client->id),"delivery_date"=>utf8_decode($order->delivery_date),"deposit"=>$order->deposit_amount.$currency_symbol);
        $tableHeader = array("id_client"=>"<b>"._("Votre No Client")."</b>","delivery_date"=>"<b>"._("Date de livraison")."</b>","deposit"=>"<b>"._("Accompte requis")."</b>");
        $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "shaded"=>2,
                                        "shadeCol"=>array(1,1,1),
                                        "shadeCol2"=>array(0.89,0.65,0.2),
                                        "lineCol"=>array(0.55,0,0),
                                        "textCol"=>array(0.137,0.122,0.126),
                                        "xPos"=>216,
                                        "width"=>400,
                                        'fontSize' => 8
                                        )
                                );

   /**
    * DRAW THE INVOICE TABLE
    **/
        //table header
        $pdf->setStrokeColor(0.55,0,0);
        $pdf->rectangle(11,395,30,12); //qty box
        $pdf->rectangle(41,395,230,12); //description box
        $pdf->rectangle(271,395,70,12); //price box
        $pdf->rectangle(341,395,70,12); //total box

        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("QTÉ"))."</b>",8,array('left'=>15));
        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("DESCRIPTION"))."</b>",8,array('left'=>115));
        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("PRIX"))."</b>",8,array('left'=>295));
        $pdf->ezSetY(406); $pdf->ezText("<b>".utf8_decode(_("TOTAL"))."</b>",8,array('left'=>362));

        //table
        $pdf->rectangle(11,125,30,270); //qty
        $pdf->rectangle(41,125,230,270); //description
        $pdf->rectangle(271,125,70,270); //price
        $pdf->setColor(0.89,0.65,0.2);
        $pdf->rectangle(341,125,70,270); //total
        $pdf->filledRectangle(342,126,68,268); //total filled<br>

        //total
        $pdf->setColor(0.89,0.65,0.2);
        $pdf->rectangle(341,105,70,20);
        $pdf->filledRectangle(342,106,68,18);
        $pdf->setColor(0,0,0);
        //$pdf->rectangle(341,50,70,20);  //To pay amount
    /**
    * WRITE SUBTOTAL TABLE
    **/
    	$pdf->setColor(0.145,0.255,0.145);
    	$pdf->setStrokeColor(0.396,0.78,0.4);
        $tableData = array();
        $tableData[]=array("title"=>_("Sous-total"),"amount"=>$order->subtotal.$currency_symbol);
        $tableHeader = array("title"=>"","amount"=>"");
        $pdf->ezSetY(123);
        $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "showLines"=>0,
                                        "shaded"=>0,
                                        "lineCol"=>array(0.145,0.255,0.145),
                                        "textCol"=>array(0,0,0),
                                        "rowGap"=>1,
                                        "xPos"=>318,
                                        "width"=>200,
                                        'fontSize' => 8,
                                        "cols"=>array(
                                                        "title"=>array("justification"=>"right"),
                                                        "amount"=>array("width"=>85,"justification"=>"right"))
                                        )
                                );
        //Write perceived amount
        if($payment_date->last_payment!=NULL){
        	$balance = $invoice->grandtotal-$invoice->payed_amount;
            $pdf->ezSetY(145);$pdf->ezText(utf8_decode(_("Montant perçu au"))." ".$payment_date->last_payment.": ".$order->payed_amount.$currency_symbol,10,array('left'=>20));
        }

    /**
    * WRITE FOOTER
    **/
	$pdf->setStrokeColor(0.55,0,0);
	$pdf->line(11,45,411,45);
	$pdf->setColor(0,0,0);
	$pdf->ezSetY(44);
	$pdf->ezText(
		utf8_decode("SARL au capital de 50 000 euros - N° Siret 501 032 015 00015 - CODE NAF 524Z\nN° TVA Intracom. FR55 493 510 374 - RCS PARIS 501 032 015"),
		6,
		array('left'=>11,'justification'=>'center')
	);
}
?>