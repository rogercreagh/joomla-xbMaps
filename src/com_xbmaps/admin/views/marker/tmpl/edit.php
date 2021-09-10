<?php
/*******
 * @package xbMaps
 * @version 0.1.2.c 9th September 2021
 * @filesource admin/views/marker/tmpl/edit.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$popuptitle = ($this->form->getValue('title')=='') ? 'Marker Title' : $this->form->getValue('title');
$popupdesc = ($this->form->getValue('description')!='') ? $this->form->getValue('description').'<br />':'';
$popupdesc .= '<i>Click to map to move marker to new position</i>';
$uid = uniqid();
$map = new XbMapHelper($uid, null, true);
$map->loadAPI(false);
$map->loadXbmapsJS();
$zoom = ($this->item->id > 0) ? 14 : $this->default_zoom;
$map->createMap($this->form->getValue('latitude'), $this->form->getValue('longitude'), $zoom );
$map->setMapType($this->map_type);
switch ($this->form->getValue('marker_type')) {
	case 1:
		$image = $this->marker_image_path.'/'.$this->form->getValue('marker_image','params');
		$map->setImageMarker($uid, $this->form->getValue('latitude'), $this->form->getValue('longitude'),$image,$popuptitle,$popupdesc);
		break;
	case 2:
		$outer = $this->form->getValue('marker_outer_icon','params','fas fa-map-marker');
		$inner = $this->form->getValue('marker_inner_icon','params','#00f');
		$outcol = $this->form->getValue('marker_outer_colour','params','');
		$incol = $this->form->getValue('marker_inner_colour','params','#fff');
		$insize = '';
		if ($this->form->getValue('marker_outer_icon','params')=='fas fa-map-marker') {
			$insize = 'line-height:1.75em;font-size:0.8em;';
		}
		
		$div = '<div><span class="fa-stack fa-2x" style="margin-left:-1em;margin-top:-2em;font-size:12pt;">';
		$div .= '<i class="'.$outer.' fa-stack-2x" style="color:'.$outcol.';"></i>';
		
		$div .= '<i class="'.$inner.' fa-stack-1x fa-inverse" style="color:'.$incol.';'.$insize.'"></i>';
		$div .= '</span></div>';
		$map->setDivMarker($uid, $this->form->getValue('latitude'),$this->form->getValue('longitude'),$div, $popuptitle,$popupdesc,'','',1);
	default:
		$map->setMarker($uid, $popuptitle, $popupdesc, $this->form->getValue('latitude'), $this->form->getValue('longitude'),'','','',1);
		
	break;
}
//$map->setDivMarker($uid,$this->form->getValue('title'),$popupdesc, $this->form->getValue('latitude'), $this->form->getValue('longitude'),'<div><span class="fa-stack fa-2x" style="margin-left:-1em;margin-top:-2em;"><i class="fas fa-map-pin fa-stack-2x" style="color:#00f;"></i><i class="fas fa-flag-checkered fa-stack-1x fa-inverse" style="color:#ff0;line-height:1.5em;font-size:0.7em;"></i></span></div>');
$map->endZoom();
$map->markerPosClick($uid);
$map->renderSearch($uid);
$map->renderFullScreenControl();

$map->renderMap();

?>
<form action="<?php echo JRoute::_('index.php?option=com_xbmaps&view=marker&layout=edit&id=' . (int) $this->item->id); ?>"
	class="form-validate" enctype="multipart/form-data"
    method="post" name="adminForm" id="adminForm">
 	<div class="row-fluid">
		<div class="span12">
         	<div class="row-fluid">
        		<div class="span11">
        			<?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        		</div>
        		<div class="span1"><?php echo $this->form->renderField('id'); ?></div>
        	</div>
        	<div class="row-fluid">
        		<div class="span6">
            	    <?php echo $this->form->renderField('maplist'); ?>            	    	 					
        		</div>
        		<div class="span6">
            	    <?php echo $this->form->renderField('description'); ?>            	    	 					
        		</div>
        	</div>
        </div>
	</div>
    <div class="row-fluid form-horizontal">
 		<div class="span12">
				<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
	
				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'details', Text::_('Details')); ?>
				<div class="row-fluid">
		    		<div class="span4">
           	    	<?php echo $this->form->renderField('marker_type'); ?> 
           	    	<?php echo $this->form->renderField('hidmarker_type','params'); ?> 
            	    	<?php echo $this->form->renderField('marker_image','params'); ?> 
            	    	<?php echo $this->form->renderField('marker_outer_icon','params'); ?> 
            	    	<?php echo $this->form->renderField('marker_outer_colour','params'); ?> 
            	    	<?php echo $this->form->renderField('marker_inner_icon','params'); ?> 
            	    	<?php echo $this->form->renderField('marker_inner_colour','params'); ?> 
		    		</div>
					<div class="span5 form-vertical">  	
						<div id="xbmaps" style="margin:0;padding:0;">
							<div align="center" style="margin:0;padding:0">
								<div id="xbMap<?php echo $uid; ?>" style="margin:0;padding:0;width:100%;height:300px">
								</div>
								<div id="coordInfo" class="pull-left"></div>
								<div class="clearfix"></div>
							</div>
						</div>
            	    	<?php echo $this->form->renderField('latitude'); ?>   					
	 	          		<?php echo $this->form->renderField('dmslatitude'); ?>   					
		        		<?php echo $this->form->renderField('longitude'); ?>
		        		<?php echo $this->form->renderField('dmslongitude'); ?>         
	    			</div>
        			<div class="span3">
        				<?php echo LayoutHelper::render('joomla.edit.global', $this); ?>
        			</div>
        		</div>
    			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
    			<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('Publishing')); ?>
    			<div class="row-fluid form-horizontal-desktop">
    				<div class="span6">
    					<?php echo LayoutHelper::render('joomla.edit.publishingdata', $this); ?>
    				</div>
    				<div class="span6">
    					<?php echo LayoutHelper::render('joomla.edit.metadata', $this); ?>
    				</div>
    			</div>
    			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
     			<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
    	
    </div>
    <input type="hidden" name="task" id="task" value="marker.edit" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<div class="clearfix"></div>
<p><?php echo XbmapsGeneral::credit();?></p>

