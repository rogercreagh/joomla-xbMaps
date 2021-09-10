<?php
/*******
 * @package xbMaps
 * @version 0.1.2.c 9th September 2021
 * @filesource admin/helpers/xbmaphelper.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

class XbMapHelper {
	
	protected $name					= 'xbMap';
	protected $id					= '';
	private	$output					= array();
	
	public $maptype				= '';
	public $currentposition			= '';
	public $fullscreen				= '';
	public $search					= '';
	public $zoomwheel				= '';
	public $zoomcontrol				= '';
	public $homebutton = '';
	public $easyprint				= '';
	public $markerclustering		= 0;
		
	function __construct($id = '', $controls=null, $selectmaparea = false) {
		
		$app 						= Factory::getApplication();
		if (!is_null($controls)) {		
			//we are being passed map specific parameters
			$this->maptype			= $controls->get( 'map_type', '' );
			$this->currentposition		= $controls->get( 'map_current_position', 0 );
			$this->fullscreen			= $controls->get( 'map_full_screen',1 );
			$this->search				= $controls->get( 'map_search', 1 );
			$this->zoomwheel			= $controls->get( 'map_zoom_wheel', 1);
			$this->zoomcontrol			= $controls->get( 'map_zoom_control', 0 );
			$this->easyprint			= $controls->get( 'map_easyprint', 0 );
			$this->markerclustering		= $controls->get( 'marker_clustering', 0 );
		} else { 
			// use the global settings
			$paramsC 					= ComponentHelper::getParams('com_xbmaps');
			$this->maptype			= $paramsC->get( 'map_type', '' );
			$this->currentposition		= $paramsC->get( 'map_current_position', 0 );
			$this->fullscreen			= $paramsC->get( 'map_full_screen',1 );
			$this->search				= $paramsC->get( 'map_search', 0 );
			$this->zoomwheel			= $paramsC->get( 'map_zoom_wheel', 1);
			$this->zoomcontrol			= $paramsC->get( 'map_zoom_control', 0 );
			$this->easyprint			= $paramsC->get( 'map_easyprint', 0 );	
			$this->markerclustering		= $paramsC->get( 'marker_clustering', 0 );	
		}
		//home button is a special case as we don't want it on track view but we might on map views.
		$this->homebutton 			= 0;
		//random id used for generating the map div id and map name for this instance
		$this->id	= $id;
		//for map location selector) we need these controls whatever the global settings		
		if ($selectmaparea) {
			$this->fullscreen 		= 1;
			$this->search			= 1;
			$this->zoomwheel		= 1;
			$this->zoomcontrol		= 1;
		}
		
	}	
	
	/**
	 * @desc loads the leaflet stuff
	 */
	function loadAPI($cluster = true, $homebtn = 0) {
	    if ($cluster == false) {
	        $this->markerclustering = false;
	    }
		$document	= Factory::getDocument();
		$this->homebutton = $homebtn;
		
		$document->addScript('https://unpkg.com/leaflet@1.7.1/dist/leaflet.js');
		$document->addStyleSheet('https://unpkg.com/leaflet@1.7.1/dist/leaflet.css');

		//need to add the images folder content before using this
		//		$document->addScript(Uri::root() . '/media/com_xbmaps/js/leaflet/leaflet.awesome/leaflet.awesome-markers.min.js');
		//		$document->addStyleSheet(Uri::root() . '/media/com_xbmaps/js/leaflet/leaflet.awesome/leaflet.awesome-markers.css');
		
		$document->addScript('https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js');
		$document->addStyleSheet('https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css');		
		
		if ($this->search==1) {
			$document->addScript(Uri::root() . '/media/com_xbmaps/js/leaflet/leaflet-search.min.js');
			$document->addStyleSheet(Uri::root() . '/media/com_xbmaps/css/leaflet/leaflet-search.min.css');			
		}
		
		if (($this->homebutton==1) && ($this->zoomcontrol==1)) {
			$document->addScript(Uri::root() . '/media/com_xbmaps/js/leaflet/leaflet.zoomhome.min.js');
			$document->addStyleSheet(Uri::root() . '/media/com_xbmaps/css/leaflet/leaflet.zoomhome.css');			
		}
		
		if ($this->easyprint == 1) {
		    $document->addScript(Uri::root(true) . '/media/com_xbmaps/js/leaflet/leaflet.easyprint/bundle.js');
		}
		
		if ($this->markerclustering == 1) {
			$document->addStyleSheet('https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css');
			$document->addStyleSheet('https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css');
			$document->addScript('https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js');
		}

		// leaflet GPX https://github.com/mpetazzoni/leaflet-gpx
		//$document->addScript('https://cdnjs.cloudflare.com/ajax/libs/leaflet-gpx/1.5.2/gpx.min.js');
		$document->addScript(Uri::root() . '/media/com_xbmaps/js/leaflet/leaflet-gpx.min.js');
		
		/** other to consider
		 * leaflet-providers https://github.com/leaflet-extras/leaflet-providers
		 * 
		 * leaflet elevation 
		 */
		
	}
	
	function loadXbmapsJS() {
		$document	= Factory::getDocument();
		$document->addScript(Uri::root(true).'/media/com_xbmaps/js/xbmaps.js');
	}
	
	function createMap($lat, $lng, $zoom) {
		
		$app = Factory::getApplication();
		
		$opt = array();
		if ($this->zoomwheel == 0) {
			$opt[] = 'scrollWheelZoom: false,';
		}
		//Zoom control is separate layer
		$opt[] = 'zoomControl: false,';
		
		$options = '{' . implode("\n", $opt) . '}';
		
		$o 	= array();
		$o[] = 'window.zoom = '.(int)$zoom.';';
		$o[] = 'window.lat = '. $lat.'.toFixed(6);';
		$o[] = 'window.lng = '. $lng.'.toFixed(6);';
		$o[] = 'window.dmslat = deg2dms(lat,\'latitude\');';
		$o[] = 'window.dmslng = deg2dms(lng,\'longitude\');';
		$o[] = 'var map'.$this->name.$this->id.' = L.map("'.$this->name.$this->id.'", '.$options.').setView(['.$lat.', '.$lng.'], '.(int)$zoom.');';
		
		
		if ($this->zoomcontrol == 1) {
			if ($this->homebutton == 1) {
				$o[] = 'var zoomHome = L.Control.zoomHome({position: \'topright\',zoomHomeTitle:\''.Text::_('XBMAPS_RECENTRE').'\',zoomHomeIcon: \'bullseye\'}).addTo(map'.$this->name.$this->id.');';
			} else {
				$o[] = 'new L.Control.Zoom({ position: \'topright\',zoomInTitle: \''.Text::_('XBMAPS_ZOOM_IN').'\', zoomOutTitle: \''.Text::_('XBMAPS_ZOOM_OUT').'\' }).addTo(map'.$this->name.$this->id.');';
			}
		}
		
		if ($this->markerclustering == 1) {
			$o[] = 'var markers' . $this->name . $this->id . ' = L.markerClusterGroup();';
		}
		
		$this->output[] = implode("\n", $o);
		return true;
	}
	
	function setMapType($type = '') {
		
		$o = array();
		if ($type === "osm_de") {
			
			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else if ($type === "cycloOSM") {
			$o[] = 'L.tileLayer(\'https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png\', {';
			$o[] = 'maxZoom: 20,';
			$o[] = 'attribution: \'<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
						
		} else if ($type === "ersi_Sat") {
			$o[] = 'L.tileLayer(\'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}\',{';
			$o[] = 'attribution: \'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else if ($type === "osm_bw") {
			
			//$o[] = 'L.tileLayer(\'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png\', {';
			$o[] = 'L.tileLayer(\'https://tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png\', {';			
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else if ($type === 'opentopomap') {
			
			$o[] = 'L.tileLayer(\'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 17,';
			$o[] = '	attribution: \'Map data: &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>, <a href="https://viewfinderpanoramas.org" target="_blank">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org" target="_blank">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC-BY-SA</a>)\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else if ($type === 'wikimedia') {
			$o[] = 'L.tileLayer(\'https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use" target="_blank">Wikimedia maps</a> | Map data © <a href="https://openstreetmap.org/copyright" target="_blank">OpenStreetMap contributors</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else if ($type == 'osm_fr') {
			
			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 20,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.fr" target="_blank">Openstreetmap France</a> & <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else if ($type == 'osm_hot') {
			
			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 20,';
			$o[] = '	attribution: \'&copy; <a href="https://hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a> & <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		} else {
					
			$o[] = 'L.tileLayer(\'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png\', {';
			$o[] = '	maxZoom: 18,';
			$o[] = '	attribution: \'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>\'';
			$o[] = '}).addTo(map'.$this->name.$this->id.');';
			
		}
		
		$this->output[] = implode("\n", $o);
		return true;
	}
	
	public function setLayerControl() {
		
	}
	
	public function setMarker($markerId, $title, $description, $lat, $lng, $width = '', $height = '', $open = 0  ) {
		
		$o = array();
		
		$o[]= 'var marker'.$markerId.' = L.marker(['.$lat.', '.$lng.'])';
					
		
// 		$style = '';
// 		if ($width != '') {
// 			$style .= 'width: '.(int)$width.'px;';
// 		}
// 		if ($height != '') {
// 			$style .= 'height: '.(int)$height.'px;';
// 		}
// 		if ($style != '') {
// 		    $style = 'style="'.$style.'"';
// 		}
// 		$text='';
// 		if ($title != ''){
// 			$text .= '<b>' . addslashes($title) . '</b><br />';
// 		}
// 		if (($title != '') && ($description != '')) {
// 			$text .= '<br />';
// 		}
// 		if ($description != '') {
// 			$text .=  addslashes($description);
// 		}	
		
		$popcontent = $this->makeMarkerPopup($title, $description, $width, $height, $open);
		
		if ($this->markerclustering != 1) {
			// marker will be added to layer with cluster function
			$o[] = '.addTo(map'.$this->name.$this->id.');';
		}		
		$o[] = ';';
		
		if ($popcontent != '') {
			$openO = '';
			if ($open == 1) {
				$openO = '.openPopup()';
			}
			$o[]= 'marker'.$markerId.'.bindPopup(\''.$popcontent.'\')'.$openO.';';
		}
		
// 		if ($text != '') {
// 			$text = '<div class="xbmarkerpop" '.$style.'>' . $text . '</div>';			
//     		$openO = '';
//     		if ($open == 1) {
//     			$openO = '.openPopup()';
//     		}
//     		$o[]= 'marker'.$markerId.'.bindPopup(\''.$text.'\')'.$openO.';';
// 		}
		
		if ($this->markerclustering == 1) {
			$o[] = 'markers' . $this->name . $this->id . '.addLayer(marker' . $markerId . ');';
		}
		
		$this->output[] = implode("\n", $o);
		return true;
		
	}
		
	//setImageMarker
	public function setImageMarker($markerId, $lat, $lng, $image, $title = '', $description = '', $width = '', $height = '', $open = 0) {
		
		$o = array();	
/*
 var myIcon = L.icon({
    iconUrl: 'my-icon.png',
    iconSize: [38, 95],
    iconAnchor: [22, 94],
    popupAnchor: [-3, -76],
    shadowUrl: 'my-icon-shadow.png',
    shadowSize: [68, 95],
    shadowAnchor: [22, 94]
});

L.marker([50.505, 30.57], {icon: myIcon}).addTo(map);

 */		
		$o[] = 'var imgIcon = L.icon({iconUrl: \''.Uri::root().$image.'\',iconSize:[33,45],iconAnchor:[16,45] });';
		$o[]= 'var marker'.$markerId.' = L.marker(['.$lat.', '.$lng.'],{icon: imgIcon})';
		
		$popcontent = $this->makeMarkerPopup($title, $description, $width, $height, $open);
		
		if ($this->markerclustering == 1) {
			// marker will be added to layer with cluster function
			$o[] = ';';
		} else {
			$o[] = '.addTo(map'.$this->name.$this->id.');';
		}
				
		if ($popcontent != '') {
			$openO = '';
			if ($open == 1) {
				$openO = '.openPopup()';
			}
			$o[]= 'marker'.$markerId.'.bindPopup(\''.$popcontent.'\')'.$openO.';';
		}
		
		if ($this->markerclustering == 1) {
			$o[] = 'markers' . $this->name . $this->id . '.addLayer(marker' . $markerId . ');';
		}
		$this->output[] = implode("\n", $o);
		return true;
	}
		
	//setDivMarker
	public function setDivMarker($markerId, $lat, $lng, $divcontent, $title = '', $description='', $width = '', $height = '', $open = 0  ) {
		
		$o = array();
		//var myIcon = L.divIcon({className: 'my-div-icon'});
		// you can set .my-div-icon styles in CSS
		
		//L.marker([50.505, 30.57], {icon: myIcon}).addTo(map);
		
		$o[]= 'var div'.$markerId.' = L.divIcon({className: \'my-div-icon\', html:\''.$divcontent.'\'})';
		$o[]= 'var marker'.$markerId.' = L.marker(['.$lat.', '.$lng.'],{icon: div'.$markerId.'})';
		if ($this->markerclustering == 1) {
			// marker will be added to layer with cluster function
			$o[] = ';';
		} else {
			$o[] = '.addTo(map'.$this->name.$this->id.');';
		}
		
		$popcontent = $this->makeMarkerPopup($title, $description, $width, $height, $open);
		
		if ($popcontent != '') {
			$openO = '';
			if ($open == 1) {
				$openO = '.openPopup()';
			}
			$o[]= 'marker'.$markerId.'.bindPopup(\''.$popcontent.'\')'.$openO.';';
		}
		
		if ($this->markerclustering == 1) {
			$o[] = 'markers' . $this->name . $this->id . '.addLayer(marker' . $markerId . ');';
		}
		
		$this->output[] = implode("\n", $o);
		return true;
		
	}
	
	private function makeMarkerPopup($title, $description, $width, $height, $open) {
		$style = '';
		if ($width != '') {
			$style .= 'width: '.(int)$width.'px;';
		}
		if ($height != '') {
			$style .= 'height: '.(int)$height.'px;';
		}
		if ($style != '') {
			$style = 'style="'.$style.'"';
		}
		$text='';
		if ($title != ''){
			$text .= '<b>' . addslashes($title) . '</b><br />';
		}
		if (($title != '') && ($description != '')) {
			$text .= '<br />';
		}
		if ($description != '') {
			$text .=  addslashes($description);
		}
		
		if ($text != '') {
			$text = '<div class="xbmarkerpop" '.$style.'>' . $text . '</div>';
		}
		return $text;
	}
	
	
	
	
	public function setMarkerClusterer() {
		
		if ($this->markerclustering == 1) {
			$o              = array();
			$o[]            = 'map' . $this->name . $this->id . '.addLayer(markers' . $this->name . $this->id . ');';
			$this->output[] = implode("\n", $o);
		}
	}
	
	public function setMarkerIcon($markerId, $icon = 'circle', $markerColor = 'blue', $iconColor = '#ffffff', $prefix = 'fa', $spin = 'false', $extraClasses = '' ) {
		
		$o = $o2 = array();
		
		$o[]= 'var icon'.$markerId.' = new L.AwesomeMarkers.icon({';
		
		$o[]= $o2[] = '   icon: "'.$icon.'",';
		$o[]= $o2[] = '   markerColor: "'.$markerColor.'",';
		$o[]= $o2[] = '   iconColor: "'.$iconColor.'",';
		$o[]= $o2[] = '   prefix: "'.$prefix.'",';
		$o[]= $o2[] = '   spin: '.$spin.',';
		$o[]= $o2[] = '   extraClasses: "'.$extraClasses.'",';
		
		$o[]= '})';
		$o[]= ' marker'.$markerId.'.setIcon(icon'.$markerId.');';
		
		$this->output[] = implode("\n", $o);
		return $o2;//return only options;
	
	}
		
//  	public function save2form($latField, $longField, $zoomField = '') {
// 		$o = array();
// 		$o[]= 'function xbmapsSaveForm() {';
// 		$o[]= 'var fldLat = jQuery(\'#'.$latField.'\', window.parent.document);';
// 		$o[] = 'fldLat.val(window.lat);';
// 		$o[]= 'var fldLng = jQuery(\'#'.$longField.'\', window.parent.document);';
// 		$o[] = 'fldLng.val(window.lng);';
// 		if ($zoomField != '') {
// 			$o[]= 'var fldZoom = jQuery(\'#'.$zoomField.'\', window.parent.document);';
// 			$o[] = 'fldZoom.val(window.zoom);';				
// 		}
// 		$o[]= '}';		
// 		$this->output[] = implode("\n", $o);
// 		return true;
		
// 	}
	
	public function endZoom() {
		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.on("zoomend", onZoomEnd);';
		
		//NB this function requires specific map name so has to be created on fly after map
		$o[] = 'function onZoomEnd(e) {';
		$o[] = 'window.zoom = map'.$this->name.$this->id.'.getZoom();';
		$o[] = 'xbModalCoordInfo();';
		$o[] = 'xbmapsSaveForm();';
		$o[] = '}';
		$this->output[] = implode("\n", $o);
		return true;
		
	}
	
	public function mapClick($markerUId) {
		
		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.on(\'click\', onMapClick);';
		
		//NB this function requires specific marker name so has to be created on fly after marker exists
		$o[] = 'function onMapClick(e) {';
		$o[] = ' window.lat = e.latlng.lat;';
		$o[] = ' window.lng = e.latlng.lng;';
		$o[] = ' xbMoveMarker(marker'.$markerUId.', e.latlng.lat, e.latlng.lng);';
		$o[] = ' xbModalCoordInfo();';
		$o[] = ' xbmapsSaveForm();';
		$o[] = '}';
		$this->output[] = implode("\n", $o);
		return true;
	}
		
	public function markerPosClick($markerUId) {
		
		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.on(\'click\', onMarkerPosClick);';
		
		//NB this function requires specific marker name so has to be created on fly after marker exists
		$o[] = 'function onMarkerPosClick(e) {';
		$o[] = ' window.lat = e.latlng.lat;';
		$o[] = ' window.lng = e.latlng.lng;';
		$o[] = ' xbMoveMarker(marker'.$markerUId.', e.latlng.lat, e.latlng.lng);';
		$o[] = ' xbMarkerCoordInfo();';
		$o[] = ' xbmapsSaveForm();';
		$o[] = '}';
		$this->output[] = implode("\n", $o);
		return true;
	}
	
	public function moveMarker() {
		
		$o = array();
		$o[]= 'function xbMoveMarker(marker, lat, lng) {';
		$o[]= '   var newLatLng = new L.LatLng(lat, lng);';
		$o[]= '   marker.setLatLng(newLatLng);';
		$o[]= '}';
		$this->output[] = implode("\n", $o);
		return true;
	}
	
	public function renderSearch($markerId = '', $position = '') {
		
		$position = $position != '' ? $position : 'topleft';
		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.addControl(new L.Control.Search({';
		
		$o[] = '	url: \'https://nominatim.openstreetmap.org/search?format=json&q={s}\',';
		$o[] = '	jsonpParam: \'json_callback\',';
		$o[] = '	propertyName: \'display_name\',';
		$o[] = '	propertyLoc: [\'lat\',\'lon\'],';
		$o[] = '	marker: L.circleMarker([0,0],{radius:30}),';
		$o[] = '	autoCollapse: true,';
		$o[] = '	autoType: false,';
		$o[] = '	minLength: 3,';
		$o[] = '	position: \''.$position.'\',';
			
		$o[] = '	textErr: \''.Text::_('XBMAPS_SEARCH_LOCATION_NOT_FOUND').'\',';
		$o[] = '	textCancel: \''.Text::_('XBMAPS_SEARCH_CANCEL').'\',';
		$o[] = '	textPlaceholder: \''.Text::_('XBMAPS_SEARCH_SEARCH').'\',';
		
		if ($markerId != '') {
			//NB this function requires specific map and marker names so has to be created on fly after map
			$o[] = '	moveToLocation: function(latlng, title, map) {';
//			$o[] = '		phmInputMarker(latlng.lat, latlng.lng);';
			$o[] = '		xbMoveMarker(marker'.$markerId.', latlng.lat, latlng.lng);';
			$o[] = '		map'.$this->name.$this->id.'.setView(latlng, 7);';// set the zoom
			$o[] = '	}';
		}
		$o[] = '}));';
		
		$this->output[] = implode("\n", $o);
		return true;
	}
	
	public function renderFullScreenControl() {
				
		if ($this->fullscreen == 0) {
			return false;
		}
		
		$o 	= array();
		$o[] = 'map'.$this->name.$this->id.'.addControl(';
		
		$o[] = '	new L.Control.Fullscreen({';
		$o[] = '		position: \'topright\',';
		$o[] = '		title: {';
		$o[] = '			\'false\': \''.Text::_('XBMAPS_VIEW_FULLSCREEN').'\',';
		$o[] = '			\'true\': \''.Text::_('XBMAPS_EXIT_FULLSCREEN').'\'';
		$o[] = '		}';
		$o[] = '	})';
		
		$o[] = ');';
		
		$this->output[] = implode("\n", $o);
		return true;
		
	}
	
	public function renderCurrentPosition() {
				
		if ($this->currentposition == 0) {
			return false;
		}
		
		$o 	= array();
		
		$o[] = 'L.control.locate({';
		$o[] = '	position: \'topright\',';
		$o[] = '	strings: {';
		$o[] = '		\'title\': \''.Text::_('XBMAPS_CURRENT_POSITION').'\'';
		$o[] = '	},';
		$o[] = '	locateOptions: {';
		$o[] = '		enableHighAccuracy: true,';
		$o[] = '		watch: true,';
		$o[] = '	}';
		$o[] = '}).addTo(map'.$this->name.$this->id.');';
		
		$this->output[] = implode("\n", $o);
		return true;
		
	}
	
	public function renderEasyPrint() {
				
		if ($this->easyprint == 0) {
			return false;
		}
		
		$o 	= array();
		
		$o[] = 'map'.$this->name.$this->id.'.addControl(';
		$o[] = '	new L.easyPrint({';
		$o[] = '	   hideControlContainer: true,';
		$o[] = '	   sizeModes: [\'Current\', \'A4Portrait\', \'A4Landscape\'],';
		$o[] = '	   position: \'topleft\',';
		$o[] = '	   exportOnly: true';
		$o[] = '	})';
		$o[] = ');';
		
		
		$this->output[] = implode("\n", $o);
		return true;
		
	}
			
	/**
	 * @name renderTrack()
	 * @param $filename - string url of gpx/kml file to load
	 * @param string $colour - optional hex colour value for track (default to blue)
	 * @param boolean $fitbounds - if true adjust the map centre and zoom to fit the track
	 * @return boolean
	 * @desc adds a gpx  filename layer
	 */
	public function renderTrack($filename, $trkcolour = '', $fitbounds = false) {
		
		$mapname = 'map'.$this->name.$this->id;
		$o 	= array();
		//$file = Uri::root().'/xbmaps-tracks/'.$filename;
		$o[] = 'var gpx = \''.$filename.'\''; 
		$o[] = 'new L.GPX(gpx, { polyline_options: {color: \''.$trkcolour.'\'},';
		$o[] = 'async: true, marker_options: ';
		$o[] = '{ startIconUrl: \''.Uri::root().'/media/com_xbmaps/images/start.png\',';
		$o[] = ' endIconUrl: \''.Uri::root().'/media/com_xbmaps/images/end.png\',';
		$o[] = ' shadowUrl: \'\', iconSize: [24, 24],iconAnchor: [12, 24]}';
		$o[] = '}).on(\'loaded\',function(e) {e.target.bindPopup(\'<b>'.addslashes($trk->title).'</b><br />\'';
		$o[] = '+\'Distance: \'+parseInt(e.target.get_distance())/1000+\' km<br />\'';
		$o[] = '+((e.target.get_moving_time() > 0) ? \'Speed: \'+e.target.get_moving_speed().toFixed(2)+\' km/hr<br />Time: \'+e.target.get_duration_string(e.target.get_moving_time())+\'<br />\' : \'\')';
		$o[] = '+((e.target.get_elevation_gain() >5) ? \'Climbed: \'+Math.trunc(e.target.get_elevation_gain())+\' m\' : \'\'))});';
//		$o[] = '}).on(\'loaded\',function(e) {e.target.bindPopup(\'<b>'.addslashes($trk->title).'</b><br />Distance: \'+parseInt(e.target.get_distance())/1000+\' km<br />
//Speed: \'+e.target.get_moving_speed().toFixed(2)+\' km/hr<br />
//Time: \'+e.target.get_duration_string(e.target.get_moving_time())+\'<br />
//Climbed: \'+Math.trunc(e.target.get_elevation_gain())+\' m\')});';
		if ($fitbounds) {
			$o[] = '.on(\'loaded\', function(e) { '.$mapname.'.fitBounds(e.target.getBounds());})';
		}		
		$o[] = '.addTo('.$mapname.');';
		$this->output[] = implode("\n", $o);		
//add info on end icon popup		
		return true;
	}
	
	public function renderTracks( $tracks, $fitbounds = false) {
		$mapname = 'map'.$this->name.$this->id;
		$aliaslist = '';
		$o 	= array();
		foreach ($tracks as $trk) {
			$cleanalias = str_replace('-','_',$trk->alias);
			$o[] = 'var gpx = \''.Uri::root().$trk->gpx_filename.'\';';
			$o[] = 'var '.$cleanalias.' = new L.GPX(gpx,';
			$o[] = '{ polyline_options: {color: \''.$trk->track_colour.'\'},';
			$o[] = 'async: true, max_point_interval: 24000, marker_options: ';
			$o[] = '{ startIconUrl: \''.Uri::root().'/media/com_xbmaps/images/start.png\',';
			$o[] = ' endIconUrl: \''.Uri::root().'/media/com_xbmaps/images/end.png\',';
			$o[] = ' shadowUrl: \'\', iconSize: [24, 25],iconAnchor: [12, 25]}';
			$o[] = '}).on(\'loaded\',function(e) {e.target.bindPopup(\'<b>'.addslashes($trk->title).'</b><br />Distance: \'+parseInt(e.target.get_distance())/1000+\' km<br />Speed: \'+e.target.get_moving_speed().toFixed(2)+\' km/hr<br />Time: \'+e.target.get_duration_string(e.target.get_moving_time())+\'<br />Climbed: \'+Math.trunc(e.target.get_elevation_gain())+\' m\')});';
			
			$aliaslist .= $cleanalias.',';
		}
		$aliaslist = trim($aliaslist,',');
		$o[] = 'var tracksLayer = L.featureGroup(['.$aliaslist.']);';
		$o[] = 'tracksLayer.addTo('.$mapname.');';
		if ($fitbounds) {
			$o[] = 'setTimeout(function(){ '.$mapname.'.fitBounds(tracksLayer.getBounds()); }, 500);';
		}
		//     $o[] = $mapname.'.on(\'load\', function() { '.$mapname.'.fitBounds(tracksLayer.getBounds());});';
		$this->output[] = implode("\n", $o);
		//			$o[] = '';
	}
	
	public function renderMap() {
		$o = array();
		$o[] = 'jQuery(document).ready(function() {';
		$o[] = implode("\n", $this->output);
		$o[] = '})';
		Factory::getDocument()->addScriptDeclaration(implode("\n", $o));
	}
	
}

