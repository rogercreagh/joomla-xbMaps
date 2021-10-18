<?php
/*******
 * @package xbMaps
 * @version 0.8.0.b 17th October 2021
 * @filesource site/views/markerlist/tmpl/default.php
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


$listOrder     = $this->escape($this->state->get('list.ordering'));
$listDirn      = $this->escape(strtolower($this->state->get('list.direction')));
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


$catclass = $this->show_cats? 'label-success' : 'label-grey';
$tagclass = $this->show_tags? 'label-info' : 'label-grey';

$uid = uniqid();
$map = new XbMapHelper($uid, null, true);
$map->loadAPI(false);
$map->loadXbmapsJS();

?>
  <script>
  jQuery(document).ready(function() {
  jQuery(".showModal").click(function(e) { 
    e.preventDefault();
    var url = jQuery(this).attr("data-href");
    jQuery("#pvModal iframe").attr("src", url);
    jQuery("#pvModal").modal("show");
  });
});
  </script>

<div class="xbmaps">
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
</div>	
<div class="row-fluid">
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=markerlist'); ?>" method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="span12">
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

	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" style="table-layout:fixed;" id="xbmapsMarkerList">	
			<thead>
				<tr>
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
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->items as $i => $item) :
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
				<td>
					<p class="xb12 xbbold xbmb8">
						<?php echo $this->escape($item->title); ?>
				</td>
				<td style="text-align:center;">
					<div class="hasTooltip" title="" data-original-title="Latitude: <?php echo $item->latitude; ?><br />Longitude: <?php echo $item->longitude; ?>" >
						<a href="#" data-href="index.php?option=com_xbmaps&view=marker&layout=preview&id=<?php echo $item->id;?>&tmpl=component"  
                         	onclick="jQuery('#mrktit').html('<?php echo $item->title; ?>');" class="showModal">
                         		<?php echo $pv; ?>
						</a>
					</div>
				</td>
				<td>
					<p class="xb095">
    					<?php if (!empty($item->summary)) : ?>
    						<?php echo $item->summary; ?>
    					<?php else : ?><span class="xbnit">
    						<?php echo Text::_('XBMAPS_NO_DESCRIPTION'); ?></span>
    					<?php endif; ?>
					</p>
				</td>
				<td><?php 
				if (count($item->maps)>0) {
					echo '<p>';
						foreach ($item->maps as $map) {
							echo '<i class="far fa-map" style="color:#aaa;"></i> ';
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
			</tr>			
			<?php endforeach; ?>
			
			</tbody>
		
		</table>
    <?php endif; ?>
	<?php echo $this->pagination->getListFooter(); ?>
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>

	<div class="clearfix"></div>
	<?php echo XbmapsGeneral::credit();?>
</div>
<div class="modal hide" id="pvModal" style="width:600px;top:150px;" >
	<div class="modal-content">
	   <!-- Modal Header -->
		<div class="modal-header">
	    	<button type="button" role="presentation" class="close" data-dismiss="modal"
	     		style="opacity:0.5;font-size:1.5em; line-height:1em;">x</button>
	    	<h4 id="mrktit" style="margin:5px 10px;">Marker preview</h4>
    	</div>
        <!-- Modal body -->
    	<div class="modal-body">
      		<iframe src="" width="100%" height="400" frameborder="0" allowtransparency="true"></iframe>
    	</div>
        <!-- Modal footer -->
    	<div class="modal-footer" style="padding:10px 20px;">
    		<button type="button" class="btn" data-dismiss="modal">Close</button>
		</div>
	</div>
</div>

