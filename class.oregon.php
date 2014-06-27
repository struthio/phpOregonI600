<?PHP
   class cOregonXML
   {
      var $sXMLHeader = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE mdsml PUBLIC "-//emacsian/MDSML 10.0" "http://web.emacsian.com/MetInfo/mdsml-10.0.dtd">';
      var $sXMLRoot = 'mdsml';
   
      function doConvertDataToXML($inArrayData)
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
                  $tEntryKey .= $this->doConvertDataToXML($inArrayEntry['value']);
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
	  function doConvertToXML($inSourceData)
	  {
         $tBuffer = $this->sXMLHeader.'<'.$this->sXMLRoot.'>';
		 $tBuffer .= $this->doConvertDataToXML($inSourceData);
		 $tBuffer .= '</'.$this->sXMLRoot.'>';
		 
		 return $tBuffer;
	  }
	  // -----------------------------------------
	  function getErrorXML()
	  {
         $tReturn = Array();
		 
		 $tErrorEntry = Array();
		 $tErrorEntry['name'] = 'error';
		 $tErrorEntry['value'] = 'no data';
		 $tErrorEntry['attributes']['xml:lang'] = 'en';
		 
		 $tReturn[] = $tErrorEntry;

		  
         return $this->doConvertToXML($tReturn);

	  }
	  
	  // No dynamic here. We do not support this, function is only presented to be compatible with Oregon
          /****************************************************
           *
           *
           *
           ****************************************************/
      function getClientInfoXML()
      {
         global $config;

         if ($config['cache']['enable'])
         {
            // Retrive data from cache (if possible)
         }

      //   $tBuffer .= '<clientsw force="1" href="http://www2.os-weather.com/download/OSWeather_1.1.msi" version="1.1.57">Weather OS client software (Windows) version 1.1.57</clientsw>';

         $tClientEntry = Array();
         $tClientEntry['name']                  = 'clientsw';
         $tClientEntry['value']                 = 'Weather OS client software (Windows) version 1.1.57';
         $tClientEntry['attributes']['force']   = 0;
         $tClientEntry['attributes']['href']    = 'http://www2.os-weather.com/download/OSWeather_1.1.msi';
         $tClientEntry['attributes']['version'] = '1.1.57';

         $tClientArray = Array();
         $tClientArray[] = $tClientEntry;

    //     print_r($tClientArray);

         return $this->doConvertToXML($tClientArray);
      }
	/*
	  <region code="1" xml:lang="en">AFRICA</region>
	*/
          /****************************************************
           *
           *
           *
           ****************************************************/
	  function getRegionXML($inRegionArray)
	  {
	     $tReturn = Array();
		 
		 foreach($inRegionArray AS $inRegionKey => $inRegionValue)
		 {
		    $tRegionEntry                           = Array();
			$tRegionEntry['name']                   = 'region';
			$tRegionEntry['value']                  = $inRegionValue;
			$tRegionEntry['attributes']['code']     = $inRegionKey;
			$tRegionEntry['attributes']['xml:lang'] = 'en';
			
			$tReturn[]                              = $tRegionEntry;
		 }
		 
		 return $this->doConvertToXML($tReturn);
	  }
          /****************************************************
           *
           *
           *
           ****************************************************/
          function getCountryAreaXML($inCountryArray)
          {
             // <countryarea code="3130" xml:lang="en">ARGENTINA</countryarea>

            $tReturn = Array();
            foreach($inCountryArray AS $inCountryKey => $inCountryName)
            {
               $tCountryEntry = Array();
               $tCountryEntry['name'] = 'countryarea';
               $tCountryEntry['value'] = $inCountryName;
               $tCountryEntry['attributes']['code'] = $inCountryKey;
               $tCountryEntry['attributes']['xml:lang'] = 'en';

               $tReturn[] = $tCountryEntry;
            }

            return $this->doConvertToXML($tReturn);
          }
          /****************************************************
           *
           *
           *
           ****************************************************/
      function getStationsXML($inStationArray)
      {
            $tReturn = Array();
            foreach($inStationArray AS $inStationKey => $inStationName)
            {
               $tCountryEntry = Array();
               $tCountryEntry['name'] = 'station';
               $tCountryEntry['value'] = $inStationName;
               $tCountryEntry['attributes']['code'] = $inStationKey;
               $tCountryEntry['attributes']['xml:lang'] = 'en';

               $tReturn[] = $tCountryEntry;
            }

            return $this->doConvertToXML($tReturn);
      }
          /****************************************************
           *
           *
           *
           ****************************************************/
      function getForecastXML($inForecastData)
      {
/*
         if (empty($inForecastData) || 
count($inForecastData) === 0 || !issereturn '';t($inForecastData['forecast'])) 
*/
/*
<wxfcst station="14071" latitude="50.81" longitude="4.35">
  <!-- UCCLE -->
  <forecast day="0">
    <>12</maxtemp>
    <mintemp>7</mintemp>
    <icon>23</icon>
  </forecast>
  <time>
    <localtime utcoffset="3600" stdoffset="0">2014-03-15 15:28:56 Europe/Brussels</localtime>
  </time>
</wxfcst>
*/
         $tReturn = Array();
         
         foreach($inForecastData AS $inForecastKey => $inForecastEntry)
         {
//echo('|||');
if (empty($inForecastEntry)) continue;

            $tForecastEntry = Array();
            $tForecastEntry['name'] = 'wxfcst';
            $tForecastEntry['attributes']['station'] = $inForecastKey;
            $tForecastEntry['attributes']['latitude'] = $inForecastEntry['position']['latitude'];
            $tForecastEntry['attributes']['longitude'] = $inForecastEntry['position']['longitude'];

            foreach($inForecastEntry['forecast'] AS $inDayForecastKey => $inDayForecastEntry)
            {
                $tDayForecastEntry = Array();
                $tDayForecastEntry['name'] = 'forecast';
                $tDayForecastEntry['value'] = 
                   Array(
                        Array('name' => 'maxtemp', 'value' => $inDayForecastEntry['temperature']['maximal']),
                        Array('name' => 'mintemp', 'value' => $inDayForecastEntry['temperature']['minimal']),
                        Array('name' => 'icon', 'value' => '23')
                   );
                $tDayForecastEntry['attributes']['day'] = $inDayForecastKey;

                $tForecastEntry['value'][] = $tDayForecastEntry;
            }
      //TODO: Change to correct Localtime!!
      $tTimeUTC = Array();
      $tTimeUTC['name'] = 'localtime';
      $tTimeUTC['value'] = date('Y-m-d H:i:s').' UTC';
      $tTimeUTC['attributes']['utcoffset'] = '0';
      $tTimeUTC['attributes']['stdoffset'] = '0';
    //  $tTimeUTC['attributes']['xml:lang'] = 'en';

      $tTimeEntry = Array();
      $tTimeEntry['name'] = 'time';
      $tTimeEntry['value'][] = $tTimeUTC;
      $tReturn[] = $tTimeEntry;

      //      $tForecastEntry['value'] = $tDayForecastEntry;

         $tReturn[] = $tForecastEntry;
      }

      //TODO: Change to correct UTC Time!
      $tTimeUTC = Array();
      $tTimeUTC['name'] = 'utc';
      $tTimeUTC['value'] = date('Y-m-d H:i:s').' UTC';
      $tTimeUTC['attributes']['utcoffset'] = '0';
      $tTimeUTC['attributes']['stdoffset'] = '0';
    //  $tTimeUTC['attributes']['xml:lang'] = 'en';

      $tTimeEntry = Array();
      $tTimeEntry['name'] = 'time';
      $tTimeEntry['value'][] = $tTimeUTC;
      $tReturn[] = $tTimeEntry;

//print_r($tReturn);

      return $this->doConvertToXML($tReturn);
   }
}
/*Array
(
    [121] => Array
        (

            [weather] => Array
                (
                    [humidity] => 45
                    [pressure] => 30.16
                    [rising] => 0
                    [visibility] => 9
                    [temperature] => 81
                    [text] => Fair
                )

            [forecast] => Array
                (
                    [0] => Array
                        (
                            [date] => 27 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 53
                                    [maximal] => 79
                                    [text] => Sunny
                                )

                        )

                    [1] => Array
                        (
                            [date] => 28 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 59
                                    [maximal] => 82
                                    [text] => Mostly Sunny
                                )

                        )

                    [2] => Array
                        (
                            [date] => 29 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 65
                                    [maximal] => 83
                                    [text] => Partly Cloudy
                                )

                        )

                    [3] => Array
                        (
                            [date] => 30 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 67
                                    [maximal] => 80
                                    [text] => Isolated Thunderstorms
                                )

                        )

                    [4] => Array
                        (
                            [date] => 1 Jul 2014
                            [temperature] => Array
                                (
                                    [minimal] => 58
                                    [maximal] => 76
                                    [text] => Scattered Thunderstorms
                                )

                        )

                )

        )

    [2312] => Array
        (
        )

    [12412] => Array
        (
            [position] => Array
                (
                    [latitude] => 57.67
                    [longitude] => -7.21
                )

            [weather] => Array
                (
                    [humidity] => 71
                    [pressure] => 30
                    [rising] => 0
                    [visibility] => 6.21
                    [temperature] => 52
                    [text] => Unknown
                )

            [forecast] => Array
                (
                    [0] => Array
                        (
                            [date] => 27 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 50
                                    [maximal] => 55
                                    [text] => Partly Cloudy/Wind
                                )

                        )

                    [1] => Array
                        (
                            [date] => 28 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 50
                                    [maximal] => 54
                                    [text] => Partly Cloudy
                                )

                        )

                    [2] => Array
                        (
                            [date] => 29 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 51
                                    [maximal] => 55
                                    [text] => Partly Cloudy
                                )

                        )

                    [3] => Array
                        (
                            [date] => 30 Jun 2014
                            [temperature] => Array
                                (
                                    [minimal] => 52
                                    [maximal] => 56
                                    [text] => Mostly Cloudy
                                )

                        )

                    [4] => Array
                        (
                            [date] => 1 Jul 2014
                            [temperature] => Array
                                (
                                    [minimal] => 53
                                    [maximal] => 57
                                    [text] => Partly Cloudy
                                )

                        )

                )

        )

)
*/


?>
