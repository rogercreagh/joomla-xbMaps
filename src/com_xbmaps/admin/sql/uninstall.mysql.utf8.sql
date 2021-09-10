# sql uninstall file for component xbMaps 1st July 2021
# NB tags themselves are not deleted, categories and tag content map entries are deleted

DROP TABLE IF EXISTS
  `#__xbmaps_maps`,
  `#__xbmaps_tracks`,
  `#__xbmaps_maptracks`,
  `#__xbmaps_markers`,
  `#__xbmaps_mapmarkers`;

DELETE FROM `#__categories` WHERE `#__categories`.`extension` = 'com_xbmaps';
DELETE FROM `#__contentitem_tag_map` WHERE type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category');
DELETE FROM `#__content_types` WHERE type_alias in ('com_xbmaps.map','com_xbmaps.track','com_xbmaps.marker','com_xbmaps.category');

