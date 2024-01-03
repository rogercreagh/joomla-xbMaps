<?php
/*******
 * @package xbMaps Component
 * @version 1.5.1.0 3rd January 2024
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
HTMLHelper::_('formbehavior.chosen', '.multipleTags', null, array('placeholder_text_multiple' => Text::_('JOPTION_SELECT_TAG')));
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
<style type="text/css" media="screen">
    .xbpvmodal .modal-body iframe { max-height:calc(100vh - 190px);}
    .xbpvmodal .modal-body { max-height:none; height:auto;}
</style>


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
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbmapsTrackList">	
		<thead>
			<tr>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Title','title',$listDirn,$listOrder);?>
				</th>				
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','Date','rec_date',$listDirn,$listOrder ); ?>
				</th>	
				<th class="hidden-phone">
					<?php echo Text::_('XBMAPS_SUMMARY');?>
				</th>
				<th class="hidden-phone">
					<?php echo Text::_('XBMAPS_MAPS');?>
				</th>
				<th class="hidden-phone">
					<?php echo Text::_('XBMAPS_MARKERS');?>
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
								<b><?php echo $this->escape($item->title); ?></b></a>&nbsp;<a href="#ajax-xbmodal" 
								data-toggle="modal" data-target="#ajax-xbmodal" 
								onclick="window.com='maps';window.view='track';window.pvid=<?php echo $item->id; ?>;"
								><i class="far fa-eye"></i></a>
								 
							<?php if($this->enable_track_view) { echo '</a>'; } ?>
						</p>
					</td>
    				<td>
    					<?php  if (!is_null($item->rec_date)) echo HTMLHelper::_('date',$item->rec_date, 'd M \'y'); ?>
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
						<?php if (!empty($item->maps)) : ?>
        					<ul class="xblist" style="margin:0;">
    							<?php foreach ($item->maps as $map) : ?>
    								<?php $tcol = is_null($map->maptrack_colour) ? $item->track_colour : $map->maptrack_colour; ?>
    								<li><i class="far fa-map" style="color:<?php echo $tcol; ?>;"></i> 
    									<?php echo $map->linkedtitle; ?>
             							&nbsp;<a href="#ajax-xbmodal" 
            								data-toggle="modal" data-target="#ajax-xbmodal" 
            								onclick="window.com='maps';window.view='map';window.pvid=<?php echo $map->id; ?>;"
            								><i class="far fa-eye"></i></a>							
            						</li>
    							<?php endforeach; ?>
						<?php else : ?>
 							<span class="xbnit">
								<?php echo Text::_('XBMAPS_NO_MAPS'); ?>
							</span>
						<?php endif; ?>
					</td>
				<td class="hidden-phone"><?php if (count($item->markers)>0) : ?>
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
    					    if ($mrk->mkdesc =='') {
    					        echo $mrk->display;
    					    } else {
    					        echo '<span class="hasTooltip"  data-original-title="'.$mrk->mkdesc.'">'.$mrk->display.'</span>';
    					    }
    						echo '&nbsp;<a href="#ajax-xbmodal" data-toggle="modal" data-target="#ajax-xbmodal" ';  
                            echo 'onclick="window.com=\'maps\';window.view=\'marker\';window.pvid='.$mrk->mkid.';" ';
                			echo '><i class="far fa-eye"></i></a>';
    						echo '</li>';
    					} ?>
    				</ul>
    				<?php if (count($item->markers)>2) : ?>
    					</details>
    				<?php endif; ?>
				<?php else: ?>
					<p class="xbnit"><?php echo Text::_('XBMAPS_NO_MARKERS'); ?></p>
				<?php endif; ?>
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
<?php echo LayoutHelper::render('xbpvmodal.layoutpvmodal', array(), JPATH_ROOT .'/components/com_xbmaps/layouts');   ?>
</div>
	