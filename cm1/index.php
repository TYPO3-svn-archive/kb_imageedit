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
 * kb_imageedit module cm1
 *
 * @author	Bernhard Kraft <kraftb@think-open.at>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  112: class tx_kbimageedit_cm1 extends t3lib_SCbase
 *  130:     public function menuConfig()
 *  313:     public function main()
 *  611:     protected function getTreeEntry(&$arr, $actualPath, $file, $saved, $titleAdd, $pid)
 *  641:     public function printContent()
 *  655:     protected function &getFileFromPath($path, &$subtree)
 *  683:     protected function moduleContent()
 *  702:     protected function getMenuItem_ImageInfo()
 *  747:     protected function getMenuItem_ImageEdit()
 *  882:     protected function render__effect_none($HTML, $cmd = '')
 *  906:     protected function render__effect_simple($HTML, $cmd = '')
 *  935:     protected function render__effect_double($HTML, $cmd = '')
 *  967:     protected function render__effect_rotate($HTML)
 * 1066:     protected function render__effect_gamma($HTML)
 * 1122:     protected function render__file_save($HTML)
 * 1161:     protected function render__edit_crop($HTML)
 * 1278:     protected function render__edit_scale($HTML)
 * 1571:     protected function getMenuItem_History()
 * 1611:     protected function func__effect_none($image, $cmd, $x, $y)
 * 1631:     protected function func__effect_simple($image, $cmd, $x, $y)
 * 1677:     protected function func__effect_double($image, $cmd, $x, $y)
 * 1707:     protected function func__effect_gamma($image, $cmd, $x, $y)
 * 1737:     protected function func__effect_rotate($image, $cmd, $x, $y, $angle = false, $dontstore = false)
 * 1763:     protected function func__file_save($save, $cmd, $x, $y)
 * 1805:     protected function func__edit_crop($image, $cmd, $x, $y)
 * 1832:     protected function func__edit_scale($image, $cmd, $x, $y)
 * 1852:     protected function identifyColors($file)
 * 1880:     protected function storeImage_Object($im, $origname, $action)
 * 1920:     protected function storeImage_File($newname, $action)
 * 1944:     protected function getTempName($origname)
 * 1956:     protected function cleanUpTempDir()
 * 1970:     protected function checkSVGsupport()
 *
 * TOTAL FUNCTIONS: 31
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

// define actoin constants
define('ACTION_FILE_SAVE', 'file_save');						// Done
define('ACTION_EDIT_CROP', 'edit_crop');						// TODO: Every corner should be dragable
define('ACTION_EDIT_SCALE', 'edit_scale');					// Done
define('ACTION_EDIT_TEXT', 'edit_text');						// Not implemented
define('ACTION_EFFECT_BLUR', 'effect_blur');					// Done
define('ACTION_EFFECT_SHARPEN', 'effect_sharpen');		// Done
define('ACTION_EFFECT_GAMMA', 'effect_gamma');				// Done
define('ACTION_EFFECT_SOLARIZE', 'effect_solarize');	// Done
define('ACTION_EFFECT_SWIRL', 'effect_swirl');				// Done
define('ACTION_EFFECT_WAVE', 'effect_wave');					// Done
define('ACTION_EFFECT_CHARCOAL', 'effect_charcoal');	// Done
define('ACTION_EFFECT_GRAY', 'effect_gray');					// Done
define('ACTION_EFFECT_EDGE', 'effect_edge');					// Done
define('ACTION_EFFECT_EMBOSS', 'effect_emboss');			// Done
define('ACTION_EFFECT_FLIP', 'effect_flip');					// Done
define('ACTION_EFFECT_FLOP', 'effect_flop');					// Done
define('ACTION_EFFECT_ROTATE', 'effect_rotate');			// Done SVG
define('ACTION_EFFECT_COLORS', 'effect_colors');			// Done
define('ACTION_EFFECT_SHEAR', 'effect_shear');				// TODO: SVG
define('ACTION_EFFECT_INVERT', 'effect_invert');			// Done
define('ACTION_IMAGE_OVERLAY', 'image_overlay');			// Not implemented
define('ACTION_DRAW_RECTANGLE', 'draw_rectangle');		// TODO: SVG
define('ACTION_DRAW_CIRCLE', 'draw_circle');					// TODO: SVG


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:kb_imageedit/cm1/locallang.php');
#include ('locallang.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
require_once (PATH_t3lib.'class.t3lib_stdgraphic.php');
	// ....(But no access check here...)
	// DEFAULT initialization of a module [END]
require_once (t3lib_extMgm::extPath('kb_imageedit').'class.tx_kbimageedit_browsehistory.php');

class tx_kbimageedit_cm1 extends t3lib_SCbase {

