var anixMenu =
[
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Général"); ?>", './index.php', null, null],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Utilisateurs"); ?>", null,null,null,
        ['icon', "<?php echo _("Groupes"); ?>", './list_groups.php', null, null],
        ['icon', "<?php echo _("Droits"); ?>", './group_rights.php?action=edit', null,null],
        ['icon', "<?php echo _("Comptes"); ?>", './list_users.php?action=edit', null,null],
    ],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Configurations"); ?>", './configurations.php', null, null],
    _cmSplit,
    ['<img src="../js/ThemeOffice/copy.gif" style="float:left;margin-right:3px;" />', "<?php echo _("Maintenance"); ?>", null, null, null,
    	['icon', "<?php echo _("Sauvegarder"); ?>", './list_categories.php?action=addproduct', null, null],
        ['icon', "<?php echo _("Restaurer"); ?>", './list_products.php?action=edit', null,null],
        ['icon', "<?php echo _("Optimiser"); ?>", './list_categories.php?action=updateproducts', null,null]
    ]
];