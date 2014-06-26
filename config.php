<?PHP
   $config = Array();

   // Just my simple AppID pointer to identify Application / Script in my catalog
   $config['app']['id'] = '';

   // 
   $config['xml']['header'] = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE mdsml PUBLIC "-//emacsian/MDSML 10.0" "http://web.emacsian.com/MetInfo/mdsml-10.0.dtd">';
   $config['xml']['root']   = 'mdsml';

   //
   // Data Cache
   //
   //   Cache also enables simple data presentation in index.php
   //   
   //
   $config['cache']['enable']    = true;     // Enable Cache
   $config['cache']['path']      = 'cache/'; // Folder where cache file should be stored
                                             // Web serwer shuld gave r/w access to this folder
   $config['cache']['retention'] = 10;       // Cache retention time (in Minutes)

   $confog['cache']['files'][''] = 'cache_city.log';

?>
