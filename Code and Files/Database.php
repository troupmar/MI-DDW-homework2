
<?php

class Database
{
	private $ratingFile;
	private $blocksFile;
	private $blocksBytesFile;

	private $numOfUsersFile;

	function __construct($ratingFile)
	{
		$this->ratingFile = $ratingFile;

		$dotPos = strrpos($ratingFile, ".");
		$this->blocksFile = substr($ratingFile, 0, $dotPos) . "_blocks.txt";
 
		$dotPos = strrpos($this->ratingFile, ".");
		$this->blocksBytesFile = substr($ratingFile, 0, $dotPos) . "_blocks_bytes.txt";

		$dotPos = strrpos($this->ratingFile, ".");
		$this->numOfUsersFile = substr($ratingFile, 0, $dotPos) . "_numOfUsers.txt";

		if(!file_exists($this->blocksBytesFile))
			$this->setByteBlocksToFile();
	}	

	private function setBlocksToFile()
	{
		$command = "./user-lines.sh " . $this->ratingFile . " > " . $this->blocksFile;
		exec($command);
	}

	private function setNumOfUsersToFile()
	{
		$command = "cat " . $this->blocksFile . " | wc -l"; 
		$result = exec($command);
		str_replace(" ", "", $result);
		file_put_contents($this->numOfUsersFile, $result -= 1);
	}

	public function getNumOfUsers() 
	{
		$num = file_get_contents($this->numOfUsersFile);
		return str_replace(" ", "", $num);
	}

	private function setByteBlocksToFile()
	{	
		$prevEnd = 0;
		$numOfBytes = 0;
		
		$ratingFileHandler = fopen($this->ratingFile, "r");
		if(file_exists($this->blocksFile))
			$blocksFileHandler = fopen($this->blocksFile, "r");
		else
		{
			$this->setBlocksToFile();
			$this->setNumOfUsersToFile();
			$blocksFileHandler = fopen($this->blocksFile, "r");

		}

		fseek($blocksFileHandler, 0);
		fseek($ratingFileHandler, 0);
		
		file_put_contents($this->blocksBytesFile, "");
		$data = $this->getNextUserNumOfLines($blocksFileHandler);
		$currentUser = $data["user_lines"];
		$blocksFileHandler = $data["handler"];
		
		while($bytes = fread($ratingFileHandler, 1000))
		{
			for($i=0; $i<strlen($bytes); $i++)
			{
				$numOfBytes++;
				if($bytes[$i] === "\n")
					$newLines++;
				if($newLines >= $currentUser)
				{
					$content = $prevEnd . " " . $numOfBytes . "\n";
					file_put_contents($this->blocksBytesFile, $content, FILE_APPEND);
					$prevEnd = $prevEnd + $numOfBytes;
					$numOfBytes = 0;
					$newLines = 0;
					$data = $this->getNextUserNumOfLines($blocksFileHandler);
					$currentUser = $data["user_lines"];
					$blocksFileHandler = $data["handler"];
				}
			}
		}
	}

	public function setAverageForUsers()
	{
		$dotPos = strrpos($this->ratingFile, ".");
		$averageFile = substr($this->ratingFile, 0, $dotPos) . "_average.txt";

		if(!file_exists($averageFile))
		{
			$command = "./average-lines.sh " . $this->ratingFile . " > " . $averageFile;
			exec($command);		
		}
		return $averageFile;
	}

	private function getNextUserNumOfLines($blocksFileHandler) 
	{
		while($line = fgets($blocksFileHandler))
		{
			if($line == "\n")
				file_put_contents($this->blocksBytesFile, $line, FILE_APPEND);
			else
			{
				$data = split(" ", $line);
				return array("handler" => $blocksFileHandler, "user_lines" => $data[1]);
			}

		}
	}

	public function getRatingFile()
	{
		return $this->ratingFile;
	}
	public function getBlocksFile()
	{
		return $this->blocksFile;
	}
	public function getBlocksBytesFile()
	{
		return $this->blocksBytesFile;
	}
	public function getNumOfUsersFile()
	{
		return $this->numOfUsersFile;
	}


}
?>