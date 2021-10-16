#add marker summary
ALTER TABLE `#__xbmaps_markers` ADD `summary` VARCHAR(190) NOT NULL  DEFAULT '' AFTER `description`;