		// Defines the array-key in which the sub-elements are stored
	var $_SUB_LEVEL = '_SUB_LEVEL';
	var $SVGsupport = true;
	var $useSVG = true;
	var $ajax = true;
	var $cropFrameX = false;
	var $cropFrameY = false;
	var $scale_x= 0;
	var $scale_y= 0;
	var $aspect = '';

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	public function menuConfig()	{
		global $LANG;
		$this->modTSconfig = t3lib_BEfunc::getModTSconfig($this->id,'mod.'.$this->MCONF['name']);
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('function_editor'),
			),
			'action' => Array (
				// Edit functions
				ACTION_EDIT_CROP => $LANG->getLL('action_edit_crop'),
				ACTION_EDIT_SCALE => $LANG->getLL('action_edit_scale'),
				ACTION_EDIT_TEXT => $LANG->getLL('action_edit_text'),
				// File functions
				ACTION_FILE_SAVE => $LANG->getLL('action_file_save'),
				// Effect functions
				ACTION_EFFECT_BLUR => $LANG->getLL('action_effect_blur'),
				ACTION_EFFECT_SHARPEN => $LANG->getLL('action_effect_sharpen'),
				ACTION_EFFECT_GAMMA => $LANG->getLL('action_effect_gamma'),
				ACTION_EFFECT_SOLARIZE => $LANG->getLL('action_effect_solarize'),
				ACTION_EFFECT_SWIRL => $LANG->getLL('action_effect_swirl'),
				ACTION_EFFECT_WAVE => $LANG->getLL('action_effect_wave'),
				ACTION_EFFECT_CHARCOAL => $LANG->getLL('action_effect_charcoal'),
				ACTION_EFFECT_GRAY => $LANG->getLL('action_effect_gray'),
				ACTION_EFFECT_EDGE => $LANG->getLL('action_effect_edge'),
				ACTION_EFFECT_EMBOSS => $LANG->getLL('action_effect_emboss'),
				ACTION_EFFECT_FLIP => $LANG->getLL('action_effect_flip'),
				ACTION_EFFECT_FLOP => $LANG->getLL('action_effect_flop'),
				ACTION_EFFECT_ROTATE => $LANG->getLL('action_effect_rotate'),
				ACTION_EFFECT_COLORS => $LANG->getLL('action_effect_colors'),
				ACTION_EFFECT_SHEAR => $LANG->getLL('action_effect_shear'),
				ACTION_EFFECT_INVERT => $LANG->getLL('action_effect_invert'),
				// Image functions
				ACTION_IMAGE_OVERLAY => $LANG->getLL('action_image_overlay'),
				// Drawing function
				ACTION_DRAW_RECTANGLE => $LANG->getLL('action_draw_rectangle'),
				ACTION_DRAW_CIRCLE => $LANG->getLL('action_draw_circle'),
			),
			'edit_crop_bordercolor' => array(
				 'ff0000' => 'red',
				 '00ff00' => 'green',
				 '0000ff' => 'blue',
				 'ffffff' => 'white',
				 '000000' => 'black',
				 '00ffff' => 'cyan',
				 'ff00ff' => 'magenta',
				 'ffff00' => 'yellow',
			),
			'zoom' => array(
				100 => '100%',
				5 => '5%',
				10 => '10%',
				30 => '30%',
				50 => '50%',
				75 => '75%',
				200 => '200%',
				400 => '400%',
				600 => '600%',
				1000 => '1000%',
				5000 => '5000%',
			),
			'rotate' => array(
				'custom' => $LANG->getLL('rotatePreset_custom'),
				90 => '90° clockwise',
				-90 => '90° counter-clockwise',
				180 => '180°',
			),
		);
		if (intval($this->modTSconfig['properties']['clearZoomLevels']))	{
					$this->MOD_MENU['zoom'] = array();
		}
		if (is_array($this->modTSconfig['properties']['zoomLevels.']))	{
			foreach ($this->modTSconfig['properties']['zoomLevels.'] as $key => $label)	{
				$label = trim($label);
				if ($label=='-')	{
					unset($this->MOD_MENU['zoom'][$key]);
				} else	{
					$this->MOD_MENU['zoom'][$key] = $label;
				}
			}
		}
		if (is_array($this->modTSconfig['properties']['rotatePresets.']))	{
			foreach ($this->modTSconfig['properties']['rotatePresets.'] as $key => $label)	{
				$label = trim($label);
				if ($label=='-')	{
					unset($this->MOD_MENU['rotate'][$key]);
				} else	{
					$this->MOD_MENU['rotate'][$key] = $label;
				}
			}
		}
		if ($this->modTSconfig['properties']['disable_EDIT_CROP'])	{
			unset($this->MOD_MENU['action'][ACTION_EDIT_CROP]);
		}
		if ($this->modTSconfig['properties']['disable_EDIT_SCALE'])	{
			unset($this->MOD_MENU['action'][ACTION_EDIT_SCALE]);
		}
		if ($this->modTSconfig['properties']['disable_EDIT_TEXT'])	{
			unset($this->MOD_MENU['action'][ACTION_EDIT_TEXT]);
		}
		if ($this->modTSconfig['properties']['disable_FILE_SAVE'])	{
			unset($this->MOD_MENU['action'][ACTION_FILE_SAVE]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_BLUR'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_BLUR]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_SHARPEN'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_SHARPEN]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_GAMMA'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_GAMMA]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_SOLARIZE'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_SOLARIZE]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_SWIRL'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_SWIRL]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_WAVE'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_WAVE]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_CHARCOAL'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_CHARCOAL]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_GRAY'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_GRAY]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_EDGE'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_EDGE]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_EMBOSS'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_EMBOSS]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_FLIP'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_FLIP]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_FLOP'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_FLOP]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_ROTATE'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_ROTATE]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_COLORS'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_COLORS]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_SHEAR'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_SHEAR]);
		}
		if ($this->modTSconfig['properties']['disable_EFFECT_INVERT'])	{
			unset($this->MOD_MENU['action'][ACTION_EFFECT_INVERT]);
		}
		if ($this->modTSconfig['properties']['disable_IMAGE_OVERLAY'])	{
			unset($this->MOD_MENU['action'][ACTION_IMAGE_OVERLAY]);
		}
		if ($this->modTSconfig['properties']['disable_DRAW_RECTANGLE'])	{
			unset($this->MOD_MENU['action'][ACTION_DRAW_RECTANGLE]);
		}
		if ($this->modTSconfig['properties']['disable_DRAW_CIRCLE'])	{
			unset($this->MOD_MENU['action'][ACTION_DRAW_CIRCLE]);
		}
		$this->MOD_MENU['cropFrames']['custom'] = $LANG->getLL('cropFrames_custom');
		if (is_array($this->modTSconfig['properties']['cropFrames.'])&&count($this->modTSconfig['properties']['cropFrames.']))	{
			foreach ($this->modTSconfig['properties']['cropFrames.'] as $key => $arr)	{
				if (intval($arr['width'])&&intval($arr['height']))	{
					$this->MOD_MENU['cropFrames'][$key] = $arr['label'];
				}
			}
		}
		$this->MOD_MENU['scaleFrames']['custom'] = $LANG->getLL('scaleFrames_custom');
		if (is_array($this->modTSconfig['properties']['scaleFrames.'])&&count($this->modTSconfig['properties']['scaleFrames.']))	{
			foreach ($this->modTSconfig['properties']['scaleFrames.'] as $key => $arr)	{
				if ((intval($arr['width'])&&intval($arr['height']))||(intval($arr['width'])&&intval($arr['aspect']))||(intval($arr['height'])&&intval($arr['aspect'])))	{
					$this->MOD_MENU['scaleFrames'][$key] = $arr['label'];
				}
			}
		}
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	public function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Draw the header.

		$this->doc = t3lib_div::makeInstance('bigDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->form='<form action="index.php" method="POST" name="kb_imageedit_form" enctype="multipart/form-data" target="_self">';

		if ($this->modTSconfig['properties']['disable_zoom'])	{
			$this->MOD_SETTINGS['zoom'] = 100.0;
		}

		$this->doc->bodyTagAdditions = '###ON_LOAD_EVENT###';

			// JavaScript
			// String "function" is split up because of extdeveval and API-Header at top of class file
		$this->doc->JScode = $this->doc->wrapScriptTags('
	func'.'tion windowLoad()	{
//###ADD_ONLOAD###
	}

var zoomLevel = '.$this->MOD_SETTINGS['zoom'].';

				script_ended = 0;
				func'.'tion jumpToUrl(URL)	{
					document.location = URL;
				}
			');
		$this->doc->JScode .= $this->doc->getDynTabMenuJScode();

		$file = t3lib_div::_GP('file');
		session_start();
		if (!strlen($file))	{
			if (!($sessionData = $_SESSION[$GLOBALS['MCONF']['name']]))	{
				$this->content = 'ERROR: No file or session data !';
				return;
			} else {
				$this->sessionData = unserialize($sessionData);
				$actualPath = $this->sessionData['actualPath'];
				if ($pathOverrule = t3lib_div::_GP('revert'))	{
					$this->sessionData['actualPath'] = $actualPath = $pathOverrule;
				}
				$actualPath = t3lib_div::trimExplode('/', $actualPath, 1);
				$file = $this->getFileFromPath($actualPath, $subtree = false);
				$file = $file[0];
				if (!@is_file($file))	{
					$this->content = 'ERROR: Invalid file \''.$file.'\' from session_data !';
				}
			}
		} else {
			if (!@is_file($file))	{
				$this->content = 'ERROR: Invalid file \''.$file.'\' !';
				return;
			}
			$this->cleanUpTempDir();
			$init_sessionData = 1;
		}

		$this->filefunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$f_ext = array(
			'webspace' => array(
				'allow' => 'jpg,jpeg,png,gif',
				'deny' => '',
			),
			'ftpspace' => array(
				'allow' => 'jpg,jpeg,png,gif',
				'deny' => '',
			),
		);
		$this->filefunc->init($GLOBALS['FILEMOUNTS'], $f_ext);
		$access = $this->filefunc->checkPathAgainstMounts($file)||(strpos($file, PATH_site.'typo3temp/kb_imageedit/')===0);

		if (strlen($access))	{
			if ($init_sessionData)	{
				$this->sessionData = array();
				$uid = $this->getTreeEntry($this->sessionData['fileTree'], '', $file, true, $LANG->getLL('original_file'), 0);
				$this->sessionData['actualPath'] = $uid;
				$this->sessionData['startFile'] = $file;
			}

			$this->absoluteFile = $file;
			$this->baseFile = basename($file);
			$this->relativeFile = $this->doc->backPath.'../'.basename($file);
			$this->stdGraphic = t3lib_div::makeInstance('t3lib_stdGraphic');
			$this->stdGraphic->init();
			$this->stdGraphic->NO_IM_EFFECTS = 0;
			if (!@is_file($this->absoluteFile))	{
				$this->content = 'File "'.$this->absoluteFile.'" not found !';
				return;
			}
			$dim = $this->stdGraphic->imageMagickIdentify($this->absoluteFile);
			if (!is_array($dim))	{
				$this->content = 'Could not read file "'.$this->absoluteFile.'" (check ImageMagick/GrapicsMagick configuration) !';
				return;
			}

			$this->x = $dim[0];
			$this->y = $dim[1];
			$this->format = $dim[2];
			if (!t3lib_div::inList('jpg,jpeg,png,gif', strtolower($this->format)))	{
				$this->content = 'Imageformat "'.$this->format.'" not supported !';
				return;
			}

				 // Perform Image operation
			$action = t3lib_div::_GP('action_save');
			if (strlen($action))	{
				$action = 'file_save';
			} else	{
				$action = t3lib_div::_GP('action');
			}
			if (in_array($action, array(ACTION_EDIT_CROP, ACTION_EDIT_SCALE, ACTION_EFFECT_BLUR, ACTION_EFFECT_SHARPEN, ACTION_EFFECT_GAMMA, ACTION_EFFECT_SOLARIZE, ACTION_EFFECT_WAVE, ACTION_EFFECT_SWIRL, ACTION_EFFECT_CHARCOAL, ACTION_EFFECT_GRAY, ACTION_EFFECT_EDGE, ACTION_EFFECT_EMBOSS, ACTION_EFFECT_COLORS, ACTION_EFFECT_INVERT, ACTION_EFFECT_FLIP, ACTION_EFFECT_FLOP, ACTION_EFFECT_ROTATE, ACTION_FILE_SAVE)))	{
				switch ($action)	{
					case ACTION_EFFECT_GRAY:
					case ACTION_EFFECT_INVERT:
					case ACTION_EFFECT_FLIP:
					case ACTION_EFFECT_FLOP:
						$this->func__effect_none($file, $action, $this->x, $this->y);
					break;
					case ACTION_EFFECT_BLUR:
					case ACTION_EFFECT_SHARPEN:
					case ACTION_EFFECT_SOLARIZE:
					case ACTION_EFFECT_SWIRL:
					case ACTION_EFFECT_CHARCOAL:
					case ACTION_EFFECT_EDGE:
					case ACTION_EFFECT_EMBOSS:
					case ACTION_EFFECT_COLORS:
						$this->func__effect_simple($file, $action, $this->x, $this->y);
					break;
					case ACTION_EFFECT_WAVE:
						$this->func__effect_double($file, $action, $this->x, $this->y);
					break;
					default:
						$func = 'func__'.$action;
						$this->$func($file, $action, $this->x, $this->y);
					break;
				}
			}

			$this->useSVG = false;
			switch ((string)$this->MOD_SETTINGS['action'])	{
				case ACTION_EFFECT_ROTATE:
					$this->checkSVGsupport();
					$this->useSVG = $this->SVGsupport;
				break;
			}

				// Reload file from sessionData : may have been modified by processing methods
			$actualPath = $this->sessionData['actualPath'];
			$actualPath = t3lib_div::trimExplode('/', $actualPath, 1);
			$file = $this->getFileFromPath($actualPath, $subtree = false);
			$file = $file[0];
			if (!@is_file($file))	{
				$this->content = 'ERROR: Invalid file \''.$file.'\' from session_data after processing !';
				return;
			}
			$this->absoluteFile = $file;
			$this->baseFile = str_replace(PATH_site, '', $file);
			$this->relativeFile = $this->doc->backPath.'../'.str_replace(PATH_site, '', $file);
			$dim = $this->stdGraphic->imageMagickIdentify($this->absoluteFile);
			$this->x = $dim[0];
			$this->y = $dim[1];
			$zoom = $this->MOD_SETTINGS['zoom']/100.0;
			$this->zoom_x = round($this->x*$zoom);
			$this->zoom_y = round($this->y*$zoom);
			$cf = $this->modTSconfig['properties']['cropFrames.'][$this->MOD_SETTINGS['cropFrames']];
			if (is_array($cf)&&$cf['width']&&$cf['height'])	{
				$this->cropFrameX = intval($cf['width']);
				$this->cropFrameX_zoom = round(intval($cf['width'])*$zoom);
				$this->cropFrameY = intval($cf['height']);
				$this->cropFrameY_zoom = round(intval($cf['height'])*$zoom);
			} else	{
				$this->cropFrameX = intval($this->x/2);
				$this->cropFrameX_zoom = intval($this->zoom_x/2);
				$this->cropFrameY = intval($this->y/2);
				$this->cropFrameY_zoom = intval($this->zoom_y/2);
			}
			$sf = $this->modTSconfig['properties']['scaleFrames.'][$this->MOD_SETTINGS['scaleFrames']];
			if (is_array($sf)&&$sf['width']&&$sf['height'])	{
				$this->scale_x = $sf['width'];
				$this->scale_y = $sf['height'];
				$this->aspect = '';
			} elseif (is_array($sf)&&$sf['width']&&$sf['aspect'])	{
				$this->scale_x = $sf['width'];
				$this->aspect = 'x';
			} elseif (is_array($sf)&&$sf['height']&&$sf['aspect'])	{
				$this->scale_y = $sf['height'];
				$this->aspect = 'y';
			}
			$this->format = $dim[2];
			if (!t3lib_div::inList('jpg,jpeg,png,gif', strtolower($this->format)))	{
				$this->content = 'Imageformat "'.$this->format.'" not supported after processing !';
				return;
			}

			if (strlen($p = t3lib_div::_GP('p')))	{
				switch ($p)	{
					case 'rotate':
						$a = doubleval(t3lib_div::_GP('a'));
						$c = intval(t3lib_div::_GP('c'));
						if ($a==0)	{
							t3lib_ajax::outputXMLreply('<image>
	<file>'.$this->relativeFile.'</file>
	<count>'.$c.'</count>
	<width>'.$this->zoom_x.'</width>
	<height>'.$this->zoom_y.'</height>
	<orig_width>'.$this->x.'</orig_width>
	<orig_height>'.$this->y.'</orig_height>
</image>');
						} else	{
							$tmpImg = $this->func__effect_rotate($file, ACTION_EFFECT_ROTATE, $this->x, $this->y, $a, true);
							$relativeFile = $this->doc->backPath.'../'.str_replace(PATH_site, '', $tmpImg);
							$dim = $this->stdGraphic->imageMagickIdentify($tmpImg);
							$x = $dim[0];
							$y = $dim[1];
							$zoom_x = round($x*$zoom);
							$zoom_y = round($y*$zoom);
							t3lib_ajax::outputXMLreply('<image>
	<file>'.$relativeFile.'</file>
	<count>'.$c.'</count>
	<width>'.$zoom_x.'</width>
	<height>'.$zoom_y.'</height>
	<orig_width>'.$x.'</orig_width>
	<orig_height>'.$y.'</orig_height>
</image>');
						}
					break;
				}
				exit();
			}

				// Load CSS from external file
			$CSS_file = t3lib_div::getFileAbsFileName('EXT:kb_imageedit/res/image_edit.css');
			$CSS = t3lib_div::getURL($CSS_file);
			$this->doc->inDocStyles = $CSS;

			$filename = basename($this->absoluteFile);
			$headerSection = $this->doc->getHeader('files', 0, $filename);


			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$this->content .= $this->doc->wrapScriptTags('
//###INIT_CODE###
');
			if ($this->useSVG)	{
					// Set docType after start page cause we need to set our own <?xml...> tag (with SVG support included)
				header ('Content-Type:application/xhtml+xml;charset='.$this->charset);
				$this->doc->docType = 'xhtml_trans';
				$parts = explode(chr(10), $this->content, 5);
				$header = preg_replace('/<link\s+rel="stylesheet"([^>]*)href="([^"]*)\/typo3\/stylesheet.css"([^>])\/>/', '<link rel="stylesheet" href="../res/stylesheet.css" type="text/css" id="basicStyle" />', $parts[4]);
				$header = preg_replace('/<meta\s+http-equiv="Content-Type"\scontent="text\/html([^"]*)"([^>]*)\/>/', '<meta http-eqiv="Content-Type" content="application/xhtml+xml$1"$2 />', $header);
				$this->content = '<?xml version="1.0" encoding="'.$this->doc->charset.'"?>
<!DOCTYPE html
		PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svg="http://www.w3.org/2000/svg">'.$header;
			}

			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);

			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}
		} else {
			$headerSection = $this->doc->getHeader('files', 0, $filename);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));

			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);

			$this->content.=$this->doc->section('No access !', 'You don\'t have permissions to access the file \''.$file.'\' !',0,1);

		}
		$this->content.=$this->doc->spacer(10);
		$this->content = str_replace('###ON_LOAD_EVENT###', '', $this->content);
	}

	/**
	 * Sets and returns an element in a file history tree
	 *
	 * @param	array		The current file history tree (reference)
	 * @param	string		The current path
	 * @param	string		The file beign set in the tree
	 * @param	booelan		Whether the file was saved or not
	 * @param	string		The title which should get added
	 * @param	integer		The pid/uid which should get set
	 * @return	integer		The uid of the element being set
	 */
	protected function getTreeEntry(&$arr, $actualPath, $file, $saved, $titleAdd, $pid)	{
		global $LANG;
		$uid = hexdec(substr(md5($file),0, 6));
		$parts = explode('.', $file);
		$ext = strtolower(array_pop($parts));
		switch ($ext)	{
			case 'gif':
				$icon = 'gif.gif';
				break;
			case 'png':
				$icon = 'png.gif';
				break;
			case 'jpg':
			case 'jpeg':
				$icon = 'jpg.gif';
				break;
		}
		$titleText = $LANG->getLL('action_'.$titleAdd);
		if (!strlen($titleText))	{
			$titleText = $titleAdd;
		}
		$arr[$uid] = array($file, $saved?1:0, $titleAdd, 'uid' => $uid, 'pid' => $pid, 'title' => basename($file).' ('.$titleText.')', 'icon' => $icon, 'path' => $actualPath.(strlen($actualPath)?'/':'').$uid);
		return $uid;
	}

	/**
	 * Prints out the finally rendered content
	 *
	 * @return	void
	 */
	public function printContent()	{
		$this->content.=$this->doc->endPage();
		$this->content = str_replace('</body>', $this->outOfContext.$this->endDHTML.chr(10).'</body>', $this->content);
		$this->content = str_replace('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">', '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">', $this->content);
		echo $this->content;
	}

	/**
	 * Returns the name of a file from the history file tree by specifying its path
	 *
	 * @param	array		The path to the requested item
	 * @param	array		The tree from which to retrieve an item
	 * @return	mixed		Either the name of the specified file in the path or "false" on error
	 */
	protected function &getFileFromPath($path, &$subtree)	{
		$subtree_before = $subtree;
		if ($subtree===false)	{
			$subtree = &$this->sessionData['fileTree'];
		}
		if (count($path)) {
			$part = array_shift($path);
			if (is_array($subtree[$part])) {
				if (count($path)&&isset($subtree[$part][$this->_SUB_LEVEL]))	{
					return $this->getFileFromPath($path, $subtree[$part][$this->_SUB_LEVEL]);
				} elseif (!count($path)) {
					return $subtree[$part];
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return $subtree;
		}
	}

	/**
	 * This function switches the module functions depending on the selected function
	 *
	 * @return	void
	 */
	protected function moduleContent()	{
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:
				$content = '';
				$this->menuItems = array();
				$this->menuItems[] = $this->getMenuItem_ImageInfo();
				$this->menuItems[] = $this->getMenuItem_ImageEdit();
				$this->menuItems[] = $this->getMenuItem_History();
				$content .= $this->doc->getDynTabMenu($this->menuItems, 'kb_imageedit', 1, true);
				$this->content.=$this->doc->section('Image Editor',$content,0,1);
			break;
		}
	}

	/**
	 * Returns the "Image Info" Tab of the image editors DynTab menu
	 *
	 * @return	array		DynTab array entry
	 */
	protected function getMenuItem_ImageInfo()	{
		global $LANG;
		$HTML = '';
		$HTML .= '<div class="kb_imageedit-dynTab">'.chr(10);
		$HTML .= '<table cellspacing="0" cellpadding="0" border="0" class="kb_imageedit-actiontable">
				<tr>
					<td class="label">
						'.$LANG->getLL('filename').'
					</td>
					<td class="value">
						'.$this->baseFile.'
					</td>
				</tr>
				<tr>
					<td class="label">
						'.$LANG->getLL('image_size').'
					</td>
					<td class="value">
						'.$this->x.' x '.$this->y.' '.$LANG->getLL('pixels').'
					</td>
				</tr>
				<tr>
					<td class="label">
						'.$LANG->getLL('format').'
					</td>
					<td class="value">
						'.$this->format.'
					</td>
				</tr>
			</table>'.chr(10);
		$HTML .= '</div>'.chr(10);
		return array(
			'label' => $LANG->getLL('imageinfo_label'),
			'description' => $LANG->getLL('imageinfo_description'),
			'linkTitle' => $LANG->getLL('imageinfo_description'),
			'content' => $HTML,
			'stateIcon' => 2,
		);
	}

	/**
	 * Returns the "Image Edit " Tab of the image editors DynTab menu
	 *
	 * @return	array		DynTab array entry
	 */
	protected function getMenuItem_ImageEdit()	{
		global $LANG;
		$HTML = '';
		$HTML .= '<script language="javascript" type="text/javascript" src="../res/wz_dragdrop.js"></script>';
		$HTML .= '<script language="javascript" type="text/javascript">
var baseXsize = '.$this->x.';
var baseYsize = '.$this->y.';

var actualXsize = '.$this->zoom_x.';
var actualYsize = '.$this->zoom_y.';

		</script>';
		$HTML .= '<div class="kb_imageedit-dynTab">'.chr(10);
		$HTML .= '<p>'.$LANG->getLL('edit_info').'</p>';
		$HTML .= '<table cellspacing="0" cellpadding="0" border="0" class="kb_imageedit-imagetable">';
		if (!$this->modTSconfig['properties']['disable_zoom'])	{
			$HTML .= '<tr>
					<td class="kb_imageedit-zoomtd">
							'.$LANG->getLL('zoom').' <br />
							'.t3lib_BEfunc::getFuncMenu(0, 'SET[zoom]', $this->MOD_SETTINGS['zoom'], $this->MOD_MENU['zoom']).'
					</td>
				</tr>';
		}
		$HTML .= '<tr>
					<td class="kb_imageedit-imagetd" id="kb_imageedit-imagetd" style="height: '.($this->zoom_y+40).'px; width: '.($this->zoom_x).'px;">
						<div id="kb_imageedit-mainimage" style="width: '.$this->zoom_x.'px; height: '.$this->zoom_y.'px;">
							'.($this->useSVG?'
    					<svg:svg id="kb_imageedit-svgtag" width="'.$this->zoom_x.'px" height="'.$this->zoom_x.'px">
					      <svg:g>
									###IMG_EXTRA###
					        <svg:image id="kb_imageedit-svgimg" xlink:href="'.$this->relativeFile.'"  width="'.$this->zoom_x.'px" height="'.$this->zoom_y.'px" />
					      </svg:g>
					    </svg:svg>
							':'<img id="kb_imageedit-imageself" src="'.$this->relativeFile.'" alt="Image getting processed" style="width:'.$this->zoom_x.'px; height: '.$this->zoom_y.'px;">').'
							###IMG_EXTRA###
						</div>
					</td>
				</tr>
			</table>
		<table cellspacing="0" cellpadding="4" border="0" class="kb_imageedit-actiontable">
				<tr>
					<td class="label">
						'.$LANG->getLL('action_to_perform').'
					</td>
					<td colspan="3">'.chr(10);
		$action = t3lib_BEfunc::getFuncMenu(0,'SET[action]',$this->MOD_SETTINGS['action'],$this->MOD_MENU['action']);
		$HTML .= $action.'
					</td>
				</tr>'.chr(10);
		switch ((string)$this->MOD_SETTINGS['action'])	{
				case ACTION_EDIT_CROP:
					$HTML = $this->render__edit_crop($HTML);
				break;
				case ACTION_EDIT_SCALE:
					$HTML = $this->render__edit_scale($HTML);
				break;
				case ACTION_EDIT_TEXT:
				break;
						// Effect functions
					// Effects which take no arguments
				case ACTION_EFFECT_GRAY:
				case ACTION_EFFECT_INVERT:
				case ACTION_EFFECT_FLIP:
				case ACTION_EFFECT_FLOP:
					$HTML = $this->render__effect_none($HTML, $this->MOD_SETTINGS['action']);
				break;
					// Effects which take one argument
				case ACTION_EFFECT_BLUR:
				case ACTION_EFFECT_SHARPEN:
				case ACTION_EFFECT_SOLARIZE:
				case ACTION_EFFECT_SWIRL:
				case ACTION_EFFECT_CHARCOAL:
				case ACTION_EFFECT_EDGE:
				case ACTION_EFFECT_EMBOSS:
				case ACTION_EFFECT_COLORS:
					$HTML = $this->render__effect_simple($HTML, $this->MOD_SETTINGS['action']);
				break;
					// Effects which take two arguments
				case ACTION_EFFECT_WAVE:
					$HTML = $this->render__effect_double($HTML, $this->MOD_SETTINGS['action']);
				break;
					// Effects which take special arguments
				case ACTION_EFFECT_GAMMA:
					$HTML = $this->render__effect_gamma($HTML);
				break;
				case ACTION_EFFECT_ROTATE:
					$HTML = $this->render__effect_rotate($HTML);
				break;
				case ACTION_EFFECT_SHEAR:
				break;
				// Image functions
				case ACTION_IMAGE_OVERLAY:
				break;
				// Drawing function
				case ACTION_DRAW_RECTANGLE:
				break;
				case ACTION_DRAW_CIRCLE:
				break;
				case ACTION_FILE_SAVE:
					$HTML = $this->render__file_save($HTML);
				break;
				default:
					$HTML .= '<tr>
					<td colspan="4">
						'.$LANG->getLL('error_invalid_action').'
					</td>
				</tr>';
				break;
		}
		if ($action!=ACTION_FILE_SAVE)	{
			$HTML .= '<tr>
				<td colspan="4" class="submit">
					<input type="submit" name="whatever" value="'.$LANG->getLL('perform_action').'" />
				</td>
			</tr>';
			$HTML = $this->render__file_save($HTML);
		}
		$HTML .= '</table>'.chr(10);
		$HTML .= '</div>'.chr(10);
		return array(
			'label' => $LANG->getLL('imageedit_label'),
			'description' => $LANG->getLL('imageedit_description'),
			'linkTitle' => $LANG->getLL('imageedit_description'),
			'content' => $HTML,
			'stateIcon' => 2,
		);
	}

	/**
	 * This methods renders the interface for effects/commands "none" which do not need any parameters (grayscale, etc.)
	 *
	 * @param	string		The current HTML code
	 * @param	string		The command being rendered
	 * @return	string		The rendered interface
	 */
	protected function render__effect_none($HTML, $cmd = '')	{
		global $LANG;
		$HTML = str_replace('###IMG_EXTRA###', '', $HTML);
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_'.$cmd).'</h3>
						</td>
					</tr>
					<tr>
						<td colspan="4" class="label">
							'.$LANG->getLL('action_label_'.$cmd).' :
							<input type="hidden" name="action" value="'.$cmd.'" />
						</td>
					</tr>';
		return $HTML;
	}

	/**
	 * This methods renders the interface for "simple" effects/commands just requiring a single parameter
	 *
	 * @param	string		The current HTML code
	 * @param	string		The command being rendered
	 * @return	string		The rendered interface
	 */
	protected function render__effect_simple($HTML, $cmd = '')	{
		global $LANG;
		$HTML = str_replace('###IMG_EXTRA###', '', $HTML);
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_'.$cmd).'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('action_label_'.$cmd).' :
						</td>
						<td class="input">
							<input type="text" name="factor" value="0" class="number" />
							<input type="hidden" name="action" value="'.$cmd.'" />
						</td>
						<td colspan="2" class="label">
						</td>
					</tr>';
		return $HTML;
	}

	/**
	 * This methods renders the interface for "double" effects/commands requiring two parameters
	 *
	 * @param	string		The current HTML code
	 * @param	string		The command being rendered
	 * @return	string		The rendered interface
	 */
	protected function render__effect_double($HTML, $cmd = '')	{
		global $LANG;
		$HTML = str_replace('###IMG_EXTRA###', '', $HTML);
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_'.$cmd).'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('action_label_'.$cmd).' :
						</td>
						<td class="input">
							<input type="text" name="factor" value="0" class="number" />
							<input type="hidden" name="action" value="'.$cmd.'" />
						</td>
						<td class="label">
							'.$LANG->getLL('action_label2_'.$cmd).' :
						</td>
						<td class="input">
							<input type="text" name="factor2" value="0" class="number" />
						</td>
					</tr>';
		return $HTML;
	}

	/**
	 * This methods renders the interface for the effect/command "rotate"
	 *
	 * @param	string		The current HTML code
	 * @return	string		The rendered interface
	 */
	protected function render__effect_rotate($HTML)	{
		global $LANG;
		$this->content = str_replace('###ON_LOAD_EVENT###', ' onload="windowLoad();" ', $this->content);
		$HTML = str_replace('###IMG_EXTRA###', '<svg:rect id="kb_imageedit-svgbg" width="'.$this->zoom_x.'" height="'.$this->zoom_y.'" fill="rgb(0,0,0)" fill-opacity="1" />', $HTML);
		$replace = array(
			'###ZOOM_X###' => $this->zoom_x,
			'###ZOOM_Y###' => $this->zoom_y,
			'###ZOOM_X_2###' => intval(round($this->zoom_x/2)),
			'###ZOOM_Y_2###' => intval(round($this->zoom_y/2)),
			'###SETTINGS_ROTATE###' => $this->MOD_SETTINGS['rotate']=='custom'?'':'rotate_to('.$this->MOD_SETTINGS['rotate'].');',
			'###USE_AJAX###' => $this->ajax?1:0,
			'###AJAX_CODE###' => $this->ajax?t3lib_ajax::getJScode('updateImage', '', 0):'',
			'###AJAX_LINK###' => t3lib_div::linkThisScript(array('p' => 'rotate')),
		);
		if ($this->useSVG&&$this->SVGsupport)	{
			$JS_file = t3lib_div::getFileAbsFileName('EXT:kb_imageedit/res/image_edit_rotate_SVG.js');
		} else	{
			$JS_file = t3lib_div::getFileAbsFileName('EXT:kb_imageedit/res/image_edit_rotate.js');
		}
		$JS = t3lib_div::getURL($JS_file);
		$JS = str_replace(array_keys($replace), array_values($replace), $JS);
		$HTML .= $this->doc->wrapScriptTags($JS);
		$this->content = str_replace('//###ADD_ONLOAD###', 'initRotate();'.chr(10).'//###ADD_ONLOAD###', $this->content);
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_effect_rotate').'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('rotate_presets').'
						</td>
						<td colspan="3">
							'.t3lib_BEfunc::getFuncMenu(0,'SET[rotate]',$this->MOD_SETTINGS['rotate'],$this->MOD_MENU['rotate']).'
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('action_label_effect_rotate').'
						</td>
						<td class="input">
							<input type="text" name="angle" value="'.(($this->MOD_SETTINGS['rotate']=='custom')?0:$this->MOD_SETTINGS['rotate']).'" class="number" '.((($this->useSVG&&$this->SVGsupport)||$this->ajax)?'onchange="return rotate_to(this.value)"':'').' />
							<input type="hidden" name="action" value="effect_rotate" />
						</td>
						'.($this->modTSconfig['properties']['disable_rotateColors']?'<td colspan="2"></td>':'
						<td class="label">
							'.$LANG->getLL('action_label2_effect_rotate').'
							
						</td>
						<td class="input">
							<input type="text" name="back_red" value="0" class="number" onchange="return update_bg();" />
						</td>
						').'
					</tr>
					'.($this->modTSconfig['properties']['disable_rotateColors']?'
							<input type="hidden" name="back_red" value="0" />
							<input type="hidden" name="back_green" value="0" />
							<input type="hidden" name="back_blue" value="0" />
							<input type="hidden" name="back_alpha" value="0" />
					':'
					<tr>
						<td colspan="2" class="label">
						</td>
						<td class="label">
							'.$LANG->getLL('action_label3_effect_rotate').'
							
						</td>
						<td class="input">
							<input type="text" name="back_green" value="0" class="number" onchange="return update_bg();" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="label">
						</td>
						<td class="label">
							'.$LANG->getLL('action_label4_effect_rotate').'
						</td>
						<td class="input">
							<input type="text" name="back_blue" value="0" class="number" onchange="return update_bg();" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="label">
						</td>
						<td class="label">
							'.$LANG->getLL('action_label5_effect_rotate').'
						</td>
						<td class="input">
							<input type="text" name="back_alpha" value="0" class="number" onchange="return update_bg();" />
						</td>
					</tr>');
		return $HTML;
	}

	/**
	 * This methods renders the interface for the effect/command "gamma"
	 *
	 * @param	string		The current HTML code
	 * @return	string		The rendered interface
	 */
	protected function render__effect_gamma($HTML)	{
		global $LANG;
		$HTML = str_replace('###IMG_EXTRA###', '', $HTML);
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_effect_sharpen').'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
					'.$LANG->getLL('action_label2_effect_sharpen').'
						</td>
						<td class="input">
							<input type="text" name="factor" value="1.0" class="number" onChange="document.forms[0][\'red_factor\'].value = this.value; document.forms[0][\'green_factor\'].value = this.value; document.forms[0][\'blue_factor\'].value = this.value; " />
							<input type="hidden" name="action" value="effect_gamma" />
						</td>
						<td class="label">
							'.$LANG->getLL('action_label3_effect_sharpen').'
							
						</td>
						<td class="input">
							<input type="text" name="red_factor" value="1.0" class="number" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="label">
						</td>
						<td class="label">
						'.$LANG->getLL('action_label4_effect_sharpen').'
						</td>
						<td class="input">
							<input type="text" name="green_factor" value="1.0" class="number" />
						</td>
					</tr>
					<tr>
						<td colspan="2" class="label">
						</td>
						<td class="label">
						'.$LANG->getLL('action_label5_effect_sharpen').'	
						</td>
						<td class="input">
							<input type="text" name="blue_factor" value="1.0" class="number" />
						</td>
					</tr>';
		return $HTML;
	}

	/**
	 * This methods renders the interface for the file-save feature
	 *
	 * @param	string		The current HTML code
	 * @return	string		The rendered interface
	 */
	protected function render__file_save($HTML)	{
		global $LANG;
		$HTML = str_replace('###IMG_EXTRA###', '', $HTML);
		$newname = $this->filefunc->getUniqueName(basename($this->sessionData['startFile']), dirname($this->sessionData['startFile']));
		$newname = basename($newname);
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_file_save').'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('filename').'
						</td>
						<td class="input">
							<input type="text" name="filename" value="'.$newname.'" class="string" />
						</td>
						<td class="label">
							'.$LANG->getLL('overwrite').'
							</td>
						<td class="input">
							<input type="checkbox" name="overwrite" value="1" />
						</td>
					</tr>
					<tr>
						<td class="save">
							<input type="submit" name="action_save" value="'.$LANG->getLL('action_file_save').'" />
						</td>
					</tr>';
		return $HTML;
	}

//CROP BEGIN
	/**
	 * This methods renders the interface for the "crop" effect
	 *
	 * @param	string		The current HTML code
	 * @return	string		The rendered interface
	 */
	protected function render__edit_crop($HTML)	{
		global $LANG;
		$this->content = str_replace('//###INIT_CODE###', '
//###INIT_CODE###
var oldDTM_toggle_Func = DTM_toggle;
var DHTML_initialized = false;
func'.'tion newDTM_toggle(idBase, index, isInit)	{
	oldDTM_toggle_Func(idBase, index, isInit);
	if (index==2)	{
		if (document.getElementById(idBase+"-"+index+"-DIV"))	{
			if ((document.getElementById(idBase+"-"+index+"-DIV").style.display == "block")&&!DHTML_initialized)	{
				DHTML_initialized = true;
				ADD_DHTML("cropdiv"+MAXOFFLEFT+0+MAXOFFRIGHT+'.($this->zoom_x-$this->cropFrameX_zoom).'+MAXOFFTOP+0+MAXOFFBOTTOM+'.($this->zoom_y-$this->cropFrameY_zoom).'+MAXWIDTH+'.$this->zoom_x.'+MAXHEIGHT+'.$this->zoom_y.'+MINWIDTH+3+MINHEIGHT+3);
				dd.elements.cropdiv.div.ondblclick = dblClick;
			}
		}
	}
}

DTM_toggle = newDTM_toggle;
', $this->content);
		$css_extra = '
#cropdiv {
	position: absolute;
	top: 0px;
	left: 0px;
	width: '.($this->cropFrameX_zoom-2).'px;
	height: '.($this->cropFrameY_zoom-2).'px;
	border: 1px solid #'.$this->MOD_SETTINGS['edit_crop_bordercolor'].';
	background-color: transparent;
}
		';
		$this->content = str_replace('/*###CSS_EXTRA###*/', $css_extra.chr(10).'/*###CSS_EXTRA###*/', $this->content);
		$img_extra = '<div name="cropdiv" id="cropdiv" style="z-index: 100;"><img src="clear.gif" width="1" height="1"></div>';
		$this->endDHTML = $this->doc->wrapScriptTags('
SET_DHTML(CURSOR_MOVE, RESIZABLE);
			');

		$HTML = str_replace('###IMG_EXTRA###', $img_extra, $HTML);


			// Load JS from external JS file
		$replace = array(
			'###X_2###' => intval($this->x/2),
			'###Y_2###' => intval($this->y/2),
			'###ZOOM_X###' => $this->zoom_x,
			'###ZOOM_Y###' => $this->zoom_y,
			'###ZOOM_X_2###' => intval(round($this->zoom_x/2)),
			'###ZOOM_Y_2###' => intval(round($this->zoom_y/2)),
		);
		$JS_file = t3lib_div::getFileAbsFileName('EXT:kb_imageedit/res/image_edit_crop.js');
		$JS = t3lib_div::getURL($JS_file);
		$JS = str_replace(array_keys($replace), array_values($replace), $JS);
		$HTML .= $this->doc->wrapScriptTags($JS);


		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_edit_crop').'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('action_label_edit_crop').'
						</td>
						<td colspan="3">
							'.t3lib_BEfunc::getFuncMenu(0,'SET[cropFrames]',$this->MOD_SETTINGS['cropFrames'],$this->MOD_MENU['cropFrames']).'
						</td>
					</tr>
					<tr>
						<td class="label">
						'.$LANG->getLL('border_color').'
						
						</td>
						<td colspan="3">
							'.t3lib_BEfunc::getFuncMenu(0,'SET[edit_crop_bordercolor]',$this->MOD_SETTINGS['edit_crop_bordercolor'],$this->MOD_MENU['edit_crop_bordercolor']).'
						</td>
					</tr>
					<tr>
						<td class="label">
						'.$LANG->getLL('ofsetx').'
							
						</td>
						<td class="input">
							<input type="text" name="offsetx" value="0" class="number" onChange="return redraw_div(\'x\')" />
						</td>
						<td class="label">
						'.$LANG->getLL('ofsety').'
							
						</td>
						<td class="input">
							<input type="text" name="offsety" value="0" class="number" onChange="return redraw_div(\'y\');" />
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('new_width').'
						</td>
						<td class="input">
							<input type="text" name="width" value="'.$this->cropFrameX.'" class="number" onChange="return redraw_div(\'w\');" />
						</td>
						<td class="label">
							'.$LANG->getLL('new_height').' 
							</td>
						<td class="input">
							<input type="text" name="height" value="'.$this->cropFrameY.'" class="number" onChange="return redraw_div(\'h\');" />
							<input type="hidden" name="action" value="edit_crop" />
						</td>
					</tr>';
		return $HTML;
	}
//CROP END

//SCALE BEGIN
	/**
	 * This methods renders the interface for the "scale" effect
	 *
	 * @param	string		The current HTML code
	 * @return	string		The rendered interface
	 */
	protected function render__edit_scale($HTML)	{
		global $LANG;
		$this->endDHTML = $this->doc->wrapScriptTags('
SET_DHTML(CURSOR_MOVE);

			');
		$init_code = '
//###INIT_CODE###
var oldDTM_toggle_Func = DTM_toggle;
var DHTML_initialized = false;
func'.'tion newDTM_toggle(idBase, index, isInit)	{
	oldDTM_toggle_Func(idBase, index, isInit);
	if (index==2)	{
		if (document.getElementById(idBase+"-"+index+"-DIV"))	{
			if ((document.getElementById(idBase+"-"+index+"-DIV").style.display == "block")&&!DHTML_initialized)	{
				DHTML_initialized = true;
				ADD_DHTML("knob_right"+MAXOFFLEFT+'.($this->zoom_x).'+MAXOFFRIGHT+'.($this->zoom_x*20).'+MAXOFFTOP+0+MAXOFFBOTTOM+0);
				ADD_DHTML("knob_bottom"+MAXOFFLEFT+0+MAXOFFRIGHT+0+MAXOFFTOP+'.($this->zoom_y).'+MAXOFFBOTTOM+'.($this->zoom_y).');
				ADD_DHTML("knob_corner"+MAXOFFLEFT+'.($this->zoom_x).'+MAXOFFRIGHT+'.($this->zoom_x*20).'+MAXOFFTOP+'.($this->zoom_y).'+MAXOFFBOTTOM+'.($this->zoom_y*20).');
			}
		}
	}
}

DTM_toggle = newDTM_toggle;

';
		$this->content = str_replace('//###INIT_CODE###', $init_code, $this->content);
		$img_extra = '';
		$img_extra .= '<div name="knob_right" id="knob_right" style="z-index: 100;"><img src="clear.gif" width="1" height="1"></div>';
		$img_extra .= '<div name="knob_bottom" id="knob_bottom" style="z-index: 101;"><img src="clear.gif" width="1" height="1"></div>';
		$img_extra .= '<div name="knob_corner" id="knob_corner" style="z-index: 102;"><img src="clear.gif" width="1" height="1"></div>';
		$HTML = str_replace('###IMG_EXTRA###', $img_extra, $HTML);
		$css_extra = '
#kb_imageedit-mainimage {
	padding-right: 6px;
	padding-bottom: 6px;
}
#knob_right, #knob_bottom, #knob_corner	{
	position: absolute;
	width: 10px;
	height: 10px;
	border: 1px solid #'.$this->MOD_SETTINGS['edit_crop_bordercolor'].';
}
#knob_right	{
	right: 0px;
	top: '.round($this->zoom_y/2).'px;
}
#knob_bottom	{
	right: '.round($this->zoom_x/2).'px;
	bottom: 0px;
}
#knob_corner	{
	right: 0px;
	bottom: 0px;
}
		';
		$this->content = str_replace('/*###CSS_EXTRA###*/', $css_extra.chr(10).'/*###CSS_EXTRA###*/', $this->content);
		$HTML .= $this->doc->wrapScriptTags('

func'.'tion my_DragFunc()
{
	if (!dd.obj.defx_orig)	{
		dd.obj.defx_orig = dd.obj.defx;
	}
	if (!dd.obj.defy_orig)	{
		dd.obj.defy_orig = dd.obj.defy;
	}
	var xdiff = dd.obj.x-dd.obj.defx_orig;
	var ydiff = dd.obj.y-dd.obj.defy_orig;
	var xval = 0;
	var yval = 0;
	var changed = "";
	var ratio = document.kb_imageedit_form.ratio.checked;
	switch (dd.obj.name)	{
		case "knob_right":
			changed = "x";
			xval = '.$this->zoom_x.'+xdiff;
		break;
		case "knob_bottom":
			changed = "y";
			yval = '.$this->zoom_y.'+ydiff;
		break;
		case "knob_corner":
			if (xdiff*xdiff>=ydiff*ydiff)	{
				changed = "x";
			} else	{
				changed = "y";
			}
			xval = '.$this->zoom_x.'+xdiff;
			yval = '.$this->zoom_y.'+ydiff;
		break;
	}
	if (zoomLevel!=100)	{
		if (xval)	{
			xval = xval/(zoomLevel/100);
		}
		if (yval)	{
			yval = yval/(zoomLevel/100);
		}
	}
	rescale(changed, xval, yval);
}

var img_x = '.$this->zoom_x.';
var img_y = '.$this->zoom_y.';

func'.'tion redraw_knobs()	{
	var img = document.getElementById("kb_imageedit-imageself");
	var xdiff = img_x-'.$this->zoom_x.';
	var ydiff = img_y-'.$this->zoom_y.';
	if (!dd.elements["knob_right"].defx_orig)	{
		dd.elements["knob_right"].defx_orig = dd.elements["knob_right"].defx;
	}
	if (!dd.elements["knob_right"].defy_orig)	{
		dd.elements["knob_right"].defy_orig = dd.elements["knob_right"].defy;
	}
	if (!dd.elements["knob_bottom"].defx_orig)	{
		dd.elements["knob_bottom"].defx_orig = dd.elements["knob_bottom"].defx;
	}
	if (!dd.elements["knob_bottom"].defy_orig)	{
		dd.elements["knob_bottom"].defy_orig = dd.elements["knob_bottom"].defy;
	}
	if (!dd.elements["knob_corner"].defx_orig)	{
		dd.elements["knob_corner"].defx_orig = dd.elements["knob_corner"].defx;
	}
	if (!dd.elements["knob_corner"].defy_orig)	{
		dd.elements["knob_corner"].defy_orig = dd.elements["knob_corner"].defy;
	}

	dd.elements["knob_right"].defx = dd.elements["knob_right"].defx_orig+xdiff;
	dd.elements["knob_right"].maxoffl = '.$this->zoom_x.'+xdiff;
	dd.elements["knob_right"].defy = dd.elements["knob_right"].defy_orig+Math.round(ydiff/2);
	dd.elements["knob_right"].moveTo(dd.elements["knob_right"].defx, dd.elements["knob_right"].defy);

	dd.elements["knob_bottom"].defx = dd.elements["knob_bottom"].defx_orig+Math.round(xdiff/2);
	dd.elements["knob_bottom"].defy = dd.elements["knob_bottom"].defy_orig+ydiff;
	dd.elements["knob_bottom"].maxofft = '.$this->zoom_y.'+ydiff;
	dd.elements["knob_bottom"].moveTo(dd.elements["knob_bottom"].defx, dd.elements["knob_bottom"].defy);

	dd.elements["knob_corner"].defx = dd.elements["knob_corner"].defx_orig+xdiff;
	dd.elements["knob_corner"].defy = dd.elements["knob_corner"].defy_orig+ydiff;
	dd.elements["knob_corner"].maxoffl = '.$this->zoom_x.'+xdiff;
	dd.elements["knob_corner"].maxofft = '.$this->zoom_y.'+ydiff;
	dd.elements["knob_corner"].moveTo(dd.elements["knob_corner"].defx, dd.elements["knob_corner"].defy);
}


func'.'tion rescale(changed, x, y)	{
	var img = document.getElementById("kb_imageedit-imageself");
	var cont = document.getElementById("kb_imageedit-mainimage");
	if (x)	{
		var xval = String(x);
	} else	{
		var xval = document.kb_imageedit_form.width.value;
	}
	if (y)	{
		var yval = String(y);
	} else	{
		var yval = document.kb_imageedit_form.height.value;
	}
	var ratio = document.kb_imageedit_form.ratio.checked;
	xval = xval.replace(/[^0-9\.\,%]/g, "");
	yval = yval.replace(/[^0-9\.\,%]/g, "");
	xval = xval.replace(/\,/g, ".");
	yval = yval.replace(/\,/g, ".");
	var xperc = false;
	if (xval.substr(-1)=="%")	{
		xperc = true;
		xval = parseInt(xval.substr(0, xval.length-1));
	} else	{
		xperc = false;
		xval = parseInt(xval);
	}
	var yperc = false;
	if (yval.substr(-1)=="%")	{
		yperc = true;
		yval = parseInt(yval.substr(0, yval.length-1));
	} else	{
		yperc = false;
		yval = parseInt(yval);
	}
	if (xperc)	{
		xval = Math.round((xval/100.0)*baseXsize);
	}
	if (yperc)	{
		yval = Math.round((yval/100.0)*baseYsize);
	}
	if (ratio)	{
		var aspect = baseXsize/baseYsize;
		if (changed=="x")	{
			yval = xval/aspect;
		} else if (changed=="y")	{
			xval = yval*aspect;
		}
	}
	if (xperc)	{
		document.kb_imageedit_form.width.value = (xval*100/baseXsize)+"%";
	} else	{
		document.kb_imageedit_form.width.value = xval;
	}
	if (yperc)	{
		document.kb_imageedit_form.height.value = (yval*100/baseYsize)+"%";
	} else	{
		document.kb_imageedit_form.height.value = yval;
	}
	var setxval = Math.round(xval*(zoomLevel/100.0));
	var setyval = Math.round(yval*(zoomLevel/100.0));
	img_x = setxval;
	img_y = setyval;
	if (xperc)	{
		img.style.width = setxval+"px";
		cont.style.width = setxval+"px";
	} else	{
		img.style.width = setxval+"px";
		cont.style.width = setxval+"px";
	}
	if (yperc)	{
		img.style.height = setyval+"px";
		cont.style.height = setyval+"px";
	} else	{
		img.style.height = setyval+"px";
		cont.style.height= setyval+"px";
	}
	redraw_knobs();
	return true;
}

'.(($this->scale_x||$this->scale_y)?'
func'.'tion windowLoad()	{
	'.($this->aspect?'
	document.kb_imageedit_form.ratio.checked = true;
	':'').'
	rescale("'.$this->aspect.'", "'.$this->scale_x.'", "'.$this->scale_y.'");
}
':'').'

	');
		if ($this->scale_x||$this->scale_y)	{
			$this->content = str_replace('###ON_LOAD_EVENT###', ' onload="windowLoad();" ', $this->content);
		}
		$HTML .= '<tr>
						<td colspan="4">
							<h3>'.$LANG->getLL('action_edit_scale').'</h3>
						</td>
					</tr>
					<tr>
						<td class="label">
							'.$LANG->getLL('action_label_edit_scale').'
						</td>
						<td colspan="3">
							'.t3lib_BEfunc::getFuncMenu(0,'SET[scaleFrames]',$this->MOD_SETTINGS['scaleFrames'],$this->MOD_MENU['scaleFrames']).'
						</td>
					</tr>
					<tr>
						<td class="label">
						'.$LANG->getLL('new_width').'
							
						</td>
						<td class="input">
							<input type="text" name="width" value="'.$this->x.'" class="number" onchange="return rescale(\'x\');" />
						</td>
						<td class="label">
							'.$LANG->getLL('new_height').'
						</td>
						<td class="input">
							<input type="text" name="height" value="'.$this->y.'" class="number" onchange="return rescale(\'y\');" />
							<input type="hidden" name="action" value="edit_scale" />
						</td>
					</tr>
					<tr>
						<td class="label">
						'.$LANG->getLL('keep_ratio').'	
						</td>
						<td colspan="3" class="input">
							<input type="checkbox" name="ratio" value="1" onchange="return rescale(\'x\');" />
						</td>
					</tr>
					<tr>
						<td class="label">
						'.$LANG->getLL('knob_color').'		
						</td>
						<td colspan="3">
							'.t3lib_BEfunc::getFuncMenu(0,'SET[edit_crop_bordercolor]',$this->MOD_SETTINGS['edit_crop_bordercolor'],$this->MOD_MENU['edit_crop_bordercolor']).'
						</td>
					</tr>';
		return $HTML;
	}

	/**
	 * Returns the "Image History" tab content of the image editors DynTab menu
	 *
	 * @return	array		DynTab array entry
	 */
	protected function getMenuItem_History()	{
		global $LANG;
		$historytree =t3lib_div::makeInstance('tx_kbimageedit_browseHistory');
		$historytree->init($this);
		if ($this->openFile)	{
			$historytree->stored = unserialize($GLOBALS['BE_USER']->uc['browseTrees'][$historytree->treeName]);
			$historytree->stored[0][$this->openFile] = 1;
			$historytree->savePosition();
		}
		$historytree->thisScript = 'index.php';
		reset($this->sessionData['fileTree']);
		$key = key($this->sessionData['fileTree']);
		if (is_array($this->sessionData['fileTree'][$key][$this->_SUB_LEVEL]))	{
			$historytree->setDataFromArray($this->sessionData['fileTree'][$key][$this->_SUB_LEVEL]);
		} else {
			$historytree->data = array();
		}
		$tree = $historytree->getBrowsableTree();
		$HTML = '';
		$HTML .= '<div class="kb_imageedit-dynTab">'.chr(10);
		$HTML .= $tree;
		$HTML .= '</div>'.chr(10);
		return array(
			'label' => $LANG->getLL('history_label'),
			'description' => $LANG->getLL('history_description'),
			'linkTitle' => $LANG->getLL('history_description'),
			'content' => $HTML,
			'stateIcon' => 2,
		);
	}

	/**
	 * This methods performs effects which do not need any parameter ("none") and stores the result in the result tree
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	void
	 */
	protected function func__effect_none($image, $cmd, $x, $y)	{
		list(,$imcmd) = explode('_', $cmd, 2);
		$command = $this->stdGraphic->IMparams($imcmd);
		if (!strlen($command))	{
			return false;
		}
		$newname = $this->getTempName($image);
		$this->stdGraphic->imageMagickExec($image,$newname,$command);
		$this->storeImage_File($newname, $cmd);
	}

	/**
	 * This methods performs effects which only needed one parameter ("simple") and stores the result in the result tree
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	void
	 */
	protected function func__effect_simple($image, $cmd, $x, $y)	{
		$factor = intval(t3lib_div::_GP('factor'));
		list(,$imcmd) = explode('_', $cmd, 2);
		if ($this->stdGraphic->V5_EFFECTS)	{
			switch ($cmd)	{
						// Workaround around bug in t3lib_stdgraphic.php: Colors can be up to 16Mio (truecolor).
				case ACTION_EFFECT_COLORS:
					$factor = t3lib_div::intInRange($factor, 1, 0xffffff);
					$command = '-'.$imcmd.' '.$factor;
				break;
						// Workaround around bug in t3lib_stdgraphic.php: v5 imagemagick takes an argument for emboss.
				case ACTION_EFFECT_EMBOSS:
					$factor = t3lib_div::intInRange($factor, 0, 100);
					$command = '-'.$imcmd.' '.$factor;
				break;
				default:
					$command = $this->stdGraphic->IMparams($imcmd.'='.$factor);
				break;
			}
		} else	{
			switch ($cmd)	{
				case ACTION_EFFECT_EMBOSS:
					return $this->func__effect_none($image, $cmd, $x, $y);
				break;
				default:
					$command = $this->stdGraphic->IMparams($imcmd.'='.$factor);
				break;
			}
		}
		if (!strlen($command))	{
			return false;
		}
		$newname = $this->getTempName($image);
		$this->stdGraphic->imageMagickExec($image,$newname,$command);
		$this->storeImage_File($newname, $cmd);
	}

	/**
	 * This methods performs effects which need two parameters ("double") and stores the result in the result tree
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	void
	 */
	protected function func__effect_double($image, $cmd, $x, $y)	{
		$factor = intval(t3lib_div::_GP('factor'));
		$factor2 = intval(t3lib_div::_GP('factor2'));
		list(,$imcmd) = explode('_', $cmd, 2);
		switch ($cmd)	{
				// Workaround around bug in t3lib_stdgraphic.php: wave should be useable with arguments bigger than 100 (arguments are pixel values no percent !!!)
			case ACTION_EFFECT_WAVE:
				$command = '-wave '.$factor.'x'.$factor2;
			break;
			default:
				$command = $this->stdGraphic->IMparams($imcmd.'='.$factor.','.$factor2);
			break;
		}
		if (!strlen($command))	{
			return false;
		}
		$newname = $this->getTempName($image);
		$this->stdGraphic->imageMagickExec($image,$newname,$command);
		$this->storeImage_File($newname, $cmd);
	}

	/**
	 * This methods performs the "gamma" effect and stores the result in the result tree
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	void
	 */
	protected function func__effect_gamma($image, $cmd, $x, $y)	{
		$factor = doubleval(t3lib_div::_GP('factor'));
		$red_factor = doubleval(t3lib_div::_GP('red_factor'));
		$green_factor = doubleval(t3lib_div::_GP('green_factor'));
		$blue_factor = doubleval(t3lib_div::_GP('blue_factor'));
		if (($red_factor!=$green_factor)||($green_factor!=$blue_factor))	{
			$command = '-gamma '.$red_factor.','.$green_factor.','.$blue_factor;
		} else	{
			list(,$imcmd) = explode('_', $cmd, 2);
			$command = $this->stdGraphic->IMparams($imcmd.'='.$factor);
		}
		if (!strlen($command))	{
			return false;
		}
		$newname = $this->getTempName($image);
		$this->stdGraphic->imageMagickExec($image,$newname,$command);
		$this->storeImage_File($newname, $cmd);
	}

	/**
	 * This methods performs the "rotate" effect and stores the result in the result tree
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @param	double		An "angle" parameter defining the degrees which to rotate the image
	 * @param	boolean		If this flag is set, the image will not get stored but only returned
	 * @return	string		The name of the rotated (and stored) temporary file
	 */
	protected function func__effect_rotate($image, $cmd, $x, $y, $angle = false, $dontstore = false)	{
		if ($angle===false)	{
			$angle = doubleval(t3lib_div::_GP('angle'));
		}
		$backr = t3lib_div::intInRange(t3lib_div::_GP('back_red'), 0, 255);
		$backg = t3lib_div::intInRange(t3lib_div::_GP('back_green'), 0, 255);
		$backb = t3lib_div::intInRange(t3lib_div::_GP('back_blue'), 0, 255);
		$backa = t3lib_div::intInRange(t3lib_div::_GP('back_alpha'), 0, 255);
		$command = ' -background "rgba('.$backr.','.$backg.','.$backb.','.$backa.')" -rotate "'.$angle.'" ';
		$newname = $this->getTempName($image);
		$this->stdGraphic->imageMagickExec($image,$newname,$command);
		if (!$dontstore)	{
			$this->storeImage_File($newname, $cmd);
		}
		return $newname;
	}

	/**
	 * This methods performs the "save" operation. On success this operation redirects the browser to the File>List module
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	boolean		"false" on error. On success browser gets redirected.
	 */
	protected function func__file_save($save, $cmd, $x, $y)	{
		$filename = trim(t3lib_div::_GP('filename'));
		$overwrite = intval(t3lib_div::_GP('overwrite'));
		if (!strlen($filename))	{
			return false;
		}
		if (strpos($filename, '.')===false)	{
			$parts = explode('.', $this->baseFile);
			$filename .= '.'.array_pop($parts);
		}
		$actPath = dirname($this->sessionData['startFile']);
		if ($overwrite&&@is_file($actPath.'/'.$filename))	{
			@unlink($actPath.'/'.$filename);
		}
		if (basename(dirname($this->absoluteFile))==='typo3temp')	{
			if (@rename($this->absoluteFile, $actPath.'/'.$filename))	{
				$this->cleanUpTempDir();
				header('Location: '.t3lib_div::locationHeaderUrl($this->doc->backPath.'file_list.php?id='.$actPath.'/'));
				exit();
			} else	{
				return false;
			}
		} else	{
			if (@copy($this->absoluteFile, $actPath.'/'.$filename))	{
				$this->cleanUpTempDir();
				header('Location: '.t3lib_div::locationHeaderUrl($this->doc->backPath.'file_list.php?id='.$actPath.'/'));
				exit();
			} else	{
				return false;
			}
		}
	}

	/**
	 * This methods performs the "crop" operation and stores the result
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	void
	 */
	protected function func__edit_crop($image, $cmd, $x, $y)	{
		$offsetx = intval(t3lib_div::_GP('offsetx'));
		$offsety = intval(t3lib_div::_GP('offsety'));
		$width = intval(t3lib_div::_GP('width'));
		$height = intval(t3lib_div::_GP('height'));
		if ($offsetx>$x||($offsetx+$width)>$x) return false;
		if ($offsety>$y||($offsety+$height)>$y) return false;
		$colors = $this->identifyColors($image);
		if ($colors==='true')	{
			$im = imagecreatetruecolor($width, $height);
		} else {
			$im = imagecreate($width, $height);
		}
		$src = $this->stdGraphic->imageCreateFromFile($image);
		imagecopyresized($im, $src, 0, 0, $offsetx, $offsety, $width, $height, $width, $height);
		$this->storeImage_Object($im, $image, 'edit_crop');
	}

	/**
	 * This methods performs the "scale" operation and stores the result
	 *
	 * @param	string		The image file being processed
	 * @param	integer		The command-constant which operation to perform
	 * @param	integer		The parameter $x passed for all "func__" methods
	 * @param	integer		The parameter $y passed for all "func__" methods
	 * @return	void
	 */
	protected function func__edit_scale($image, $cmd, $x, $y)	{
		$width = intval(t3lib_div::_GP('width'));
		$height = intval(t3lib_div::_GP('height'));
		$colors = $this->identifyColors($image);
		if ($colors==='true')	{
			$im = imagecreatetruecolor($width, $height);
		} else {
			$im = imagecreate($width, $height);
		}
		$src = $this->stdGraphic->imageCreateFromFile($image);
		imagecopyresized($im, $src, 0, 0, 0, 0, $width, $height, imagesx($src), imagesy($src));
		$this->storeImage_Object($im, $image, 'edit_scale');
	}

	/**
	 * This methods returns the number of colors in an indexed-color file, or "true" for truecolor files ;)
	 *
	 * @param	string		The image to identify
	 * @return	mixed		Either the size of the indexed-color palette or "true" for truecolor (24 bit) images
	 */
	protected function identifyColors($file)	{
		if (!$this->stdGraphic->NO_IMAGE_MAGICK)	{
			$frame = $this->stdGraphic->noFramePrepended?'':'[0]';
			$cmd = t3lib_div::imageMagickCommand('identify', escapeshellarg($file).$frame);
			exec($cmd, $returnVal);
			$ret = $returnVal[0];
			if (strpos($ret, 'DirectClass')!==false)	{
				return 'true';
			}
			if (($pos=strpos($ret, 'PseudoClass'))!==false)	{
				$h1 = substr($ret, $pos+12);
				$p1 = strpos($h1, 'c');
				$h2 = substr($h1, 0, $p1);
				return intval($h2);
			}
			return 'true';
		}
	}

	/**
	 * This method stores the passed image object in a temporary location ("/typo3temp") using the passed
	 * original name as guide and the "action" which was performed as addition to the filename
	 *
	 * @param	object		The GD image object
	 * @param	string		The original name of the file which should get used as guide for the temporary name
	 * @param	string		The action which was performed. This label will get added to the temporary file name
	 * @return	void
	 */
	protected function storeImage_Object($im, $origname, $action)	{
		$actualPath = t3lib_div::trimExplode('/', $this->sessionData['actualPath'], 1);
		$actualArr = &$this->getFileFromPath($actualPath, $subtree = false);
		$childArr = &$actualArr[$this->_SUB_LEVEL];
		$pid = $actualArr['uid'];
		if (!is_array($childArr))	{
			$actualArr[$this->_SUB_LEVEL] = Array();
			$actualArr = &$actualArr[$this->_SUB_LEVEL];
		} else {
			$actualArr = &$childArr;
		}
		$uid = $this->getTreeEntry($actualArr, $this->sessionData['actualPath'], $newname = $this->getTempName($origname), false, $action, $pid);
		$this->openFile = $pid;
		$parts = explode('.', $origname);
		$ext = strtolower(array_pop($parts));
		$this->sessionData['actualPath'] .= '/'.$uid;
		switch($ext)	{
			case 'gif':
				ImageGif($im, $newname);
				break;
			case 'png':
				ImagePng($im, $newname);
				break;
			case 'jpg':
			case 'jpeg':
				ImageJpeg($im, $newname);
				break;
			default:
				echo 'Invalid extension ! How did you came here ?';
				break;
		}
	}

	/**
	 * This method stores the passed image filename in the history tree
	 *
	 * @param	string		The name of the file to store in the history tree
	 * @param	string		The action which was performed. This information will get added to the history tree
	 * @return	void
	 */
	protected function storeImage_File($newname, $action)	{
		$actualPath = t3lib_div::trimExplode('/', $this->sessionData['actualPath'], 1);
		$actualArr = &$this->getFileFromPath($actualPath, $subtree = false);
		$childArr = &$actualArr[$this->_SUB_LEVEL];
		$pid = $actualArr['uid'];
		if (!is_array($childArr))	{
			$actualArr[$this->_SUB_LEVEL] = Array();
			$actualArr = &$actualArr[$this->_SUB_LEVEL];
		} else {
			$actualArr = &$childArr;
		}
		$uid = $this->getTreeEntry($actualArr, $this->sessionData['actualPath'], $newname, false, $action, $pid);
		$this->openFile = $pid;
		$parts = explode('.', $origname);
		$ext = strtolower(array_pop($parts));
		$this->sessionData['actualPath'] .= '/'.$uid;
	}

	/**
	 * Returns a temporary name, based on the passed original name
	 *
	 * @param	string		Basename of original file on which temporary name should be based
	 * @return	string		Absolute filename for temporary file in typo3temp/ directory
	 */
	protected function getTempName($origname)	{
		$parts = explode('.', $origname);
		$ext = strtolower(array_pop($parts));
		$tempname = PATH_site.'typo3temp/kb_imageedit/tmp_'.$GLOBALS['BE_USER']->user['uid'].'_'.substr(md5(time().getmypid()),0,10).'.'.$ext;
		return $tempname;
	}

	/**
	 * Removes all temporary files of the current BE-User from the typo3temp/directory
	 *
	 * @return	void
	 */
	protected function cleanUpTempDir()	{
		$dh = opendir(PATH_site.'typo3temp/kb_imageedit');
		while ($file = readdir($dh))	{
			if (strpos($file, 'tmp_'.$GLOBALS['BE_USER']->user['uid'].'_')===0)	{
				unlink(PATH_site.'typo3temp/kb_imageedit/'.$file);
			}
		}
	}

	/**
	 * Checks if the current browser supports SVG canvas. This method should get extended to new versions of IE, Opera and Safari
	 *
	 * @return	void
	 */
	protected function checkSVGsupport()	{
		$ua = t3lib_div::getIndpEnv('HTTP_USER_AGENT');
		if (preg_match('/Mozilla\/(4|5).*Firefox\/([0-9\.]+)/s', $ua, $matches)>0)	{
			$reqVer = t3lib_div::int_from_ver('1.5.0');
			$curVer = t3lib_div::int_from_ver($matches[2]);
			if (($curVer>=$reqVer)&&(strpos($ua, 'Windows')!==false))	{
				$this->SVGsupport = true;
			}
		}
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_imageedit/cm1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_imageedit/cm1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_kbimageedit_cm1');
$SOBE->init();


$SOBE->main();

$_SESSION[$GLOBALS['MCONF']['name']] = serialize($SOBE->sessionData);

$SOBE->printContent();


?>
