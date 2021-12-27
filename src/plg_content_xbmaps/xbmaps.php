<?php
/*******
 * @package xbMaps Content Plugin
 * @version 0.1.0.e 26th December 2021
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
	
	protected $autoloadLanguage = true;
	
	public function __construct(& $subject, $config) {
		
		parent::__construct($subject, $config);
		
	}
	
	public function onContentPrepare($context, &$article, &$params, $page = 0) {
	
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}
		//only use on site side
		if (Factory::getApplication()->isClient('adninistrator')) {
			return true;
		}
		// quick check if {xbmaps shortcode is present
		if (false === strpos($article->text, '{xbmaps ')) {
			return true; 
		}
		
		$xbmap_cmds		= '/({xbmaps\s*)(.*?)(})/si';
		$xbmap_scode		= '/{xbmaps\s*.*?}/si';
		
		//strip out xbshowref and xbhideref if they are present leaving enclosed content
//		$article->text=preg_replace('!<span class="xbhideref" (.*?)>(.*?)</span>!', '${2}', $article->text);
//		$article->text=preg_replace('!<span class="xbshowref" (.*?)>(.*?)</span>!', '${2}', $article->text);

		//TODO get param for show errors in article display (for debugging) and make error strings conditional
		
		$show_errors = $this->params->get('show_errors');
		$errdiv = '<div class="xbbox xbboxyell" style="width:400px;"><p class="xbnit" style="color:red;">';
		$enderrdiv = '</p></div>';
		//if com_xbmaps not enabled simply strip out shortcode and replace with optional message 
		$errstr = '';
		if (!ComponentHelper::isEnabled('com_xbmaps'))  {
			if ($show_errors) {
				$errstr = $errdiv.Text::_('PLGCON_XBMAPS_MISSING_COMPONENT_ERROR').$enderrdiv;
			}
			$article->text = preg_replace($xbmap_scode, $errstr, $article->text);			
			return true;
		}

		//get the defaults from params
		$def_title = (int)$this->params->get('show_title');
		$def_info = $this->params->get('show_map_info');
		$validinfo = array('0','above','right','left','below');
		$def_desc = (int)$this->params->get('show_map_desc');
		$def_ht = (int)$this->params->get('def_map_ht');
		$maxht = (int)$this->params->get('max_ht');
		$def_wd = (int)$this->params->get('def_map_wd');
		$def_float = $this->params->get('def_map_float');
		
		// load stylesheets, javascript, language
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::base(). 'media/com_xbmaps/css/xbmaps.css');
		require_once( JPATH_ADMINISTRATOR.'/components/com_xbmaps/helpers/xbmapsgeneral.php');
		
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
			foreach ($cmdsarr as $value) {
				$nv = explode('=',$value,2);
				$cmds[$nv[0]]=$nv[1];
			}			
			// use alias as alternative to id
			if ((array_key_exists('view',$cmds)) && (array_key_exists('alias', $cmds))) {
				$cmds['id'] = XbmapsGeneral::getIdFromAlias('#__xbmaps_'.$cmds['view'].'s',$cmds['alias']);
			}
			$output = '';			
			$err = false;
			$errstr = '';
			// must have view and id
			if ((!array_key_exists('view',$cmds)) || (!array_key_exists('id', $cmds))) {
				$err = true;
				if ($show_errors) {
					$errstr = $errdiv.Text::_('PLGCON_XBMAPS_MISSING_IN_SCODE').$enderrdiv;					
				}
			} else {
				$view=strtolower(trim($cmds['view']));
				if (($view !== 'map') && ($view !== 'track')) {
			        $err = true;
			        if ($show_errors) {
			        	$errstr = $errdiv.Text::_('PLGCON_XBMAPS_INVALID_VIEW').$enderrdiv;
			        }
				} else {
    				//check if map/track id exists and published, in database if not post error with name 
    				$id=(int)$cmds['id'];
    				if (($view==='map') && (!XbmapsGeneral::idExists($id,'#__xbmaps_maps'))) {
    					$err = true;
    					if ($show_errors) {
    						$errstr = $errdiv.Text::sprintf('PLGCON_XBMAPS_ID_NOT_FOUND','Map', $id).$enderrdiv;
    					}
    				} elseif (($view==='track') && (!XbmapsGeneral::idExists($id,'#__xbmaps_tracks'))) {
    					$err = true;
    					if ($show_errors) {
    						$errstr = $errdiv.Text::sprintf('PLGCON_XBMAPS_ID_NOT_FOUND','Track', $id).$enderrdiv;
    					}
    				}								
				}
			}
			if ($err) {
				// shortcode invalid so blank this one and go on to next instance (any new match will again be the first one remaining)
				$article->text = preg_replace($xbmap_scode, $errstr, $article->text, 1);			
			} else {
				//shortcode has valid view and id exists so go ahead
				$divstr = '<div';
				//add float to div for iframe
				$class = ($def_float!='-1')? $def_float : '';
				if (array_key_exists('float', $cmds)) {
					if ($cmds['float']==='left') {
						$class = 'pull-left';
					} elseif ($cmds['float']==='right') {
						$class = 'pull-right';
					}
				}
				$stylestr = ' style="';
				if ($class != '') {
					$divstr .= ' class="'.$class.'"';
					//if floating add a margin between frame and text
					if ($class === 'pull-left') {
						$stylestr .= 'margin-right:15px;';
					} elseif ($class === 'pull-right') {
						$stylestr .= 'margin-left:15px;';
					}
				}
				
				//add width to div for iframe
				$wd = $def_wd; 
				if (array_key_exists('wd', $cmds)) {
					$wd = (int)$cmds['wd'];
					//wd= must be between 20 and 100, otherwise use default
					$wd = (($wd>=20) && ($wd<=100)) ? $wd : $def_wd;
				} 
				$stylestr .= 'width:'.$wd.'%;';
				//get height for map
				$ht = $def_ht;
				if (array_key_exists('ht', $cmds)) {
					$ht = (int)$cmds['ht'];
					// ht= must be between 100 and max_ht otherwise use default
					$ht = (($ht>=100) && ($ht <= $maxht)) ? $ht : $def_ht; 
				}
				//get the map settings so we can extend div height if necessary
				 
				$frmstr = '<iframe src="/index.php?option=com_xbmaps&view='.$view.'&id='.$id;
				//add in title,desc,info if set
				$title = $def_title;
				if (array_key_exists('title', $cmds)) {
					$title = (int)$cmds['title'];
					if (($title !== 0) && ($title !== 1)) {
						$title = $def_title;
					}
				}
				$frmstr .= ($title > -1)? '&title='.$title : '';
				
				$desc = $def_desc;
				if (array_key_exists('desc', $cmds)) {
					$desc = (int)$cmds['desc'];
					if (($desc<0) || ($desc>4)) {
						$desc = $def_desc;
					}
				}
				$frmstr .= ($desc > -1)? '&desc='.$desc : '';
				
				$info = $def_info;
				if (array_key_exists('info', $cmds)) {
					$info = strtolower($cmds['info']);
					if (!in_array($info, $validinfo)) {
						$info = $def_info;
					}
				}
				$frmstr .= ($info != '-1')? '&info='.$info : '';
				
				//scale ht by width
				$ht = intdiv(($ht * $wd), 100 );
				$frmstr .= '&ht='.$ht; //this will be the height of the map itself
				//add 10px to div to eliminate scrollbar
				$ht += 10;
				// width of the iframe is always 100%, the shortcode wd value sets the width of the containing div in $stylestr
				$frmstr .= '&tmpl=component" width="100%" ';
				
				//now we can correct the div and frame height to allow for title and info above/below
				if ($title==1) { $ht = $ht + 50; }
				if ($desc > 1) { $ht = $ht + 150; } //allow 150px for desc if above or below - it might be much more in practice
				if (($info == 'above') || ($info == 'below')) { $ht = $ht + 200; } //allow 200px for info if above or below
				
				$frmstr .= 'height="'.$ht.'" frameborder="0" style="border:0" allowfullscreen></iframe>';
				
				$stylestr .= 'height:'.$ht.'px;"';
				$divstr .= $stylestr.' >';
				$output = $divstr.$frmstr.'</div>';
				// replace the first instance of shortcode with output (so next time around the new match will again be the first one remaining)
				$article->text = preg_replace($xbmap_scode, $output, $article->text, 1);
			}
			
		} //endfor $count_matches
		
		return true;
	} //end contentprepare
		
}