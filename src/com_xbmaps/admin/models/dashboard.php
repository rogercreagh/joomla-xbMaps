<?php
/*******
 * @package xbMaps Component
 * @version 1.5.2.0 4th January 2024
 * @filesource admin/models/dashboard.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;

class XbmapsModelDashboard extends JModelList {
	
	public function __construct() {

		parent::__construct();
	}
	
	public function getMapStates() {
		$cnts = $this->stateCnts('#__xbmaps_maps');
		return $cnts;
	}
	
	public function getCatStates() {
		return $this->stateCnts('#__categories','published','com_xbmaps');
	}
		
	public function getMarkerStates() {
		return $this->stateCnts('#__xbmaps_markers');
	}
	
	public function getTrackStates() {
		$cnts = $this->stateCnts('#__xbmaps_tracks');
		return $cnts;
	}
	
	public function getTrackCounts() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(DISTINCT map_id) AS mapswithtracks, COUNT(DISTINCT track_id) AS tracksonmaps')->from('#__xbmaps_maptracks');
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	public function getMarkerMapCounts() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(DISTINCT map_id) AS mapswithmarkers, COUNT(DISTINCT marker_id) AS markersonmaps')->from('#__xbmaps_mapmarkers');
		$db->setQuery($query);
		return $db->loadAssoc();
	}
	
	public function getMarkerTrackCounts() {
	    $db = $this->getDbo();
	    $query = $db->getQuery(true);
	    $query->select('COUNT(DISTINCT track_id) AS trackswithmarkers, COUNT(DISTINCT marker_id) AS markersontracks')->from('#__xbmaps_trackmarkers');
	    $db->setQuery($query);
	    return $db->loadAssoc();
	}
	
	public function getCats() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*')
		->select('(SELECT COUNT(*) FROM #__xbmaps_maps AS m WHERE m.catid=a.id) AS mapcnt')
		->select('(SELECT COUNT(*) FROM #__xbmaps_markers AS mk WHERE mk.catid=a.id) AS mrkcnt')
		->select('(SELECT COUNT(*) FROM #__xbmaps_tracks AS tk WHERE tk.catid=a.id) AS trkcnt')
		->from('#__categories AS a')
		->where('a.extension = '.$db->quote("com_xbmaps"))
		->order($db->quoteName('path') . ' ASC');
		$db->setQuery($query);
		return $db->loadAssocList('alias');
	}
	
	public function getClient() {
		$result = array();
		$client = Factory::getApplication()->client;
		$class = new ReflectionClass('Joomla\Application\Web\WebClient');
		$constants = array_flip($class->getConstants());
		
		$result['browser'] = $constants[$client->browser].' '.$client->browserVersion;
		$result['platform'] = $constants[$client->platform].($client->mobile ? ' (mobile)' : '');
		$result['mobile'] = $client->mobile;
		return $result;
	}
	
	public function getTagcnts() {
		$result = array('tagcnts' => array('mapcnt' =>0, 'mrkcnt' => 0, 'trkcnt' => 0), 'tags' => array(), 'taglist' => '' );
		$db = $this->getDbo();
		$query =$db->getQuery(true);
		//first we get the total number of each type of item with one or more tags
		$query->select('type_alias,core_content_id, COUNT(*) AS numtags')
		->from('#__contentitem_tag_map')
		->where('type_alias LIKE '.$db->quote('com_xbmaps%'))
		->group('core_content_id, type_alias');
		//not checking that tag is published, not using numtags at this stage - poss in future
		$db->setQuery($query);
		$db->execute();
		$items = $db->loadObjectList();
		foreach ($items as $it) {
			switch ($it->type_alias) {
				case 'com_xbmaps.map' :
					$result['tagcnts']['mapcnt'] ++;
					break;
				case 'com_xbmaps.marker':
					$result['tagcnts']['mrkcnt'] ++;
					break;
				case 'com_xbmaps.track':
					$result['tagcnts']['trkcnt'] ++;
					break;
			}
		}
		//now we get the number of each type of item assigned to each tag
		$query->clear();
		$query->select('type_alias,t.id, t.title AS tagname, t.level AS level, t.path AS path, COUNT(*) AS tagcnt')
    		->from('#__contentitem_tag_map')
    		->join('LEFT', '#__tags AS t ON t.id = tag_id')
    		->where('type_alias LIKE '.$db->quote('%xbmaps%'))
    		->where('t.published = 1') //only published tags
    		->group('type_alias, tagname')
    		->order('path');
		$db->setQuery($query);
		$db->execute();
		$tags = $db->loadObjectList();
		foreach ($tags as $k=>$t) {
			if (!key_exists($t->tagname, $result['tags'])) {
				$result['tags'][$t->tagname]=array('id' => $t->id, 'mapcnt' =>0, 'mrkcnt' => 0, 'trkcnt' => 0, 'tagcnt'=>0, 'path' => $t->path, 'level' => $t->level);
			}
			$result['tags'][$t->tagname]['tagcnt'] += $t->tagcnt;
			switch ($t->type_alias) {
				case 'com_xbmaps.map' :
					$result['tags'][$t->tagname]['mapcnt'] += $t->tagcnt;
					break;
				case 'com_xbmaps.marker':
					$result['tags'][$t->tagname]['mrkcnt'] += $t->tagcnt;
					break;
				case 'com_xbmaps.track':
					$result['tags'][$t->tagname]['trkcnt'] += $t->tagcnt;
					break;
			}
		}
		return $result;
	}
	
	private function stateCnts(string $table, string $colname = 'state', string $ext='com_xbmaps') {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT a.'.$colname.', a.alias')
		->from($db->quoteName($table).' AS a');
		if ($table == '#__categories') {
			$query->where('extension = '.$db->quote($ext));
		}
		$db->setQuery($query);
		$col = $db->loadColumn();
		$vals = array_count_values($col);
		$result['total'] = count($col);
		$result['published'] = key_exists('1',$vals) ? $vals['1'] : 0;
		$result['unpublished'] = key_exists('0',$vals) ? $vals['0'] : 0;
		$result['archived'] = key_exists('2',$vals) ? $vals['2'] : 0;
		$result['trashed'] = key_exists('-2',$vals) ? $vals['-2'] : 0;
		return $result;
	}
	
}