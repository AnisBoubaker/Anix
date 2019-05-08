<?php
include ("./pdfConfig.php");
$link = dbConnect();

//retrieve arguments
if(isset($_POST["id"])){
    $id=$_POST["id"];
} elseif(isset($_GET["id"])){
    $id=$_GET["id"];
} else $id=0;
//get the DB information
$request = request("SELECT * from `$TBL_ecommerce_invoice` WHERE `id`='$id'",$link);
if(!mysql_num_rows($request)) die (_("La facture spéifiée est invalide."));
$invoice=mysql_fetch_object($request);

$request = request("SELECT * from `$TBL_ecommerce_customer` WHERE `id`='$invoice->id_client'",$link);
$client = mysql_fetch_object($request);

$request = request("SELECT `order_date`,`mailing_address` from `$TBL_ecommerce_order` WHERE `id`='$invoice->id_order'",$link);
$order= mysql_fetch_object($request);

$request = request("SELECT * from `$TBL_ecommerce_terms` WHERE `id`='$invoice->id_terms'",$link);
$terms= mysql_fetch_object($request);

$request=request("SELECT MAX(reception_date) as last_payment FROM $TBL_ecommerce_payment_allocation,$TBL_ecommerce_payment WHERE `$TBL_ecommerce_payment_allocation`.`id_invoice` ='$invoice->id' AND `$TBL_ecommerce_payment`.`id` =`$TBL_ecommerce_payment_allocation`.`id_payment`",$link);
$payment_date = mysql_fetch_object($request);

$request = request("SELECT `$TBL_ecommerce_tax_authority`.name,`$TBL_ecommerce_tax_item`.amount
                                FROM `$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_item`
                                WHERE `$TBL_ecommerce_tax_item`.`id_invoice`='$invoice->id'
                                AND `$TBL_ecommerce_tax_authority`.`id`=`$TBL_ecommerce_tax_item`.id_tax_authority",$link);
$taxes=array();
while($tax=mysql_fetch_object($request)){
    $taxes[]=array("name"=>$tax->name,"amount"=>$tax->amount);
}

//Generates the PDF file
$pdf =& new Cezpdf('A5');
$pdf->ezSetCmMargins(0,0,0,0);



//$euro_diff = array(33=>'Euro');
//$pdf->selectFont('./fonts/Helvetica.afm',
//        array('encoding'=>'WinAnsiEncoding','differences'=>$euro_diff));
$pdf->selectFont('../../3rdparty/ezpdf/fonts/Helvetica.afm');
//$pdf->setStrokeColor(0,0.196,0.4);
//$pdf->setColor(0.137,0.122,0.126,0);
$pdf->setStrokeColor(0.55,0,0);
drawInvPage($pdf,$invoice,$order,$client,$terms,$taxes,$payment_date);


//52 cars per line!!!
//32 lines maximum per page!!!
    //items table
    $tableData=array();
    //get the invoice items
    $request=request("SELECT * FROM `$TBL_ecommerce_invoice_item` WHERE `id_invoice`='$invoice->id' ORDER BY `id`",$link);
    $totLines = 0;
    $tableHeader = array("qty"=>"","description"=>"","price"=>"","total"=>"");
    while($item=mysql_fetch_object($request)){
        $descriptionStr = "<b>".$item->description."</b> ";
        $descriptionStr.= "\n<i>(Réf:".$item->reference.")</i>";
        $descriptionStr.= $item->details==""?"":"\n".$item->details;
        $descriptionStr=unhtmlentities(utf8_decode($descriptionStr));
        $total = number_format($item->uprice*$item->qty,2,","," ");
        $nbLines=countLines($descriptionStr,40)+1; //+1 because the row gap between items is equivalent to 1 line
        if($totLines+$nbLines<=28){//still space remaining in the page
            $tableData[]=array("qty"=>$item->qty,"description"=>$descriptionStr,"price"=>number_format($item->uprice,2,","," ").$currency_symbol,"total"=>$total.$currency_symbol);
            $totLines+=$nbLines;
        } else { //no space remaining => write the current table items and start a new page
            $pdf->ezSetY(400);
            $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "showLines"=>0,
                                        "shaded"=>0,
                                        //"lineCol"=>array(0,0.196,0.4),
                                        //"textCol"=>array(0,0.196,0.4),
                                        "textCol"=>array(0,0,0),
                                        "colGap"=>3,
                                        "rowGap"=>4,
                                        "xPos"=>415,
                                        "width"=>400,
                                        'xOrientation' => 'left',
                                        'fontSize' => 8,
                                        "cols"=>array(
                                                        "qty"=>array("justification"=>"right","width"=>55),
                                                        "description"=>array("justification"=>"left","width"=>230),
                                                        "price"=>array("justification"=>"right","width"=>70),
                                                        "total"=>array("justification"=>"right","width"=>70),
                                                        )
                                        )
                                );
            $pdf->ezNewPage(); //start new page
            drawInvPage($pdf,$invoice,$order,$client,$terms,$taxes,$payment_date); //draw the empty page
            $tableData=array(); //start a new items table
            $totLines = 0;
            $tableData[]=array("qty"=>$item->qty,"code"=>unhtmlentities(utf8_decode($item->reference)),"description"=>$descriptionStr,"price"=>$item->uprice.$currency_symbol,"total"=>$total.$currency_symbol);
            $totLines+=$nbLines;
        }
    }
    $pdf->ezSetY(400);
    $pdf->ezTable($tableData,$tableHeader,"",
                                    array(
                                        "showHeadings"=>0,
                                        "showLines"=>0,
                                        "shaded"=>0,
                                        //"lineCol"=>array(0,0.196,0.4),
                                        //"textCol"=>array(0,0.196,0.4),
                                        "textCol"=>array(0,0,0),
                                        "colGap"=>3,
                                        "rowGap"=>4,
                                        "xPos"=>415,
                                        "width"=>400,
                                        'xOrientation' => 'left',
                                        'fontSize' => 8,
                                        "cols"=>array(
                                                        "qty"=>array("justification"=>"right","width"=>55),
                                                        "description"=>array("justification"=>"left","width"=>230),
                                                        "price"=>array("justification"=>"right","width"=>70),
                                                        "total"=>array("justification"=>"right","width"=>70),
                                                        )
                                        )
                                );

    /**
     * TERMS
     */
    //Logo
/**
    $pdf->ezNewPage(); //start new page for terms
    $pdf->ezImage('./logo_invoices3.png',0,570,'none','center');
    $pdf->ezSetY(755);
    $pdf->ezText("<b>"._("Facture")." #".utf8_decode(id_format($invoice->id))."</b>",18,array('justification'=>'right'));
    $pdf->ezSetY(730);
    $pdf->ezText("<i>"._("Date").": ".utf8_decode($invoice->invoice_date)." </i>",14,array('justification'=>'right'));
    $pdf->ezSetY(685);
    //$pdf->ezSetY(690);
    $pdf->ezText(utf8_decode($terms->description),7,array('left'=>25));
**/
    /**
    * WRITE FOOTER FORM TERMS
    **/
	//$pdf->ezImage('./logo_invoices4.png',0,570,'none','center');
/**
	$pdf->setStrokeColor(0.396,0.78,0.4);
	$pdf->line(18,40,585,40);
	$pdf->ezSetY(40);
	$pdf->ezText(
		utf8_decode("SARL au capital de 20000 euros - N° Siret 493 510 374 00017 - CODE NAF 524Z - N° TVA Intracom. FR55 493 510 374 - RCS PARIS 493 510 374"),
		8.3,
		array('left'=>25)
	);
**/

$pdf->ezStream();
mysql_close($link);
?>