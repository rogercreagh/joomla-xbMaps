<?php
/*******
 * @package xbMaps
 * @filesource site/layouts/xbpvmodal/layoutpvmodal.php
 * @version 1.4.4.0 14th December 2023
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Uri\Uri;

?>
<script>
jQuery(document).ready(function(){
    jQuery(this).find('.modal-body iframe').attr("src","");
    jQuery('#ajax-xbmodal').on('show', function () {
        jQuery(this).find('.modal-body iframe').attr("src",
            "/index.php?option=com_xb"+window.com+"\&view="+window.view+"\&layout=default\&tmpl=component&id="+window.pvid);
        jQuery(this).find('.modal-header h4').html("Preview "+window.view);
    })
    jQuery('#xbif').load(function () {
        jQuery('.modal-body iframe').height(jQuery(this).contents().height());
    })
    jQuery('#ajax-xbmodal').on('hidden', function () {
        jQuery(this).find('.modal-body iframe').attr("src","");
    })
});
// fix multiple backdrops
jQuery(document).bind('DOMNodeInserted', function(e) {
    var element = e.target;
    if (jQuery(element).hasClass('modal-backdrop')) {
         if (jQuery(".modal-backdrop").length > 1) {
           jQuery(".modal-backdrop").not(':last').remove();
       }
	}    
});
</script>
    
<div class="modal fade xbpvmodal" id="ajax-xbmodal" style="max-width:85%">
    <div class="modal-dialog">
    	<div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"
        		style="opacity:unset;line-height:unset;border:none;font-size:2rem;">&times;</button>
        	<h4 class="modal-title" style="margin:5px;">Preview</h4>
        </div>
        <div class="modal-body">
        	<div style="margin:0 30px;">
        		<iframe id="xbif" src="" title="Preview"></iframe>
        	</div>
        </div>
    </div>
</div>
  