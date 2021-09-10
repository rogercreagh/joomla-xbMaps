<?php
/*******
 * @package xbMaps
 * @version 0.1.2.c 9th September 2021
 * @filesource admin/views/markers/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$user = Factory::getUser();
$userId = $user->get('id');

$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
	$listOrder='title';
	$listDirn = 'ascending';
}

$saveOrder      = $listOrder == 'ordering';
$canOrder       = $user->authorise('core.edit.state', 'com_xbmaps.marker');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_xbmaps&task=maps.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'xbmapsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$markerelink='index.php?option=com_xbmaps&view=marker&task=marker.edit&id=';
$cvlink = 'index.php?option=com_categories&view=category&task=category.edit&extension=com_xbmaps';
$tvlink = 'index.php?option=com_tags&id=';

$catclass = $this->show_cats? 'label-success' : 'label-grey';
$tagclass = $this->show_tags? 'label-info' : 'label-grey';
?>
<div class="row-fluid">
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=markers'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
	<?php else : ?>
        <div id="j-main-container" class="span12">
	<?php endif;?>
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. JText::_(($fnd==1)?'XBMAPS_MARKER':'XBMAPS_MARKERS').' '.JText::_('XBMAPS_FOUND');
            ?>
		</p>
	</div>
	<div class="clearfix"></div>
	
	<?php
        // Search tools bar
        echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
    ?>
	<div class="clearfix"></div>
	
	<div class="pagination">
		<?php  echo $this->pagination->getPagesLinks(); ?>
		<br />
	    <?php //echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
	</div>

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbmapsMarkerList">	
			<thead>
				<tr>
					<th class="nowrap center hidden-phone" style="width:25px;">
						<?php echo HTMLHelper::_('searchtools.sort', '', 'ordering', 
						    $listDirn, $listOrder, null, 'asc', 'XBMAPS_HEADING_ORDERING_DESC', 'icon-menu-2'); ?>
					</th>
					<th class="hidden-phone center" style="width:25px;">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="nowrap center" style="width:55px">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','XBMAPS_TITLE','title',$listDirn,$listOrder).
    						' <span style="font-size:0.9em;">'.
    						'</span>';
						?>
					</th>	
					<th>
					</th>
					<th>
						<?php echo Text::_('XBMAPS_DESCRIPTION');?>
					</th>
					<th>
						<?php echo ucfirst(Text::_('XBMAPS_MAPS'));?>
					</th>
					<th class="hidden-tablet hidden-phone" style="width:15%;">
						<?php if ($this->show_cats) {
						    echo HTMLHelper::_('searchtools.sort','XBMAPS_CATEGORY','category_title',$listDirn,$listOrder );
						} else {
						    echo '<span class="xbdim">'.Text::_( 'XBMAPS_CATEGORY' ).'</span>';
						}
                        echo ' &amp; ';
                        if ($this->show_tags) {
                            echo Text::_( 'XBMAPS_TAGS' ); 
                        } else {
                            echo '<span class="xbdim">'.Text::_( 'XBMAPS_TAGS' ).'</span>';
                        }?>
					</th>
					<th class="nowrap hidden-tablet hidden-phone" style="width:45px;">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder );?>&nbsp;
						<span class="xbnit"><?php echo Text::_('XBMAPS_SAVED');?></span>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->items as $i => $item) :
                $canEdit    = $user->authorise('core.edit', 'com_xbmaps.marker.'.$item->id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                        || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_xbmaps.marker.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbmaps.marker.'.$item->id) && $canCheckin;
                //$tc = $item->params->get('track_colour','#444');
                $markertype = $item->marker_type;
                $pv = '<img src="/media/com_xbmaps/images/marker-icon.png" />';
                switch ($markertype) {
                	case 1:
                		$pv = '<img src="'.$this->marker_image_path.'/'.$item->params->get('marker_image','').'" style="height:40px;" />';
	                	break;
                	case 2:
                		$pv = '<span class="fa-stack fa-2x" style="font-size:12pt;">';
                		$pv .='<i class="'.$item->params->get('marker_outer_icon','').' fa-stack-2x" ';
                		$pv .= 'style="color:'.$item->params->get('marker_outer_colour','').';"></i>';
                		if ($item->params->get('marker_inner_icon')!=''){
	                		$pv .= '<i class="'.$item->params->get('marker_inner_icon','').' fa-stack-1x fa-inverse" ';
	                		$pv .= 'style="color:'.$item->params->get('marker_inner_colour','').';';
	                		if ($item->params->get('marker_outer_icon')=='fas fa-map-marker') {
	                			$pv .= 'line-height:1.75em;font-size:0.8em;';
	                		}
	                		$pv .= '"></i>';                			
                		}
                		$pv .= '</span>';
						break;                	
                	default:
	                	break;
                }
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid; ?>">	
				<td class="order nowrap center hidden-phone">
					<?php
						$iconClass = '';
						if (!$canChange) {
							$iconClass = ' inactive';
						} elseif (!$saveOrder) {
							$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED');
						}
					?>
					<span class="sortable-handler<?php echo $iconClass; ?>">
						<span class="icon-menu" aria-hidden="true"></span>
					</span>
					<?php if ($canChange && $saveOrder) : ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
					<?php endif; ?>
				</td>
				<td class="center hidden-phone">
					<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<div class="btn-group">
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'marker.', $canChange, 'cb'); ?>
						<?php if ($item->note!=""){ ?>
							<span class="btn btn-micro active hasTooltip" title="" data-original-title="<?php echo '<b>'.JText::_( 'XBMAPS_NOTE' ) .'</b>: '. htmlentities($item->note); ?>">
								<i class="icon- xbinfo"></i>
							</span>
						<?php } else {?>
							<span class="btn btn-micro inactive" style="visibility:hidden;" title=""><i class="icon-info"></i></span>
						<?php } ?>
					</div>
				</td>
				<td>
					<p class="xb12 xbbold xbmb8">
					<?php if ($item->checked_out) {
					    $couname = Factory::getUser($item->checked_out)->username;
					    echo HTMLHelper::_('jgrid.checkedout', $i, JText::_('XBMAPS_OPENED_BY').': '.$couname, $item->checked_out_time, 'track.', $canCheckin);
					} ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo JRoute::_($markerelink.$item->id);?>"
							title="<?php echo JText::_('XBMAPS_EDIT_MARKER'); ?>" >
							<b><?php echo $this->escape($item->title); ?></b></a> 
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
                    <br />                        
					<?php $alias = JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    	<span class="xbnit xb08"><?php echo $alias;?></span>
					</p>
				</td>
				<td style="text-align:center;">
					<div class="hasTooltip" title="" data-original-title="Latitude: <?php echo $item->latitude; ?><br />Longitude: <?php echo $item->longitude; ?>" >
					   <?php echo $pv; ?>
					</div>
				</td>
				<td>
					<p class="xb095">
    					<?php if (!empty($item->description)) : ?>
    						<?php echo XbmapsGeneral::makeSummaryText($item->description,200,false); ?>
    					<?php else : ?><span class="xbnit">
    						<?php echo Text::_('XBMAPS_NO_DESCRIPTION'); ?></span>
    					<?php endif; ?>
					</p>
                    <?php $plaintext = strip_tags($item->description);
                    if (strlen($plaintext)>200) : ?>
                    	<p class="xbnit xb09 hasTooltip" data-original-title="<?php echo $plaintext;?>">   
                        	<?php  echo Text::_('XBMAPS_FULL_DESCRIPTION').' '.str_word_count($plaintext).' '.Text::_('XBMAPS_WORDS'); ?>
						</p>
					<?php endif; ?>
				</td>
				<td><?php 
				if (count($item->maps)>0) {
					echo '<p>';
						foreach ($item->maps as $map) {
							$tcol = (empty($map->track_colour)) ? '#ccf' : $map->track_colour;
							echo '<i class="far fa-map" style="color:'.$tcol.';"></i> ';
							echo $map->linkedtitle;
							echo '<br />';
						}
					echo '</p>';
				} else {
					echo '<p class="xbnit">'.Text::_('XBMAPS_NO_MAPS').'</p>';
				}
				?>
				</td>
				<td>
					<p><a class="label <?php echo $catclass; ?>" href="<?php echo $cvlink.$item->catid; ?>" 
						title="<?php echo Text::_( 'XBMAPS_VIEW_CATEGORY' );?>::<?php echo $item->category_title; ?>">
							<?php echo $item->category_title; ?>
						</a>
					</p>												
					<ul class="inline">
					<?php foreach ($item->tags as $t) : ?>
						<li><a href="<?php echo $tvlink.$t->id; ?>" class="label <?php echo $tagclass; ?>">
							<?php echo $t->title; ?></a>
						</li>												
					<?php endforeach; ?>
					</ul>						    											
				</td>
				<td class="hidden-phone">
					<?php echo $item->id; ?>
					<br /><span class="xbnit"><?php echo HtmlHelper::date($item->modified, Text::_('d M Y'));?></span>
				</td>
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		
		</table>
        <?php // load the modal for displaying the batch options
			echo HTMLHelper::_( 'bootstrap.renderModal', 'collapseModal',
            	array(
                	'title' => JText::_('XBMAPS_BATCH_TITLE'),
                	'footer' => $this->loadTemplate('batch_footer')
            	),
            	$this->loadTemplate('batch_body')
        	); 
        ?>
    <?php endif; ?>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
</div>