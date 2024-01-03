#add table for track markers. 3rd January 2024
CREATE TABLE IF NOT EXISTS `#__xbmaps_trackmarkers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `track_id` int NOT NULL,
  `marker_id` int NOT NULL,
  `listorder` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)  ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
