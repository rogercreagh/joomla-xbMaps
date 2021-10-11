<?php
/*******
 * @package xbMaps
 * @version 0.7.0.d 11h October 2021
 * @filesource admin/views/trackview/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$uid = uniqid();

$map = new XbMapHelper($uid,$this->params);
$map->loadAPI(false,false);
$map->loadXbmapsJS();
$map->createMap($this->centre_latitude, $this->centre_longitude, $this->default_zoom);
$map->setMapType($this->track_map_type);
$map->renderFullScreenControl();
$map->renderTracks(array($this->item),true,$this->show_stats,$this->show_track_popover);

$map->renderMap();

?>
<div class="xbmaps">
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=trackview&id='.$this->item->id); ?>" method="post" name="adminForm" id="adminForm">
<?php  if($this->show_track_title) :?>
	<div class="row-fluid">
		<div class="span12">
			<h1><?php echo $this->item->title; ?></h1>
	 	</div>
	</div>
<?php endif; ?>

<?php if (($this->show_track_desc=='2') || (($this->show_track_desc=='1') && ($this->show_track_info=='above'))) : ?>
	<?php echo $this->descbox; ?>
	<p> </p>
<?php endif; ?>
<?php if ($this->show_track_info=='above') :?>
	<?php echo $this->infobox;?>
	<p> </p>
<?php endif; ?>	

<div class="row-fluid">
	<?php if (($this->show_track_info=='left')): ?>
    	<div class="span<?php echo $this->track_info_width; ?>">
			<?php echo $this->infobox;?>
    		<?php if ($this->show_track_desc==1) {
    			echo '<p> </p>'.$this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>

	<div class="span<?php echo (($this->show_track_info == 'left') || ($this->show_track_info=='right')) ? $this->mainspan : '12'; ?>">
		<div id="xbmaps" style="margin:0;padding:0;">
			<div align="center" style="margin:0;padding:0; <?php echo $this->borderstyle; ?>">
				<div id="xbMap<?php echo $uid; ?>" style="<?php echo $this->mapstyle; ?>">
				
				</div>
			</div>
		</div>			
	</div>
	<?php if (($this->show_track_info=='right')): ?>
    	<div class="span<?php echo $this->track_info_width; ?>">
			<?php echo $this->infobox;?>
    		<?php if ($this->show_track_desc==1) {
    			echo '<p> </p>'.$this->descbox;
    		} ?>
    	</div>
	<?php endif; ?>
	
</div>
<?php if ($this->show_track_info=='below') :?>
	<p> </p>
	<?php echo $this->infobox;?>
<?php endif; ?>	
<?php if (($this->show_track_desc=='3') || (($this->show_track_desc=='1') && ($this->show_track_info=='below'))) : ?>
	<p> </p>
	<?php echo $this->descbox; ?>
<?php endif; ?>

	<div class="row-fluid xbmt16">
	<?php if ($this->show_cats >0) : ?>       
		<div class="span4<?php echo ($this->show_cats ==0) ? ' xbdim' : ''; ?>">
			<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_CATEGORY'); ?></div>
			<div class="pull-left">
				<?php if($this->show_cats==2) : ?>
					<a class="label label-success" href="<?php echo JRoute::_($clink.$this->item->catid); ?>">
						<?php echo $this->item->category_title; ?></a>
				<?php else: ?>
					<span class="label label-success"><?php echo $this->item->category_title; ?></span>
				<?php endif; ?>		
			</div>
        </div>
    <?php endif; ?>
    <?php if (($this->show_tags) && (!empty($this->item->tags))) : ?>
    	<div class="span<?php echo ($this->show_cats>0) ? '8' : '12'; ?> <?php echo ($this->show_tags ==0) ? ' xbdim' : ''; ?>">
			<div class="pull-left xbnit xbmr10"><?php echo JText::_('XBMAPS_TAGS'); ?></div>
			<div class="pull-left">
				<?php  $tagLayout = new JLayoutFile('joomla.content.tags');
    				echo $tagLayout->render($this->item->tags); ?>
			</div>
    	</div>
	<?php endif; ?>
	</div>



		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
	<div class="clearfix"></div>
	<?php echo XbmapsGeneral::credit();?>

