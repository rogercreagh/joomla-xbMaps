<?php
/*******
 * @package xbMaps Component
 * @version 1.4.2.0 13th December 2023
 * @filesource site/views/track/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.modal');

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getCategoriesRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$clink = 'index.php?option=com_xbmaps&view=category'.$itemid.'&id=';

$tracklink = XbmapsHelperRoute::getTrackLink('');
$trackslink = 'index.php?option=com_xbmaps&view=tracklist';

?>

<div class="xbmaps">
<?php if (!$this->tmplcomp) : ?>
	<?php if(($this->header['showheading']) || ($this->header['title'] != '') || ($this->header['text'] != '')) {
		echo XbmapsHelper::sitePageheader($this->header);
	} ?>
<?php endif; ?>
	
<?php  if($this->show_track_title) :?>
	<div class="row-fluid">
		<div class="span12">
			<h1><?php echo $item->title; ?></h1>
	 	</div>
	</div>
<?php endif; ?>

<?php if (($this->show_track_desc=='2') || (($this->show_track_desc=='1') && ($this->show_track_info=='above'))) : ?>
	<?php echo $this->descbox; ?>
<?php endif; ?>
<?php if ($this->show_track_info=='above') :?>
	<?php echo $this->infobox;?>
<?php endif; ?>	

<div class="row-fluid">
	<?php if (($this->show_track_info === 'left')): ?>
    	<div class="span<?php echo $this->track_info_width; ?>">
			<?php echo $this->infobox;?>
    		<?php if ($this->show_track_desc==1) {
    			echo $this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>

	<div class="span<?php echo $this->mainspan; ?>">
		<?php $uid = uniqid();
			$map = new XbMapHelper($uid,$item->params);
			$map->loadAPI(false,false);
			$map->loadXbmapsJS();
			$map->createMap($this->centre_latitude, $this->centre_longitude, $this->default_zoom);
			$map->setMapType($this->track_map_type);
			$map->renderFullScreenControl();
			//$map->renderEasyPrint();
			$map->renderTracks(array($item),true,$this->show_stats,$this->show_track_popover);
			$map->renderMap();
		?>
		<div id="xbmaps" style="margin:0;padding:0;">
			<div align="center" style="margin:0;padding:0; <?php echo $this->borderstyle; ?>">
				<div id="xbMap<?php echo $uid; ?>" style="<?php echo $this->mapstyle; ?>">
				
				</div>
			</div>
		</div>	
		<?php if ($item->elev_filename !='') : ?>
			<?php if (file_exists(JPATH_ROOT.$item->elev_filename)) : ?>
			<div id="elevimg">
				<a href="<?php echo $item->elev_filename; ?>" class="modal">
					<img src="<?php echo $item->elev_filename; ?>" />
				</a>
			</div>
			<?php endif; ?>
		<?php endif; ?>	
	</div>
	<?php if (($this->show_track_info === 'right')): ?>
    	<div class="span<?php echo $this->track_info_width; ?>">
			<?php echo $this->infobox;?>
    		<?php if ($this->show_track_desc==1) {
    			echo $this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>
	
</div>

<?php if ($this->show_track_info=='below') :?>
	<?php echo $this->infobox;?>
<?php endif; ?>	
<?php if (($this->show_track_desc=='3') || (($this->show_track_desc=='1') && ($this->show_track_info=='below'))) : ?>
	<?php echo $this->descbox; ?>
<?php endif; ?>


<?php if ($this->tmplcomp) : ?>
	<div class="row-fluid xbmt16">
	<?php if ($this->show_cats >0) : ?>       
		<div class="span4">
			<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_CATEGORY'); ?></div>
				<div class="pull-left">
					<?php if($this->show_cats==2) : ?>
						<a class="label label-success" href="<?php echo JRoute::_($clink.$item->catid); ?>">
    						<?php echo $item->category_title; ?></a>
    				<?php else: ?>
    					<span class="label label-success"><?php echo $item->category_title; ?></span>
    				<?php endif; ?>		
				</div>
	        </div>
        <?php endif; ?>
        <?php if (($this->show_tags) && (!empty($item->tags))) : ?>
        	<div class="span<?php echo ($this->show_tags>0) ? '8' : '12'; ?>">
				<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_TAGS'); ?></div>
				<div class="pull-left">
					<?php  $tagLayout = new JLayoutFile('joomla.content.tags');
	    				echo $tagLayout->render($item->tags); ?>
				</div>
        	</div>
		<?php endif; ?>
	</div>
	<div class="row-fluid xbbox xbboxgrey">
		<div class="span2">
			<?php if (($item->prev>0) || ($item->next>0)) : ?>
			<span class="hasTooltip xbinfo" title 
				data-original-title="<?php echo JText::_('XBMAPS_INFO_PREVNEXT'); ?>" >
			</span>&nbsp;
			<?php endif; ?>
			<?php if($item->prev > 0) : ?>
				<a href="<?php echo JRoute::_(XbmapsHelperRoute::getTrackLink($item->prev)); ?>" class="btn btn-small">
					<?php echo Text::_('XBMAPS_PREV'); ?></a>
		    <?php endif; ?>
		</div>
		<div class="span8"><center>
			<a href="<?php echo JRoute::_($trackslink); ?>" class="btn btn-small">
				<?php echo JText::_('XBMAPS_TRACKSLIST'); ?></a></center>
		</div>
		<div class="span2">
			<?php if($item->next > 0) : ?>
				<a href="<?php echo JRoute::_(XbmapsHelperRoute::getTrackLink($item->next)); ?>" class="btn btn-small pull-right">
					<?php echo JText::_('XBMAPS_NEXT'); ?></a>
		    <?php endif; ?>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php echo XbmapsGeneral::credit();?>
<?php endif; ?>

</div>
