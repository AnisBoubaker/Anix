<?php
function getCatTable($result){
	$articleCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $articleCat[$lastInserted]["last"]=false;
		$articleCat[$row->id]=array();
		$articleCat[$row->id]["id"]=$row->id;
		$articleCat[$row->id]["subcats"]=array();
		if(isset($row->deletable)) $articleCat[$row->id]["deletable"]=$row->deletable;
		if(isset($row->contain_items)) $articleCat[$row->id]["contain_items"]=$row->contain_items;
		$articleCat[$row->id]["ordering"]=$row->ordering;
		$articleCat[$row->id]["name"]=$row->name;
		$articleCat[$row->id]["description"]=$row->description;
		$articleCat[$row->id]["id_parent"]=$row->id_parent;
		$articleCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $articleCat[$row->id]["first"]=true;
		else $articleCat[$row->id]["first"]=false;
		if($row->id_parent!=0) $articleCat[$row->id_parent]["subcats"][$row->id]=$row->id;
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$articleCat[$row->id]["nbActiveArticle"]=0;
		$articleCat[$row->id]["nbInactiveArticle"]=0;
		$articleCat[$row->id]["nbExpiredArticle"]=0;
		$articleCat[$row->id]["nbAwaitingArticle"]=0;
		$articleCat[$row->id]["nbArchivedArticle"]=0;
		$articleCat[$row->id]["nbTotalArticle"]=0;
	}
	return $articleCat;
}
function getStats($table,$link){
	global $TBL_articles_categories;
	global $TBL_articles_article;
	$currentDate=date("Y-m-d");
	//Get how many active article we have in each Cat
	$request = request("select $TBL_articles_categories.id,COUNT(*) as nbArticle from  $TBL_articles_categories,$TBL_articles_article where $TBL_articles_article.id_category=$TBL_articles_categories.id and ($TBL_articles_article.active='Y' or ($TBL_articles_article.active='DATE' and $TBL_articles_article.from_date<=$currentDate and $TBL_articles_article.to_date>=$currentDate)) group by $TBL_articles_article.id_category", $link);
	//echo "select $TBL_articles_categories.id,COUNT(*) as nbArticle from  $TBL_articles_categories,$TBL_articles_article where $TBL_articles_article.id_category=$TBL_articles_categories.id and ($TBL_articles_article.active='Y' or ($TBL_articles_article='DATE' and $TBL_articles_article.from_date<=$currentDate and $TBL_articles_article.to_date>=$currentDate)) group by $TBL_articles_article.id_category";
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbActiveArticle"]=$cat->nbArticle;
	}
	//Get how many inactive article we have in each Cat
	$request = request("select $TBL_articles_categories.id,COUNT(*) as nbArticle from  $TBL_articles_categories,$TBL_articles_article where $TBL_articles_article.id_category=$TBL_articles_categories.id and $TBL_articles_article.active='N' group by $TBL_articles_article.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbInactiveArticle"]=$cat->nbArticle;
	}
	//Get how many awaiting article we have in each Cat
	$request = request("select $TBL_articles_categories.id,COUNT(*) as nbArticle from  $TBL_articles_categories,$TBL_articles_article where $TBL_articles_article.id_category=$TBL_articles_categories.id and $TBL_articles_article.active='DATE' and $TBL_articles_article.from_date>'$currentDate' group by $TBL_articles_article.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbAwaitingArticle"]=$cat->nbArticle;
	}
	//Get how many expired article we have in each Cat
	$request = request("select $TBL_articles_categories.id,COUNT(*) as nbArticle from  $TBL_articles_categories,$TBL_articles_article where $TBL_articles_article.id_category=$TBL_articles_categories.id and $TBL_articles_article.active='DATE' and $TBL_articles_article.to_date<'$currentDate' group by $TBL_articles_article.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbExpiredArticle"]=$cat->nbArticle;
	}
	//Get how many archived article we have in each Cat
	$request = request("select $TBL_articles_categories.id,COUNT(*) as nbArticle from  $TBL_articles_categories,$TBL_articles_article where $TBL_articles_article.id_category=$TBL_articles_categories.id and $TBL_articles_article.active='ARCHIVE' group by $TBL_articles_article.id_category", $link);
	while($cat=mysql_fetch_object($request)){
		$table[$cat->id]["nbArchivedArticle"]=$cat->nbArticle;
	}
	//Compute general values
	foreach($table as $category){
		$table[$category["id"]]["nbTotalArticle"]=$category["nbActiveArticle"]+$category["nbInactiveArticle"]+$category["nbAwaitingArticle"]+$category["nbExpiredArticle"]+$category["nbArchivedArticle"];
	}
	return $table;
}

