<?PHP
   /**
    *
    * Main Controller Script - Handle all requests from Weather OS
    *
    * @author Meller Jaroslaw
    * @version 0.1
    */
   echo("\n\n".getClientInfo()."\n\n");
   die(101);
   //
   // If we received empty request just fill it with any data (to avoid unset variable)
   //
   if (!isset($_GET['mode']) || empty($_GET['mode']))
      $_GET['mode'] = 'null';

   switch($_GET['mode'])
   {
      case 'client_software_info':
         break;
      case 'region_list':
         break;
      case 'country_area_list':
         break;
      case 'station_list':
         break; 
      case 'wxfcsts':
         break;
      default:
         //
         // Just throw 404
         // 
         //TODO: Implement correst OS Error Message
         header("HTTP/1.0 404 Not Found");
         //header('Location: index.php');
         // Stop after giving 404!
         die();
   }


/*
 * Get Client Software Information
 */
function getClientInfo()
{
   global $config;

   if ($config['cache']['enable'])
   {
      // Retrive data from cache (if possible)
   }

//   $tBuffer = '';
//   $tBuffer .= '<clientsw force="1" href="http://www2.os-weather.com/download/OSWeather_1.1.msi" version="1.1.57">Weather OS client software (Windows) version 1.1.57</clientsw>';

   $tClientEntry = Array();
   $tClientEntry['name']                  = 'clientsw';
   $tClientEntry['value']                 = 'Weather OS client software (Windows) version 1.1.57';
   $tClientEntry['attributes']['force']   = 0;
   $tClientEntry['attributes']['href']    = 'http://www2.os-weather.com/download/OSWeather_1.1.msi';
   $tClientEntry['attributes']['version'] = '1.1.57';

   $tClientArray = Array();
   $tClientArray[] = $tClientEntry;

   print_r($tClientArray);

   return doConvertToXML($tClientArray);
}

function doConvertToXML($inArrayData)
{
   $tBuffer = '';

   foreach($inArrayData AS $inArrayName => $inArrayEntry)
   {
      // Just Skip Empty Array Entry (Probably we should kill loop here)
      if (!isset($inArrayEntry['name']) || empty($inArrayEntry['name']))
         continue;

      $tEntryKey = '<'.$inArrayEntry['name'];

      if (isset($inArrayEntry['attributes']) && !empty($inArrayEntry['attributes']) && is_array($inArrayEntry['attributes']))
      {
         foreach($inArrayEntry['attributes'] AS $tAttrName => $tAttrValue)
         	$tEntryKey .= ' '.$tAttrName.'="'.$tAttrValue.'"';
      }

      $tEntryKey .= '>';

      // Key Value
      if (isset($inArrayEntry['value']) && !empty($inArrayEntry['value']))
      {
         if (is_array($inArrayEntry['value']))
         {
            $tEntryKey .= doConvertToXML($inArrayEntry['value']);
         } else
         {
            $tEntryKey .= $inArrayEntry['value'];
         }
      }

      //
      $tEntryKey .= '</'.$inArrayEntry['name'].'>';
      $tBuffer .= $tEntryKey;
   }
   return $tBuffer;
}


?>
