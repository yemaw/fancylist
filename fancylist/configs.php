<?php
$fl_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
$fl_url .= '://'. $_SERVER['HTTP_HOST'];
$fl_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
                
                
$array = array(
            'title' => 'Fancy List',
            'favicon' => 'favicon.png',
            'css'   => array('jquery-mobile/jquery.mobile-1.0b3.min.css'),
            'js'  	=> array('jquery-1.5.1.min.js','jquery-mobile/jquery.mobile-1.0b3.min.js'),
            'fl_icon16_path' => dirname(__FILE__).DIRECTORY_SEPARATOR."icons".DIRECTORY_SEPARATOR."filetypes-16".DIRECTORY_SEPARATOR,
            'fl_icon16_url'  => $fl_url."icons"."/"."filetypes-16"."/",
            'no_index.php'   => true,
            'show_about'     => true,
             );
echo json_encode( $array );
exit;