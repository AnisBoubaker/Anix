<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
<table style='width:100%'>
<tr>
<td style='vertical-align:top;'><!--Col1-->
	<table>
	<tr><td colspan="2"><h3><?php echo _("Infos client");?>:</h3></td></tr>
    <tr>
      <td><?php echo _("Prenom").":";?></td>
      <td>
      	<select name='greating'>
			<option value='M' <?php
				if($action=="edit" && $edit->greating=="M") echo "selected='selected'";
				if(($action=="insert" || $action=="update") && $_POST["greating"]=="M") echo "selected='selected'";
			?>>M</option>
			<option value='Mme' <?php
				if($action=="edit" && $edit->greating=="Mme") echo "selected='selected'";
				if(($action=="insert" || $action=="update") && $_POST["greating"]=="Mme") echo "selected='selected'";
			?>>Mme</option>
			<option value='Mlle' <?php
				if($action=="edit" && $edit->greating=="Mlle") echo "selected='selected'";
				if(($action=="insert" || $action=="update") && $_POST["greating"]=="Mlle") echo "selected='selected'";
			?>>Mlle</option>
		</select>
        <input type='text' name='firstname' size='11' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->firstname'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["firstname"]."'";
        //Automatically fill the login field if new customer
        if($action=="add") echo " onChange='javascript:autoLogin()'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Nom").":";?></td>
      <td>
        <input type='text' name='lastname' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->lastname'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["lastname"]."'";
        //Automatically fill the login field if new customer
        if($action=="add") echo " onChange='javascript:autoLogin()'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Compagnie").":";?></td>
      <td>
        <input type='text' name='company' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->company'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["company"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Telephone").":";?></td>
      <td>
        <input type='text' name='phone' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->phone'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["phone"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Cellulaire").":";?></td>
      <td>
        <input type='text' name='cell' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->cell'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["cell"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Telecopie").":";?></td>
      <td>
        <input type='text' name='fax' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->fax'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["fax"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Courriel").":";?></td>
      <td>
        <input type='text' name='email' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->email'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["email"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Langue de correspondance").":";?></td>
      <td>
        <SELECT name='language'>
          <OPTION value='0'>--<?php echo _("Choisissez");?>--</OPTION>
        <?
        $languages=request("SELECT id,name FROM `$TBL_gen_languages` WHERE used='Y' ORDER BY name",$link);
        while($language=mysql_fetch_object($languages)){
        	echo "<OPTION value='".$language->id."'";
        	if($action=="edit" && $edit->language==$language->id) echo " SELECTED";
        	if(($action=="insert" || $action=="update") && $_POST["language"]==$language->id)  echo " SELECTED";
        	echo ">".$language->name."</OPTION>";
        }
    	  ?>
        </SELECT>
      </td>
    </tr>
    </table>
