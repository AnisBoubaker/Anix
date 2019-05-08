var anixMenu =
[
	['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Inventaire"); ?>", null, null, null,
        ['icon', "<?php echo _("Liste des produits"); ?>", './list_products.php?action=edit', null,null],
        ['icon', "<?php echo _("Màj rapide"); ?>", './list_categories.php?action=updateproducts', null,null],
        ['icon', "<?php echo _("Valider les avis clients"); ?>", './moderate_reviews.php', null,null]
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Marques"); ?>", './list_brands.php', null, null],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Vedettes"); ?>", './list_featured.php', null, null],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Recherche");?>", './search.php', null, null]
];