<?
require_once("../config.php");
require_once("./module_config.php");
include("./moderate_reviews.xcommon.php");

$link = dbConnect();

$title = _("Validation des avis des clients");

include("../html_header.php");
setTitleBar($title);

$reviews = new CatalogueReviewsList();
$reviews->loadNotModerated();

if($reviews->getNbReviews()){
?>
	<h3><?php echo $reviews->getNbReviews()." "._("Avis clients à valider"); ?></h3>
	<table class='reviews' style='width:100%;background:#ffffff;' id='table_reviews'>
	<tr>
		<th></th>
		<th>Date</th>
		<th>Note</th>
		<th>Produit</th>
	</tr>
	<tbody>
	<?php
	foreach ($reviews as $review){
	?>
		<tr id='review_title_<?php echo $review->getId(); ?>'>
			<td class='title' style='width:42px;'>
				<a href='javascript:void(0);' onclick='javascript:xajax_deleteReview(<?php echo $review->getId();?>);'><img src='../images/del.gif' /></a>
				<a href='javascript:void(0);' onclick='javascript:xajax_acceptReview(<?php echo $review->getId();?>);'><img src='../images/icon_validate.jpg' /></a>
			</td>
			<td class='title' style='width:120px;'><?php echo $review->getReviewDate();?></td>
			<td class='title' style='width:80px;text-align:center;'><?php echo $review->getScore();?></td>
			<td class='title'>
				<?php
				//get the product information
				$request=request("SELECT $TBL_catalogue_info_products.name,$TBL_catalogue_products.ref_store
								   FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages)
								   WHERE $TBL_catalogue_products.id='".$review->getIdProduct()."'
								   AND $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
								   AND $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id
								   AND $TBL_gen_languages.id='$used_language_id'",$link);
				$product=mysql_fetch_object($request);
				echo unhtmlentities($product->name);
				?>
			</td>
		</tr>
		<tr id='review_details_<?php echo $review->getId(); ?>'>
			<td></td>
			<td colspan="3">
			<?php
			echo "<i><b>"._("Par").": ";
			if($review->getIdCustomer()) echo "<a href='../mod_ecommerce/view_client.php?idClient=".$review->getIdCustomer()."'>";
			echo $review->getCustomerName()."</b></i>";
			if($review->getIdCustomer()) echo "</a>";
			echo " (<a href='mailto:'>".$review->getCustomerEmail()."</a>)";
			echo "<br />";
			echo $review->getReview();
			?>
			</td>
		</tr>
	<?
	}//foreach
	?>
	</tbody>
	</table>
<?php
} else {// if nbReviews
?>
<p style='text-align:center;'><i><b><?php echo _("Aucun avis à valider pour l'instant"); ?></b></i></p>
<?php
}//else
?>
<script type='text/javascript'>
function removeFromTable(id){
	document.getElementById('review_title_'+id).style.display='none';
	document.getElementById('review_details_'+id).style.display='none';
}
</script>
<?php
include ("../html_footer.php");
mysql_close($link);
?>

