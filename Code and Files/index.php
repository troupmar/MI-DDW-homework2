<?php

function deliver_response($status, $status_message, $data)
{
	header("HTTP/1.1 $status $status_message");
	$response["status"] = $status;
	$response["status_message"] = $status_message;
	$response["data"] = $data;

	$json_response = json_encode($response);
	echo $json_response;
}

function process($userId)
{
	ini_set("memory_limit","50M");

	include('RecommendSystem.php');
	$man = new RecommendSystem("ratings.txt", $userId);

	$result = $man->process(8);
	return $result["similarNames"];
}
ob_start();

header("Content-Type:application/json");
if(!empty($_GET['user_id']))
{
	$userId = $_GET['user_id'];
	if(empty($userId))
		deliver_response(200, "user not found", NULL);
	else
	{
		$data = process($userId);
		deliver_response(200, "user found", $data);
	}
}
else
{
	deliver_response(400, "invalid request", NULL);
}

?>