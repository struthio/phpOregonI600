<?PHP
    class cOregonXML
    {
        var $sXMLHeader = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE mdsml PUBLIC "-//emacsian/MDSML 10.0" "http://web.emacsian.com/MetInfo/mdsml-10.0.dtd">';
        var $sXMLRoot = 'mdsml';


        /****************************************************
         *
         * Convert simple PHP Array to XML
         *
         ****************************************************/   
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
                    foreach($inArrayEntry['attributes'] AS $tAttrName => $tAttrValue)
                        $tEntryKey .= ' '.$tAttrName.'="'.$tAttrValue.'"';

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
        /****************************************************
         *
         * Generate Weather OS Error Message
         *
         ****************************************************/
        function getErrorXML($inMessage = 'no data')
        {
            $tReturn                               = Array();
		 
            $tErrorEntry                           = Array();
            $tErrorEntry['name']                   = 'error';
            $tErrorEntry['value']                  = $inMessage;
            $tErrorEntry['attributes']['xml:lang'] = 'en';
		 
            $tReturn[]                             = $tErrorEntry;
		  
            return $this->doConvertToXML($tReturn);
        }

        /****************************************************
         *
         * Generate Client Software Information Message
         *
         * No dynamic generation is available here.
         * This method is implemented to be compatible with Weather OS
         * But returned value should be treated more of a 'dummy' value.
         *
         ****************************************************/
        function getClientInfoXML()
        {
            global $config;

            if ($config['cache']['enable'])
            {
                // Retrive data from cache (if possible)
            }

            $tClientEntry = Array();
            $tClientEntry['name']                  = 'clientsw';
            $tClientEntry['value']                 = 'Weather OS client software (Windows) version 1.1.57';
            $tClientEntry['attributes']['force']   = 0;
            $tClientEntry['attributes']['href']    = 'http://www2.os-weather.com/download/OSWeather_1.1.msi';
            $tClientEntry['attributes']['version'] = '1.1.57';

            $tClientArray = Array();
            $tClientArray[] = $tClientEntry;

            return $this->doConvertToXML($tClientArray);
        }

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
            $tReturn = Array();
         
            foreach($inForecastData AS $inForecastKey => $inForecastEntry)
            {
                if (empty($inForecastEntry))
                    continue;

                $tForecastEntry = Array();
                $tForecastEntry['name'] = 'wxfcst';
                $tForecastEntry['attributes']['station'] = $inForecastKey;
                $tForecastEntry['attributes']['latitude'] = $inForecastEntry['position']['latitude'];
                $tForecastEntry['attributes']['longitude'] = $inForecastEntry['position']['longitude'];

		$tIconTest = 0;
                foreach($inForecastEntry['forecast'] AS $inDayForecastKey => $inDayForecastEntry)
                {
                    $tDayForecastEntry = Array();
                    $tDayForecastEntry['name'] = 'forecast';
                    $tDayForecastEntry['value'] = Array
                    (
                        Array('name' => 'maxtemp', 'value' => $inDayForecastEntry['temperature']['maximal']),
                        Array('name' => 'mintemp', 'value' => $inDayForecastEntry['temperature']['minimal']),
                        Array('name' => 'icon', 'value' => ++$tIconTest)
                    );
                    $tDayForecastEntry['attributes']['day'] = $inDayForecastKey;

                    $tForecastEntry['value'][] = $tDayForecastEntry;
                }
                
                //$tReturn[] = $tForecastEntry;
                
                //TODO: Change to correct Localtime!!
                $tTimeLocal = Array();
                $tTimeLocal['name'] = 'localtime';
                $tTimeLocal['value'] = $inForecastEntry['general']['time']['value'];
                $tTimeLocal['attributes']['utcoffset'] = $inForecastEntry['general']['time']['utcoffset'];
                $tTimeLocal['attributes']['stdoffset'] = '0';

                $tTimeEntry = Array();
                $tTimeEntry['name'] = 'time';
                $tTimeEntry['value'][] = $tTimeLocal;
   
                $tForecastEntry['value'][] = $tTimeEntry;

                //$tReturn[] = $tTimeEntry;
                $tReturn[] = $tForecastEntry;
            }

            //TODO: Change to correct UTC Time!
            $tUTCDateTime = new DateTime('now');
            $tUTCDateTime->setTimezone(new DateTimeZone('UTC'));

            $tTimeUTC = Array();
            $tTimeUTC['name'] = 'utc';
            $tTimeUTC['value'] = $tUTCDateTime->format('Y-m-d H:i:s').' UTC';
            $tTimeUTC['attributes']['utcoffset'] = '0';
            $tTimeUTC['attributes']['stdoffset'] = '0';

            $tTimeEntry = Array();
            $tTimeEntry['name'] = 'time';
            $tTimeEntry['value'][] = $tTimeUTC;
            $tReturn[] = $tTimeEntry;

            return $this->doConvertToXML($tReturn);
        }
    }
?>
