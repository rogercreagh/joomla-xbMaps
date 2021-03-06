<?php
/*******
 * @package xbMaps Component
 * @version 0.8.0.g 21st October 2021
 * @filesource admin/views/catslist/tmpl/default.php
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
$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape($this->state->get('list.direction'));

$catlink = 'index.php?option=com_xbmaps&view=catinfo&id=';
$maplink = 'index.php?option=com_xbmaps&view=maps&catid=';
$mrklink = 'index.php?option=com_xbmaps&view=markers&catid=';
$trklink = 'index.php?option=com_xbmaps&view=tracks&catid=';

?>
<form action="index.php?option=com_xbmaps&view=catslist" method="post" id="adminForm" name="adminForm">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
	<?php if ($this->global_use_cats == 0) : ?>
		<div class="j-toggle-main alert alert-error">
	      <button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading"><?php echo Text::_('XBMAPS_CATS_GLOBAL_DISABLED'); ?></h4> 
			<div class="alert-message"><?php echo Text::_('XBMAPS_CATS_GLOBAL_DISABLED_INFO'); ?>      
	      </div>
		</div>
	<?php  elseif (!$this->mapcats || !$this->mrkcats || !$this->trkcats) : ?>
		<div class="j-toggle-main alert alert-warn">
	      <button type="button" class="close" data-dismiss="alert">×</button>
			<h4 class="alert-heading">
				<?php if(!$this->mapcats) echo 'Map '; ?>
				<?php if(!$this->mrkcats) echo 'Marker '; ?>
				<?php if(!$this->trkcats) echo 'Track '; ?>
				<?php echo Text::_('XBMAPS_CATSTYPE_DISABLED'); ?>
			</h4> 
			<div class="alert-message"><?php echo Text::_('XBMAPS_CATS_GLOBAL_DISABLED_INFO'); ?>
	      	</div>
		</div>
	<?php endif; ?>
	<div>
		<h3><?php echo Text::_('XBMAPS_CATSPAGE_TITLE'); ?></h3>
      	<p class="xb095"><?php echo Text::_('XBMAPS_CATSPAGE_SUBTITLE'); ?></p>
     </div>
	
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. Text::_(($fnd==1)?'XBMAPS_CATEGORY':'XBMAPS_CATEGORIES').' '.Text::_('XBMAPS_FOUND'); ?>
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
				<?php echo Text::_('JSTATUS'); ?>
			</th>
			<th>
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_HIERARCHY', 'path', $listDirn, $listOrder );?>&nbsp;
				<?php echo HTMLHelper::_('grid.sort', 'XBMAPS_TITLE', 'title', $listDirn, $listOrder );?>
			</th>
			<th>
				<?php echo Text::_('XBMAPS_DESCRIPTION') ;?>
			</th>
			<th<?php if (!$this->mapcats) echo ' class="xbdim"';?> style="text-align:center;">
				<?php echo HTMLHelper::_('grid.sort', ucfirst('XBMAPS_MAPS'), 'mapcnt', $listDirn, $listOrder );?>
			</th>
			<th<?php if (!$this->mrkcats) echo ' class="xbdim"';?> style="text-align:center;">
				<?php echo HTMLHelper::_('grid.sort', ucfirst('XBMAPS_MARKERS'), 'mrkcnt', $listDirn, $listOrder );?>
			</th>
			<th<?php if(!$this->trkcats) echo ' class="xbdim"';?> style="text-align:center;">
				<?php echo HTMLHelper::_('grid.sort', ucfirst('XBMAPS_TRACKS'), 'trkcnt', $listDirn, $listOrder );?>
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
			<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>" >	
					<td class="center hidden-phone">
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
				<td class="center">
					<div class="btn-group">
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'category.', false, 'cb'); ?>
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
    					echo HTMLHelper::_('jgrid.checkedout', $i, Text::_('XBMAPS_OPENED_BY').': '.$couname, $item->checked_out_time, 'categories.', false);
    				} ?>
					<span class="xbnote"> 
 					<?php if ($listOrder=='path') {
 					    $prefix = '';
 					    $slashes = substr_count($item->path,'/');
 					    if ($slashes>0) {
     					    $prefix .= '<span style="padding-left:'.($slashes*15).'px">';
     					    $prefix .= '└─&nbsp;</span>';					        
 					    }
 					} else {
                      $prefix = substr($item->path,0,strrpos($item->path, '/')).' ';
                    }
                      echo $prefix;
                      ?>
						</span>    				
    					<a href="<?php echo JRoute::_($catlink . $item->id); ?>" title="Details" 
    						class="label label-success" style="padding:2px 8px;">
    						<span class="xb11"><?php echo $item->title; ?></span>
    					</a>
    			</td>
    			<td>
    				<p class="xb09"><?php echo $item->description; ?></p>
    			</td>
    			<td style="text-align:center;">
   					<?php if ($item->mapcnt >0) : ?> 
   						<span class="badge mapcnt">
   							<a href="<?php echo $maplink.$item->id;?>"><?php echo $item->mapcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td style="text-align:center;">
   					<?php if ($item->mrkcnt >0) : ?> 
   						<span class="badge mrkcnt">
   							<a href="<?php echo $mrklink.$item->id;?>"><?php echo $item->mrkcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
    			<td style="text-align:center;">
   					<?php if ($item->trkcnt >0) : ?> 
   						<span class="badge trkcnt">
   							<a href="<?php echo $trklink.$item->id;?>"><?php echo $item->trkcnt; ?>
   						</a></span>
   					<?php endif; ?>
   				</td>
  				<td>
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

