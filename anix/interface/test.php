<?php
  include("./config.php");
  //include("./product.php");
  include("./page.php");
  //$myProduct = new product(1417,1);
  $myPage = new Page(6,1);
  echo unhtmlentities($myPage->content);
?>
