<?php
/*******
 * @package xbMaps
 * @version 0.1.1.j 27th August 2021
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
$map->renderTracks(array($this->item),true);

$map->renderMap();

?>
<div class="xbmaps">
	<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=trackview&id='.$this->item->id); ?>" method="post" name="adminForm" id="adminForm">
		<div class="row-fluid">
			<div class="span9">
				<h1 class="center"><?php echo $this->item->title; ?></h1>
		 	</div>
		 	<div class="span3">
		 		<p style="margin:12px 0;"><span class="xbit">Recorded:</span> <?php echo $this->item->rec_date; ?></p>
		 	</div>
		</div>
		<div class="row-fluid">
			<div class="span12" style="margin:0;padding:0; <?php echo $this->borderstyle; ?>">
				<div id="xbMap<?php echo $uid; ?>" style="<?php echo $this->mapstyle; ?>">
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
