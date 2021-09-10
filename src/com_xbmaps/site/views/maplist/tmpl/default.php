<?php
/*******
 * @package xbMaps
 * @version 0.1.1.f 19th August 2021
 * @filesource site/views/maplist/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

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

$itemid = XbmapsHelperRoute::getMapsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$mlink = 'index.php?option=com_xbmaps&view=map'.$itemid.'&id=';

//$mlink = 'index.php?option=com_xbmaps&view=map&id=';
?>

<div class="xbmaps">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
	
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=maplist'); ?>" method="post" name="adminForm" id="adminForm">       
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
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbmapsMapList">	
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Title','title',$listDirn,$listOrder);?>
				</th>					
				<th class="hidden-phone">
					<?php echo JText::_('XBMAPS_DESCRIPTION');?>
				</th>
				<th class="hidden-phone">
					<?php echo JText::_('XBMAPS_MARKERS');?>
				</th>
				<th class="hidden-phone">
					<?php echo JText::_('XBMAPS_TRACKS');?>
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
							<a href="<?php echo $mlink.$item->id;?>" >
								<b><?php echo $this->escape($item->title); ?></b></a></p> 
					</td>
					<td class="hidden-phone">
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
					<td class="hidden-phone">
						<p class="xb095">
							<?php if (!empty($item->markers)) : ?>
								<?php echo $item->markers; ?>
    						<?php else : ?>
     							<span class="xbnit">
    								<?php echo Text::_('XBMAPS_NO_MARKERS'); ?>
    							</span>
    						<?php endif; ?>
                        </p>
					</td>
					<td class="hidden-phone">
						<p class="xb095">
							<?php if (!empty($item->tracks)) : ?>
								<?php foreach ($item->tracks as $trk) {
									echo '<i class="fas fa-project-diagram" style="color:'.$trk->track_colour.';"></i> ';
 									echo $trk->linkedtitle;
									echo '<br />';
								}?>
    						<?php else : ?>
     							<span class="xbnit">
    								<?php echo Text::_('XBMAPS_NO_TRACKS'); ?>
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
	