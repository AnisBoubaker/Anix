<?php
function getCatTable($result){
	$faqCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $faqCat[$lastInserted]["last"]=false;
		$faqCat[$row->id]=array();
		$faqCat[$row->id]["id"]=$row->id;
		$faqCat[$row->id]["subcats"]=array();
		if(isset($row->deletable)) $faqCat[$row->id]["deletable"]=$row->deletable;
		if(isset($row->contain_items)) $faqCat[$row->id]["contain_items"]=$row->contain_items;
		$faqCat[$row->id]["ordering"]=$row->ordering;
		$faqCat[$row->id]["name"]=$row->name;
		$faqCat[$row->id]["description"]=$row->description;
		$faqCat[$row->id]["id_parent"]=$row->id_parent;
		$faqCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $faqCat[$row->id]["first"]=true;
		else $faqCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $faqCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$faqCat[$row->id]["nbActiveFaq"]=0;
		$faqCat[$row->id]["nbInactiveFaq"]=0;
		$faqCat[$row->id]["nbTotalFaq"]=0;
	}
	return $faqCat;
}

function getOtherCatTable($result,$prohibited){
	$faqCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if($row->id!=$prohibited && ($row->id_parent==0 || isset($faqCat[$row->id_parent]))){
			if(!$first && $lastParent==$row->id_parent) $faqCat[$lastInserted]["last"]=false;
			$faqCat[$row->id]=array();
			$faqCat[$row->id]["id"]=$row->id;
			$faqCat[$row->id]["subcats"]=array();
			$faqCat[$row->id]["ordering"]=$row->ordering;
			$faqCat[$row->id]["name"]=$row->name;
			$faqCat[$row->id]["description"]=$row->description;
			$faqCat[$row->id]["id_parent"]=$row->id_parent;
			$faqCat[$row->id]["last"]=true;
			if($first || $lastParent!=$row->id_parent) $faqCat[$row->id]["first"]=true;
			else $faqCat[$row->id]["first"]=false;
			if($row->id_parent!=0) $faqCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			$lastInserted=$row->id;
			$lastParent=$row->id_parent;
			$first=false;
		}
	}
	return $faqCat;
}
function getCategoriesList($table,$idParent,$level){
	$returnTable=array();
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnTable[$row["id"]]=array();
		$returnTable[$row["id"]]["id"]=$row["id"];
		$returnTable[$row["id"]]["name"]="";
		for($i=0;$i<$level;$i++) $returnTable[$row["id"]]["name"].="&nbsp;&nbsp;";
		$returnTable[$row["id"]]["name"].=$row["name"];
		if(count($row["subcats"])) {
			$subTable=getCategoriesList($table,$row["id"],$level+1);
			$returnTable = $returnTable + $subTable;
		}
	}
	return $returnTable;
}
function showCategories($table,$idParent,$level,$actionURL,$showButtons,$link_containters_only=false,$except=0){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>";
		if($showButtons){
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			if($row["deletable"]=="Y") $returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		$showActionURL=$actionURL!="" && (!$link_containters_only || $row["contain_items"]=="Y");
		if($except && $except==$row["id"]) $showActionURL=false;
		if($showActionURL) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($showActionURL) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategories($table,$row["id"],$level+1,$actionURL,$showButtons,$link_containters_only,$except);
	}
	return $returnStr;
}

function showCategoriesExcept($table,$idParent,$level,$actionURL,$showButtons,$idProhibited){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>";
		if($showButtons){
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la catégorie")."\"></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la catégorie")."\"></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la catégorie")."\"></a>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $row["id"]!=$idProhibited) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="" && $row["id"]!=$idProhibited) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showCategoriesExcept($table,$row["id"],$level+1,$actionURL,$showButtons,$idProhibited);
	}
	return $returnStr;
}

