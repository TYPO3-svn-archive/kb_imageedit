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
 * Class for rendering the History Tree.
 *
 * $Id$
 *
 * @author	Bernhard Kraft <kraftb@think-open.at>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */


require_once (PATH_t3lib.'class.t3lib_treeview.php');


class tx_kbimageedit_browseHistory extends t3lib_treeView {
	
	/**
	 * Constructor. This method initializes the object
	 *
	 * @return	void
	 */
	public function tx_kbimageedit_browseHistory()	{
		$this->treeName='history';
		$this->titleAttrib=''; //don't apply any title
		$this->domIdPrefix = 'history';
		parent::init();
	}

	/**
	 * This "init" method is called by the parent object
	 *
	 * @param	object	A reference to the parent object
	 * @return	void
	 */
	public function init($parent)	{
		$this->parent = &$parent;
		$this->backPath = &$parent->doc->backPath;
		$this->iconPath = 'gfx/fileicons/';
		$this->expandFirst = true;		// If set, the first element in the tree is always expanded.
	}

	/**
	 * Get icon for the row.
	 * If $this->iconPath and $this->iconName is set, try to get icon based on those values.
	 *
	 * @param	array			Item row
	 * @return	string		Image tag
	 */
	public function getIcon($row) {
		if ($this->iconPath) {
			$icon = '<img'.t3lib_iconWorks::skinImg($this->backPath, $this->iconPath.$row['icon'],'width="18" height="16"').' alt="" />';
		} 
		return $this->wrapIcon($icon,$row);
	}
	
	/**
	 * Returns the root icon for a tree/mountpoint (defaults to the globe)
	 *
	 * @param	array		Record for root.
	 * @return	string		Icon image tag.
	 */
	public function getRootIcon($rec) {
		return $this->wrapIcon('<img'.t3lib_iconWorks::skinImg($this->backPath,'gfx/fileicons/'.$rec['icon'],'width="18" height="16"').' alt="" />',$rec);
	}
	
	/**
	 * Returns root record for uid (<=0)
	 *
	 * @param	integer		uid, <= 0 (normally, this does not matter)
	 * @return	array		Array with title/uid keys with values of $this->title/0 (zero)
	 */
	public function getRootRecord($uid) {
		reset($this->parent->sessionData['fileTree']);
		$key = key($this->parent->sessionData['fileTree']);
		$title = $this->parent->sessionData['fileTree'][$key]['title'];
		$icon = $this->parent->sessionData['fileTree'][$key]['icon'];
		$path = $this->parent->sessionData['fileTree'][$key]['path'];
		return array('title' => $title, 'uid' => 0, 'icon' => $icon, 'path' => $path);
	}

	/**
	 * Wraps the passed title with a link
	 *
	 * @param	string	The title around which to wrap a link
	 * @param	array		The current database row
	 * @param	integer	bank
	 * @return	string	The wrapped title
	 */
	public function wrapTitle($title,$row,$bank=0)	{
		return '<a href="index.php?revert='.rawurlencode($row['path']).'">'.$title.'</a>';
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_imageedit/class.tx_kbimageedit_browsehistory.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_imageedit/class.tx_kbimageedit_browsehistory.php']);
}

?>
