<?php
/*******
 * @package xbMaps
 * @version 0.8.0.c 18th October 2021
 * @filesource admin/views/marker/tmpl/edit.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/geocoder.php');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use What3words\Geocoder\Geocoder;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('formbehavior.chosen', '#jform_catid', null, array('disable_search_threshold' => 0 ));
HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
HTMLHelper::_('formbehavior.chosen', 'select');

$popuptitle = ($this->form->getValue('title')=='') ? 'Marker Title' : $this->form->getValue('title');
$popupdesc = '';
if ($this->form->getValue('marker_popdesc','params')) {
	$popupdesc .= ($this->form->getValue('summary')=='') ? '<i>no summary yet</i><br />' : $this->form->getValue('summary').'<br />';
}
$disp = $this->form->getValue('marker_popcoords','params');
if ($disp>0) $popupdesc .= '<hr /><b>Location</b></br>';
$lat = $this->form->getValue('latitude');
$long = $this->form->getValue('longitude');
if (($disp & 1)==1) {
    $popupdesc .= '<span style="padding-right:20px"><i>Lat:</i> '.$lat.'</span><i>Long:</i> '.$long.'<br />';
}
if (($disp & 2)==2) {
    $popupdesc .= '<span style="padding-right:20px"><i>Lat:</i> '.$this->form->getValue('dmslatitude').'</span><i>Long:</i> '.$this->form->getValue('dmslongitude').'<br />';
}
if ($disp > 3) {
    $w3w = $this->form->getValue('marker_w3w','params');
    if ($w3w=='') {
        $api = new Geocoder($this->params->get('w3w_api'));
        $w3w = $api->convertTo3wa($lat,$long)['words'];
        $this->form->setValue('marker_w3w','params',$w3w);
    }
    $popupdesc .= '<i>What 3 Words</i>: <b>/// '.$w3w.'</b>';
}
$popupdesc .= '<hr /><i>Click to map to move marker</i>';



$lat = $this->form->getValue('latitude');
$long = $this->form->getValue('longitude');
$uid = uniqid();
$map = new XbMapHelper($uid, null, true);
$map->loadAPI(false);
$map->loadXbmapsJS();
$zoom = ($this->item->id > 0) ? 13 : $this->default_zoom;
$map->createMap($lat, $long, $zoom );
$map->setMapType($this->map_type);
switch ($this->form->getValue('marker_type')) {
	case 1:
		$image = $this->marker_image_path.'/'.$this->form->getValue('marker_image','params');
		$map->setImageMarker($uid, $lat, $long, $image, $popuptitle, $popupdesc,'','',1);
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
		$map->setDivMarker($uid, $lat, $long, $div, $popuptitle,$popupdesc,'','',1);
		break;
	default:
		$map->setMarker($uid, $lat, $long, $popuptitle, $popupdesc,'','',1);		
	break;
}
$map->endZoom();
$map->markerPosClick($uid,$disp);
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
        		<div class="span3">
            	    <?php echo $this->form->renderField('summary'); ?>            	    	 					
        		</div>
        		<div class="span9">
            	    <?php echo $this->form->renderField('maplist'); ?>            	    	 					
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
            	    	<hr />
            	    	<div class="form-vertical">
	            	    	<?php echo $this->form->renderField('marker_popdesc','params'); ?> 
          	    	<?php echo $this->form->renderField('hid_w3wapi','params'); ?> 
	            	    	<?php echo $this->form->renderField('marker_popcoords','params'); ?> 
            	    	</div>
		    		</div>
					<div class="span5">
						<div class="form-vertical">
							<div id="xbmaps" style="margin:0;padding:0;">
								<div align="center" style="margin:0;padding:0">
									<div id="xbMap<?php echo $uid; ?>" style="margin:0;padding:0;width:100%;height:300px">
									</div>
									<div id="coordInfo" class="pull-left"></div>
									<div class="clearfix"></div>
								</div>
							</div>
	                  		<div class="row-fluid">
		                  		<div class="span6"><?php echo $this->form->renderField('latitude'); ?> </div>
		                  		<div class="span6"><?php echo $this->form->renderField('dmslatitude'); ?></div>
	                  		</div>
	                  		<div class="row-fluid">
		                  		<div class="span6"><?php echo $this->form->renderField('longitude'); ?></div>
		                  		<div class="span6"><?php echo $this->form->renderField('dmslongitude'); ?></div>
	                  		</div>
							</div> 
							<?php if ($this->w3w_api!='') : ?>
		                  		<div class="form-horizontal pull-left"><?php echo $this->form->renderField('marker_w3w','params'); ?></div>
		                  		<div class="pull-left"><button type="button" onclick="xbFormUpdatew3w(document.getElementById('jform_params_marker_w3w').value);">Update Map</button></div>
								<div class="clearfix"></div>
							<?php endif; ?>
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

