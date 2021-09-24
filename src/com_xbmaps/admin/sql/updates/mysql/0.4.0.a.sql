#adding 2 new fields to #__xbmaps_tracks
ALTER TABLE `#__xbmaps_tracks` 
ADD `activity` VARCHAR(190) NOT NULL DEFAULT '' COMMENT 'added v0.4.0.a' AFTER `rec_date`,
ADD `rec_device` VARCHAR(190) NOT NULL DEFAULT '' COMMENT 'added v0.4.0.a' AFTER `rec_date`;
