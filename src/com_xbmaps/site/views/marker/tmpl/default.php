<?php
/*******
 * @package xbMaps Component
 * @version 1.2.1.1 20th February 2023
 * @filesource site/views/marker/tmpl/preview.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/geocoder.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xbparsedown.php');

use Xbmaps\Xbparsedown\Xbparsedown;

use What3words\Geocoder\Geocoder;
use Joomla\CMS\Language\Text;

$popuptitle = $this->item->title;
$lat = $this->item->latitude;
$long = $this->item->longitude;
$popupdesc = '';
if ($this->item->params['marker_popdesc']) {
    $popupdesc .= Xbparsedown::instance()->text($this->item->summary).'<br />';
}
$disp = $this->item->params['marker_popcoords'];
if ($disp=='') $disp=0;
if ($disp>0) $popupdesc .= '<hr /><b>'.Text::_('XBMAPS_LOCATION').'</b></br>';

if (($disp & 1)==1) {
	$popupdesc .= '<span style="padding-right:20px"><i>Lat:</i> '.$lat.'</span><i>Long:</i> '.$long.'<br />';
}
if (($disp & 2)==2) {
	$popupdesc .= '<span style="padding-right:20px"><i>Lat:</i> '.XbmapsGeneral::Deg2DMS($lat).'</span><i>Long:</i> '.XbmapsGeneral::Deg2DMS($long,false).'<br />';
}
if ($disp > 3) {
	$api = new Geocoder($this->w3w_api);
	$w3w = $api->convertTo3wa($lat,$long,$this->w3w_lang)['words'];
	$popupdesc .= '<i>w3w</i>: ///<b>'.$w3w.'</b>';
}

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
