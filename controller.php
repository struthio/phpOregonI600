<?PHP
    /**
     *
     * Main Controller Script - Handle all requests from Weather OS
     *
     * @author Meller Jaroslaw
     * @version 0.1
     */
    include('class.oregon.php');
    include('class.yahoo.php');

    header("Content-type: text/xml");

    //
    // If we received empty request just fill it with any data (to avoid unset variable)
    //
    if (!isset($_GET['mode']) || empty($_GET['mode']))
        $_GET['mode'] = 'null';

    
    // Setup Oregon Class
    $pOregon  = new cOregonXML();
    $pWeather = new cYahooWeather(false,'/tmp/');
    
    switch($_GET['mode'])
    {
        case 'client_software_info':
            echo($pOregon->getClientInfoXML());
            break;
        case 'region_list':
            $pRegionData = $pWeather->getRegions();
            echo($pOregon->getRegionXML($pRegionData));
            break;
        case 'country_area_list':
            if (isset($_GET['region']) && !empty($_GET['region']) && is_numeric($_GET['region']))
            {
                $tCountryData = $pWeather->getCountryByRegion($_GET['region']);
                echo($pOregon->getCountryAreaXML($tCountryData));
            } else echo($pOregon->getErrorXML('NoRegion'));
            break;
        case 'station_list':
            $tStationsData = $pWeather->getStationsByCountry($_GET['area']);
            echo($pOregon->getStationsXML($tStationsData));
            break; 
        case 'wxfcsts':
            $tStationList = explode(',',$_GET['stations']);
            $tStationForecast = Array();
            foreach($tStationList AS $tStationEntry)
                $tStationForecast[$tStationEntry] = $pWeather->getForecastByStation($tStationEntry);

            echo($pOregon->getForecastXML($tStationForecast));
            break;
        default:
            echo($pOregon->getErrorXML());
    }

?>
