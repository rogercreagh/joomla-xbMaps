<?php 
/*******
 * @package xbMaps
 * @version 0.3.0.h 22nd September 2021
 * @filesource site/views/tags/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$xblink = 'index.php?option=com_xbmaps&view=';

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getTagsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$tvlink = $xblink.'tag'.$itemid.'&id=';

$itemid = XbmapsHelperRoute::getMapsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$mapslink = $xblink.'maplist'.$itemid.'&tagid=';

//$itemid = XbmapsHelperRoute::getMarkersRoute();
//$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
//$rllink = $xblink.'markers'.$itemid.'&tagid=';

$itemid = XbmapsHelperRoute::getTracksRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$trkslink = $xblink.'tracklist'.$itemid.'&tagid=';

?>

<div class="xbmaps">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=tags'); ?>" method="post" name="adminForm" id="adminForm">
	
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbtags">	
			<thead>
				<tr>
					<th>
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TAG', 'path', $listDirn, $listOrder );?>&nbsp;
						<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TITLE', 'title', $listDirn, $listOrder );?>
					</th>
				<?php  if ($this->show_desc != 0) : ?>      
					<th class="hidden-phone"><?php echo JText::_('XBMAPS_DESCRIPTION');?></th>
				<?php endif; ?>
				<?php if ($this->maptags) : ?>				
					<th class="center" style="width:50px;"><?php echo HTMLHelper::_('grid.sort', 'XBMAPS_MAPS', 'mapcnt', $listDirn, $listOrder );?></th>
				<?php endif; ?>
				<?php if ($this->mrktags) : ?>
					<th class="center" style="width:50px;"><?php echo HTMLHelper::_('grid.sort', 'XBMAPS_MARKERS', 'mrkcnt', $listDirn, $listOrder );?></th>
				<?php endif; ?>
				<?php if ($this->trktags) : ?>
					<th class="center" style="width:50px;"><?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TRACKS', 'trkcnt', $listDirn, $listOrder );?></th>
				<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
				<tr>
	 				<td>
						<p class="xbml10">
 						<?php  if ($this->show_parent != 0) : ?>
 						    <span class="xbnote xb09">
 						    <?php if (substr_count($item->path,'/')>0) {
 						    	$ans = substr($item->path, 0, strrpos($item->path, '/'));
 						    	echo str_replace('/',' - ',$ans).' - ';
 						    } ?>
                        	</span>
						<?php endif; //show_parent?>
	    				<span  class="xb11 xbbold">
	    					<a href="<?php echo JRoute::_($tvlink . $item->id); ?>" title="Details"
    						class="label label-info" style="padding:2px 8px;">
    						<span class="xb11"><?php echo $item->title; ?></span>
	    					</a>
	    				</span>
	    				</p>
	    			</td>
				<?php  if ($this->show_desc != 0) : ?>      
					<td class="hidden-phone"><?php echo $item->description; ?></td>
				<?php endif; ?>
				<?php if ($this->maptags) : ?>				
	    			<td class="center">
	   					<?php if ($item->mapcnt >0) : ?>
	   						<a class="badge mapcnt" href="<?php  echo JRoute::_($mapslink.$item->id); ?>"><?php echo $item->mapcnt; ?></a>
	   					<?php endif; ?>
	   				</td>
				<?php endif; ?>
				<?php if ($this->mrktags) : ?>				
	    			<td class="center">
	   					<?php if ($item->mrkcnt >0) : ?> 
	   						<span class="badge mrkcnt"><?php echo $item->mrkcnt; ?></span>
	   					<?php endif; ?>
	   				</td>
				<?php endif; ?>
				<?php if ($this->trktags) : ?>				
	    			<td class="center">
	   					<?php if ($item->trkcnt >0) : ?> 
	   						<a class="badge trkcnt" href="<?php  echo JRoute::_($trkslink.$item->id); ?>"><?php echo $item->trkcnt; ?></a>
	   					<?php endif; ?>
	   				</td>
				<?php endif; ?>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>
</div>

