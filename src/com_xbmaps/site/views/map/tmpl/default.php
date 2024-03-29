<?php
/*******
 * @package xbMaps Component
 * @version 1.5.0.1 2nd January 2024
 * @filesource site/views/map/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/geocoder.php');

use What3words\Geocoder\Geocoder;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbmaps&view=category'.$itemid.'&id=';

$mapslink = 'index.php?option=com_xbmaps&view=maplist';

?>
<script>
  	function handleClick(cb) {
		document.cookie = cb.id + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
		if (cb.checked == true) {
			document.cookie=cb.id+"=1; path=/;";
		} else {
			document.cookie=cb.id+"=0; path=/;";
		}
  		location.reload();
    }
</script>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>

<div class="xbmaps">
<?php if (!$this->tmplcomp) : ?>
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
<?php endif; ?>
<form action="<?php echo Route::_('index.php?option=com_xbmaps&view=map&id='.$item->id); ?>" method="post" name="adminForm" id="adminForm">       
<?php  if($this->show_map_title) :?>
<div class="row-fluid">
	<div class="span12">
		<h1><?php echo $item->title; ?></h1>
 	</div>
</div>
<?php endif; ?>

<?php if ( ($this->show_map_desc=='2') || ( ($this->show_map_desc=='1') && ($this->show_map_info=='above') ) ) : ?>
	<?php echo $this->descbox; ?>
<?php endif; ?>
<?php if ($this->show_map_info=='above') :?>
	<?php echo $this->keybox;?>
<?php endif; ?>	
<div class="row-fluid">
	<?php if ($this->show_map_info ===  'left'): ?>
    	<div class="span<?php echo $this->map_info_width; ?>">
    		<?php echo $this->keybox;?>
    		<?php if ($this->show_map_desc==1) {
    			echo $this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>
	<div class="span<?php echo $this->mainspan; ?>">
		<?php $uid = uniqid();
			$map = new XbMapHelper($uid,$item->params);
			$map->loadAPI($this->clustering,$this->homebutton);
			$map->loadXbmapsJS();
			$map->createMap($item->centre_latitude, $item->centre_longitude, $item->default_zoom);
			$map->setMapType($item->map_type);
			if ($this->clustering) {
				$map->setMarkerClusterer();
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
			    $showtracks = array();
			    foreach ($item->tracks as $track) {
			        if ($item->trackstate[$track->id] == 1) {
			            $showtracks[] = $track;
			        }
			    }
				if (!empty($showtracks)) $map->renderTracks($showtracks,$this->fit_bounds,0,$this->show_track_popover);
			}
			if (!empty($item->markers)) {
			    foreach ($item->markers as $mrk) {
			        $popuptitle =  '';
			        $popupdesc = '';
			        	$popuptitle = ($mrk->mktitle=='') ? '' : $mrk->mktitle;
			        	if ($mrk->mkshowdesc==1) {
			        		$popupdesc = ($mrk->mkdesc =='') ? '' : $mrk->mkdesc.'<br />';
			        	}
			        	$disp = $mrk->mkshowcoords;
//			        	Factory::getApplication()->enqueueMessage($disp);
			        	if ($disp=='') $disp=0;
			        	if ($disp>0) $popupdesc .= '<hr /><b>'.Text::_('XBMAPS_LOCATION').'</b></br>';
			        	
			        	if (($disp & 1)==1) {
			        		$popupdesc .= '<span style="padding-right:20px"><i>Lat:</i> '.$mrk->mklat.'</span><i>Long:</i> '.$mrk->mklong.'<br />';
			        	}
			        	if (($disp & 2)==2) {
			        		$popupdesc .= '<span style="padding-right:20px"><i>Lat:</i> '.XbmapsGeneral::Deg2DMS($mrk->mklat).'</span><i>Long:</i> '.XbmapsGeneral::Deg2DMS($mrk->mklong,false).'<br />';
			        	}
			        	if ($disp > 3) {
			        		$api = new Geocoder($this->w3w_api);
			        		$w3w = $api->convertTo3wa($mrk->mklat,$mrk->mklong,$this->w3w_lang)['words'];
			        		$popupdesc .= '<i>w3w</i>: ///<b>'.$w3w.'</b>';
			        	}
			        	switch ($mrk->markertype) {
			            case 1:
			                $image = $this->marker_image_path.'/'.$mrk->mkparams['marker_image'];
			                $map->setImageMarker($uid, $mrk->mklat, $mrk->mklong, $image, $popuptitle, $popupdesc,'','',0);
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
			                $map->setDivMarker($uid, $mrk->mklat,$mrk->mklong,$div, $popuptitle,$popupdesc,'','',0);
			                break;
			            default:
			            	$map->setMarker($uid, $mrk->mklat, $mrk->mklong, $popuptitle, $popupdesc,'','',0);
			                
			                break;
			        }
			        
			    }
			}
			if ($this->map_click_marker>0) {
//				$map->setMarker($uid,'52.507373', '-24.301758', '', '', '','','',0); //initial pos mid-Atlantic, prob off screen
				$map->setImageMarker($uid,'52.50','-24.301','media/com_xbmaps/images/greendot-20x20.png' ,'', '', '','','',0); //initial pos mid-Atlantic, prob off screen
				$map->mapClick($uid,$this->map_click_marker);
			}
			if ($this->show_scale) {
				$map->renderScale(250);
			}
			
			
			//$map->renderCurrentPosition();
			//other controls to be added
			$map->renderMap();
			?>
            <div id="xbmaps" style="margin:0;padding:0;">
            	<div align="center" style="margin:0;padding:0; <?php echo $this->borderstyle; ?>">
            		<div id="xbMap<?php echo $uid; ?>" style="<?php echo $this->mapstyle; ?>">
            		</div>
            		<?php if ($this->map_click_marker) : ?>
            			<div class="xbnit xb08">Click on map to see coordinates of location clicked</div>
            		<?php endif; ?>
            	</div>
            </div>
			
	</div>
	<?php if ($this->show_map_info === 'right'): ?>
    	<div class="span<?php echo $this->map_info_width; ?>">
    		<?php echo $this->keybox;?>
    		<?php if ($this->show_map_desc==1) {
    			echo $this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>
	</div>
	<?php if ($this->show_map_info=='below') :?>
		<?php echo $this->keybox;?>
	<?php endif; ?>	
	<?php if (($this->show_map_desc=='3') || (($this->show_map_desc=='1') && ($this->show_map_info=='below'))) : ?>
		<?php echo $this->descbox; ?>
	<?php endif; ?>

<?php if (!$this->tmplcomp) : ?>
<div class="row-fluid xbmt16">
	<?php if ($this->show_cats >0) : ?>       
		<div class="span4">
			<div class="xbbox xbboxyell">
				<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_CATEGORY'); ?></div>
				<div class="pull-left">
					<?php if($this->show_cats==2) : ?>
						<a class="label label-success" href="<?php echo Route::_($clink.$item->catid); ?>">
    						<?php echo $item->category_title; ?></a>
    				<?php else: ?>
    					<span class="label label-cat"><?php echo $item->category_title; ?></span>
    				<?php endif; ?>		
				</div>
				<div class="clearfix"></div>
	        </div>
	    </div>
        <?php endif; ?>
        <?php if (($this->show_tags) && (!empty($item->tags))) : ?>
        	<div class="span<?php echo ($this->show_tags>0) ? '8' : '12'; ?>">
   			<div class="xbbox xbboxmag">
				<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_TAGS'); ?></div>
				<div class="pull-left">
					<?php  $tagLayout = new FileLayout('joomla.content.tags');
	    				echo $tagLayout->render($item->tags); ?>
				</div>
				<div class="clearfix"></div>
        	</div>
        	</div>
		<?php endif; ?>
</div>
<div class="row-fluid xbbox xbboxgrey">
	<div class="span2">
		<?php if (($item->prev>0) || ($item->next>0)) : ?>
		<span class="hasTooltip xbinfo" title 
			data-original-title="<?php echo JText::_('XBMAPS_INFO_PREVNEXT'); ?>" >
		</span>&nbsp;
		<?php endif; ?>
		<?php if($item->prev > 0) : ?>
			<a href="<?php echo Route::_(XbmapsHelperRoute::getMapLink($item->prev)); ?>" class="btn btn-small">
				<?php echo Text::_('XBMAPS_PREV'); ?></a>
	    <?php endif; ?>
	</div>
	<div class="span8"><center>
		<a href="<?php echo Route::_($mapslink); ?>" class="btn btn-small">
			<?php echo JText::_('XBMAPS_MAPSLIST'); ?></a></center>
	</div>
	<div class="span2">
		<?php if($item->next > 0) : ?>
			<a href="<?php echo Route::_(XbmapsHelperRoute::getMapLink($item->next)); ?>" class="btn btn-small pull-right">
				<?php echo JText::_('XBMAPS_NEXT'); ?></a>
	    <?php endif; ?>
	</div>
</div>
</form>
	<div class="clearfix"></div>

	<?php echo XbmapsGeneral::credit();?>
<?php endif; ?>
<div id="mapid" data=""></div>
<?php echo LayoutHelper::render('xbpvmodal.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbmaps/layouts');   ?>
</div>

