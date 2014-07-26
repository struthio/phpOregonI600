<?PHP
    // Class for obtaining Weather from php

    // @url https://developer.yahoo.com/yql/console/
    require_once('config.php');
	require_once('cache.php');

    class cYahooWeather
    {
        var $bCacheEnabled   = false;
        var $sCachePath      = './cache/';
        var $sCacheFile      = 'cache_yahoo.xxx';
        var $iCacheRetention = 10;                // How many minutes cache is considered 'fresh'
                                                // This value will be overwritten by Yahoo TTL parameter
        var $aCacheStore   = Array();
        var $bDebugMode    = true;
		var $oCache        = null;

	  
        function __construct($inCacheEnabled = false, $inCachePath = '', $inCacheRetention = 3600)
        {
            $this->bCacheEnabled    = $inCacheEnabled;
            $this->sCachePath       = $inCachePath;
            $this->iCacheRetention  = $inCacheRetention;
			
			if ($this->bCacheEnabled)
				$this->oCache           = new cCache();
        }
	  
        // Function caches data in file on disk
        // This will be very ineffective and my cause a lot of problems
        //   but for our home use (*) we can stick with that.
        //
        // (*) - Weather OS query server once every 15 minutes - there is no risk of access problems
        function getYahooData($inYqlQuery)
        {
            // Try to load cache data from file
            // TODO: Change this to fopen with locking (EX).
            ////if ($this->bCacheEnabled && count($this->aCacheStore) === 0 && file_exists($this->sCachePath.$this->sCacheFile))
            ////    $this->aCacheStore = unserialize(file_get_contents($this->sCachePath.$this->sCacheFile));

            // If we have 'fresh' data then return value from cache
            // We can check it anyway (even with cache disabled because then no data will be available at all).
            ////if (
            ////    isset($this->aCacheStore[$inYqlQuery])  &&
            ////    !empty($this->aCacheStore[$inYqlQuery]) &&
            ////    $this->aCacheStore[$inYqlQuery]['valid-till'] > time()
            ////)
            ////{
            ////    return $this->aCacheStore[$inYqlQuery]['data'];
            ////}
			
			if ($this->bCacheEnabled)
			{
				$cachedValue = $this->oCache->getValue('cYahooWeather','JQL:'.$inYqlQuery,$this->iCacheRetention);
				if (!empty($cachedValue))
					return $cachedValue;
			}

            // Retrive data from Yahoo!
            $tURL = 'http://query.yahooapis.com/v1/public/yql?q='.rawurlencode($inYqlQuery).'&format=json&diagnostics=false';
            $tBuffer = @file_get_contents($tURL);

            if (strlen($tBuffer) === 0)
                return '';

            $tReturn = json_decode($tBuffer);
            
            if (isset($tReturn->query->results->channel->ttl) && is_numeric($tReturn->query->results->channel->ttl))
                $tResultCacheRetention = $tReturn->query->results->channel->ttl;
            else
                $tResultCacheRetention = $this->iCacheRetention;

            // If we retrived any data - store it on drive
            if
            (
                $this->bCacheEnabled && 
                !empty($tReturn) //&& 
            ////    (
            ////        (
            ////            is_writable(dirname($this->sCachePath.$this->sCacheFile)) && 
            ////            !file_exists($this->sCachePath.$this->sCacheFile)
            ////        ) || 
            ////        (
            ////            file_exists($this->sCachePath.$this->sCacheFile) &&
            ////            is_writable($this->sCachePath.$this->sCacheFile)
            ////        )
            ////    )
            )
            {
            ////    $this->aCacheStore[$inYqlQuery]['data'] = $tReturn;
            ////    // Internal Cache Time is Persisted in seconds
            ////    $this->aCacheStore[$inYqlQuery]['valid-till'] = time() + (60 * $tResultCacheRetention);

                //TODO: Check if folder is writeable
            ////    @file_put_contents($this->sCachePath.$this->sCacheFile,serialize($this->aCacheStore));
			    $this->oCache->setValue('cYahooWeather','JQL:'.$inYqlQuery,$tReturn);
            }

            return $tReturn;
        }

        function getRegions()
        {
		    // 
			// Check if we have any usable data in cache.
			//
			if ($this->bCacheEnabled)
			{
				$cachedValue = $this->oCache->getValue('cYahooWeather','getRegions',$this->iCacheRetention);
				if (!empty($cachedValue))
					return unserialize($cachedValue);
			}
		
            $yqlQuery = 'select woeid,name from geo.continents';

            $tResult = Array();

            $tYahooData = $this->getYahooData($yqlQuery);

            if (!empty($tYahooData))
                foreach($tYahooData->query->results->place AS $tPlaceEntry)
                    $tResult[$tPlaceEntry->woeid] = $tPlaceEntry->name;

		    // 
			// Store value in cache.
			//
            if ($this->bCacheEnabled && !empty($tReturn))
			{
				$this->oCache->getValue('cYahooWeather','getRegions',serialize($this->iCacheRetention))
			}
					
            return $tResult;
        }

        //TODO: Add Search by WOEID
        function getCountryByRegion($inRegionID)
        {
		    // 
			// Check if we have any usable data in cache.
			//
			if ($this->bCacheEnabled)
			{
				$cachedValue = $this->oCache->getValue('cYahooWeather','getCountryByRegion:'.$inRegionID,$this->iCacheRetention);
				if (!empty($cachedValue))
					return unserialize($cachedValue);
			}
		
            $tRequestName = 'EUROPE';
            // YQL Accept only searching country name by 'place' string
            // So translate WOEID to NAME
            $tRegionData = $this->getRegions();
            foreach ($tRegionData AS $tRegionID => $tRegionName)
                if ($inRegionID === $tRegionID)
                    $tRequestName = $tRegionName;

            $yqlQuery = 'select woeid,name from geo.countries where place="'.$tRequestName.'"';

            $tYahooData = $this->getYahooData($yqlQuery);

            $tReturn = Array();
            if (!empty($tYahooData))
                foreach($tYahooData->query->results->place AS $tPlaceEntry)
                    $tReturn[$tPlaceEntry->woeid] = $tPlaceEntry->name;

		    // 
			// Store value in cache.
			//
            if ($this->bCacheEnabled && !empty($tReturn))
			{
				$this->oCache->getValue('cYahooWeather','getCountryByRegion:'.$inRegionID,serialize($this->iCacheRetention))
			}

            return $tReturn;
        }

        function getStationsByCountry($inCountryID)
        {
		    // 
			// Check if we have any usable data in cache.
			//
			if ($this->bCacheEnabled)
			{
				$cachedValue = $this->oCache->getValue('cYahooWeather','getStationsByCountry:'.$inCountryID,$this->iCacheRetention);
				if (!empty($cachedValue))
					return unserialize($cachedValue);
			}
		
            $yqlQuery = 'select woeid, placeTypeName, name from geo.places.descendants where ancestor_woeid in (select woeid from geo.states where place='.$inCountryID.') and placetype = 7';
            //$yqlQuery = 'select woeid, name from geo.places.children where parent_woeid = '.$inCountryID;
            $tYahooData = $this->getYahooData($yqlQuery);

            $tReturn = Array();
            if (!empty($tYahooData))
                foreach($tYahooData->query->results->place AS $tStationEntry)
                    $tReturn[$tStationEntry->woeid] = $tStationEntry->name;

		    // 
			// Store value in cache.
			//
            if ($this->bCacheEnabled && !empty($tReturn))
			{
				$this->oCache->getValue('cYahooWeather','getStationsByCountry:'.$inCountryID,serialize($this->iCacheRetention))
			}
					
            return $tReturn;
        }
        
        function getForecastByStation($inStationID)
        {
		    // 
			// Check if we have any usable data in cache.
			//
			if ($this->bCacheEnabled)
			{
				$cachedValue = $this->oCache->getValue('cYahooWeather','getForecastByStation:'.$inStationID,$this->iCacheRetention);
				if (!empty($cachedValue))
					return unserialize($cachedValue);
			}
		
            $yqlQuery = 'select atmosphere,item from weather.forecast where woeid='.$inStationID.' and u=\'c\'';
            $tYahooData = $this->getYahooData($yqlQuery);
            
            if
            (
                !isset($tYahooData) ||
                empty($tYahooData) ||
                !isset($tYahooData->query) ||
                !isset($tYahooData->query->results->channel) ||
                !isset($tYahooData->query->results->channel->item->lat)
            ) return Array();

            $tReturn = Array();
            $tReturn['position']['latitude'] = $tYahooData->query->results->channel->item->lat;
            $tReturn['position']['longitude'] = $tYahooData->query->results->channel->item->long;

            $tReturn['weather']['humidity'] = $tYahooData->query->results->channel->atmosphere->humidity;
            $tReturn['weather']['pressure'] = $tYahooData->query->results->channel->atmosphere->pressure;
            $tReturn['weather']['rising'] = $tYahooData->query->results->channel->atmosphere->rising;
            $tReturn['weather']['visibility'] = $tYahooData->query->results->channel->atmosphere->visibility;
            $tReturn['weather']['temperature'] = $tYahooData->query->results->channel->item->condition->temp;
            $tReturn['weather']['text'] =$tYahooData->query->results->channel->item->condition->text;
            //TODO: Add code

            foreach($tYahooData->query->results->channel->item->forecast AS $inForecastKey => $inForecastEntry)
            {
                $tReturn['forecast'][$inForecastKey]['date'] = $inForecastEntry->date; //TODO: Change to format YYYYMMDD
                $tReturn['forecast'][$inForecastKey]['temperature']['minimal'] = $inForecastEntry->low;
                $tReturn['forecast'][$inForecastKey]['temperature']['maximal'] = $inForecastEntry->high;
                // TODO TRANSLATE CODES TO REGON ! $inForecastEntry->code;
                $tReturn['forecast'][$inForecastKey]['temperature']['text'] = $inForecastEntry->text;
            }
            
            $tReturn['general']['time'] = $this->getTimeByID($inStationID);
            
		    // 
			// Store value in cache.
			//
            if ($this->bCacheEnabled && !empty($tReturn))
			{
				$this->oCache->getValue('cYahooWeather','getForecastByStation:'.$inStationID,serialize($this->iCacheRetention))
			}
			
            return $tReturn;
        }

        function getTimeByID($inStationID)
        {
			// 
			// Check if we have any usable data in cache.
			//
			if ($this->bCacheEnabled)
			{
				$cachedValue = $this->oCache->getValue('cYahooWeather','getTimeByID:'.$inStationID,$this->iCacheRetention);
				if (!empty($cachedValue))
					return unserialize($cachedValue);
			}
		
            $yqlQuery = 'select timezone from geo.places where woeid = '.$inStationID;
            $tYahooData = $this->getYahooData($yqlQuery);
            $tReturn = Array('value'=>time(),'utcoffset' => 0);
            if (isset($tYahooData->query->count) && $tYahooData->query->count > 0)
            {
                $tLocalTimeZone = $tYahooData->query->results->place->timezone->content;
                $tDateTime = new DateTime(); // Get current server time
                $tTimeZone = new DateTimeZone($tLocalTimeZone);
                $tDateTime->setTimezone($tTimeZone);
                //$tLocalTime = $tDateTime->format('Y-m-d H:i:s').' '.$tTimeZone;

                //$tUTCTimeZone = new DateTimeZone("UTC");

                $tReturn['value'] = $tDateTime->format('Y-m-d H:i:s').' '.$tLocalTimeZone;
                $tReturn['utcoffset'] = $tTimeZone->getOffset($tDateTime);// - $tUTCTimeZone->getOffset();
            }

		    // 
			// Store value in cache.
			//
            if ($this->bCacheEnabled && !empty($tReturn))
			{
				$this->oCache->getValue('cYahooWeather','getTimeByID:'.$inStationID,serialize($this->iCacheRetention))
			}
			
            return $tReturn;
        }
    };
?>
