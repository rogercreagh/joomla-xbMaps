<?php
/*******
 * @package xbMaps
 * @version 1.0.1 20th December 2021
 * @filesource admin/helpers/xbmapsgeneral.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/geocoder.php');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\Registry\Registry;
use What3words\Geocoder\Geocoder;

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
		if (XbmapsGeneral::penPont()) {
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
		$beer = trim($params->get('map_beer'));
		//        Factory::getApplication()->enqueueMessage(password_hash($beer, PASSWORD_BCRYPT));
		return password_verify($beer,'$2y$10$WOBclRcbj8hksmSuI1Yi9.a0TwXe/ICYN2iWIignns0NMJ8UlJIda');
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
			t.gpx_filename AS gpx_filename, t.params AS tparams, t.rec_date AS rec_date, t.rec_device AS rec_device, t.activity AS activity')
		->from('#__xbmaps_maptracks AS a')
		->join('LEFT','#__xbmaps_tracks AS t ON t.id=a.track_id')
		->where('a.map_id = "'.$mapid.'"' )
		->order('a.listorder', 'ASC');
		if ($state!=4){
			$query->where('t.state = '.$state);
		}
				
		$db->setQuery($query);
		$list = $db->loadObjectList();
	    $params = new Registry;
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
			$item->rec_date = HtmlHelper::date($item->rec_date, 'd M Y');
			$params->loadString($item->tparams, 'JSON');
			$item->params = $params;
		}
		return $list;
	}
	
	public static function markerMapsArray(int $mrkid, int $state = 4) {
		$isAdmin = Factory::getApplication()->isClient('administrator');
		$link = 'index.php?option=com_xbmaps';
		$link .= $isAdmin ? '&task=map.edit&id=' : '&view=map&id=';
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('m.title, m.id, m.state AS mstate ')
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
		
		$query->select('mk.title AS mktitle, mk.id AS mkid,
                mk.summary AS mkdesc, mk.latitude AS mklat, mk.longitude AS mklong,
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
	
	public static function buildTrackList($tracks, $infopos, $infodisp = 1) {
		$trklist = '<ul class="xblist" style="margin:0;">';
		foreach ($tracks as $trk) {
			$trklist .=	'<li><i class="fas fa-project-diagram" style="color:'.$trk->track_colour.'"></i>&nbsp; &nbsp;';
			$trklist .= '<span';
			$trksum = $trk->summary;
			if ($trksum!=''){$trklist .= ' class= "hasTooltip" title="" data-original-title="'.$trksum.'"';}
			$trklist .=	'><b>'.$trk->linkedtitle.'</b></span> ';
			if (($infodisp & 1)==1) {
			    $trklist .= ' : '.$trk->activity;
			}
			$trklist .= ($infopos == 'side') ? '<br >' : ' - ';
			if (($infodisp & 2)==2) {
			    $trklist .= '<span class="xbnit xbml20">Recorded: </span>'.$trk->rec_date.'<br />';
			}
			if (($infodisp > 3)) {
			    $trklist .= '<span class="xbnit xbml20">Device: </span>'.$trk->rec_device.'';
			}
			$trklist .=	 '</li>';
		} // endforeach;
		$trklist .=	 '</ul>';
		return $trklist;
	}
	
	public static function buildMarkerList($markers, $infopos, $marker_image_path, $locdisp = 2) {
        $params = ComponentHelper::getParams('com_xbmaps');
        $w3w_api = $params->get('w3w_api');
		$mrklist = '<ul class="xblist" style="margin:0;">';
		foreach ($markers as $mrk) {
			$mrklist .=	'<li>';
			$pv = '<img src="'.Uri::root().'media/com_xbmaps/images/marker-icon.png"  style="height:20px;margin-left:4px;"/>';
			switch ($mrk->markertype) {
				case 1:
					$pv = '<img src="'.Uri::root().$marker_image_path.'/'.$mrk->mkparams['marker_image'].'" style="height:20px;margin-left:4px;" />';
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
			switch ($locdisp) {
			    case 1:
        			$mrklist .= '<span class="xbpr20""><i>Lat:</i> '.$mrk->mklat.'</span><i>Long:</i>&nbsp;'.$mrk->mklong.'</span>';
			        break;
			    case 2:
			        $mrklist .= '<span class="xbpr20""><i>Lat:</i> '.XbmapsGeneral::Deg2DMS($mrk->mklat).'</span><i>Long:</i>&nbsp;'.XbmapsGeneral::Deg2DMS($mrk->mklong,false).'</span>';
			        break;
			    case 4:
			        $api = new Geocoder($w3w_api);
			        $w3w = $api->convertTo3wa($mrk->mklat,$mrk->mklong,$params->get('w3w_lang'))['words'];
			        $mrklist .= '<i>w3w</i>: ///<b>'.$w3w.'</b>';
			        break;
			        
			    default:
			        ;
			    break;
			}
			$mrklist .=	 '</li>';
		} // endforeach;
		$mrklist .= '</ul>';
		return $mrklist;
	}
	
	/**
	 * @name getIdFromAlias()
	 * @desc gets the id of an item in a given table from the alias
	 * @param string $table - table to search
	 * @param string $alias - alias to find
	 * @param string $ext - the extension to match if searching in #__categories for a different extension
	 * @return int - item id or 0 if not found
	 */
	public static function getIdFromAlias($table, $alias, $ext = 'com_xbmaps') {
		$alias = trim($alias,"' ");
		$table = trim($table,"' ");
		//check for valid $alias (letter numbers and hyphens only)
		if (!preg_match('[A-Za-z0-9-]+$', $alias)) {
			return false;
		}		
		// check for valid format for $table ('#__' followed by letters nubers and underscores only)
		if (!preg_match('#__[A-Za-z0-9\$_]+$', $table)) {
			return false;
		}
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->quoteName($table))->where($db->quoteName('alias')." = ".$db->quote($alias));
		if ($table === '#__categories') {
			$query->where($db->quoteName('extension')." = ".$db->quote($ext));
		}
		$db->setQuery($query);
		$res =0;
		$res = $db->loadResult();
		return $res;
	}
	
	public static function idExists(int $id, string $table, $ext = 'com_xbmaps') {
		//no zero id's
		if ($id==0) {
			return false;
		}
		// check for valid format for $table ('#__' followed by letters nubers and underscores only)
		if (!preg_match('#__[A-Za-z0-9\$_]+$', $table)) {
			return false;
		}
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->quoteName($table))->where($db->quoteName('id')." = ".$db->quote($id));
		if ($table === '#__categories') {
			$query->where($db->quoteName('extension')." = ".$db->quote($ext));
		}
		$db->setQuery($query);
		//trap errors
		$res = $db->loadResult();
		return $res;		
	}
}
