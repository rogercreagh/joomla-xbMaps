<?php
/*******
 * @package xbMaps
 * @version 0.6.0.d 4th October 2021
 * @filesource site/views/map/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbmaps&view=category'.$itemid.'&id=';

$maplink = XbmapsHelperRoute::getMapLink('');
//$mapslink = XbmapsHelperRoute::getMapsLink();
$mapslink = 'index.php?option=com_xbmaps&view=maplist';

?>

<div class="xbmaps">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>

<?php  if($this->show_map_title) :?>
<div class="row-fluid">
	<div class="span12">
		<h1><?php echo $item->title; ?></h1>
 	</div>
</div>
<?php endif; ?>

<?php if (($this->show_map_desc=='2') || (($this->show_map_desc=='1') && ($this->show_map_info=='above'))) : ?>
	<?php echo $this->descbox; ?>
<?php endif; ?>
<?php if ($this->show_map_info=='above') :?>
	<?php echo $this->keybox;?>
<?php endif; ?>	
<div class="row-fluid">
	<?php if (($this->show_map_info=='left') && (($this->show_map_key) || ($this->show_map_desc==1))): ?>
    	<div class="span<?php echo $this->map_info_width; ?>">
    		<?php echo $this->keybox;?>
    		<?php if ($this->show_map_desc==1) {
    			echo $this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>
	<div class="span<?php echo (($this->show_map_info == 'left') || ($this->show_map_info=='right')) ? $this->mainspan : '12'; ?>">
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
				$map->renderTracks($item->tracks,$this->fit_bounds);
			}
			if (!empty($item->markers)) {
			    foreach ($item->markers as $mrk) {
			        $popuptitle =  '';
			        $popupdesc = '';
			        if ($mrk->show_popup!='') {
			        	$popuptitle = ($mrk->mktitle=='') ? '' : $mrk->mktitle;
			        	if ($mrk->mkshowdesc==1) {
			        		$popupdesc = ($mrk->mkdesc =='') ? '' : $mrk->mkdesc.'<br />';
			        	}
			        	if ($mrk->mkshowcoords==1) {
			        		$popupdesc .= '<hr />'.XbmapsGeneral::Deg2DMS($mrk->mklat).'<br />'.XbmapsGeneral::Deg2DMS($mrk->mklong,false);
			        	}
			        }
			        $popopen = ($mrk->show_popup == 2) ? 1 : 0;
			        switch ($mrk->markertype) {
			            case 1:
			                $image = $this->marker_image_path.'/'.$mrk->mkparams['marker_image'];
			                $map->setImageMarker($uid, $mrk->mklat, $mrk->mklong, $image, $popuptitle, $popupdesc,'','',$popopen);
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
			                $map->setDivMarker($uid, $mrk->mklat,$mrk->mklong,$div, $popuptitle,$popupdesc,'','',$popopen);
			                break;
			            default:
			            	$map->setMarker($uid, $popuptitle, $popupdesc, $mrk->mklat, $mrk->mklong,'','',$popopen);
			                
			                break;
			        }
			        
			    }
			}
			
			//$map->renderCurrentPosition();
			//other controls to be added
			$map->renderMap();
			?>
            <div id="xbmaps" style="margin:0;padding:0;">
            	<div align="center" style="margin:0;padding:0; <?php echo $this->borderstyle; ?>">
            		<div id="xbMap<?php echo $uid; ?>" style="<?php echo $this->mapstyle; ?>">
            		</div>
            	</div>
            </div>
			
	</div>
	<?php if (($this->show_map_info=='right') && (($this->show_map_key) || ($this->show_map_desc==1))): ?>
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

<div class="row-fluid xbmt16">
	<?php if ($this->show_cats >0) : ?>       
		<div class="span4">
			<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_CATEGORY'); ?></div>
				<div class="pull-left">
					<?php if($this->show_cats==2) : ?>
						<a class="label label-success" href="<?php echo JRoute::_($clink.$item->catid); ?>">
    						<?php echo $item->category_title; ?></a>
    				<?php else: ?>
    					<span class="label label-success"><?php echo $item->category_title; ?></span>
    				<?php endif; ?>		
				</div>
	        </div>
        <?php endif; ?>
        <?php if (($this->show_tags) && (!empty($item->tags))) : ?>
        	<div class="span<?php echo ($this->show_tags>0) ? '8' : '12'; ?>">
				<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_TAGS'); ?></div>
				<div class="pull-left">
					<?php  $tagLayout = new JLayoutFile('joomla.content.tags');
	    				echo $tagLayout->render($item->tags); ?>
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
			<a href="<?php echo JRoute::_(XbmapsHelperRoute::getMapLink($item->prev)); ?>" class="btn btn-small">
				<?php echo Text::_('XBMAPS_PREV'); ?></a>
	    <?php endif; ?>
	</div>
	<div class="span8"><center>
		<a href="<?php echo JRoute::_($mapslink); ?>" class="btn btn-small">
			<?php echo JText::_('XBMAPS_MAPSLIST'); ?></a></center>
	</div>
	<div class="span2">
		<?php if($item->next > 0) : ?>
			<a href="<?php echo JRoute::_(XbmapsHelperRoute::getMapLink($item->next)); ?>" class="btn btn-small pull-right">
				<?php echo JText::_('XBMAPS_NEXT'); ?></a>
	    <?php endif; ?>
	</div>
</div>
	<div class="clearfix"></div>
	<?php echo XbmapsGeneral::credit();?>

</div>
