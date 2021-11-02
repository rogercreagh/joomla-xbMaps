/*****
 * @package xbMaps
 * @version 0.8.0.i 26th October 2021
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

function xbSaveForm(doW3w=true) {
	//used by helper::endZoom, helper::mapAreaClick and 
	//NB the field names are hard coded here, map form uses centre_lat... marker uses lat...
	// marker_w3w field is only present on marker form and when w3w_api is set
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
	if (doW3w) {
       var fld = window.parent.document.getElementById("jform_params_marker_w3w");
		fld.value=window.w3w;
	}
}

function xbMoveMarker(marker, lat, lng, display,  lang='en', doCoordBox=false, doForm=false, doPop=true) {
// calls xbMarkerPopup, xbMarkerCoordInfo and xbSaveForm as required
	window.lat = lat.toFixed(6);
	window.lng = lng.toFixed(6);
	window.dmslat = deg2dms(lat,'latitude');
	window.dmslng = deg2dms(lng,'longitude');
	var newLatLng = new L.LatLng(lat, lng);
	marker.setLatLng(newLatLng);
	if (display>3) {
    	what3words.api.convertTo3wa({lat:  window.lat, lng: window.lng}, lang).then(function(response)
			{ window.w3w = response.words;
				if (doPop) xbMarkerPopup(marker,display,lang);
				if (doCoordBox) xbMarkerCoordInfo(display);
				if (doForm) xbSaveForm();
			 }).catch(error => alert(error.message));; 		
	} else {
		if (doPop) xbMarkerPopup(marker,display);
		if (doCoordBox) xbMarkerCoordInfo(display);
		if (doForm) xbSaveForm(false);
	}
}

function xbMapCoordInfo() {	    
	var coordMsg = '<div class="xbmsgsuccess" style="text-align:left;">';
	coordMsg += '<span class="xbpr20"><i>Lat:</i> '+window.lat+'</span><i>Long:</i> '+window.lng+'<br />';
	coordMsg += '<span class="xbpr20"><i>Lat:</i> '+window.dmslat+'</span><i>Long:</i> '+window.dmslng+'<br />';
	coordMsg += 'Zoom: '+ window.zoom +'</div>';		
	jQuery('#coordInfo', window.parent.document).html(coordMsg);	    
}

function xbMarkerCoordInfo(display=7) {	    
	var coordMsg = '<div class="xbmsgsuccess" style="text-align:left;">';
	if ((display & 1)==1) coordMsg += '<span class="xbpr20"><i>Lat:</i> '+window.lat+'</span><i>Long:</i> '+window.lng+'<br />';
	if ((display & 2)==2) coordMsg += '<span class="xbpr20"><i>Lat:</i> '+window.dmslat+'</span><i>Long:</i> '+window.dmslng+'<br />';
	if ((display & 4)==4) coordMsg += '<i>What 3 Words</i>: ///<b>'+window.w3w+'</b>';
	coordMsg += '</div>';		
	jQuery('#coordInfo', window.parent.document).html(coordMsg);	    
}

function xbMarkerPopup(marker,display, lang='en') {
	var popupContent = '<b>Location</b><br />'
	var deg = (display & 1)==1;
	var dms = (display & 2)==2;
	var w3w = (display > 3);
	if (deg) {		
		popupContent += '<span class="xbpr20"><i>Lat:</i> '+window.lat+'</span><i>Long:</i> '+window.lng+'<br />';
	}
	if (dms) {
		popupContent += '<span class="xbpr20"><i>Lat:</i> '+window.dmslat+'</span><i>Long:</i> '+window.dmslng+'<br />';		
	}
	if (w3w) {
    	what3words.api.convertTo3wa({lat:  window.lat, lng: window.lng}, lang).then(function(response)
			{ window.w3w = response.words;
				marker.bindPopup(popupContent+'<i>w3w</i>: ///<b>'+response.words+'</b>').openPopup();				
			 }).catch(error => alert(error.message));; 
	} else {
		window.w3w = '';
		marker.bindPopup(popupContent).openPopup();
    }
}

function xbUpdateMarkerW3w(w3w) {
	what3words.api.convertToCoordinates(w3w).then(function(response) 
		{ var coords=response.coordinates; 
         	w3w = w3w.replace(/^[\/\s]+|\s+$/g, '')
			window.lat = coords.lat;
			window.lng = coords.lng;
			window.w3w = w3w;
			xbSaveForm();
			document.getElementById('task').value='marker.apply';
			document.adminForm.submit();
  	}).catch(error => alert(error.message));
}
