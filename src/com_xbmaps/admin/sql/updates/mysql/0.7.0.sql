#adding summary to #__xbmaps_maps and #__xbmaps_tracks
#removing map width and height fields
ALTER TABLE `#__xbmaps_tracks` 
ADD `summary` VARCHAR(190) NOT NULL DEFAULT '' COMMENT 'added v0.7.0.' AFTER `description`;
ALTER TABLE `#__xbmaps_maps`
ADD `summary` VARCHAR(190) NOT NULL DEFAULT '' COMMENT 'added v0.7.0' AFTER `description`,
DROP `map_width`, DROP `width_unit`, DROP `map_height`, DROP `height_unit`;
