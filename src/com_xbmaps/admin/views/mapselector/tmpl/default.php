<?php
/*******
 * @package xbMaps
 * @version 0.8.0.a 15th October 2021
 * @filesource admin/views/mapselector/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

$uid = uniqid();
$map = new XbMapHelper($uid, null, true);
$map->loadAPI(false, );
$map->loadXbmapsJS();
$map->createMap($this->latitude, $this->longitude, $this->zoom,);
$map->setMapType($this->map_type);
	//we're doing a modal with no item details
$map->setMarker($uid, $this->latitude, $this->longitude, 'Map Centre Set', 'Zoom: '.$this->zoom, '','','',1);
	// Export, Move, Input, renderSearch are dependent
//	$map->moveMarker();
//	$map->save2form('jform_centre_latitude_id', 'jform_centre_longitude_id', 'jform_default_zoom_id');
//	if ($this->settype == 'marker') {
//		$map->inputMarker('jform_latitude_id', 'jform_longitude_id', '', 1);
//	} else {
//		$map->inputMarker('jform_centre_latitude_id', 'jform_centre_longitude_id', 'jform_default_zoom_id');
//	}
//	$map->exportMarker($uid);
$map->endZoom();
$map->mapAreaClick($uid);
$map->renderSearch($uid,'',$this->searchdisplay);
//	$map->storeZoom($uid);
	

$map->renderFullScreenControl();

$map->renderMap();

?>
<div id="xbmaps" style="margin:0;padding:0;">
	<div align="center" style="margin:0;padding:0">
		<div id="xbMap<?php echo $uid; ?>" style="margin:0;padding:0;width:100%;height:97vh">
</div></div></div>