function getOtherCatTable($result,$prohibited){
	$articleCat=array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if($row->id!=$prohibited && ($row->id_parent==0 || isset($articleCat[$row->id_parent]))){
			if(!$first && $lastParent==$row->id_parent) $articleCat[$lastInserted]["last"]=false;
			$articleCat[$row->id]=array();
			$articleCat[$row->id]["id"]=$row->id;
			$articleCat[$row->id]["subcats"]=array();
			$articleCat[$row->id]["ordering"]=$row->ordering;
			$articleCat[$row->id]["name"]=$row->name;
			$articleCat[$row->id]["description"]=$row->description;
			$articleCat[$row->id]["id_parent"]=$row->id_parent;
			$articleCat[$row->id]["last"]=true;
			if($first || $lastParent!=$row->id_parent) $articleCat[$row->id]["first"]=true;
			else $articleCat[$row->id]["first"]=false;
			if($row->id_parent!=0) $articleCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			$lastInserted=$row->id;
			$lastParent=$row->id_parent;
			$first=false;
		}
	}
	return $articleCat;
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
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt='"._("Modifier la catégorie")."'></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt='"._("Copier la catégorie")."'></a>";
			if($row["deletable"]=="Y") $returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt='"._("Supprimer la catégorie")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt='"._("Monter")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt='"._("Descendre")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Catégorie")."'>";
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
function showStats($table,$idParent,$level,$unitsPerPixel){
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.=$row["name"]."(".$row["nbTotalArticle"].")";
		$returnStr.="</td>";
		$returnStr.="<td width='130' align='left' valign='middle'>";
		if($row["nbTotalArticle"]){
			$returnStr.="<img src='../images/bar_blue.jpg' height='5' width='".$row["nbAwaitingArticle"]*$unitsPerPixel."' alt='".$row["nbAwaitingArticle"]." "._("Articles en attente")."(".$row["nbAwaitingArticle"]*100/$row["nbTotalArticle"]."%)'>";
			$returnStr.="<img src='../images/bar_green.jpg' height='5' width='".$row["nbActiveArticle"]*$unitsPerPixel."' alt='".$row["nbActiveArticle"]." "._("Articles actifs")."(".$row["nbActiveArticle"]*100/$row["nbTotalArticle"]."%)'>";
			$returnStr.="<img src='../images/bar_orange.jpg' height='5' width='".$row["nbExpiredArticle"]*$unitsPerPixel."' alt='".$row["nbExpiredArticle"]." "._("Articles expirés")."(".$row["nbExpiredArticle"]*100/$row["nbTotalArticle"]."%)'>";
			$returnStr.="<img src='../images/bar_red.jpg' height='5' width='".$row["nbInactiveArticle"]*$unitsPerPixel."' alt='".$row["nbInactiveArticle"]." "._("Articles inactifs")."(".$row["nbInactiveArticle"]*100/$row["nbTotalArticle"]."%)'>";
			$returnStr.="<img src='../images/bar_blue.jpg' height='5' width='".$row["nbArchivedArticle"]*$unitsPerPixel."' alt='".$row["nbArchivedArticle"]." "._("Articles archivés")."(".$row["nbArchivedArticle"]*100/$row["nbTotalArticle"]."%)'>";
		}
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showStats($table,$row["id"],$level+1,$unitsPerPixel);
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
			$returnStr.="<a href='./mod_category.php?action=edit&idCat=".$row["id"]."'><img src='../images/edit.gif' border='0' alt='"._("Modifier la catégorie")."'></a>";
			$returnStr.="<a href='./copy_category.php?idCat=".$row["id"]."'><img src='../images/copy.gif' border='0' alt='"._("Copier la catégorie")."'></a>";
			$returnStr.="<a href='./del_category.php?idCat=".$row["id"]."'><img src='../images/del.gif' border='0' alt='"._("Supprimer la catégorie")."'></a>";
			if(!$row["first"]) $returnStr.="<a href='./list_categories.php?action=moveup&idCat=".$row["id"]."'><img src='../images/order_up.gif' border='0' alt='"._("Monter")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if(!$row["last"]) $returnStr.="<a href='./list_categories.php?action=movedown&idCat=".$row["id"]."'><img src='../images/order_down.gif' border='0' alt='"._("Descendre")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		$returnStr.="<img src='../images/folder.gif' border='0' alt='Catégorie'>";
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

function showArticle($table,$idParent,$showCategory,$level,$actionURL,$showButtons,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_info_article;
	global $TBL_articles_article;
	global $TBL_gen_languages,$used_language_id;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
			//Display the Add item button
			$returnStr.="<td style='width:122px;background:#e7eff2;text-align:right;'>";
			if($row["contain_items"]=="Y") $returnStr.="<a href='./mod_article.php?action=add&idCat=".$row["id"]."'><img src='../images/add.gif' alt=\""._("Ajouter un article à cette catégorie.")."\" /></a>";
			$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a name='".$row["id"]."'>";
		for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt='"._("Catégorie courante")."'>";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Catégorie")."'>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="<a href='".$actionURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($actionURL!="" && $showCategory!=$row["id"]) $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		if($showCategory==$row["id"]){
			$maxOrder = getMaxArticleOrder($showCategory,$link)-1;
			$request = request("SELECT $TBL_articles_article.id,$TBL_articles_article.ordering,$TBL_articles_article.active,$TBL_articles_article.from_date,$TBL_articles_article.to_date,$TBL_articles_info_article.title,$TBL_articles_info_article.short_desc FROM $TBL_articles_article,$TBL_articles_info_article,$TBL_gen_languages WHERE $TBL_articles_article.id_category='$showCategory' and $TBL_articles_article.id=$TBL_articles_info_article.id_article and $TBL_articles_info_article.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_article.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun article dans cette catégorie")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($article=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='122' bgcolor='#e7eff2'>";
				if($showButtons){
					$returnStr.="<a href='./mod_article.php?action=edit&idArticle=".$article->id."'><img src='../images/edit.gif' border='0' alt='"._("Modifier l'article")."'></a>";
					$returnStr.="<a href='./copy_article.php?idArticle=".$article->id."'><img src='../images/copy.gif' border='0' alt='"._("Copier l'article")."'></a>";
					$returnStr.="<a href='./move_article.php?idArticle=".$article->id."'><img src='../images/move.gif' border='0' alt=\""._("Déplacer l'article")."\"></a>";
					$returnStr.="<a href='./del_article.php?idArticle=".$article->id."'><img src='../images/del.gif' border='0' alt='"._("Supprimer l'article")."'></a>";
					if($article->ordering!=1) $returnStr.="<a href='./list_articles.php?action=moveup&idCat=$showCategory&idArticle=".$article->id."'><img src='../images/order_up.gif' border='0' alt='"._("Monter")."'></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
					if($article->ordering<$maxOrder) $returnStr.="<a href='./list_articles.php?action=movedown&idCat=$showCategory&idArticle=".$article->id."'><img src='../images/order_down.gif' border='0' alt='"._("Descendre")."'></a>";
					else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
				}
				$returnStr.="</td>";
				$returnStr.="<td>";
				//Get the activation status of the article
				if($article->active=="Y") $active=_("Actif");
				elseif($article->active=="N") $active=_("Désactivé");
				elseif($article->active=="ARCHIVE") $active=_("Archivé");
				elseif($article->active=="DATE") {
					$currentDate=date("Y-m-d");
					if($currentDate<$article->from_date) $active="En attente";
					elseif($currentDate>$article->to_date) $active="Expiré";
					else $active="Actif";
				}
				//Get the substring to show from short_desc of the article (100 Cars totally)
				$maxLength = 110;
				if(strlen($article->title)>$maxLength){
					$showDesc=substr($article->title, 0, $maxLength-3);
					$showDesc.="...";
				} else $showDesc=$article->title;
				for($i=0;$i<$level;$i++) $returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
				$returnStr.=$showDesc." - <i><font color='green'><b>$active</b></font></i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
		if(count($row["subcats"])) $returnStr.=showArticle($table,$row["id"],$showCategory,$level+1,$actionURL,$showButtons,$link);
	}
	return $returnStr;
}
function getParentsPath($idFirstParent,$idLanguage,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_info_article;
	global $TBL_articles_article;
	global $TBL_gen_languages;
	$category=request("select $TBL_articles_categories.id,$TBL_articles_info_categories.name,$TBL_articles_categories.id_parent from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_categories.id='$idFirstParent' and $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id", $link);
	$row = mysql_fetch_object($category);
	$retString = $row->name;
	while($row->id_parent!=0){
		$category=request("select $TBL_articles_categories.id,$TBL_articles_info_categories.name,$TBL_articles_categories.id_parent from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_categories.id='".$row->id_parent."' and $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_gen_languages.id='$idLanguage' and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id", $link);
		$row = mysql_fetch_object($category);
		$retString=$row->name." > ".$retString;
	}
	return $retString;
}

