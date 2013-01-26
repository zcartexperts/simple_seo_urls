<?php
/**
* @package Pages
* @copyright Copyright 2003-2006 Zen Cart Development Team
* @copyright Portions Copyright 2003 osCommerce
* @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
* @version $Id: es.php 218 2009-07-20 02:38:17Z yellow1912 $
*/
    // note: we can later move part of this function into sub-functions, which we can store in the base class.
class SSULanguageTr extends SSULanguage{      
  static function parseName($name){
  $cyrillic = array("�", "�", "&#351;", "&#350;", "�", "�", "&#231;", "&#199;", "�", "�", "&#220;", "&#252;", "�", "�", "&#246;", "&#214;", "�", "�", "&#287;", "&#286;", "�", "�", "&#305;", "&#304;");

  $translit = array("s", "S", "s", "S", "c", "C", "c", "C", "U", "u", "U", "u", "o", "O", "o", "O", "g", "G", "g", "G", "i", "I", "i", "I");
	
  $name = str_ireplace($cyrillic, $translit, $name);
		
	$name = strtolower($name);

	// we replace any non alpha numeric characters by the name delimiter
	$name = self::removeNonAlphaNumeric($name, SSUConfig::registry('delimiters', 'name'));
	
	// Remove short words first
	$name = self::removeShortWords($name, SSUConfig::registry('configs', 'minimum_word_length'), SSUConfig::registry('delimiters', 'name'));
	
	// trim the sentence
	$name = self::trimLongName($name);
			
	// remove excess SSUConfig::registry('delimiters', 'name')
	$name = self::removeDelimiter($name);
	
	// remove identifiers
	$name = self::removeIdentifiers($name);
	
	// remove trailing _
	$name = trim($name, SSUConfig::registry('delimiters', 'name'));
	
	return urlencode($name);	
	}
}