<?php
/*******
 * @package xbMaps Component
 * @version 1.2.1.1 20th February 2023
 * @filesource site/views/tracklist/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xbparsedown.php');

use Xbmaps\Xbparsedown\Xbparsedown;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Layout\FileLayout;

HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => JText::_('JOPTION_SELECT_TAG')));
HTMLHelper::_('formbehavior.chosen', 'select');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape(strtolower($this->state->get('list.direction')));
if (!$listOrder) {
	$listOrder='title';
	$listDirn = 'ascending';
}
require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbmaps&view=category'.$itemid.'&id=';

$itemid = XbmapsHelperRoute::getTracksRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$tlink = 'index.php?option=com_xbmaps&view=track'.$itemid.'&id=';

?>

<div class="xbmaps">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=tracklist'); ?>" method="post" name="adminForm" id="adminForm">       
		<?php  // Search tools bar
			if ($this->search_bar) {
				$hide = '';
				if ((!$this->show_cats) || ($this->hide_catsch)) { $hide .= 'filter_category_id, filter_subcats,';}
				if ((!$this->show_tags) || $this->hide_tagsch) { $hide .= 'filter_tagfilt,filter_taglogic,';}
				echo '<div class="row-fluid"><div class="span12">';
	            echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this,'hide'=>$hide));       
	         echo '</div></div>';
			} 
		?>
		<div class="row-fluid pagination" style="margin-bottom:10px 0;">
			<div class="pull-right">
				<p class="counter" style="text-align:right;margin-left:10px;">
					<?php echo $this->pagination->getResultsCounter().'.&nbsp;&nbsp;'; 
					   echo $this->pagination->getPagesCounter().'&nbsp;&nbsp;'.$this->pagination->getLimitBox().' per page'; ?>
				</p>
			</div>
			<div>
				<?php  echo $this->pagination->getPagesLinks(); ?>
            	<?php //echo 'sorted by '.$orderNames[$listOrder].' '.$listDirn ; ?>
			</div>
		</div>
		<div class="row-fluid">
        	<div class="span12">
				<?php if (empty($this->items)) : ?>
					<div class="alert alert-no-items">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbmapsTrackList">	
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Title','title',$listDirn,$listOrder);?>
				</th>					
				<th class="hidden-phone">
					<?php echo JText::_('XBMAPS_SUMMARY');?>
				</th>
				<th class="hidden-phone">
					<?php echo JText::_('XBMAPS_MAPS');?>
				</th>
				<?php if($this->show_cats || $this->show_tags) : ?>
    				<th class="hidden-tablet hidden-phone">
    					<?php if ($this->show_cats) {
    						echo HTMLHelper::_('searchtools.sort','XBMAPS_CATEGORY','category_title',$listDirn,$listOrder );
    					}
    					if (($this->show_cats) && ($this->show_tags)) {
    					    echo ' &amp; ';
    					}
    					if($this->show_tags) {
    					    echo Text::_( 'XBMAPS_TAGS' ); 
    					} ?>                
    				</th>
                <?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">	
					<td>
						<p class="xbtitle">
							<?php if($this->enable_track_view) { echo '<a href="'.$tlink.$item->id.'">'; }?>
								<b><?php echo $this->escape($item->title); ?></b> 
							<?php if($this->enable_track_view) { echo '</a>'; } ?>
						</p>
					</td>
					<td class="hidden-phone">
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
					<td class="hidden-phone">
						<p class="xb095">
							<?php if (!empty($item->maps)) : ?>
								<?php foreach ($item->maps as $map) {
									$tcol = is_null($map->maptrack_colour) ? $item->track_colour : $map->maptrack_colour;
									echo '<i class="far fa-map" style="color:'.$tcol.';"></i> ';
									echo $map->linkedtitle;
									echo '<br />';
								}?>
    						<?php else : ?>
     							<span class="xbnit">
    								<?php echo Text::_('XBMAPS_NO_MAPS'); ?>
    							</span>
    						<?php endif; ?>
                        </p>
					</td>
    				<?php if($this->show_cats || $this->show_tags) : ?>
    					<td class="hidden-phone">
     						<?php if($this->show_cats) : ?>	
     							<p>
     							<?php if($this->show_cats==2) : ?>											
    								<a class="label label-success" href="<?php echo $clink.$item->catid; ?>"><?php echo $item->category_title; ?></a>
    							<?php else: ?>
    								<span class="label label-success"><?php echo $item->category_title; ?></span>
    							<?php endif; ?>
    							</p>
    						<?php endif; ?>
    						<?php if($this->show_tags) {
    							$tagLayout = new FileLayout('joomla.content.tags');
        						echo $tagLayout->render($item->tags);
    						}
        					?>
    					</td>
					<?php endif; ?>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
	<?php echo $this->pagination->getListFooter(); ?>
<?php endif; //items found?>	
<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</form>
	<div class="clearfix"></div>
	<?php echo XbmapsGeneral::credit();?>
</div>
	