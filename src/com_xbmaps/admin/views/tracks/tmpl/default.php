<?php
/*******
 * @package xbMaps Component
 * @version 1.5.2.0 4th January 2024
 * @filesource admin/views/tracks/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xbparsedown.php');

use Xbmaps\Xbparsedown\Xbparsedown;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

$user = Factory::getUser();
$userId = $user->get('id');

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
$canOrder       = $user->authorise('core.edit.state', 'com_xbmaps.track');
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_xbmaps&task=tracks.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable', 'xbmapsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$trackelink='index.php?option=com_xbmaps&view=track&task=track.edit&id=';
$cvlink = 'index.php?option=com_categories&view=category&task=category.edit&extension=com_xbmaps';
$tvlink = 'index.php?option=com_tags&id=';

$catclass = $this->show_cats? 'label-success' : 'label-grey';
$tagclass = $this->show_tags? 'label-info' : 'label-grey';
?>
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>
<div class="row-fluid">
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=tracks'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" >
	<div class="pull-right span2">
		<p style="text-align:right;">
			<?php $fnd = $this->pagination->total;
			echo $fnd .' '. JText::_(($fnd==1)?'XBMAPS_TRACK':'XBMAPS_TRACKS').' '.JText::_('XBMAPS_FOUND');
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
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbmapsTrackList">	
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
						<?php echo HTMLHelper::_('searchtools.sort','Date','rec_date',$listDirn,$listOrder ); ?>
					</th>	
					<th style="width:4em;text-align:center;"><?php echo Text::_('XBMAPS_COLOUR');?></th>	
					<th>
						<?php echo Text::_('XBMAPS_FILENAME'); ?>
					</th>			
					<th>
						<?php echo Text::_('XBMAPS_SUMMARY');?>
					</th>
					<th>
						<?php echo ucfirst(Text::_('XBMAPS_MARKERS'));?>
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
                $canEdit    = $user->authorise('core.edit', 'com_xbmaps.track.'.$item->id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') 
                                        || $item->checked_out==$userId || $item->checked_out==0;
				$canEditOwn = $user->authorise('core.edit.own', 'com_xbmaps.track.'.$item->id) && $item->created_by == $userId;
                $canChange  = $user->authorise('core.edit.state', 'com_xbmaps.track.'.$item->id) && $canCheckin;
                //$tc = $item->params->get('track_colour','#444');
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
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'track.', $canChange, 'cb'); ?>
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
						<a href="<?php echo JRoute::_($trackelink.$item->id);?>"
							title="<?php echo JText::_('XBMAPS_EDIT_TRACK'); ?>" >
							<b><?php echo $this->escape($item->title); ?></b></a> 
							&nbsp;<a href="#ajax-xbmodal" 
								data-toggle="modal" data-target="#ajax-xbmodal" 
								onclick="window.com='maps';window.view='track';window.pvid=<?php echo $item->id; ?>;"
									><i class="far fa-eye"></i></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
                    <br />                        
					<?php $alias = JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
                    	<span class="xbnit xb08"><?php echo $alias;?></span>
					</p>
				</td>
				<td>
					<?php  if (!is_null($item->rec_date)) echo HTMLHelper::_('date',$item->rec_date, 'd M \'y'); ?>
				</td>
				<td style="text-align:center;">
					<i class="fas fa-project-diagram" style="color:<?php echo $item->track_colour; ?>;"></i>
				</td>
				<td>
					<?php echo pathinfo($item->gpx_filename,PATHINFO_BASENAME);?>
					<?php if ($item->elev_filename != '') : ?>
						<br><span class="xbit xb09"><?php echo pathinfo($item->elev_filename,PATHINFO_BASENAME);?></span>
					<?php endif; ?>
				</td>
				<td>
					<p class="xb095">
    					<?php echo Xbparsedown::instance()->text($item->summary); ?>
					</p>
                    <?php $plaintext = strip_tags($item->description);
                    if (strlen($plaintext)>180) : ?>
                    	<p class="xbnit xb09 hasTooltip" data-original-title="<?php echo $plaintext;?>">   
                        	<?php  echo Text::_('XBMAPS_FULL_DESCRIPTION').' '.str_word_count($plaintext).' '.Text::_('XBMAPS_WORDS'); ?>
						</p>
					<?php endif; ?>
				</td>
				<td><?php if (count($item->markers)>0) : ?>
				
    				<?php if (count($item->markers)>2) : ?>
    					<details>
    						<summary>
    							<?php echo Text::_(count($item->markers).' markers assigned'); ?>
    						</summary>						
    				<?php endif; ?>
					<ul class="xblist" style="margin:0;">
						<?php foreach ($item->markers as $mrk) {
						    $pv = '<img src="/media/com_xbmaps/images/marker-icon.png" style="height:20px;" />';
						    switch ($mrk->markertype) {
						        case 1:
						            $pv = '<img src="'.$this->marker_image_path.'/'.$mrk->mkparams['marker_image'].'" style="height:20px;" />';
						            break;
						        case 2:
						            $pv = '<span class="fa-stack fa-2x" style="font-size:6pt;">';
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
						    echo '<li>'.$pv.'&nbsp;';
 							echo $mrk->linkedtitle;
 							echo '&nbsp;<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" ';
 							echo 'onclick="window.com=\'maps\';window.view=\'marker\';window.pvid='.$mrk->mkid.';" ';
 							echo '><i class="far fa-eye"></i></a>';
						} ?>
					</ul>
    				<?php if (count($item->markers)>2) : ?>
    					</details>
    				<?php endif; ?>
				<?php else: ?>
					<p class="xbnit"><?php echo Text::_('XBMAPS_NO_MARKERS'); ?></p>
				<?php endif; ?>
				
				</td>
				
				<td><?php 
				if (count($item->maps)>0) : ?>
					<ul class="xblist" style="margin:0;">
						<?php foreach ($item->maps as $map) : ?>
							<?php $tcol = $map->maptrack_colour=='' ? $item->track_colour : $map->maptrack_colour; ?>
							<li><i class="fas fa-map" style="color:<?php echo $tcol; ?>;"></i>
							<?php echo $map->linkedtitle; ?>
 							&nbsp;<a href="#ajax-xbmodal" 
								data-toggle="modal" data-target="#ajax-xbmodal" 
								onclick="window.com='maps';window.view='map';window.pvid=<?php echo $map->id; ?>;"
									><i class="far fa-eye"></i></a>							
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="xbnit"><?php  echo Text::_('XBMAPS_NO_MAPS'); ?>
				<?php endif; ?>	
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
					<br /><span class="xbnit xb09"><?php echo HtmlHelper::date($item->modified, 'd/m/y');?></span>
				</td>
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		</table>
        <?php // load the modal for displaying the batch options
			echo HTMLHelper::_( 'bootstrap.renderModal', 'collapseModal',
            	array(
                	'title' => Text::_('XBMAPS_BATCH_TITLE'),
                	'footer' => $this->loadTemplate('batch_footer')
            	),
            	$this->loadTemplate('batch_body')
        	); 
        ?>
    <?php endif; //items ?>
	<?php echo $this->pagination->getListFooter(); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
</div>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>
<?php echo LayoutHelper::render('xbpvmodal.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbmaps/layouts');   ?>

