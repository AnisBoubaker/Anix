<?php
function showErrors($message,$errMessage,$errors){
  $messToDisplay = (strlen($message)>0);
  $errToDisplay = (strlen($errMessage)>0);
  if($messToDisplay || $errToDisplay){
    echo "<table class='message' align='center' width='80%'>\n";
	if($messToDisplay){
      echo "<tr>\n";
      echo "<td align='center' width='50' valign='center'><img src='../images/infos.gif' border='0'></td>\n";
      echo "<td align='left' valign='top'>$message</td>\n";
      echo "</tr>\n";
	}
	if($errToDisplay){
	  echo "<tr>\n";
      echo "<td align='center' width='50' valign='center'><img src='../images/warning.gif' border='0'></td>\n";
      echo "<td align='left' valign='top'>$errors Erreur(s) Bloquante(s).<br>$errMessage</td>\n";
      echo "</tr>\n";
	}
    echo "</table>\n";
  }
}
?>