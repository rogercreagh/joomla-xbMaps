<?php
/*******
 * @package xbMaps
 * @version 0.1.0.k 16th July 2021
 * @filesource site/helpers/xbmaps.php
 * @author Roger C-O
 * @copyright Copyright (c) Roger Creagh-Osborne, 2021
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 ******/
defined( '_JEXEC' ) or die( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;

class XbmapsHelper extends ContentHelper {
	
	public static function sitePageHeader($displayData) {
		$header ='';
		if (!empty($displayData)) {
			$header = '	<div class="row-fluid"><div class="span12 xbpagehead">';
			if ($displayData['showheading']) {
				$header .= '<div class="page-header"><h1>'.$displayData['heading'].'</h1></div>';
			}
			if ($displayData['title'] != '') {
				$header .= '<h3>'.$displayData['title'].'</h3>';
				if ($displayData['subtitle']!='') {
					$header .= '<h4>'.$displayData['subtitle'].'</h4>';
				}
				if ($displayData['text'] != '') {
					$header .= '<p>'.$displayData['text'].'</p>';
				}
			}
		}
		return $header;
	}
	
	public static function credit() {
		if (self::penPont()) {
			return '';
		}
		$credit='<div class="xbcredit">';
		$credit .= Text::_('XBMAPS_BEER_CREDIT');
		$credit .= '</div>';
		return $credit;
	}
	
	private static function penPont() {
		$params = ComponentHelper::getParams('com_xbmaps');
		$beer = trim($params->get('roger_beer'));
		//        Factory::getApplication()->enqueueMessage(password_hash($beer.'PASSWORD_DEFAULT'));
		$hashbeer = $params->get('penpont');
		if (password_verify($beer,$hashbeer)) { return true; }
		return false;
	}
	
	
}