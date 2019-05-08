<?php
include("./cart.class.php");

$cart = new Cart("O");

$cart->addItem(5,"Allo","Testing","Retesting",10.20);
$cart->addItem(1,"Allo","Testing4","Retesting4",15.00);
$cart->addItem(3,"Allo","Testing4","Retesting4",30);
$cart->delItem(1);
$cart->delItem(0);
$cart->addItem(5,"Allo","Testing","Retesting",10.20);
$cart->addItem(1,"Allo","Testing4","Retesting4",15.00);
$cart->addItem(3,"Allo","Testing4","Retesting4",30);
echo "<pre>";
print_r($cart);
echo "</pre>";
?>
