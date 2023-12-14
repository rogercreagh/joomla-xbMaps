# NOT USED SINCE V1.3 to allow for keeping data on uninstall
# functionality now handled in component script file
# sql uninstall file for component xbMaps 1st July 2021
# NB tags themselves are not deleted, categories and tag content map entries are deleted

DROP TABLE IF EXISTS
  `#__xbmaps_maps`,
  `#__xbmaps_tracks`,
  `#__xbmaps_maptracks`,
  `#__xbmaps_markers`,
  `#__xbmaps_mapmarkers`;

DELETE FROM `#__categories` WHERE `#__categories`.`extension` = 'com_xbmaps';
DELETE FROM `#__ucm_history` WHERE ucm_type_id in 
	(select type_id from `#__content_types` where type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category'));
DELETE FROM `#__ucm_base` WHERE ucm_type_id in 
	(select type_id from `#__content_types` WHERE type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category'));
DELETE FROM `#__ucm_content` WHERE core_type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category');
DELETE FROM `#__contentitem_tag_map` WHERE type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category');
DELETE FROM `#__content_types` WHERE type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category');

