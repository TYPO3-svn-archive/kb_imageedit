<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2009 Bernhard Kraft (kraftb@think-open.at)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Addition of an item to the clickmenu
 *
 * @author	Bernhard Kraft <kraftb@think-open.at>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   42: class tx_kbimageedit_cm1
 *   54:     public function main(&$backRef,$menuItems,$table,$uid)
 *   90:     protected function includeLL()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_kbimageedit_cm1 {


	/**
	 * The clickmenu method. This method adds the new menu-item for the image editor to the clickmenu
	 *
	 * @param	object		A reference to the calling object
	 * @param	array		The current list of menu items
	 * @param	string		The current table. As this is a filelist-clickmenu this parameter contains the absolute path of the clicked file
	 * @param	integer		The current uid. As this is a filelist-clickmenu this parameter is empty
	 * @return	array		The list of menu items with the new option for the image editor
	 */
	public function main(&$backRef,$menuItems,$table,$uid) {
		global $BE_USER,$TCA,$LANG;

		$localItems = Array();
		if (!$backRef->cmLevel) {

				// Returns directly, because the clicked item was not a file
			if (!is_file($table))	return $menuItems;
				// Returns directly if the extension is no supported image file
			$parts = explode('.', $table);
			$ext = strtolower(array_pop($parts));
			if ($ext!='jpg'&&$ext!='jpeg'&&$ext!='png'&&$ext!='gif')	return $menuItems;

				// Adds the regular item:
			$this->includeLL();
				// Repeat this (below) for as many items you want to add!
				// Remember to add entries in the localconf.php file for additional titles.
			$url = t3lib_extMgm::extRelPath('kb_imageedit').'cm1/index.php?file='.rawurlencode($table);
			$localItems[] = $backRef->linkItem(
				$GLOBALS['LANG']->getLL('cm1_title'),
				$backRef->excludeIcon('<img src="'.t3lib_extMgm::extRelPath('kb_imageedit').'cm1/cm_icon.gif" width="15" height="12" border=0 align=top>'),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
			);

			// Simply merges the two arrays together and returns ...
			$menuItems=array_merge($menuItems,$localItems);
		}
		return $menuItems;
	}

	/**
	 * Includes the [extDir]/locallang.xml for usage in this module
	 *
	 * @return	void
	 */
	protected function includeLL() {
		global $LANG;
		$LANG->includeLLFile('EXT:kb_imageedit/locallang.xml');
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_imageedit/class.tx_kbimageedit_cm1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_imageedit/class.tx_kbimageedit_cm1.php']);
}

?>
