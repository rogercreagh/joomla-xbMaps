<?php
/*******
 * @package xbMaps
 * @version 0.1.0 19th December 2021
 * @filesource xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

class plgContentXbMaps extends JPlugin {
	
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
	
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}
		if (false === strpos($article->text, '{xbmap '))
		{
			return true; //don't bother if no {xbref tags
		}
		
		$xbmap_cmds		= '/({xbmap\s*)(.*?)(})/si';
		$xbmap_scode		= '/{xbmap\s*.*?}/si';
		
		//strip out xbshowref and xbhideref if they are present leaving enclosed content
//		$article->text=preg_replace('!<span class="xbhideref" (.*?)>(.*?)</span>!', '${2}', $article->text);
//		$article->text=preg_replace('!<span class="xbshowref" (.*?)>(.*?)</span>!', '${2}', $article->text);

		//TODO get param for show errors in article display (for debugging) and make error strings conditional
		
		$errdiv = '<div class="xbbox xbboxyell" style="width:400px;"><p class="xbnit" style="color:red;">';
		//if com_xbmaps not enabled simply strip out shortcode and replace with message (?param to disable
		if (!ComponentHelper::isEnabled('com_xbmaps')) {
			$errstr = $errdiv.'xbMap display requires component xbMaps to be installed and enabled</p></div>';
			$article->text = preg_replace($xbmap_scode, $errstr, $article->text);			
			return true;
		}

		// load stylesheets, javascript, language
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::base(). 'media/com_xbmaps/css/xbmaps.css');
		require_once( JPATH_ADMINISTRATOR.'/components/com_xbmaps/helpers/xbmaps.php');
		
		//  get array of {xbref ...} shortcodes
		$matches 		= array();
		$count_matches	= preg_match_all($xbmap_scode,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		
			
		for($i = 0; $i < $count_matches; $i++) {
			
		// shortcode format
		// {xbmap view=map/track id=N width=Wx  height=Hx title=0/1 info=0..4 desc=0..4 float=left/right/none}
		// either mapid or trackid, mapid will include any markers or tracks linked
		// if x not specified for Wx and Hx assue px
		// info and desc default to 0 (not shown) (will need to override map/track params in iframe url)
					
			$shortcode	= $matches[0][$i][0];
						
			preg_match($xbmap_cmds,$shortcode,$sc_parts);
			
			$cmdsarr = explode(",", $sc_parts[2]);
			$cmds = array();
// 			$cmds['view'] = '';
// 			$cmds['id'] = 0;
// 			$cmds['title']= '';
// 			$cmds['desc']= '';
// 			$cmds['info']= '';
// 			$cmds['width']= '';
// 			$cmds['height']= '';
// 			$cmds['float']= '';
			foreach ($cmdsarr as $value) {
				$nv = explode('=',$value,2);
				$cmds[$nv[0]]=$nv[1];
			}			
			// use alias as alternative to id
			if ((array_key_exists('view',$cmds)) && (array_key_exists('alias', $cmds))) {
				$cmds['id'] = XbmapsHelper::getIdFromAlias($cmds['alias'],'#__xbmaps_'.$cmds['view'].'s');
			}
			$output = '';			
			// must have view and id
			if ((!array_key_exists('view',$cmds)) || (!array_key_exists('id', $cmds))) {
				$output = $errdiv.'Missing view or id in xbmap shortcode'.$enderrdiv;
			} else {
				//check if map/track id exists and published, in database if not post error with name 
				$view=strtolower(trim($cmds['view']));
				$id=(int)$cmds['id'];
				if (($view==='map') && (!$this->idExists($id,'#__xbmaps_maps'))) {
					$output = $errdiv.'Map id '.$id.' not found'.$enderrdiv;
				} elseif (($view==='track') && (!$this->idExists($id,'#__xbmaps_tracks'))) {
					$output = $errdiv.'Track id '.$id.' not found'.$enderrdiv;				
				}
				
			}
			if ($output=='') {
			$output = '<iframe src="/index.php?option=com_xbmaps&view='.$view.'&id='.$id;
				//add in title,desc,info if set
				$output .= '&tmpl=component" ';
				//$output .= ' width="'.strip_tags($width).'" height="'.strip_tags($height).'"
				$output .=' frameborder="0" style="border:0" allowfullscreen></iframe>';
			}

			Factory::getApplication()->enqueueMessage('<pre>'.$output.'</pre>');
			
			// replace the first instance of shortcode with output (so next time around the new match will again be the first one remaining)
			$article->text = preg_replace($xbmap_scode, $output, $article->text, 1);
						
		} //endfor $count_matches
		
		
		return true;
	}
	
	//TODO move these to xbmaps general
	private function idExists(int $id, string $table, $ext = 'com_xbmaps') {
		//no zero id's
		if ($id==0) {
			return false;
		}
		// check for valid format for $table ('#__' followed by letters nubers and underscores only)
		if (!preg_match('/#__[A-Za-z0-9\$_]+$/', $table)) {
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