<?php
/*******
 * @package xbMaps
 * @version 0.3.0.e 19th September 2021
 * @filesource admin/views/tagslist/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId         = $user->get('id');
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));

$tageditlink = 'index.php?option=com_tags&view=tag&task=tag.edit&id=';
$taginfolink = 'index.php?option=com_xbmaps&view=taginfo&id=';
$mapslink = 'index.php?option=com_xbmaps&view=maps&tagid=';
$markerslink = 'index.php?option=com_xbmaps&view=markers&tagid=';
$trackslink = 'index.php?option=com_xbmaps&view=tracks&tagid=';
?>
<form action="index.php?option=com_xbmaps&view=tagslist" method="post" id="adminForm" name="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container">
	<?php endif;?>
	
	<div>
      <h3><?php echo Text::_('XBMAPS_TAGSPAGE_TITLE'); ?></h3>
      <p><?php echo Text::_('XBMAPS_TAGSPAGE_INFO'); ?></p>
      </div>
	
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. Text::_(($fnd==1)?'XBMAPS_TAG':'XBMAPS_TAGS').' '.Text::_('XBMAPS_FOUND'); ?>
		</p>
	</div>
	<div class="clearfix"></div>

	<?php
        // Search tools bar
        echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>	

<table class="table table-striped table-hover">
<thead>
<tr>
					<th class="hidden-phone center" style="width:25px;">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
			<th width="5%">
				<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_HIERARCHY', 'path', $listDirn, $listOrder );?>&nbsp;
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TAG', 'title', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo Text::_('XBMAPS_DESCRIPTION') ;?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_MAPS', 'mapcnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_MARKERS', 'mrkcnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TRACKS', 'trkcnt', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo Text::_('XBMAPS_OTHERS') ;?>
			</th>
			<th class="nowrap hidden-tablet hidden-phone" style="width:45px;">
				<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>
			</th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->items as $i => $item) :
				$canCheckin = $user->authorise('core.manage', 'com_checkin')
			?>
			<tr class="row<?php echo $i % 2; ?>" >	
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
				<td class="center">
					<div class="btn-group">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'tag.', false, 'cb'); ?>
							<?php if ($item->note!=''){ ?>
								<span class="btn btn-micro active hasTooltip" title="" 
									data-original-title="<?php echo '<b>'.Text::_( 'XBMAPS_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
									<i class="icon- xbinfo"></i>
								</span>
							<?php } else {?>
								<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
							<?php } ?>
					</div>
				</td>
 				<td>
					<?php if ($item->checked_out) {
    					$couname = Factory::getUser($item->checked_out)->username;
    					echo JHtml::_('jgrid.checkedout', $i, Text::_('XBMAPS_OPENED_BY').': '.$couname, $item->checked_out_time, 'tags.', false);
    				} ?>
					<span class="xbnote"> 
 					<?php 	$path = substr($item->path, 0, strrpos($item->path, '/'));
						$path = str_replace('/', ' - ', $path);
						echo $path; ?>
					  - </span>    				
    				<a href="<?php echo JRoute::_($taginfolink . $item->id); ?>" title="Details" 
    					class="label label-info" style="padding:4px 8px;">
    					<span class="xb12"><?php echo $item->title; ?></span>
    				</a>
     			</td>
    			<td>
    				<p class="xb09"><?php echo $item->description; ?></p>
    			</td>
    			<td align="center">
   					<?php if ($item->mapcnt >0) : ?> 
   						<span class="badge mapcnt">
   							<a href="<?php echo $mapslink.$item->id;?>"><?php echo $item->mapcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->mrkcnt >0) : ?> 
   						<span class="badge mrkcnt">
   							<a href="<?php echo $markerslink.$item->id;?>"><?php echo $item->mrkcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->trkcnt >0) : ?> 
   						<span class="badge trkcnt">
   							<a href="<?php echo $trackslink.$item->id;?>"><?php echo $item->trkcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td align="center">
   					<?php if ($item->othcnt >0) : ?>
   						<span class="badge othcnt">
   							<?php echo $item->othcnt; ?>  						
   						</span>
   					<?php endif; ?>
   				</td>
  				<td align="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>