function getMaxCategoryOrder($idParent,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_info_article;
	global $TBL_articles_article;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_articles_categories where id_parent=$idParent GROUP BY id_parent", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function getMaxArticleOrder($idCat,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_info_article;
	global $TBL_articles_article;
	$request=request("SELECT id FROM $TBL_articles_categories where id='$idCat'", $link);
	if(!mysql_num_rows($request)) return false;
	$request=request("SELECT MAX(ordering) as maximum FROM $TBL_articles_article where id_category='$idCat' GROUP BY id_category", $link);
	if(mysql_num_rows($request)) {
		$row=mysql_fetch_object($request);
		return $row->maximum+1;
	}
	else return 1;
}

function deleteCategory($idCat,$table,$thread,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_article;
	global $TBL_articles_info_article;
	global $TBL_articles_attachments;
	global $CATALOG_folder_attachments;
	global $ANIX_messages;
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
	//Delete the item attachment files from the file system (items and category)
	if(!$ANIX_messages->nbErrors){
		//retrieve and delete articles attachments from file system
		$request=request("SELECT file_name from $TBL_articles_attachments,$TBL_articles_article where $TBL_articles_article.id_category='$idCat' AND $TBL_articles_attachments.id_article=$TBL_articles_article.id",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression du fichier attaché à l'article."));
				}
			}
		}
		//retrieve and delete category attachments from file system
		$request=request("SELECT file_name from $TBL_articles_attachments where id_category='$idCat'",$link);
		while($files=mysql_fetch_object($request)) {
			if($files->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$files->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression du fichier attaché à l'article."));
				}
			}
		}
		//Delete items attachments from database
		request("DELETE $TBL_articles_attachments
               FROM $TBL_articles_article,
                    $TBL_articles_attachments
               WHERE $TBL_articles_article.id_category='$idCat'
               AND $TBL_articles_attachments.id_article=$TBL_articles_article.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression des fichiers attachés."));
		}
		//Delete category attachments from database
		request("DELETE FROM $TBL_articles_attachments
               WHERE id_category='$idCat'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des fichiers attaché."));
		}
	}
	if(!$return["errors"]){
		request("DELETE $TBL_articles_article,$TBL_articles_info_article
               FROM $TBL_articles_article,$TBL_articles_info_article
               WHERE $TBL_articles_article.id_category='$idCat'
               AND $TBL_articles_info_article.id_article = $TBL_articles_article.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la suppression des articles lies a la categorie");
		}
	}
	if(!$return["errors"]){
		request("DELETE $TBL_articles_categories,$TBL_articles_info_categories
               FROM  $TBL_articles_categories,$TBL_articles_info_categories
               WHERE $TBL_articles_categories.id='$idCat'
               AND $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la suppression de la categorie");
		}
	}
	//re-order the category
	if(!$return["errors"] && $idCat==$thread){
		request("UPDATE $TBL_articles_categories set ordering=ordering-1 where id_parent='".$table[$idCat]["id_parent"]."' and ordering > ".$table[$idCat]["ordering"],$link);
		if(mysql_errno()){
			$return["errors"]++;
			$return["errMessage"]="Une erreur s'est produite lors de la mise a jour de l'ordre des categories.";
		}
	}
	return $return;
}

