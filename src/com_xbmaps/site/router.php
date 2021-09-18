<?php
/*******
 * @package xbMaps
 * @version 0.1.2.a 30th August 2021
 * @filesource site/router.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;

class XbmapsRouter extends JComponentRouterBase {
    
	public function build(&$query)
	{
		//      Factory::getApplication()->enqueueMessage('<pre>'.print_r($query,true).'</pre>','build');
		$segments = array();
		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			unset($query['view']);
		}
		if (isset($query['id']))
		{
			$db = Factory::getDbo();
			$qry = $db->getQuery(true);
			$qry->select('alias');
			switch($segments[0])
			{
			    case 'map':
			        $qry->from('#__xbmaps_maps');
			        break;
			    case 'track':
			    	$qry->from('#__xbmaps_tracks');
			    	break;
			    case 'marker':
			    	$qry->from('#__xbmaps_markers');
			    	break;
			    case 'category':
					$qry->from('#__categories');
					break;
				case 'tag':
					$qry->from('#__tags');
					break;
			}
			$qry->where('id = ' . $db->quote($query['id']));
			$db->setQuery($qry);
			$alias = $db->loadResult();
			$segments[] = $alias;
			unset($query['id']);
		}
		return $segments;
	}
	
	public function parse(&$segments)
	{
		$vars = array();
		
		$db = Factory::getDbo();
		$qry = $db->getQuery(true);
		$qry->select('id');
		switch($segments[0])
		{
		    case 'maplist':
		        $vars['view'] = 'maplist';
		        break;
		    case 'tracklist':
		    	$vars['view'] = 'tracklist';
		    	break;
//		    case 'markerlist':
//		    	$vars['view'] = 'markerlist';
//		    	break;
		    case 'catlist':
				$vars['view'] = 'catlist';
				break;
			case 'taglist':
				$vars['view'] = 'taglist';
				break;
			case 'map':
			    $vars['view'] = 'map';
			    $qry->from('#__xbmaps_maps');
			    $qry->where('alias = ' . $db->quote($segments[1]));
			    $db->setQuery($qry);
			    $id = $db->loadResult();
			    $vars['id'] = (int) $id;
			    break;
			case 'track':
			    $vars['view'] = 'track';
			    $qry->from('#__xbmaps_tracks');
			    $qry->where('alias = ' . $db->quote($segments[1]));
			    $db->setQuery($qry);
			    $id = $db->loadResult();
			    $vars['id'] = (int) $id;
			    break;
			case 'marker':
				$vars['view'] = 'marker';
				$qry->from('#__xbmaps_markers');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'category':
				$app= Factory::getApplication();
				$ext = $app->input->get('ext');
				if ($ext=='') {$ext='com_xbmaps'; }
				$vars['view'] = 'category';
				$qry->from('#__categories');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$qry->where('extension = ' . $db->quote($ext));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
			case 'tag':
				$vars['view'] = 'tag';
				$qry->from('#__tags');
				$qry->where('alias = ' . $db->quote($segments[1]));
				$db->setQuery($qry);
				$id = $db->loadResult();
				$vars['id'] = (int) $id;
				break;
		}	
		
		return $vars;
	}
	
	public function preprocess($query)
	{
		return $query;
	}
	
}
