#add track elev_filename to include any path relative to config elev_folder
ALTER TABLE `#__xbmaps_tracks` ADD `elev_filename` varchar(190) NOT NULL DEFAULT '' AFTER `gpx_filename`;
