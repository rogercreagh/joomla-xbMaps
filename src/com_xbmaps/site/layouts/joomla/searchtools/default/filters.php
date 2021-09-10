<?php 
/*******
 * @package xbMaps
 * @version 0.1.0.k 16th July 2021
 * @filesource site/layouts/joomla/searchtools/default/filters.php
 * @desc adds labels to the filter fields
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\FormHelper;

$data = $displayData;

// Load the form filters
$filters = $data['view']->filterForm->getGroup('filter');
$hide = $data['hide'];
?>
<?php if ($filters) : ?>
	<?php foreach ($filters as $fieldName => $field) : ?>
		<?php if ($fieldName !== 'filter_search') : ?>
		<?php if (strpos($hide, $fieldName) === false) : ?>
			<?php $dataShowOn = ''; ?>			
			<?php if ($field->showon) : ?>
				<?php HTMLHelper::_('jquery.framework'); ?>
				<?php HTMLHelper::_('script', 'jui/cms.js', array('version' => 'auto', 'relative' => true)); ?>
				<?php $dataShowOn = " data-showon='" . json_encode(FormHelper::parseShowOnConditions($field->showon, $field->formControl, $field->group)) . "'"; ?>
			<?php endif; ?>
			<div class="js-stools-field-filter" <?php echo $dataShowOn; ?> >
		       <?php echo $field->label; ?>
				<?php echo $field->input; ?>
 			</div>
 		<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>

