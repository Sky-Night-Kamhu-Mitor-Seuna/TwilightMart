<?php

$smarty->assign("action",$_GET['action']);

	error_reporting(0);
	session_start();
	//require_once('./assets/p/connectSftp.php');
	$phPoints = array();

	//Best practice is to create a separate file for handling connection to database
	try{
		// Creating a new connection.
		// Replace your-hostname, your-db, your-username, your-password according to your database
		$result = $db->each('SELECT * from p_orders;'); 
		foreach($result as $row){
			array_push($phPoints, array("y"=> $row->amount, "label"=> $row->created_at));
		
		}
		$link = null;
    }
	catch(PDOException $ex){
    }
    
    $ccc = strval(json_encode($phPoints, JSON_NUMERIC_CHECK));

    $smarty->assign("ccc",$ccc);
?>