function copyCategory($idCat,$copyTo,$table,$thread,$link){
	global $TBL_articles_categories;
	global $TBL_articles_info_categories;
	global $TBL_articles_article;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	//Check if the category exists
	$request = request("SELECT id,id_menu FROM `$TBL_articles_categories` WHERE `id`='$idCat'",$link);
	if(!mysql_num_rows($request)){
		$return["errors"]++;
		$return["errMessage"]=_("La catégorie spécifiée n'existe pas.");
		return $return;
	} else {
		$originalCat = mysql_fetch_object($request);
	}
	$ordering=getMaxCategoryOrder($copyTo,$link );
	if(!$return["errors"]){
		request("INSERT INTO $TBL_articles_categories (`ordering`, `id_parent`,`id_menu`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$ordering','$copyTo','$originalCat->id_menu','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie de la categorie.");
		} else {
			$newCatId=mysql_insert_id($link);
		}
	}
	//Copy the category information
	if(!$return["errors"]){
		request("INSERT INTO $TBL_articles_info_categories (`id_article_cat`,`id_language`,`name`,`description`,`htmltitle`,`htmldescription`,`keywords`,`alias_name`) SELECT '$newCatId',id_language,name,description,htmltitle,htmldescription,keywords,alias_name FROM $TBL_articles_info_categories WHERE id_article_cat='$idCat'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des informations de la categorie.");
		}
	}
	//Copy the article of the category
	if(!$return["errors"]){
		$articleList=request("SELECT * from $TBL_articles_article where id_category=$idCat",$link);
		while($article=mysql_fetch_object($articleList)){
			$tmp=copyArticle($article->id,$newCatId,0,$link);
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

//Copy article to specified category
// if conserve the same ordering, put 0
function copyArticle($idArticle,$copyTo,$ordering,$link){
	global $TBL_articles_article;
	global $TBL_articles_info_article;
	global $TBL_articles_attachments;
	global $CATALOG_folder_attachments;
	global $anix_username;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$request = request("SELECT * from $TBL_articles_article where id='$idArticle'",$link);
	if(mysql_num_rows($request)){
		$article=mysql_fetch_object($request);
	} else {
		$return["errors"]++;
		$return["errMessage"]=_("L'article n'existe pas.");
	}
	if(!$return["errors"]){
		if(!$ordering) $ordering=$article->ordering;
		request("INSERT INTO `$TBL_articles_article` (`id_category`,`active`,`from_date`,`to_date`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$copyTo','".$article->active."','".$article->from_date."','".$article->to_date."','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie de l'article")." $idArticle.<br>";
		} else {
			$idNewArticle=mysql_insert_id($link);
		}
	}
	if(!$return["errors"]){
		request("INSERT INTO `$TBL_articles_info_article` (`id_article`,`id_language`,`title`,`short_desc`,`details`,`keywords`,`htmltitle`,`htmldescription`,`alias_name`) SELECT '$idNewArticle',id_language,title,short_desc,details,keywords,htmltitle,htmldescription,alias_name FROM $TBL_articles_info_article WHERE id_article='$idArticle'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"]=_("Une erreur s'est produite lors de la copie des informations de l'article")." $idArticle.<br>";
		}
	}
	//copy the attachments
	if(!$return["errors"]){
		$request = request("SELECT * from `$TBL_articles_attachments` WHERE id_article='$idArticle'",$link);
		while($attachment=mysql_fetch_object($request)){
			request("INSERT INTO $TBL_articles_attachments (`id_article`,`id_language`,`file_name`,`title`,`description`,`ordering`) VALUES ('$idNewArticle','".$attachment->id_language."','','".$attachment->title."','".$attachment->description."','".$attachment->ordering."')",$link);
			$newAttachment = mysql_insert_id($link);
			if($attachment->file_name!=""){
				//We copy the file
				//$tmp = explode("/",$attachment->file_name);
				$fileName = $attachment->file_name;
				$tmp = explode("_",$fileName,2);
				$destFileName="article".$newAttachment."_".$tmp[1];
				if(!copy("../".$CATALOG_folder_attachments.$attachment->file_name,"../".$CATALOG_folder_attachments.$destFileName)){
					$destFileName = "";
					$return["errMessage"].=_("Une erreur s'est produite lors de la copie du fichier attaché.")."<br />";
				}
			}
			if($destFileName!=""){
				request("UPDATE $TBL_articles_attachments set `file_name`='$destFileName' WHERE id='$newAttachment'",$link);
				if(mysql_errno($link)){
					$return["errMessage"]=_("Une erreur s'est produite lors de la copie du fichier attaché.")."<br />";
				}
			}
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
		$returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Catégorie")."'>";
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
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt='"._("Catégorie courante")."'>";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Catégorie")."'>";
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
				$returnStr.="<i>"._("Aucun article dans cette categorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($products=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td>";
				//Get the activation status of the article
				if($products->active=="Y") $active=_("Actif");
				elseif($products->active=="N") $active=_("Désactivé");
				//Get the substring to show from short_desc of the article (100 Cars totally)
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
