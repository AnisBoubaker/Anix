<?php
include ("./pdfConfig.php");
$link = dbConnect();

//retrieve arguments
if(isset($_POST["id"])){
    $id=$_POST["id"];
} elseif(isset($_GET["id"])){
    $id=$_GET["id"];
} else $id=0;

$request = request("SELECT * from `$TBL_ecommerce_order` WHERE `id`='$id'",$link);
$order= mysql_fetch_object($request);

$request = request("SELECT * from `$TBL_ecommerce_customer` WHERE `id`='$order->id_client'",$link);
$client = mysql_fetch_object($request);

$request=request("SELECT MAX(reception_date) as last_payment FROM $TBL_ecommerce_payment_allocation,$TBL_ecommerce_payment WHERE `$TBL_ecommerce_payment_allocation`.`id_order` ='$order->id' AND `$TBL_ecommerce_payment`.`id` =`$TBL_ecommerce_payment_allocation`.`id_payment`",$link);
$payment_date = mysql_fetch_object($request);

//Generates the PDF file
$pdf =& new Cezpdf('A5');
$pdf->ezSetCmMargins(0,0,0,0);

$pdf->selectFont('../../3rdparty/ezpdf/fonts/Helvetica.afm');
$pdf->setStrokeColor(0.145,0.255,0.145);
$pdf->ezStartPageNumbers(560,695,12,'',"<i>"._("Page")." {PAGENUM} / {TOTALPAGENUM}</i></b>",1);
drawOrdPage($pdf,$order,$client,$payment_date);

//items table
$tableData=array();
//get the invoice items
$request=request("SELECT * FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$order->id' ORDER BY `id`",$link);
$totLines = 0;
$tableHeader = array("qty"=>"","code"=>"","description"=>"","price"=>"","total"=>"");
while($item=mysql_fetch_object($request)){
    $descriptionStr = "<b>".$item->description."</b>";
    $descriptionStr.= "\n<i>(Réf:".$item->reference.")</i>";
    $descriptionStr.= $item->details==""?"":"\n".$item->details;
    $descriptionStr=unhtmlentities(utf8_decode($descriptionStr));
    $total = number_format($item->uprice*$item->qty,2);
    $nbLines=countLines($descriptionStr,40)+1; //+1 because the row gap between items is equivalent to 1 line
    if($totLines+$nbLines<=28){//still space remaining in the page
        $tableData[]=array("qty"=>$item->qty,"description"=>$descriptionStr,"price"=>$item->uprice.$currency_symbol,"total"=>$total.$currency_symbol);
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
        drawOrdPage($pdf,$order,$client,$payment_date); //draw the empty page

        $tableData=array(); //start a new items table
        $totLines = 0;
        $tableData[]=array("qty"=>$item->qty,"code"=>utf8_decode($item->reference),"description"=>$descriptionStr,"price"=>$item->uprice.$currency_symbol,"total"=>$total.$currency_symbol);
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

$pdf->ezStream();
mysql_close($link);
?>