function showFaq($table,$idParent,$showCategory,$level,$actionURL,$showButtons,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_info_faq;
	global $TBL_faq_faq;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		//Display the Add item button
			$returnStr.="<td style='width:122px;background:#e7eff2;text-align:right;'>";
			if($row["contain_items"]=="Y") $returnStr.="<a href='./mod_faq.php?action=add&idCat=".$row["id"]."'><img src='../images/add.gif' alt=\""._("Ajouter une question à cette catégorie.")."\" /></a>";
			$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie courante")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$maxOrder = getMaxFaqOrder($showCategory,$link)-1;
			$request = request("SELECT $TBL_faq_faq.id,$TBL_faq_faq.ordering,$TBL_faq_faq.active,$TBL_faq_info_faq.question FROM $TBL_faq_faq,$TBL_faq_info_faq,$TBL_gen_languages WHERE $TBL_faq_faq.id_category='$showCategory' and $TBL_faq_faq.id=$TBL_faq_info_faq.id_faq and $TBL_faq_info_faq.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_faq.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucune question dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($faq=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>";
				if($showButtons){
					$returnStr.="<a href='./mod_faq.php?action=edit&idFaq=".$faq->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la question")."\"></a>";
					$returnStr.="<a href='./copy_faq.php?idFaq=".$faq->id."'><img src='../images/copy.gif' border='0' alt=\""._("Copier la question")."\"></a>";
					$returnStr.="<a href='./move_faq.php?idFaq=".$faq->id."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer la question")."\"></a>";
					$returnStr.="<a href='./del_faq.php?idFaq=".$faq->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la question")."\"></a>";
					if($faq->ordering!=1) $returnStr.="<a href='./list_faq.php?action=moveup&idCat=$showCategory&idFaq=".$faq->id."'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
					if($faq->ordering<$maxOrder) $returnStr.="<a href='./list_faq.php?action=movedown&idCat=$showCategory&idFaq=".$faq->id."'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				}
				$returnStr.="</td>";
				$returnStr.="<td>";
				//Get the activation status of the news
				if($faq->active=="Y") $active=_("Active");
				elseif($faq->active=="N") $active=_("Désactivée");
				//Get the substring to show from short_desc of the news (100 Cars totally)
				$totalCars=strlen($active)+3; //+3 for the dash.
				$maxLength = 110;
				if(strlen($faq->question)+$totalCars>$maxLength){
					$showDesc=substr($faq->question, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$faq->question;
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$returnStr.=$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showFaq($table,$row["id"],$showCategory,$level+1,$actionURL,$showButtons,$link);
	}
	return $returnStr;
}

function getParentsPath($idFirstParent,$idLanguage,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_info_faq;
	global $TBL_faq_faq;
	global $TBL_gen_languages;
	$category=request("select $TBL_faq_categories.id,$TBL_faq_info_categories.name,$TBL_faq_categories.id_parent from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_categories.id='$idFirstParent' and $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id", $link);
	$row = mysql_fetch_object($category);
	$retString = $row->name;
	while($row->id_parent!=0){
		$category=request("select $TBL_faq_categories.id,$TBL_faq_info_categories.name,$TBL_faq_categories.id_parent from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_categories.id='".$row->id_parent."' and $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id", $link);
		$row = mysql_fetch_object($category);
		$retString=$row->name." > ".$retString;
	}
	return $retString;
}

function getMaxCategoryOrder($idParent,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_info_faq;
	global $TBL_faq_faq;

	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_faq_categories where id_parent=$idParent GROUP BY id_parent", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function getMaxFaqOrder($idCat,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_info_faq;
	global $TBL_faq_faq;
	$request=request("SELECT id FROM $TBL_faq_categories where id='$idCat'", $link);
	if(!mysql_num_rows($request)) return false;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_faq_faq where id_category='$idCat' GROUP BY id_category", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function deleteCategory($idCat,$table,$thread,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_faq;
	global $TBL_faq_info_faq;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	foreach($table[$idCat]["subcats"] as $subcategory){
		$tmp = deleteCategory($subcategory,$table,$thread,$link);
		$return["errors"]+=$tmp["errors"];
		$return["errMessage"].=$tmp["errMessage"];
		$return["message"].=$tmp["message"];
	}
	if(!$return["errors"]){
		request("DELETE $TBL_faq_faq,$TBL_faq_info_faq
               FROM $TBL_faq_faq,$TBL_faq_info_faq
               WHERE $TBL_faq_faq.id_category='$idCat'
               AND $TBL_faq_info_faq.id_faq=$TBL_faq_faq.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la suppression des questions reliées à la catégorie.")."<br />";
		}
	}
	if(!$return["errors"]){
		request("DELETE $TBL_faq_categories,$TBL_faq_info_categories
               FROM $TBL_faq_categories,$TBL_faq_info_categories
               WHERE $TBL_faq_categories.id='$idCat'
               AND $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la suppression de la catégorie.")."<br />";
		}
	}
	//re-order the category
	if(!$return["errors"] && $idCat==$thread){
		request("UPDATE $TBL_faq_categories set ordering=ordering-1 where id_parent='".$table[$idCat]["id_parent"]."' and ordering > ".$table[$idCat]["ordering"],$link);
		if(mysql_errno()){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la mise à jour de l'ordre des catégories.")."<br />";
		}
	}
	return $return;
}

function copyCategory($idCat,$copyTo,$table,$thread,$link){
	global $TBL_faq_categories;
	global $TBL_faq_info_categories;
	global $TBL_faq_faq;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	//Check if the category exists
	$request = request("SELECT id,id_menu FROM `$TBL_faq_categories` WHERE `id`='$idCat'",$link);
	if(!mysql_num_rows($request)){
		$return["errors"]++;
		$return["errMessage"]=_("La catégorie spécifiée n'existe pas.");
		return $return;
	} else {
		$originalCat = mysql_fetch_object($request);
	}
	$ordering=getMaxCategoryOrder($copyTo,$link );
	if(!$return["errors"]){
		request("INSERT INTO $TBL_faq_categories (`ordering`, `id_parent`,`id_menu`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$ordering','$copyTo','".$originalCat->id_menu."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie de la catégorie.")."<br />";
		} else {
			$newCatId=mysql_insert_id($link);
		}
	}
	//Copy the category information
	if(!$return["errors"]){
		request("INSERT INTO $TBL_faq_info_categories (`id_faq_cat`,`id_language`,`name`,`description`,`alias_name`,`keywords`,`htmltitle`,`htmldescription`) SELECT '$newCatId',id_language,name,description,alias_name,keywords,htmltitle,htmldescription FROM $TBL_faq_info_categories WHERE id_faq_cat='$idCat'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des informations de la catégorie.")."<br />";
		}
	}
	//Copy the faq of the category
	if(!$return["errors"]){
		$faqList=request("SELECT * from $TBL_faq_faq where id_category=$idCat",$link);
		while($faq=mysql_fetch_object($faqList)){
			$tmp=copyFaq($faq->id,$newCatId,0,$link);
			$return["errors"]+=$tmp["errors"];
			$return["errMessage"].=$tmp["errMessage"];
			$return["message"].=$tmp["message"];
		}
	}
	if(!$return["errors"]){
		//Recursive copy of the subcategories
		foreach($table[$idCat]["subcats"] as $subcategory){
			$tmp = copyCategory($subcategory,$newCatId,$table,$thread,$link);
			$return["errors"]+=$tmp["errors"];
			$return["errMessage"].=$tmp["errMessage"];
			$return["message"].=$tmp["message"];
		}
	}
	return $return;
}

