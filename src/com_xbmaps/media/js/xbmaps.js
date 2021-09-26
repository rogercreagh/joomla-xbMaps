/*****
 * @package xbMaps
 * @version 0.1.2.a 30th August 2021
 * @filesource media/js/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*****/

function setDMSLongitude(inputValue) {
	longitudeValue = deg2dms(inputValue, 'longitude');
	window.top.document.forms.adminForm.elements.gpslongitude.value = longitudeValue;
}

function setDMSLatitude(inputValue) {
	latitudeValue = deg2dms(inputValue, 'latitude');
	window.top.document.forms.adminForm.elements.gpslatitude.value = latitudeValue;
}

function setDMSLongitudeJForm(inputValue) {
	longitudeValue = deg2dms(inputValue, 'longitude');
	if (window.parent) window.parent.xbSelectMapArea_jform_dmslongitude(longitudeValue);
}

function setDMSLatitudeJForm(inputValue) {
	latitudeValue = deg2dms(inputValue, 'latitude');
	if (window.parent) window.parent.xbSelectMapArea_jform_dmslatitude(latitudeValue);
}

function deg2dms(degval, latlong) {
	var isSW = false;
	var axis = '';
	var deg = 0;
	var min = 0;
	var sec = 0.0;
	var dmsstr = '';
	isSW = (degval < 0);
	if (latlong == 'latitude') {
		axis = (isSW) ? ' S' : ' N';
	} else {
		axis = (isSW) ? ' W' : ' E';
	}
	value = Math.abs(degval);
	deg = Math.floor(value);
	value = (value - deg)*60;
	min = Math.floor(value);
	sec = ((value - min)*60).toFixed(3);
	dmsstr = Math.abs(deg) + '\u00b0' + '\u0020' + min + '\u0027' + '\u0020' + sec + '\u0022' + axis;
	return dmsstr;	
}

function dms2deg(deg, min, sec, dir) {
	var value = 0.0;
	var neg = -1;
	value = deg + (min/60) + (sec/3600);
	neg = ((dir='W') || (dir='S')) ? -1 : 1;
	value = value * neg;
	return value.toFixed(6);
}

function dmsstr2deg(dmsstr) {
	var dir = 'N';
	var deg = 0;
	var min = 0;
	var sec = 0.0;
	var strarr = trim(dmsstr.split(" "));
	if (strarr.length>0) deg = parseInt(trim(strarr[0]));
	var dir = trim(strarr[strarr.length-1]).toUpperCase;
	dir = dir.charAt(0); 
	var neg = ((dir='W') || (dir='S')) ? -1 : 1;
	deg = deg * neg;
	if (strarr.length>1) min = parseInt(trim(strarr[1]));
	if (strarr.length>2) sec = parseFloat(trim(strarr[2]));
	if ((dir == 'E') || (dir === 'W')) {
		dir = 'longitude';
	} else {
		dir = 'latitude';
	}
	return dms2deg(deg, min, sec, dir);
}

function xbmapsSaveForm() {
	//NB the field names are hard coded here, map form uses centre_lat... marker uses lat...
	var fldLat = jQuery('#jform_centre_latitude_id', window.parent.document);
	if (fldLat) fldLat.val(window.lat);
	fldLat = jQuery('#jform_latitude_id', window.parent.document);
	if (fldLat) fldLat.val(window.lat);
	var fldLng = jQuery('#jform_centre_longitude_id', window.parent.document);
	if (fldLng) fldLng.val(window.lng);
	fldLng = jQuery('#jform_longitude_id', window.parent.document);
	if (fldLng) fldLng.val(window.lng);
	var fldZoom = jQuery('#jform_default_zoom_id', window.parent.document);
	if (fldZoom) fldZoom.val(window.zoom);		
	var fldDmsLat = jQuery('#jform_dmslatitude_id', window.parent.document);
	if (fldDmsLat) fldDmsLat.val(window.dmslat);		
	var fldDmsLng = jQuery('#jform_dmslongitude_id', window.parent.document);
	if (fldDmsLng) fldDmsLng.val(window.dmslng);		
}

function xbMoveMarker(marker, lat, lng) {
	window.lat = lat.toFixed(6);
	window.lng = lng.toFixed(6);
	window.dmslat = deg2dms(lat,'latitude');
	window.dmslng = deg2dms(lng,'longitude');
	var newLatLng = new L.LatLng(lat, lng);
	marker.setLatLng(newLatLng);
}

function xbModalCoordInfo() {	    
	var coordMsg = '<div class="xbmsgsuccess" style="text-align:left;">';
	coordMsg += 'Lat: '+window.lat+' ('+window.dmslat+')<br />Long: '+window.lng+' ('+window.dmslng+')';
	coordMsg += '<br />Zoom: '+ window.zoom +'</div>';		
	coordMsg += '</div>';   
	jQuery('#coordInfo', window.parent.document).html(coordMsg);	    
}

function xbMarkerCoordInfo() {	    
	var coordMsg = '<div class="xbmsgsuccess" style="text-align:left;">';
	coordMsg += 'Lat: '+window.lat+' ('+window.dmslat+'),&nbsp;&nbsp;Long: '+window.lng+' ('+window.dmslng+')';
	coordMsg += '</div>';  
	jQuery('#coordInfo', window.parent.document).html(coordMsg);	    
}

function xbSaveTrackStats(trkuid, dist, movetime, speed, climbed) {
	
}

function xbSetDirectory(srcCtrl,destCtrl) {
	document.getElementById(destCtrl).value = document.getElementById(srcCtrl).value;
}

