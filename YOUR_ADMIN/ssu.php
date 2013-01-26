<?php
/**
* @package Pages
* @copyright Copyright 2003-2006 Zen Cart Development Team
* @copyright Portions Copyright 2003 osCommerce
* @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
* @version $Id: ssu.php 316 2010-02-21 14:49:37Z yellow1912 $
*/
set_time_limit(0);
require('includes/application_top.php');
require_once(DIR_WS_CLASSES.'ssu.php');

class RITemplate extends template_func{
	var $view;
  var $path;
  var $css_path;
  var $base;
  var $data = array();
  var $admin = false;
  
  function RITemplate($admin = false){
  	$this->admin = $admin;
  	if($this->admin){
  		if(empty($this->base)) $this->base = preg_replace('/\.php/','',substr(strrchr($_SERVER['PHP_SELF'],'/'),1),1);
  	}
  	else 
  		if(empty($base)) $this->base = $_GET['main_page'];
  }
  
//  
//  function loadAdminCss(){
//  	if($this->_checkPath(DIR_FS_ADMIN.$this->css_path));
//  	echo '<link rel="stylesheet" type="text/css" href="'.$this->css_path.'">';
//  }
  
  function _checkPath($path){
  	if(file_exists($path))
  		return true;
  	return false;
	}
	
  function setView($view){
  	$this->view = $view;
  }

  function set($one, $two = null) {
    $data = null;
    if (is_array($one)) {
      if (is_array($two)) {
        $data = array_combine($one, $two);
      } else {
        $data = $one;
      }
    } else {
      $data = array($one => $two);
    }
		if ($data == null) {
    	return false;
    }
	 foreach($this->data as $key=>$value)
     	if(key($this->data[$key]) == key($data))
     		unset($this->data[$key]);

     	$this->data[] = $data;
	}

	function setByReference($one, &$two = null) {
    $data = null;
    if (is_array($one)) {
      if (is_array($two)) {
        $data = array_combine($one, $two);
      } else {
        $data = $one;
      }
    } else {
      $data = array($one => $two);
    }
		if ($data == null) {
    	return false;
    }
	 foreach($this->data as $key=>$value)
     	if(key($this->data[$key]) == key($data))
     		unset($this->data[$key]);

     	$this->data[] = $data;
	}
	
  function setArray($array){
		foreach($array as $element){
			$this->set($element);
		}
  }
  
  // admin css
  // $this->css_path = "includes/templates/template_default/css/".$this->base.'.css';
  
  function render(){
  	if(empty($this->view)){
  		if(!isset($_GET['action']) || empty($_GET['action']))
  			$this->view .= 'index';
  		else
  			$this->view .= $_GET['action'];
  		$this->view .= '.php';
  	}
  	
  	if($this->admin)
			$this->path = DIR_FS_ADMIN."includes/templates/template_default/templates/".$this->base.'/';
  	else
	  	$this->path = $this->get_template_dir($this->view, DIR_WS_TEMPLATE, $this->base, 'templates/'.$this->base).'/';
 
  	if($this->_checkPath($this->path.$this->view)){
  		foreach($this->data as $element)
    	    extract($element, EXTR_SKIP);
  		ob_start();
  		require_once($this->path.$this->view);
  		$out = ob_get_clean();
  		print $out;
  	}
  	// error output
  	else{
  		echo "Render error, file not found(".$this->path.$this->view.")";
  	}
  }
}



$ri_template = new RITemplate(true);

switch($_GET['action']){
	case 'reset_cache':
		$ri_template->set('file_counter', SSUManager::resetCache($_GET['folder']));
		$ri_template->setView('reset_cache_folder.php');
	break;
	case 'reset_cache_timer':
		SSUManager::resetCacheTimer();
		$ri_template->setView('reset_cache_timer.php');
	break;
	case 'check_and_fix_cache':
		$ri_template->set('file_counter', SSUManager::checkAndFixCache());
		$ri_template->setView('reset_cache_folder.php');
	break;
	
}
		
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
if (typeof _editor_url == "string") HTMLArea.replaceAll();
 }
 // -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<div class="header_area">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
</div>
<!-- header_eof //-->
<fieldset>
	<legend>Instruction</legend>
	SSU caches your categories/products names and links in order to reduce the number of sql queries and minimize the performance penalty on your server. That has its drawback, however. If you change your categories/products names, you will need to reset the cache to force SSU reload and update the names.
</fieldset>
<fieldset>
	<legend>Cache Functions</legend>
	Check and fix cache(Run this once if you upgrade from any version older than 3.6.5): <a href="<?php echo zen_href_link(FILENAME_SSU,'action=check_and_fix_cache'); ?>">Click here</a><br />
	<!--Reset cache timer(Run this when you add new product ONLY IF you use Auto Alias): <a href="<?php echo zen_href_link(FILENAME_SSU,'action=reset_cache_timer'); ?>">Click here</a><br />-->
	Reset all cache: <a href="<?php echo zen_href_link(FILENAME_SSU,'action=reset_cache&folder=all'); ?>">Click here</a><br />
	Reset alias cache: <a href="<?php echo zen_href_link(FILENAME_SSU,'action=reset_cache&folder=aliases'); ?>">Click here</a><br />
	<?php foreach(SSUConfig::registry('plugins', 'parsers') as $parser) { ?>
	Reset only <?= $parser ?> cache: <a href="<?php echo zen_href_link(FILENAME_SSU,"action=reset_cache&folder=$parser"); ?>">Click here</a><br />
	<?php } ?>
</fieldset>

<?php $ri_template->render(); ?>

<!-- footer //-->
<div class="footer-area">
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>