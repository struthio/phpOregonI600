<?PHP
    class cCache
    {
		var $pConnector;
	
		function __construct($inConnectionData)
		{
			// Connect to MySQL (Persistent connection).
			//$this->pConnector = new mysqli("localhost", "my_user", "my_password", "world");
			$this->pConnector = new mysqli($inConnectionData['host'], $inConnectionData['user'], $inConnectionData['pass'], $inConnectionData['dbname']);
			$this->pConnector->autocommit(TRUE);
		}
		
		function initialize()
		{
			//
			//CREATE TABLE oi600_cache (cnf_class,cnf_key,cnf_value,cnf_timestamp,cnf_hash);
			// PK (cnf_class,cnf_key)
		}

		function run_sql($inSQL,$inIsReturn)
		{
			if (!isset($this->pConnector))
				return;
			
			$tmpReturn = '';
			
			if ($result = $mysqli->query($inSQL))
			{
				/* fetch object array */
				while ($row = $result->fetch_row())
				{
					$tmpReturn = $row[0];
				}

				/* free result set */
				$result->close();
			}
			
			return $tmpReturn;
		}
		
		function getValue($inClass,$inKey,$inTimeout = 10)
		{
			if (empty($inKey))
				return '';
			
			if (empty($inClass))
				$inClass = '<default>';
			
			$inTimeout = $inTimeout * 60;
			$tmpHash = crc32($inClass.'.'.$inKey);
			return run_sql("SELECT cnf_value FROM oi600_cache WHERE cnf_hash = ".$tmpHash." AND cnf_class = '".mysqli_real_escape_string ($inClass)."' AND cnf_key = '".mysqli_real_escape_string ($inKey)."' AND cnf_timestamp > (NOW() - ".$inTimeout.")",true);

		}
		
		function setValue($inClass,$inKey,$inValue)
		{
			if (empty($inKey))
				return '';
			
			if (empty($inClass))
				$inClass = '<default>';
			
			$tmpHash = crc32($inClass.'.'.$inKey);
			run_sql("INSERT INTO oi600_cache (cnf_timestamp,cnf_hash,cnf_class,cnf_key,cnf_value) VALUES(NOW(),".$tmpHash." , '".mysqli_real_escape_string ($inClass)."', '".mysqli_real_escape_string ($inKey)."', '".mysqli_real_escape_string ($inValue)."') ON DUPLICATE KEY UPDATE cnf_value = '".mysqli_real_escape_string ($inValue)."'");
		}
    };
?>
