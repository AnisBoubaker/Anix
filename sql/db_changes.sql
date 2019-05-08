ALTER TABLE `articles_categories` ADD `image_file_small` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'imgcatarticle_small_no_image.jpg' AFTER `contain_items` ,
ADD `image_file_large` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'imgcatarticle_large_no_image.jpg' AFTER `image_file_small` ;

ALTER TABLE `lists_categories` ADD `items_ordering` ENUM( 'manual', 'alpha' ) NOT NULL DEFAULT 'manual' AFTER `itemimg_large_height` ,
ADD `subcats_ordering` ENUM( 'manual', 'alpha' ) NOT NULL DEFAULT 'manual' AFTER `items_ordering` ;

