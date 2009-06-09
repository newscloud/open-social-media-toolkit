<?php
	$init=parse_ini_file($_SERVER['DOCUMENT_ROOT'].'../genomics.ini');
	$api_key = $init['fbAPIKey'];
	$secret  = $init['fbSecretKey'];
	// the facebook client library
	include_once '../facebook/facebook.php';		
	$fbLib = new Facebook($api_key, $secret);
	require_once "../classes/fbApp.class.php";
	$fbApp=new fbApp($fbLib);
	require_once "../classes/gifts.class.php";
	$giftsObj=new gifts($fbApp);


	switch ($_POST['method']){
		case 'publisher_getInterface':
			$contentWrap='style="margin:0px;margin-top:10px;padding:0px;width:500px;float:left;"';
			$code=$fbApp->buildFBJS(485);
			$code.='<div '.$contentWrap.'>';
			$code.='<div><img style="border:0px;padding:0px 0px 0px 0px;" src="'.$fbApp->callback.'/images/genomicsWallHeader.jpg"></div>';
			$code.='<div style="float:left;margin-bottom:10px;"><h2>Select a Gene below from the <a href="'.$fbApp->home.'">Genomics Application</a></h2></div>';
			$code.='<div style="float:left;">'.$giftsObj->makeGiftSelector('wall').'</div>';	  
			$code.='<div style="float:left;"><h3><a href="'.$fbApp->home.'">Send a gene with Genome Alberta now!</a></h3></div>';
			$code.='</div> <!-- end main div  -->';
			
			
			$fbmlArray=array("content"=>array("fbml"=>$code,
											  "publishEnabled"=>true, 
											  "commentEnabled"=>true ),
							 "method"=>"publisher_getInterface"	
								);
		break;

		case 'publisher_getFeedStory':
			if (!isset($_POST['app_params']['pickGift']) || trim($_POST['app_params']['pickGift'])==''){
				$fbmlArray=array("errorCode"=>1,
								"errorTitle"=>'Wait!',
								"errorMessage"=>'You must select a Gene to post. Post cancelled. Please select select away from Genomics, then try again.'
								);
			}else{
				if (isset($_POST['app_params']) && trim($_POST['app_params']['comment_text'])!=''){
					$msg=$_POST['app_params']['comment_text'];
				}
				$feedElements=$giftsObj->makeWallPost($_POST['app_params']['pickGift'],$msg);
				$fbmlArray=array("content"=>array("feed"=>array("template_id"=>$fbApp->wallTemplateBundleID,
																"template_data"=>array("gene"=>$feedElements['gene'],
																					   "url"=>$fbApp->home,
																					   "shortDescription"=>$feedElements['shortDescription'],
																					   "description"=>$feedElements['description'],
																					   "msg"=>$feedElements['msg'],
																					   'images'=>array(array('src'=>$feedElements['img'], 'href'=>$fbApp->home)) 
																					)
																)
												  ),
	
								 "method"=>"publisher_getFeedStory"	
									);
			}
		break;
	}
	$fbml=json_encode($fbmlArray);
	//echo $fbml;	
?>