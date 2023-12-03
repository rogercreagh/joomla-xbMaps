#add map start & end date and track start and end date as datetime
ALTER TABLE `#__xbmaps_maps` ADD `map_start_date` datetime AFTER `fit_bounds`;
ALTER TABLE `#__xbmaps_maps` ADD `map_end_date` datetime AFTER `fit_bounds`;