</td><!--Col1-->
<td style='vertical-align:top;'><!--Col2-->
	<table>
	<tr><td colspan="2"><h3><?php echo _("Codes d'accès");?>:</h3></td></tr>
	<tr>
      <td><?php echo _("Login").":";?></td>
      <td>
        <input type='text' name='login' size='20' Maxlength='100' <?
        if($action=="edit") echo " value='$edit->login'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["login"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Mot de passe").":";?><?
      if($action=="edit" || $action=="update") echo "<i>(Modification)</i>";
      ?></td>
      <td>
        <input type='password' name='password1' size='20' Maxlength='100' <?
        //if($action=="edit") echo " value='".unhtmlentities($edit->password1)."'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["password1"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Mot de passe (Verification)").":";?></td>
      <td>
        <input type='password' name='password2' size='20' Maxlength='100' <?
        //if($action=="edit") echo " value='".unhtmlentities($edit->password2)."'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["password2"]."'";
        ?>>
      </td>
    </tr>
    <?php
    if($action=="add" || $action=="insert"){
    ?>
    <tr>
    	<td colspan="2">
    	<input type='checkbox' name='send_login' <?php
    		if($action=="insert" && isset($_POST["send_login"]))  echo " checked='checked'";
    		if($action=="add")  echo " checked='checked'";
    	?> /> <b><?php echo _("Envoyer les nouveaux codes d'accès au client"); ?></b>
    	</td>
    </tr>
    <?php
    }
    ?>
    <tr><td colspan="2"><br /><h3><?php echo _("E-Commerce");?>:</h3></td></tr>
    <tr>
        <td><?php echo _("Groupe de prix").":"; ?></td>
        <td>
        <?php
        $priceGroups = request("SELECT id, name from `$TBL_catalogue_price_groups`, `$TBL_catalogue_info_price_groups` WHERE `$TBL_catalogue_info_price_groups`.`id_language`='$used_language_id' AND `$TBL_catalogue_info_price_groups`.`id_price_group`=`$TBL_catalogue_price_groups`.`id`",$link);
        echo "<select name='price_group'>";
        echo "<option value='0'>"._("Public")."</option>";
        while($group = mysql_fetch_object($priceGroups)){
        	echo "<option value='".$group->id."'";
        	if($action=="edit" && $edit->id_user_group==$group->id) echo " SELECTED";
        	if(($action=="insert" || $action=="update") && $_POST["price_group"]==$group->id)  echo " SELECTED";
        	echo ">".$group->name."</option>";
        }
        echo "</select>";
        ?>
        </td>
    </tr>
    <tr>
        <td><?php echo _("Termes").":"; ?></td>
        <td>
        <?php
        $terms = request("SELECT * from `$TBL_ecommerce_terms` ORDER BY ordering",$link);
        echo "<select name='terms'>";
        while($term = mysql_fetch_object($terms)){
        	echo "<option value='".$term->id."'";
        	if($action=="add" && $term->default=='Y') echo " SELECTED";
        	if($action=="edit" && $edit->id_terms==$term->id) echo " SELECTED";
        	if(($action=="insert" || $action=="update") && $_POST["terms"]==$term->id)  echo " SELECTED";
        	echo ">".$term->name."</option>";
        }
        echo "</select>";
        ?>
        </td>
    </tr>
    <tr>
      <td><?php echo _("Marge de crédit").":";?></td>
      <td>
        <input type='text' name='credit_margin' size='20' Maxlength='100' <?
        if($action=="add") echo " value='0.00'";
        if($action=="edit") echo " value='".unhtmlentities($edit->credit_margin)."'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["credit_margin"]."'";
        ?>>
      </td>
    </tr>
    <tr>
        <td><?php echo _("Groupe de taxes").":"; ?></td>
        <td>
        <?php
        $tax_groups = request("SELECT * from `$TBL_ecommerce_tax_group` ORDER BY ordering",$link);
        echo "<select name='tax_group'>";
        while($tax_group = mysql_fetch_object($tax_groups)){
        	echo "<option value='".$tax_group->id."'";
        	if($action=="add" && $tax_group->default=='Y') echo " SELECTED";
        	if($action=="edit" && $edit->id_tax_group==$tax_group->id) echo " SELECTED";
        	if(($action=="insert" || $action=="update") && $_POST["tax_group"]==$tax_group->id)  echo " SELECTED";
        	echo ">".$tax_group->name."</option>";
        }
        echo "</select>";
        ?>
        </td>
    </tr>
	</table>
</td><!--Col2-->
</tr>
</table>
<?php
TABS_closeTab();
/**
 * TAB2: Addresses
 */
