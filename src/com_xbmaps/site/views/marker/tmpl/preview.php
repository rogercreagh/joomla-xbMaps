<?php
/*******
 * @package xbMaps
 * @version 0.8.0.a 15th October 2021
 * @filesource site/views/marker/tmpl/preview.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

$popuptitle = $this->item->title;
$popupdesc = '';
$popupdesc .= $this->item->summary;
$lat = $this->item->latitude;
$long = $this->item->longitude;
$popupdesc .= '<hr />'.XbmapsGeneral::Deg2DMS($lat).' '.XbmapsGeneral::Deg2DMS($long,false);
$uid = uniqid();
$map = new XbMapHelper($uid, null, true);
$map->loadAPI(false);
$map->loadXbmapsJS();
$zoom = 11;
$map->createMap($lat, $long, $zoom );
$map->setMapType($this->map_type);
switch ($this->item->marker_type) {
    case 1:
    	$image = $this->marker_image_path.'/'.$this->item->params['marker_image'];
        $map->setImageMarker($uid, $lat, $long, $image, $popuptitle, $popupdesc,'','',1);
        break;
    case 2:
    	$outer = $this->item->params['marker_outer_icon'];
    	$inner = $this->item->params['marker_inner_icon'];
    	$outcol = $this->item->params['marker_outer_colour'];
    	$incol = $this->item->params['marker_inner_colour'];
        $insize = '';
        if ($outer=='fas fa-map-marker') {
            $insize = 'line-height:1.75em;font-size:0.8em;';
        }
        
        $div = '<div><span class="fa-stack fa-2x" style="margin-left:-1em;margin-top:-2em;font-size:12pt;">';
        $div .= '<i class="'.$outer.' fa-stack-2x" style="color:'.$outcol.';"></i>';
        
        $div .= '<i class="'.$inner.' fa-stack-1x fa-inverse" style="color:'.$incol.';'.$insize.'"></i>';
        $div .= '</span></div>';
        $map->setDivMarker($uid, $lat, $long, $div, $popuptitle,$popupdesc,'','',1);
        break;
    default:
        $map->setMarker($uid, $lat, $long, $popuptitle, $popupdesc,'','',1);
        break;
}

$map->renderMap();

?>
		<div id="xbmaps" style="margin:0;padding:0;">
			<div align="center" style="margin:0;padding:0">
				<div id="xbMap<?php echo $uid; ?>" style="margin:0;padding:0;width:100%;height:400px;">
				</div>
				<div id="coordInfo" class="pull-left"></div>
				<div class="clearfix"></div>
			</div>
		</div>