//Copy faq to specified category
// if conserve the same ordering, put 0
function copyFaq($idFaq,$copyTo,$ordering,$link){
	global $TBL_faq_faq;
	global $TBL_faq_info_faq;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$request = request("SELECT * from $TBL_faq_faq where id='$idFaq'",$link);
	if(mysql_num_rows($request)){
		$faq=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"]=_("La question n'existe pas.")."<br />";
	}
	if(!$return["errors"]){
		if(!$ordering) $ordering=$faq->ordering;
		request("INSERT INTO `$TBL_faq_faq` (`id_category`,`active`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$copyTo','".$faq->active."','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie de la question")." $idFaq.<br>";
		} else {
			$idNewFaq=mysql_insert_id($link);
		}
	}
	if(!$return["errors"]){
		request("INSERT INTO `$TBL_faq_info_faq` (`id_faq`,`id_language`,`question`,`response`,`alias_name`,`keywords`,`htmltitle`,`htmldescription`) SELECT '$idNewFaq',id_language,question,response,alias_name,keywords,htmltitle,htmldescription FROM $TBL_faq_info_faq WHERE id_faq='$idFaq'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des informations de la question")." $idFaq.<br>";
		}
	}
	return $return;
}

function getPrdCatTable($result){
	$catalogueCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
		$catalogueCat[$row->id]=array();
		$catalogueCat[$row->id]["id"]=$row->id;
		$catalogueCat[$row->id]["subcats"]=array();
		$catalogueCat[$row->id]["ordering"]=$row->ordering;
		$catalogueCat[$row->id]["name"]=$row->name;
		$catalogueCat[$row->id]["description"]=$row->description;
		$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
		$catalogueCat[$row->id]["contain_products"]=$row->contain_products;
		$catalogueCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
		else $catalogueCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$catalogueCat[$row->id]["nbActiveProducts"]=0;
		$catalogueCat[$row->id]["nbInactiveProducts"]=0;
		$catalogueCat[$row->id]["nbTotalProducts"]=0;
	}
	return $catalogueCat;
}

