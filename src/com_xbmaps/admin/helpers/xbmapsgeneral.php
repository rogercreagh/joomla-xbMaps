<?php
/*******
 * @package xbMaps
 * @version 0.7.0.c 9th October 2021
 * @filesource admin/helpers/xbmapsgeneral.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;

class XbmapsGeneral extends ContentHelper {

	/**
	 * @name makeSummaryText
	 * @desc returns a plain text version of the source trunctated at the first or last sentence within the specified length
	 * @param string $source the string to make a summary from
	 * @param int $len the maximum length of the summary
	 * @param bool $first if true truncate at end of first sentence, else at the last sentence within the max length
	 * @return string
	 */
	public static function makeSummaryText(string $source, int $len=250, bool $first = true) {
		if ($len == 0 ) {$len = 100; $first = true; }
		//first strip any html and truncate to max length
		$summary = HTMLHelper::_('string.truncate', $source, $len, true, false);
		//strip off ellipsis if present (we'll put it back at end)
		$hadellip = false;
		if (substr($summary,strlen($summary)-3) == '...') {
			$summary = substr($summary,0,strlen($summary)-3);
			$hadellip = true;
		}
		// get a version with '? ' and '! ' replaced by '. '
		$dotsonly = str_replace(array('! ','? '),'. ',$summary.' ');
		if ($first) {
			// look for first ". " as end of sentence
			$dot = strpos($dotsonly,'. ');
		} else {
			// look for last ". " as end of sentence
			$dot = strrpos($dotsonly,'. ');
		}
		// are we going to cut some more off?)
		if (($dot!==false) && ($dot < strlen($summary)-3)) {
			$hadellip = true;
		}
		if ($dot>3) {
			$summary = substr($summary,0, $dot+1);
		}
		if ($hadellip) {
			// put back ellipsis with a space
			$summary .= ' ...';
		}
		return $summary;
	}
	
	public static function credit() {
		if (self::penPont()) {
			return '';
		}
		$credit='<div class="xbcredit">';
		if (Factory::getApplication()->isClient('administrator')==true) {
			$xmldata = Installer::parseXMLInstallFile(JPATH_ADMINISTRATOR.'/components/com_xbmaps/xbmaps.xml');
			$credit .= '<a href="http://crosborne.uk/xbmaps" target="_blank">
                xbMaps Component '.$xmldata['version'].' '.$xmldata['creationDate'].'</a>';
			$credit .= '<br />'.Text::_('XBMAPS_BEER_TAG');
			$credit .= Text::_('XBMAPS_BEER_FORM');
		} else {
			$credit .= 'xbMaps by <a href="http://crosborne.uk/xbmaps" target="_blank">CrOsborne</a>';
		}
		$credit .= '</div>';
		return $credit;
	}
	
	public static function Deg2DMS($value, $islat=true, $rettype="string") {
		$isnortheast = ($value >= 0);
		if ($islat) {
			$dir = ($isnortheast) ? 'N' : 'E';
		} else {
			$dir = ($isnortheast) ? 'S' : 'W';
		}
		$value = abs($value);
		$deg = floor($value);
		$value = ($value - $deg)*60;
		$min = floor($value);
		$sec = ($value - $min)*60;
		if ($rettype == 'array') {
			return array($deg, $min, $sec, $dir);
		}
		// or if you want the string representation
		return sprintf('%d&deg; %d\' %d&quot; %s', $deg, $min, $sec, $dir);
	}
	
	public static function DMS2Deg($deg, $min, $sec, $dir) {
		$value = $deg + ($min/60) + ($sec/3600);
		$neg = (($dir='W') || ($dir='S')) ? -1 : 1;
		$value = $value * $neg;
		return $value;
	}
	
	public static function penPont() {
		$params = ComponentHelper::getParams('com_xbmaps');
		$beer = trim($params->get('roger_beer'));
		//        Factory::getApplication()->enqueueMessage(password_hash($beer.'PASSWORD_DEFAULT'));
		$hashbeer = $params->get('penpont');
		if (password_verify($beer,$hashbeer)) { return true; }
		return false;
	}
	
	public static function isJ4() {
		$version = new Version();
		return $version->isCompatible('4.0.0-alpha');
	}
	
	public static function trackMapsArray(int $trkid, int $state = 4) {
		$isAdmin = Factory::getApplication()->isClient('administrator');
		$link = 'index.php?option=com_xbmaps';
		$link .= $isAdmin ? '&task=map.edit&id=' : '&view=map&id=';
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('a.track_colour AS maptrack_colour, m.title, m.id, m.state AS mstate, m.description AS description, m.summary AS summary ')
		->from('#__xbmaps_maptracks AS a')
		->join('LEFT','#__xbmaps_maps AS m ON m.id=a.map_id')
		->where('a.track_id = "'.$trkid.'"' )
		
		->order('m.title', 'ASC');
		if ($state!=4){
			$query->where('m.state = '.$state);
		}
		
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach ($list as $i=>$item){
			if (($isAdmin) || ($item->mstate == 1)) {
				$ilink = Route::_($link . $item->id);
				
			} else {
				$ilink = '';
			}
			$item->display = '';
			//if not published highlight in yellow if admin or grey if view
			if ($item->mstate != 1) {
				$flag = $isAdmin ? 'xbhlt' : 'xbdim';
				$item->display .= '<span class="'.$flag.'">'.$item->title.'</span>';
			} else {
				$item->display .= $item->title;
			}
			//link if isAdmin or isPublished
			if (($isAdmin) || ($item->mstate == 1)) {
				$item->linkedtitle = '<a href="'.$ilink.'">'.$item->display.'</a>';
			} else {
				$item->linkedtitle = $item->display;
			}
		}
		return $list;
	}
	
	public static function mapTracksArray(int $mapid, int $state = 4) {
		$isAdmin = Factory::getApplication()->isClient('administrator');
		$link = 'index.php?option=com_xbmaps';
		$link .= $isAdmin ? '&task=track.edit&id=' : '&view=track&id=';
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('a.track_colour AS track_colour, t.title, t.id, t.alias, t.description, t.summary, t.state AS tstate, t.track_colour AS defcol,
			t.gpx_filename AS gpx_filename, t.params AS tparams, t.rec_date AS rec_date')
		->from('#__xbmaps_maptracks AS a')
		->join('LEFT','#__xbmaps_tracks AS t ON t.id=a.track_id')
		->where('a.map_id = "'.$mapid.'"' )
		->order('a.listorder', 'ASC');
		if ($state!=4){
			$query->where('t.state = '.$state);
		}
				
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach ($list as $i=>$item){
			$ilink = Route::_($link . $item->id);
			$item->display = '';
			//if not published highlight in yellow if editable or grey if view
			if ($item->tstate != 1) {
				$flag = $isAdmin ? 'xbhlt' : 'xbdim';
				$item->display .= '<span class="'.$flag.'">'.$item->title.'</span>';
			} else {
				$item->display .= $item->title;
			}
			//if item not published only link if isAdmin
			if (($isAdmin) || ($item->tstate == 1)) {
				$item->linkedtitle = '<a href="'.$ilink.'">'.$item->display.'</a>';
			} else {
				$item->linkedtitle = $item->display;
			}
			if ($item->track_colour=='') {$item->track_colour = $item->defcol; }
			$item->rec_date = HtmlHelper::date($item->rec_date, Text::_('d M Y'));
		}
		return $list;
	}
	
	public static function markerMapsArray(int $mrkid, int $state = 4) {
		$isAdmin = Factory::getApplication()->isClient('administrator');
		$link = 'index.php?option=com_xbmaps';
		$link .= $isAdmin ? '&task=map.edit&id=' : '&view=map&id=';
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('a.show_popup AS show_popup, m.title, m.id, m.state AS mstate ')
		->from('#__xbmaps_mapmarkers AS a')
		->join('LEFT','#__xbmaps_maps AS m ON m.id=a.map_id')
		->where('a.marker_id = "'.$mrkid.'"' )
		
		->order('m.title', 'ASC');
		if ($state!=4){
			$query->where('m.state = '.$state);
		}
		
		$db->setQuery($query);
		$list = $db->loadObjectList();
		foreach ($list as $i=>$item){
		    if (($isAdmin) || ($item->mstate == 1)) {
				$ilink = Route::_($link . $item->id);
				
			} else {
				$ilink='';
			}
			$item->display = '';
			//if not published highlight in yellow if admin or grey if view
			if ($item->mstate != 1) {
				$flag = $isAdmin ? 'xbhlt' : 'xbdim';
				$item->display .= '<span class="'.$flag.'">'.$item->title.'</span>';
			} else {
				$item->display .= $item->title;
			}
			//link if isAdmin or isPublished
			if (($isAdmin) || ($item->mstate == 1)) {
				$item->linkedtitle = '<a href="'.$ilink.'">'.$item->display.'</a>';
			} else {
				$item->linkedtitle = $item->display;
			}
		}
		return $list;
	}
	
	public static function mapMarkersArray(int $mapid, int $state = 4) {
		$isAdmin = Factory::getApplication()->isClient('administrator');
		$link = 'index.php?option=com_xbmaps';
		$link .= $isAdmin ? '&task=marker.edit&id=' : '&view=marker&id=';
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('a.show_popup AS show_popup, mk.title AS mktitle, mk.id AS mkid,
                mk.description AS mkdesc, mk.latitude AS mklat, mk.longitude AS mklong,
                mk.marker_type AS markertype, mk.params AS mkparams, mk.state AS mkstate ')
                ->from('#__xbmaps_mapmarkers AS a')
                ->join('LEFT','#__xbmaps_markers AS mk ON mk.id=a.marker_id')
                ->where('a.map_id = "'.$mapid.'"' )
                
                ->order('a.listorder', 'ASC');
                if ($state!=4){
                    $query->where('mk.state = '.$state);
                }
                
                
			$db->setQuery($query);
			$list = $db->loadObjectList();
			foreach ($list as $i=>$item){
				$params = json_decode($item->mkparams, TRUE);
				$item->mkshowdesc = $params['marker_popdesc'];
				$item->mkshowcoords = $params['marker_popcoords'];
				$ilink = Route::_($link . $item->mkid);
				$item->display = '';
				//if not published highlight in yellow if editable or grey if view
				if ($item->mkstate != 1) {
					$flag = $isAdmin ? 'xbhlt' : 'xbdim';
					$item->display .= '<span class="'.$flag.'">'.$item->mktitle.'</span>';
				} else {
					$item->display .= $item->mktitle;
				}
				//if item not published only link if isAdmin
				if (($isAdmin) || ($item->mkstate == 1)) {
					$item->linkedtitle = '<a href="'.$ilink.'">'.$item->display.'</a>';
				} else {
					$item->linkedtitle = $item->display;
				}
				$item->mklat = $item->mklat;
				$item->mklong = $item->mklong;
				$params = new Registry;
				$params->loadString($item->mkparams, 'JSON');
				$item->mkparams = $params;
				
			}
			return $list;
	}
	
	public static function buildTrackList($tracks, $infopos) {
		$trklist = '';
		foreach ($tracks as $trk) {
			$trklist .=	'<li><i class="fas fa-project-diagram" style="color:'.$trk->track_colour.'"></i>&nbsp; &nbsp;';
			$trklist .= '<span';
			$trksum = $trk->summary;
			if ($trksum!=''){$trklist .= ' class= "hasTooltip" title="" data-original-title="'.$trksum.'"';}
			$trklist .=	'><b>'.$trk->linkedtitle.'</b></span>&nbsp;';
			$trklist .= ($infopos == 'side') ? '<br >' : ' - ';
			$trklist .= '<span class="xbnit xbml20">Recorded: '.$trk->rec_date.'</span>&nbsp;';
			$trklist .=	 '</li>';
		} // endforeach;
		return $trklist;
	}
	
	public static function buildMarkerList($markers, $infopos, $marker_image_path) {
		$mrklist = '';
		foreach ($markers as $mrk) {
			$mrklist .=	'<li>';
			$pv = '<img src="'.Juri::root().'media/com_xbmaps/images/marker-icon.png"  style="height:20px;margin-left:4px;"/>';
			switch ($mrk->markertype) {
				case 1:
					$pv = '<img src="'.Juri::root().$marker_image_path.'/'.$mrk->mkparams['marker_image'].'" style="height:20px;margin-left:4px;" />';
					break;
				case 2:
					$pv = '<span class="fa-stack fa-2x" style="font-size:8pt;">';
					$pv .='<i class="'.$mrk->mkparams['marker_outer_icon'].' fa-stack-2x" ';
					$pv .= 'style="color:'.$mrk->mkparams['marker_outer_colour'].';"></i>';
					if ($mrk->mkparams['marker_inner_icon']!=''){
						$pv .= '<i class="'.$mrk->mkparams['marker_inner_icon'].' fa-stack-1x fa-inverse" ';
						$pv .= 'style="color:'.$mrk->mkparams['marker_inner_colour'].';';
						if ($mrk->mkparams['marker_outer_icon']=='fas fa-map-marker') {
							$pv .= 'line-height:1.75em;font-size:0.8em;';
						}
						$pv .= '"></i>';
					}
					$pv .= '</span>';
					break;
				default:
					break;
			}
			$mrklist .=	$pv.'&nbsp; &nbsp;<span';
			$mrksum = strip_tags($mrk->mkdesc);
			if ($mrksum !='') {$mrklist .= ' class= "hasTooltip" title="" data-original-title="'.$mrksum.'"';}
			$mrklist .=	'><b>'.$mrk->display.'</b></span>&nbsp;';
			$mrklist .= ($infopos == 'side') ? '<br >' : '';
			$mrklist .= '<span class="xbnit xbml20">Lat:&nbsp;'.XbmapsGeneral::Deg2DMS($mrk->mklat).' Long:&nbsp;'.XbmapsGeneral::Deg2DMS($mrk->mklong,false).'</span>';
			$mrklist .=	 '</li>';
		} // endforeach;
		return $mrklist;
	}
	
	
}
