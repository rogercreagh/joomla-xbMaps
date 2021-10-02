<?php 
/*******
 * @package xbMaps
 * @version 0.6.0.a 2nd October 2021
 * @filesource site/views/catlist/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$catlink = 'index.php?option=com_xbmaps&view=category'.$itemid.'&id=';

$itemid = XbmapsHelperRoute::getMapsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$maplink = 'index.php?option=com_xbmaps&view=maplist' . $itemid.'&catid=';

$itemid = XbmapsHelperRoute::getTracksRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$trklink = 'index.php?option=com_xbmaps&view=tracklist' . $itemid.'&catid=';

$prevext='';
?>
<div class="xbmaps">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=catlist'); ?>" method="post" name="adminForm" id="adminForm">

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbcats">	
			<thead>
				<tr>
					<th>
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_CATEGORY', 'path', $listDirn, $listOrder );?>&nbsp;
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TITLE', 'title', $listDirn, $listOrder );?>
					</th>
					<th class="hidden-phone"><?php echo JText::_('XBMAPS_DESCRIPTION');?></th>
				<?php if ($this->mapcats) : ?>				
					<th class="center" style="width:50px;">
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_MAPS', 'mapcnt', $listDirn, $listOrder );?>
					</th>
				<?php endif; ?>
				<?php if ($this->mrkcats) : ?>
					<th class="center" style="width:50px;">
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_MARKERS', 'trkcnt', $listDirn, $listOrder );?>
					</th>
				<?php endif; ?>
				<?php if ($this->trkcats) : ?>
					<th class="center" style="width:50px;">
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TRACKS', 'mrkcnt', $listDirn, $listOrder );?>
					</th>
				<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<?php if ($item->allcnt>0) : ?>
				<tr>
	 				<td>
						<p class="xbml20">
 						<?php  if ($this->show_parent != 0) : ?>      
					<span class="xbnote xb09"> 
 						    <?php if (substr_count($item->path,'/')>0) {
 						    	$ans = substr($item->path, 0, strrpos($item->path, '/'));
 						    	echo str_replace('/',' - ',$ans).' - ';
 						    } ?>
					 </span>
 						<?php endif; //show_parent?>
    					<a href="<?php echo JRoute::_($catlink . $item->id.'&ext='.$item->extension); ?>" title="Details" 
    						class="label label-success" style="padding:2px 8px;">
    						<span class="xb11"><?php echo $item->title; ?></span>
    					</a>
	    				</p>
	    			</td>
					<td class="hidden-phone"><?php echo $item->description; ?></td>
				<?php if ($this->mapcats) : ?>
	    			<td class="center">
	   					<?php if ($item->mapcnt >0) : ?> 
	   						<a href="<?php echo JRoute::_($maplink.$item->id); ?>" class="badge mapcnt"><?php echo $item->mapcnt; ?></a>
	   					<?php endif; ?>
	   				</td>
				<?php endif; ?>
				<?php if ($this->mrkcats) : ?>
	    			<td class="center">
	   					<?php if ($item->mrkcnt >0) : ?> 
	   						<span class="badge percnt"><?php echo $item->mrkcnt; ?></span>
	   					<?php endif; ?>
	   				</td>
				<?php endif; ?>
				<?php if ($this->trkcats) : ?>
	    			<td class="center">
	   					<?php if ($item->trkcnt >0) : ?> 
	   						<a href="<?php echo JRoute::_($trklink.$item->id); ?>" class="badge trkcnt"><?php echo $item->trkcnt; ?></a>
	   					<?php endif; ?>
	   				</td>
				<?php endif; ?>
				</tr>
				<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>

	<?php endif; //got items?>
		
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>
</div>
