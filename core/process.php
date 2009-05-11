<?php
	/* Process incoming variable requests */	
	if (isset($_GET['action'])) {
		$action=$_GET['action'];
	} else 
		$action='unknown';
	if (isset($_GET['userid']))
		$userid=$_GET['userid'];
	else
		$userid=0;
	if (isset($_GET['itemid']))
		$itemid=$_GET['itemid'];
	else
		$id=0;
	/* begin building ajax response */
	switch ($action) {
		case 'readWire':
			require_once PATH_CORE."/classes/log.class.php";
			$logObj=new log($db);
			$info=$logObj->serialize(0,$userid,'readWire',$itemid);
			$logObj->update($info);					
			require_once PATH_CORE."/classes/newswire.class.php";
			$nwObj=new newswire($db);
			$url=$nwObj->getWebpage($itemid);
			header("Location: ".$url);
		break;
	}	
?>
