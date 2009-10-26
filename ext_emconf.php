<?php

########################################################################
# Extension Manager/Repository config file for ext: "kb_imageedit"
#
# Auto generated 26-10-2009 11:35
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'KB Image Edit',
	'description' => 'This extension enables you to modify images directly on the server. You can crop, scale, blur, flip/flop or even rotate them. The interface is fully based on DHTML (and SVG for the rotate feature) so no Flash or JAVA is required. Finally all image modifications are performed on the server using ImageMagick',
	'category' => 'be',
	'shy' => 0,
	'version' => '0.0.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'cm1',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => 'typo3temp/kb_imageedit/',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Bernhard Kraft',
	'author_email' => 'kraftb@kraftb.at',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"a42a";s:10:"README.txt";s:4:"4130";s:8:"TODO.txt";s:4:"9bbc";s:38:"class.tx_kbimageedit_browsehistory.php";s:4:"88dd";s:28:"class.tx_kbimageedit_cm1.php";s:4:"3e12";s:9:"clear.gif";s:4:"cc11";s:12:"ext_icon.gif";s:4:"b7ea";s:14:"ext_tables.php";s:4:"d051";s:13:"locallang.php";s:4:"742e";s:13:"cm1/clear.gif";s:4:"cc11";s:15:"cm1/cm_icon.gif";s:4:"9f2d";s:12:"cm1/conf.php";s:4:"8e88";s:13:"cm1/index.php";s:4:"0cab";s:17:"cm1/locallang.php";s:4:"f02f";s:18:"res/bg_pattern.png";s:4:"4082";s:18:"res/stylesheet.css";s:4:"17d9";s:18:"res/wz_dragdrop.js";s:4:"b02b";s:19:"doc/wizard_form.dat";s:4:"b9cc";s:20:"doc/wizard_form.html";s:4:"f3e1";}',
);

?>