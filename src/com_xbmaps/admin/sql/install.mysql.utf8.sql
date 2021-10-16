# sql installation file for component xbMaps v0.8.0.a 15th October 2021
# NB no data is installed with this file, default categories are created by the installation script

CREATE TABLE IF NOT EXISTS `#__xbmaps_maps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `description` text,
  `summary` varchar(190) NOT NULL DEFAULT '',
  
  `centre_latitude` varchar(20) NOT NULL DEFAULT '',
  `centre_longitude` varchar(20) NOT NULL DEFAULT '',
#  `centre` point NOT NULL, 
  `default_zoom` tinyint unsigned NOT NULL DEFAULT '10',
  `map_type` varchar(20) NOT NULL DEFAULT 'osm',
  `fit_bounds`  tinyint  unsigned NOT NULL DEFAULT '0',  
  
  `catid` int NOT NULL  DEFAULT '0',
  `access` int NOT NULL  DEFAULT '0',
  `state` tinyint NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int NOT NULL  DEFAULT '0',
  `checked_out` int NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `metadata` mediumtext,
  `params` mediumtext,
  `ordering` int NOT NULL DEFAULT '0',
  `note` text,
  `asset_id` int unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE UNIQUE INDEX `mapaliasindex` ON `#__xbmaps_maps` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbmaps_tracks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `description` text,
  `summary` varchar(190) NOT NULL DEFAULT '',
 
  `gpx_filename` varchar(190) NOT NULL DEFAULT '',
  `rec_date` datetime,
  `rec_device` varchar(190) NOT NULL DEFAULT '',
  `activity` varchar(190) NOT NULL DEFAULT '',
  `track_colour` varchar(10),
   
  `catid` int NOT NULL  DEFAULT '0',
  `access` int NOT NULL  DEFAULT '0',
  `state` tinyint NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int NOT NULL  DEFAULT '0',
  `checked_out` int NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `params` mediumtext,
  `ordering` int NOT NULL DEFAULT '0',
  `note` text,
  `asset_id` int unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE UNIQUE INDEX `trackaliasindex` ON `#__xbmaps_tracks` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbmaps_maptracks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `map_id` int NOT NULL,
  `track_id` int NOT NULL,
  `track_colour` varchar(10) NOT NULL DEFAULT '',
  `listorder` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__xbmaps_markers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(190) NOT NULL DEFAULT '',
  `alias` varchar(190) NOT NULL DEFAULT '',
  `summary` varchar(190) NOT NULL DEFAULT '',
  `description` text,
 
  `latitude` varchar(20) NOT NULL DEFAULT '',
  `longitude` varchar(20) NOT NULL DEFAULT '',
  `marker_type` varchar(190) NOT NULL DEFAULT '',
#  `marker_colour` varchar(10) NOT NULL DEFAULT '',
#  `icon` varchar(190) NOT NULL DEFAULT '',
#  `icon_colour` varchar(10) NOT NULL DEFAULT '#FFF',
#  `geopos` point NOT NULL,
   
  `catid` int NOT NULL  DEFAULT '0',
  `access` int NOT NULL  DEFAULT '0',
  `state` tinyint NOT NULL DEFAULT '0',
  `created` datetime,
  `created_by` int NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_by` int NOT NULL  DEFAULT '0',
  `checked_out` int NOT NULL DEFAULT '0',
  `checked_out_time` datetime,
  `metadata` mediumtext,
  `params` mediumtext,
  `ordering` int NOT NULL DEFAULT '0',
  `note` text,
  `asset_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE UNIQUE INDEX `markeraliasindex` ON `#__xbmaps_markers` (`alias`);

CREATE TABLE IF NOT EXISTS `#__xbmaps_mapmarkers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `map_id` int NOT NULL,
  `marker_id` int NOT NULL,
  `show_popup` tinyint NULL DEFAULT NULL,
  `listorder` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

###Create content types ---------------------

INSERT INTO `#__content_types` (`type_title`, `type_alias`, `content_history_options`, `table`, `field_mappings`, `router`,`rules`) 
VALUES
('xbMaps Map', 'com_xbmaps.map', 
'{"formFile":"administrator\\/components\\/com_xbmaps\\/models\\/forms\\/map.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbmaps_maps","key":"id","type":"Map","prefix":"XbmapsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "title",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "description",
    "core_catid": "catid"
  }}',
'XbmapsHelperRoute::getMapRoute',''),

('xbMaps Track', 'com_xbmaps.track', 
'{"formFile":"administrator\\/components\\/com_xbmaps\\/models\\/forms\\/track.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbmaps_tracks","key":"id","type":"Track","prefix":"XbmapsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "title",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "description",
    "core_catid": "catid"
  }}',
'XbmapsHelperRoute::getTrackRoute',''),

('xbMaps Marker', 'com_xbmaps.marker', 
'{"formFile":"administrator\\/components\\/com_xbmaps\\/models\\/forms\\/marker.xml", 
    "hideFields":["checked_out","checked_out_time"], 
    "ignoreChanges":["checked_out", "checked_out_time"],
    "convertToInt":[], 
    "displayLookup":[
        {"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}
    ]
 }',
'{"special":{"dbtable":"#__xbmaps_markers","key":"id","type":"Marker","prefix":"XbmapsTable","config":"array()"},
    "common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
    "core_content_item_id": "id",
    "core_title": "title",
    "core_state": "state",
    "core_alias": "alias",
    "core_created_time": "created",
    "core_body": "description",
    "core_catid": "catid"
  }}',
'XbmapsHelperRoute::getMarkerRoute',''),

('xbMaps Category', 'com_xbmaps.category',
'{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", 
"hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], 
"ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],
"convertToInt":["publish_up", "publish_down"], 
"displayLookup":[
{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},
{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},
{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}',
'{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},
"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}',
'{"common": {
	"core_content_item_id":"id",
	"core_title":"title",
	"core_state":"published",
	"core_alias":"alias",
	"core_created_time":"created_time",
	"core_modified_time":"modified_time",
	"core_body":"description", 
	"core_hits":"hits",
	"core_publish_up":"null",
	"core_publish_down":"null",
	"core_access":"access", 
	"core_params":"params", 
	"core_featured":"null", 
	"core_metadata":"metadata", 
	"core_language":"language", 
	"core_images":"null", 
	"core_urls":"null", 
	"core_version":"version",
	"core_ordering":"null", 
	"core_metakey":"metakey", 
	"core_metadesc":"metadesc", 
	"core_catid":"parent_id", 
	"core_xreference":"null", 
	"asset_id":"asset_id"}, 
  "special":{
    "parent_id":"parent_id",
	"lft":"lft",
	"rgt":"rgt",
	"level":"level",
	"path":"path",
	"extension":"extension",
	"note":"note"}}',
'XbmapsHelperRoute::getCategoryRoute','');


