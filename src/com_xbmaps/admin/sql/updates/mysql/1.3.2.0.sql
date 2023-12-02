#add map start & end date and track start and end date as datetime
ALTER TABLE `#__xbmaps_maps` ADD `map_start_date` datetime AFTER `fitbounds`;
ALTER TABLE `#__xbmaps_maps` ADD `map_end_date` datetime AFTER `fitbounds`;
ALTER TABLE `#__xbmaps_tracks` ADD `track_start_date` datetime AFTER `track_colour`;
ALTER TABLE `#__xbmaps_tracks` ADD `track_end_date` datetime AFTER `track_colour`;
