var anixMenu =
[
	['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Nouvelles"); ?>", './list_news.php?action=edit', null, null],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Catégories"); ?>", null,null,null,
        ['icon', "<?php echo _("Ajouter"); ?>", './list_categories.php?action=add', null, null],
        ['icon', "<?php echo _("Modifier"); ?>", './list_categories.php?action=edit', null,null]
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Recherche");?>", './search.php', null, null]
];