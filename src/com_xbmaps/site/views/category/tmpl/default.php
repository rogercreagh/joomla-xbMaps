<?php
/*******
 * @package xbMaps
 * @version 0.4.0.1 28th September 2021
 * @filesource site/views/category/tmpl/default.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Language\Text;

$item = $this->item;

require_once JPATH_COMPONENT.'/helpers/route.php';

$itemid = XbmapsHelperRoute::getMapsRoute();
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$maplink = 'index.php?option=com_xbmaps&view=map'.$itemid.'&id=';

$itemid = XbmapsHelperRoute::getTracksRoute() ;
$itemid = $itemid !== null ? '&Itemid=' . $itemid : '';
$trklink = 'index.php?option=com_xbmaps&view=track'.$itemid.'&id=';

$itemid = XbmapsHelperRoute::getCategoriesRoute();
if ($itemid !== null) {
    $cclink = 'index.php?option=com_xbmaps&Itemid='.$itemid.'';
} else {
    $cclink = 'index.php?option=com_xbmaps&view=catlist';
}

$show_catdesc = $this->params->get('show_catdesc',1);

?>
<div class="row-fluid" style="margin-bottom:20px;">
	<div class="span3">
		<h4><?php echo JText::_('XBMAPS_ITEMSCAT'); ?></h4>		
	</div>	
	<div class="span9">
          <div class="badge badge-success pull-left"><h3><?php echo $item->title; ?></h3></div>
          
		<?php if (($this->show_empty) && (strpos($item->path,'/')!==false)) : ?>
			<div class="xb11 pull-left" style="padding-top:20px;margin-left:40px;">
				<i><?php echo JText::_('XBMAPS_CATPARENTS'); ?>:</i> 
				<?php  $path = substr($item->path, 0, strrpos($item->path, '/'));
					$path = str_replace('/', ' - ', $path);
					echo $path; ?>
        	</div>
        <?php endif; ?>
	</div>	
</div>
<?php if (($show_catdesc) && ($item->description != '')) : ?>
	<div class="row-fluid">
		<div class= "span2">
			<p><i>Description:</i></p>
		</div>
		<div class="span10">
			<?php echo $item->description; ?>
		</div>
	</div>
<?php endif; ?>
	<div class="row-fluid">
<?php if ($this->mapcats) : ?>
    	<div class= "span4">
    		<div class="xbbox xbboxcyan xbyscroll xbmh300">
    			<p><?php echo $item->mapcnt; ?> maps</p>
    			<?php if ($item->mapcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->maps as $i=>$bk) { 
    					echo '<li><a href="'.$maplink.$bk->id.'">'.$bk->title.'</a></li> ';
    				} ?>				
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
 <?php endif; ?>
 <?php if ($this->mrkcats) : ?>
    	<div class= "span4">
    		<div class="xbbox xbboxgrn xbyscroll xbmh300">
    			<p><?php echo $item->mrkcnt; ?> markers</p>
    			<?php if ($item->mrkcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->markers as $i=>$rev) { 
    					//echo '<li><a href="'.$mrklink.$rev->id.'">'.$rev->title.'</a></li> ';
    					echo '<li>'.$rev->title.'</li> ';
    				} ?>				
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
 <?php endif; ?>
 <?php if ($this->trkcats) : ?>
    	<div class= "span4">
    		<div class="xbbox xbboxmag xbyscroll xbmh300">
    			<p><?php echo $item->trkcnt; ?> tracks</p>
    			<?php if ($item->trkcnt > 0 ) : ?>
    				<ul>
    				<?php foreach ($item->tracks as $i=>$rev) { 
    					echo '<li><a href="'.$trklink.$rev->id.'">'.$rev->title.'</a></li> ';
    				} ?>				
    				</ul>
    			<?php else: ?>
    				<p class="xbnit"><?php echo Text::_('no items assigned to category')?></p>
    			<?php endif; ?>
    		</div>
    	</div>
<?php endif; ?>
</div>
<div class="clearfix"></div>
<p class="xbtc xbmt16">
	<a href="<?php echo $cclink; ?>" class="btn btn-small">
		<?php echo JText::_('XBMAPS_CATSLIST'); ?>
	</a>
</p>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>