TABS_addTab(2,_("Adresse(s)"));
?>
<table style='width:100%'>
<tr>
<td style='vertical-align:top;'><!--Col1-->
	<table>
    <tr><td colspan="2"><h3><?php echo _("Adresse de livraison");?>:</h3><br /><br /></td></tr>
    <tr>
      <td><?php echo _("Numero civique").":";?></td>
      <td>
        <input type='text' name='mailing_num' size='10' Maxlength='100' <?
        if($action=="edit") echo " value='$edit_mailing->num'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_num"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Rue").":";?></td>
      <td>
        <input type='text' name='mailing_street1' size='40' Maxlength='100' <?
        if($action=="edit") echo " value='$edit_mailing->street1'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_street1"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type='text' name='mailing_street2' size='40' Maxlength='100' <?
        if($action=="edit") echo " value='$edit_mailing->street2'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_street2"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <?php echo _("Bat.");?>:<input type='text' name='mailing_building' style='width:30px;' Maxlength='10' <?
        if($action=="edit") echo " value='$edit_mailing->building'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_building"]."'";
        ?>>&nbsp;
        <?php echo _("Esc.");?>:<input type='text' name='mailing_stairs' style='width:30px;' Maxlength='10' <?
        if($action=="edit") echo " value='$edit_mailing->stairs'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_stairs"]."'";
        ?>>&nbsp;
        <?php echo _("Étage.");?>:<input type='text' name='mailing_floor' style='width:30px;' Maxlength='10' <?
        if($action=="edit") echo " value='$edit_mailing->floor'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_floor"]."'";
        ?>>&nbsp;
        <?php echo _("Code");?>:<input type='text' name='mailing_code' style='width:30px;' Maxlength='10' <?
        if($action=="edit") echo " value='$edit_mailing->code'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_code"]."'";
        ?>>&nbsp;
      </td>
    </tr>
    <tr>
      <td><?php echo _("Ville").":";?></td>
      <td>
        <input type='text' name='mailing_city' size='40' Maxlength='100' <?
        if($action=="edit") echo " value='$edit_mailing->city'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_city"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Province").":";?></td>
      <td>
        <input type='text' name='mailing_province' size='40' Maxlength='100' <?
        if($action=="edit") echo " value='$edit_mailing->province'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_province"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Code postal").":";?></td>
      <td>
        <input type='text' name='mailing_zip' size='10' Maxlength='100' <?
        if($action=="edit") echo " value='$edit_mailing->zip'";
        if($action=="insert" || $action=="update")  echo " value='".$_POST["mailing_zip"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Pays").":";?></td>
      <td>
      	<select name='mailing_country'>
      	<?php
      	foreach($countryList as $countryCode => $countryName){
      		if($countryName!="separator"){
      			echo "<option value='".$countryCode."'";
      			if($action=="edit" && $edit_mailing->country_code==$countryCode)  echo " selected='selected'";
      			elseif(($action=="insert" || $action=="update") && $_POST["mailing_country"]==$countryCode)  echo " selected='selected'";
      			echo ">".$countryName."</option>";
      		} else {
      			echo "<option value='' disabled='disabled'>----------</option>";
      		}
      	}
		?>
      	</select>
      </td>
    </tr>
    </table>
</td><!--Col1-->
<td style='vertical-align:top;'><!--Col2-->
	<table>
	<tr><td colspan="2"><h3><?php echo _("Adresse de facturation");?>:</h3></td></tr>
    <tr>
      <td colspan='2'>
        <input type='checkbox' name='same_address' onClick='javascript:chkBox_same_address()'<?
        if($action=="edit" && $edit->id_address_billing==$edit->id_address_mailing) echo " CHECKED";
        elseif(($action=="insert" || $action=="update") && isset($_POST["same_address"]))  echo " CHECKED";
        elseif($action=="add") echo " CHECKED";
        //else echo " CHECKED";
        ?>><i><?php echo _("Identique a l'adresse de livraison");?></i>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Numero civique").":";?></td>
      <td>
        <input type='text' name='billing_num' size='10' Maxlength='100' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->num'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_num"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Rue").":";?></td>
      <td>
        <input type='text' name='billing_street1' size='40' Maxlength='100' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->street1'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_street1"]."'";
        ?>>
      </td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
    	<td>
        <input type='text' name='billing_street2' size='40' Maxlength='100' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->street2'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_street2"]."'";
        ?>>
        </td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>
        <?php echo _("Bat.");?>:<input type='text' name='billing_building' style='width:30px;' Maxlength='10' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->building'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_building"]."'";
        ?>>&nbsp;
        <?php echo _("Esc.");?>:<input type='text' name='billing_stairs' style='width:30px;' Maxlength='10' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->stairs'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_stairs"]."'";
        ?>>&nbsp;
        <?php echo _("Étage.");?>:<input type='text' name='billing_floor' style='width:30px;' Maxlength='10' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->floor'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_floor"]."'";
        ?>>&nbsp;
        <?php echo _("Code");?>:<input type='text' name='billing_code' style='width:30px;' Maxlength='10' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->code'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_code"]."'";
        ?>>&nbsp;
      </td>
    </tr>
    <tr>
      <td><?php echo _("Ville").":";?></td>
      <td>
        <input type='text' name='billing_city' size='40' Maxlength='100' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='".$edit_billing->city."'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_city"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Province").":";?></td>
      <td>
        <input type='text' name='billing_province' size='40' Maxlength='100' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->province'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_province"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Code postal").":";?></td>
      <td>
        <input type='text' name='billing_zip' size='10' Maxlength='100' <?
        if(!isset($_POST["same_address"]) && $action=="edit") echo " value='$edit_billing->zip'";
        if(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update"))  echo " value='".$_POST["billing_zip"]."'";
        ?>>
      </td>
    </tr>
    <tr>
      <td><?php echo _("Pays").":";?></td>
      <td>
      	<select name='billing_country'>
      	<?php
      	foreach($countryList as $countryCode => $countryName){
      		if($countryName!="separator"){
      			echo "<option value='".$countryCode."'";
      			if(!isset($_POST["same_address"]) && $action=="edit" && $edit_billing->country_code==$countryCode)  echo " selected='selected'";
      			elseif(!isset($_POST["same_address"]) && ($action=="insert" || $action=="update") && $_POST["billing_country"]==$countryCode)  echo " selected='selected'";
      			echo ">".$countryName."</option>";
      		} else {
      			echo "<option value='' disabled='disabled'>----------</option>";
      		}
      	}
		?>
      	</select>
      </td>
    </tr>
	</table>
</td><!--Col2-->
</tr>
</table>
<?php
TABS_closeTab();
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	/*TABS_disableTab(2);
	TABS_disableTab(3);*/
}
?>