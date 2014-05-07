<?php
include_once('Database.php');

class RecommendSystem
{
	private $database;
	private $user;
	private $userAverage;
	private $averageFileHandler;

	function __construct($file, $userID)
	{
		$this->database = new Database($file);
		$averageFileName = $this->database->setAverageForUsers();
		$this->averageFileHandler = fopen($averageFileName, "r");

		$numOfUsers = $this->database->getNumOfUsers();
		if($userID >= $numOfUsers || $userId < 0)
			$myUser = $this->loadUser(1);
		else
			$myUser = $this->loadUser($userID);
		$this->user = $this->parseUser($myUser);
		
		$file = new SplFileObject($averageFileName);
		$file->seek($userID);
		$this->userAverage = $file->current();

	}

	// Used to load my user for comparsion with others (in constructor)
	private function loadUser($userID)
	{
		$file = new SplFileObject($this->database->getBlocksBytesFile());
		$ratingFilehandler = fopen($this->database->getRatingFile(), "r");

		$file->seek($userID);
		$userData = split(" ", $file->current());
		return $this->getUser($userData, $ratingFilehandler);
	}

	private function getUser($userData, $handler)
	{
		fseek($handler, $userData[0]);
		$data = fread($handler, $userData[1]);
		$data = explode("\n", $data);
		return $data;
	}

	public function process()
	{

		$blocksBytesFileHandler = fopen($this->database->getBlocksBytesFile(), "r");
		$ratingFilehandler = fopen($this->database->getRatingFile(), "r");

		$users = array();
		$n = 0;
		
		while($userI = fgets($blocksBytesFileHandler))
		{
			if($userI === "\n")
			{
				$users[$n] = 0;
				fgets($this->averageFileHandler);
				$n++;
				continue;
			}
			
			$userData = split(" ", $userI);
			$users[$n] = $this->getDistance($this->getUser($userData, $ratingFilehandler));
			
			$n++;
		}
		$similarUsers = $this->getN($users, 5);

		$similar = $this->getRecommendedArtists($similarUsers, 5);
		$similarNames = $this->getArtistNames($similar);
		
		$myNames = $this->getArtistNames($this->user);
		
		return array("myNames" => $myNames, "similarNames" => $similarNames);


	}

	private function getRecommendedArtists($similarUsers, $num)
	{
		// AVOID 0-USER SINCE IT DOES NOT REALLY EXISTS
		foreach($similarUsers as $artistId => $simScore)
		{
			if($artistId == 0)
				unset($similarUsers[$artistId]);
		}	
		$gatheredSimilar = array();
		$n = 0;
		foreach($similarUsers as $artistId => $simScore)
		{
			
			$simUser = $this->loadUser($artistId);
			$simUser = $this->parseUser($simUser);
			foreach($simUser as $artistId => $rank)
			{
				if($rank > 100)
					$simUser[$artistId] = 0;
			}
			$gatheredSimilar[$n++] = array_diff_key($simUser, $this->user);
		}
		//print_r($gatheredSimilar);
		$finalRanking = $this->recommendation($gatheredSimilar);
		return array_slice($finalRanking, -$num, $num, true);
	}

	private function getArtistNames($array)
	{
		foreach($array as $artistId => $rank)
		{
			$command = "grep \"^" . $artistId . "\\t\" artists.txt";
			$return = exec($command);
			$data = split("\t", $return);
			$array[$artistId] = $data[1];
		}
		return $array;
	}

	private function recommendation($arrOfArrays)
	{
		$results = array();
		for($i=0; $i<count($arrOfArrays); $i++)
		{
			foreach($arrOfArrays[$i] as $artistId => $rank)
			{
				if(!array_key_exists($artistId, $results))
				{
					$results[$artistId]["sum"] = $rank;
					$results[$artistId]["cnt"] = 1;
				}
				else
				{
					$results[$artistId]["sum"] += $rank;
					$results[$artistId]["cnt"] += 1;
				}
			}
		}

		$factor = 0.5;
		$finalRanking = array();
		$users = count($arrOfArrays);
		foreach($results as $artistId => $value)
		{
			$rate = $factor * (($users - $value["cnt"]) / $users);
			$avgRank = ($value["sum"] / $value["cnt"]) / 100;
			if($avgRank < 0.5)
				$finalRanking[$artistId] = $avgRank + (0.5 - $avgRank) * $rate;
			else
				$finalRanking[$artistId] = $avgRank - ($avgRank - 0.5) * $rate;
		}
		asort($finalRanking);
		return $finalRanking;
	}

	// MAKE SURE YOU GO WITHOUT FIRST - ZERO LINE, PAY ATTENTION TO 255
	private function getDistance($userI)
	{
		// TODO MAIN MEASURE DISTANCE
		$userI = $this->parseUser($userI);
		$average = fgets($this->averageFileHandler);

		$intersec = array_intersect_key($this->user, $userI);
		
		return $this->pearson($this->user, $userI, $this->userAverage, $average, $intersec);
	}

	private function pearson($userA, $userB, $averageA, $averageB, $intersecArray)
	{
		$numerator = 0;
		$denominator = 0;
		$xPartDenominator = 0;
		$yPartDenominator = 0;

		foreach($intersecArray as $key => $value)
		{
			$averageA = str_replace(",", ".", $averageA);
			$averageB = str_replace(",", ".", $averageB);
			
			if($userA[$key] > 100)
				$userA[$key] = 0;
			if($userB[$key] > 100)
				$userB[$key] = 0;

			$numerator += ($userA[$key] - $averageA) * ($userB[$key] - $averageB);
			$xPartDenominator += ($userA[$key] - $averageA) * ($userA[$key] - $averageA);
			$yPartDenominator += ($userB[$key] - $averageB) * ($userB[$key] - $averageB);

		}
		$denominator = $xPartDenominator * $yPartDenominator;
		$denominator = sqrt($denominator);
		
		if($denominator == 0)
			return 0;
		else
			return $numerator / $denominator;
		
	}

	private function getN($array, $n)
	{
		asort($array);
		return array_slice($array, -$n, $n, true);
	}


	private function parseUser($data)
	{
		for($i=0; $i<(count($data) - 1); $i++)
		{
			$splitted = split("\t", $data[$i]);
			$user[$splitted[1]] = $splitted[2];
 		}		
 		return $user;
	}

	public function getMyUser()
	{
		return $this->user;
	}

}


?>