<?php

$filepath = dirname(__FILE__) . $_SERVER['REQUEST_URI'];


// does user requested file + '.php' exist?
$possibleFilename = dirname(__FILE__).$_SERVER['REQUEST_URI'].'.php';
if (is_file($possibleFilename)){
  //only include it if there IS NOT a '?' and var being passed
  if (strpos($_SERVER['REQUEST_URI'], '?') == false){
      $filepath = $possibleFilename;
  }
}

//add 'index.php' to requests for '/'
if ($_SERVER['REQUEST_URI'] == '/') {
  $filepath = dirname(__FILE__).'/index.php';
}

// if requested file is php, include it
include_once $filepath;

?>