function showPrdCategories($table,$idParent,$level,$actionURL){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($actionURL!="") $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		$returnStr.=$row["name"];
		if($actionURL!="") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showPrdCategories($table,$row["id"],$level+1,$actionURL);
	}
	return $returnStr;
}

function showProducts($table,$idParent,$showCategory,$level,$actionURL,$reloadURL,$link){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<a name='".$row["id"]."'>";
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie courante")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ordering,$TBL_catalogue_products.active,$TBL_catalogue_products.ref_store,$TBL_catalogue_info_products.name FROM $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages WHERE $TBL_catalogue_products.id_category='$showCategory' and $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun produit dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($products=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				//Get the activation status of the faq
				if($products->active=="Y") $active=_("Active");
				elseif($products->active=="N") $active=_("Désactivée");
				//Get the substring to show from short_desc of the news (100 Cars totally)
				$totalCars=strlen($active)+3; //+3 for the dash.
				if($products->ref_store!="") $totalCars+=strlen($products->ref_store)+3; //+2 for parenthesis
				$maxLength = 110;
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				if(strlen($products->name)+$totalCars>$maxLength){
					$showDesc=substr($products->name, 0, $maxLength-$totalCars-3);
					$showDesc.="...";
				} else $showDesc=$products->name;
				if($products->ref_store!="") $returnStr.="(".$products->ref_store.") ";
				$returnStr.=$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.=" <a href='$actionURL".$products->id."'>+ "._("Ajouter")."</a>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showProducts($table,$row["id"],$showCategory,$level+1,$actionURL,$reloadURL,$link);
	}
	return $returnStr;
}

function getMenusList($idCat,$link,$idLanguage,$idParent=0,$level=0){
	global $TBL_content_menuitems,$TBL_content_info_menuitems;
	//echo str_pad("getMenusList($idCat,link,language,$idParent,$level)\n",$level,"  ",STR_PAD_LEFT);
	$return = array();
	//get submenus
	$request = request(
	"SELECT `$TBL_content_info_menuitems`.`id_menuitem`,`$TBL_content_info_menuitems`.`title`,`$TBL_content_menuitems`.`type`
  		 FROM `$TBL_content_info_menuitems`,`$TBL_content_menuitems`
  		 WHERE `$TBL_content_menuitems`.`id_category`='$idCat'
  		 AND `$TBL_content_menuitems`.`id_parent`='$idParent'
  		 AND `$TBL_content_info_menuitems`.`id_menuitem`=`$TBL_content_menuitems`.`id`
  		 AND `id_language`='$idLanguage'
  		 ORDER BY `id_parent`,`ordering`"
	,$link
	);
	//echo "nb submenus: ".mysql_num_rows($request)."\n";
	$counter = 0;
	while($submenu = mysql_fetch_object($request)){
		$return[$counter]=array();
		$return[$counter]["id"]=$submenu->id_menuitem;
		$return[$counter]["type"]=$submenu->type;
		$return[$counter]["title"]=$submenu->title;
		if($submenu->type=="submenu") $return[$counter]["title"]="|_".$return[$counter]["title"];
		$return[$counter]["title"]=str_repeat("&nbsp;&nbsp;",$level+1).$return[$counter]["title"];
		if($submenu->type=="submenu") {
			$tmp = getMenusList($idCat,$link,$idLanguage,$submenu->id_menuitem,$level+1);
			$return=array_merge($return,$tmp);
			$counter+=count($tmp);
		}
		$counter++;
	}
	return $return;
}
?>
