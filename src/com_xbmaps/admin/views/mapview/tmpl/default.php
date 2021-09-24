<?php
/*******
 * @package xbMaps
 * @version 0.4.0 24th September 2021
 * @filesource admin/views/mapview/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$item = $this->item;
$uid = uniqid();

$map = new XbMapHelper($uid,$this->params);
$map->loadAPI($this->clustering,$this->homebutton);
$map->loadXbmapsJS();
$map->createMap($item->centre_latitude, $item->centre_longitude, $item->default_zoom);
$map->setMapType($item->map_type);
if ($this->clustering) {
    $map->setMarkerClusterer();
}
if ($this->centremarker>0) {
    $map->setMarker($uid.'centre', $item->title, XbmapsGeneral::Deg2DMS($item->centre_latitude).'<br />'.XbmapsGeneral::Deg2DMS($item->centre_longitude,false), $item->centre_latitude, $item->centre_longitude,'','','',1);
}
if ($item->params->get('map_search',0)>0) {
    $map->renderSearch();
}
if ($item->params->get('map_full_screen',0)>0) {
    $map->renderFullScreenControl();
}
if ($item->params->get('map_easyprint',0)>0) {
    $map->renderEasyPrint();
}

if (!empty($item->tracks)) {
	$map->renderTracks($item->tracks,$this->fit_bounds);
}

if (!empty($item->markers)) {
    foreach ($item->markers as $mrk) {
//        Factory::getApplication()->enqueueMessage('<pre>'.print_r($mrk,true).'</pre>');
    	$popuptitle =  '';
    	$popupdesc = '';
    	if ($mrk->show_popup!='') {
    		$popuptitle = ($mrk->mktitle=='') ? '' : $mrk->mktitle;
    		$popupdesc = ($mrk->mkdesc=='') ? '' :$mrk->mkdesc;
    	}
        switch ($mrk->markertype) {
            case 1:
                $image = $this->marker_image_path.'/'.$mrk->mkparams['marker_image'];
                $map->setImageMarker($uid, $mrk->mklat, $mrk->mklong, $image, $popuptitle, $popupdesc);
                break;
            case 2:
                $outer = $mrk->mkparams['marker_outer_icon'];
                $inner = $mrk->mkparams['marker_inner_icon'];
                $outcol = $mrk->mkparams['marker_outer_colour'];
                $incol = $mrk->mkparams['marker_inner_colour'];
                $insize = '';
                if ($mrk->mkparams['marker_outer_icon']=='fas fa-map-marker') {
                    $insize = 'line-height:1.75em;font-size:0.8em;';
                }
                
                $div = '<div><span class="fa-stack fa-2x" style="margin-left:-1em;margin-top:-2em;font-size:12pt;">';
                $div .= '<i class="'.$outer.' fa-stack-2x" style="color:'.$outcol.';"></i>';
                
                $div .= '<i class="'.$inner.' fa-stack-1x fa-inverse" style="color:'.$incol.';'.$insize.'"></i>';
                $div .= '</span></div>';
                $map->setDivMarker($uid, $mrk->mklat,$mrk->mklong,$div, $popuptitle,$popupdesc,'','',1);
                break;
            default:
            	$map->setMarker($uid, $popuptitle, $popupdesc, $mrk->mklat, $mrk->mklong,'','','',1);
                
                break;
       }
        
    }
}

$map->renderMap();

?>
<div class="xbmaps">
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=mapview&id='.$item->id); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row-fluid">
		<?php if (!empty( $this->sidebar)) : ?>
        	<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
        	</div>
        	<div id="j-main-container" class="span10">
		<?php else : ?>
        	<div id="j-main-container" class="span12">
		<?php endif;?>
				<h1><?php echo $item->title; ?></h1>
        		<div class="row-fluid">
        			<div class="span9" style="margin:0;padding:0; <?php echo $this->borderstyle; ?>">
        				<div id="xbMap<?php echo $uid; ?>" 
        					style="margin:0;padding:0;
        					height:<?php echo $item->map_height > 0 ? $item->map_height.$item->height_unit.';' : '67vh;'; ?>
        					width:<?php echo $item->map_width > 0 ? $item->map_width.$item->width_unit.';' : '100%;'?>">
        				</div>
        			</div>
        		 	<div class="span3">
        		 		<p><span class="xbnit">Map description: </span>
        		 		<?php echo $item->description; ?>
        		 		<hr />
        		 		<p>Markers</p>
        		 		<ul>
       						<?php foreach ($item->markers as $mrk) : ?>
       						
       							<li><span <?php echo $mrk->mkstate!=1 ? ' class="xbhlt"' : ''; ?>>
           							<?php $pv = '<img src="/media/com_xbmaps/images/marker-icon.png" />';
           							switch ($mrk->markertype) {
           							    case 1:
           							        $pv = '<img src="'.$this->marker_image_path.'/'.$mrk->mkparams['marker_image'].'" style="height:20px;" />';
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
           							echo $pv.'&nbsp;';
           							?>
       								<b><?php echo $mrk->linkedtitle; ?></b> - <?php echo $mrk->mkdesc; ?>
       								<br /><span class="xbnit">Lat:&nbsp;<?php echo $mrk->mklat; ?> Long:&nbsp;<?php echo $mrk->mklong; ?></span>
       							</span></li>
        		 			<?php  endforeach; ?>
        		 		</ul>
        		 		<hr />
        		 		<p>Tracks</p>
        		 		<ul>
       						<?php foreach ($item->tracks as $trk) : ?>
       							<li><span <?php echo $trk->tstate!=1 ? ' class="xbhlt"' : ''; ?>>
       							<i class="fas fa-project-diagram" style="color:<?php echo $trk->track_colour; ?>"></i>&nbsp;
       							<b><?php echo $trk->linkedtitle; ?></b> - <?php echo $trk->description; ?>
       							</span></li>
        		 			<?php  endforeach; ?>
        		 		</ul>
            		</div>
    		 	</div>
			</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>

<div id="xbmaps" style="margin:0;padding:0;">
	<div align="center" style="margin:0;padding:0 <?php echo $this->borderstyle; ?>">
		<div id="xbMap<?php echo $uid; ?>" 
			style="margin:0;padding:0;
				width:<?php echo $item->map_width > 0 ? $item->map_width.$item->width_unit.';' : '100%;';?>
				height:<?php echo $item->map_height > 0 ? $item->map_height.$item->height_unit.';' : '50vh;'; ?>">
		</div>
	</div>
</div>
			
