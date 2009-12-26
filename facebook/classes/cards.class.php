<?php
/*
 * Send a card/Card
 */

require_once (PATH_CORE . '/classes/dbRowObject.class.php');
class CardRow extends dbRowObject
{
	
}

class CardTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Cards";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "CardRow";
	static $fields = array(		
		"name" 		=> "VARCHAR(255) default ''",
		"shortCaption" 		=> "VARCHAR(255) default ''",
		"longCaption" 		=> "TEXT default NULL",
		"points" 		=> "INT(4) default 0",
		"isFeatured" 		=> "INT(4) default 0",
		"slug" 		=> "VARCHAR(25) default ''", // used for image file
	"notSendable" 		=> "TINYINT(1) default 0",
		"dateCreated" 				=> "timestamp",
		"dateAvailable" 				=> "timestamp"	
	);

	static $keydefinitions = array(); 
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
	}	
	// although many functions will be duplicated between table subclasses, having a parent class gets too messy	
	function getRowObject()
	{	
		$classname = self::$dbRowObjectClass; 
		return new $classname($this->db, self::$tablename, array_keys(self::$fields), self::$idname);
	}		
	
	// Cardric table creation routine, same for all *Table classes 		
	static function createTable($manageObj)
	{			
		$manageObj->addTable(self::$tablename,self::$idname,self::$idtype,"MyISAM");
		foreach (array_keys(self::$fields) as $key)
		{
			$manageObj->updateAddColumn(self::$tablename,$key,self::$fields[$key]);
		}
		foreach (self::$keydefinitions as $keydef)
		{
			$manageObj->updateAddKey(self::$tablename,$keydef[0], $keydef[1], $keydef[2], $keydef[3]);
		}
		
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////	
}


class cards
{
	var $db;	
	var $utilObj;
	var $templateObj;
		
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
		require_once(PATH_CORE.'/classes/utilities.class.php');
		$this->utilObj=new utilities($this->db);		

	}	
	
	function buildCardList($mode='tx',$userid=0) {
		switch ($mode) {
			case 'rx':
				$title='Received '.CARDS_NAME.'s';
				$temp=$this->makeFancyTitle($title);
				$temp.=$this->listCardsReceived($userid);
			break;
			case 'tx':
				$title='Sent '.CARDS_NAME.'s';
				$temp=$this->makeFancyTitle($title);
				$temp.=$this->listCardsSent($userid);
			break;
		}
		return $temp;
	}
	
	function buildCardDisplay($rx) {
			$q=$this->db->query("SELECT fbId FROM UserInfo WHERE userid=".$rx->userid1);
			$sender=$this->db->readQ($q);
			$title='You\'ve Received a '.CARDS_NAME;
			$temp=$this->makeFancyTitle($title);
			$temp.=$this->makeOneCard($rx->itemid,$rx->txt,'',$sender->fbId);
			$temp.='<h2><a href="?p=cards&o=send&prefillId='.$sender->fbId.'" requirelogin="1">Send a different '.CARDS_NAME.' to <fb:name ifcantsee="Anonymous" uid="'.$sender->fbId.'" capitalize="true" firstnameonly="true" linked="false" /></a></h2>';
			$temp.='<br><h2><a href="?p=cards&o=send" requirelogin="1">Send a '.CARDS_NAME.' to other friends</a></h2>';
			$temp.='<br><h2><a href="?p=cards&o=rx">View all the '.CARDS_NAME.'s you have received.</a></h2>';
		return $temp;
	}	
	
	function buildSendForm($prefillId=0){
		$defaultText='Click a '.CARDS_NAME.' above to see details/send.';
		$title='Send a '.CARDS_NAME;
		$theWidth='500px';
		$detWidth='290px';
		$numCards=8;
		$thumbMargin='5px';
		$colcount=1;
		$today=date("Ymd");
		$temp=$this->makeFancyTitle($title,$theWidth);
		$temp.='<div class="cards_floatwide" id="iconPane">';
		$temp.='<div id="thumbPanel">';
		$query=$this->db->query("SELECT * FROM Cards WHERE notSendable=0 and dateAvailable<=$today ORDER BY id DESC;");
		$count=0;
		while ($data=$this->db->readQ($query)) {
			$count++;
			$temp.='<a href="#ppj" onclick="changeCard(\''.$data->id.'\');return false;"><img class="cards_image" title="Click to see more info"  src="'.URL_BASE.'/images/cards/'.$data->slug.'.png"></a>';
			
			$img='<img class="cards_thumb"  src="'.URL_BASE.'/images/cards/'.$data->slug.'.png">';
			$fbmlBlocks.='<fb:js-string var="details.card'.$data->id.'">'.$this->makeCardDisplay($data->id,$img,$data->name,$data->shortCaption,$data->longCaption,'').'</fb:js-string>';
		 }
		$temp.='</div></div>';
		$temp.=$this->makePager($count,$numCards);
		$temp.='<div id="selCardStage"><div class="cards_insideSelStage">&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$defaultText.'</b></div></div>';
		$temp.=$fbmlBlocks;	
		$temp.='<form requirelogin="1" name="send_cards" action="?p=cards&o=send&submit" method="post"><input type="hidden" id="pickCard" name="pickCard" value="0">
		<p><b>To:</b> <fb:multi-friend-input '.($prefillId>0?'prefill_ids="'.$prefillId.'"':'').' max="20" width="500px" border_color="#64B74D" /></p>
     	<p><b>Message:</b> <br />'.CARDS_DEFAULTMSG.'<br /><textarea class="cards_textarea" name="msg"></textarea><br /><br /></p>
     	<input class="cards_noborder btn_1" type="submit" value="Send '.CARDS_NAME.'">
     	</form>';
		return $temp;
	}
		
	function makePager($totCards,$numPage,$width='575px'){
		$numPDec=$totCards/$numPage;
		if ($numPDec==floor($numPDec)){
			$numPages=$numPDec;
		}else{
			$numPages=floor($numPDec)+1;
		}
		for ($x=1;$x<=$numPages;$x++){
			if ($x!=1){$pgDisplay.='&nbsp;|';}
			$pgDisplay.='&nbsp;<a href="#ppj" onclick="movePanel(\''.$x.'\',\''.$numPages.'\');return false;" id="pgLink'.$x.'">'.$x.'</a>';
		}
		$pager='<div class="cards_pagerWrap cards_floatwide">';
		$pager.='<div class="cards_pagerBar"><b>PAGE</b>:'.$pgDisplay.'</div>';
		$pager.='</div>';
		return $pager;
	}
			
	function makeCardDisplay($uniqueId,$img,$name,$shortCaption,$longCaption='',$msg='',$rxList='',$fromStr=''){
		$content='<div class="cards_displayOuter">';
		$content.='<div class="cards_displayImage">'.$img.'</div>'; // removed extra end <a> tag
		$content.='<div class="cards_displayCaption" ><span class="cards_displayCaptionSpan">'.$name.'</span><br>';
		if (trim($longCaption)!=''){
			$description.=$shortCaption;
			$description.='<div id="fd'.$uniqueId.'" class="cards_displayLongCaption" >'.$longCaption.'</div>';
			$description.='<div class="cards_float90" id="showLink'.$uniqueId.'"><b>+ <a href="#ppj" onclick="readMore(\'fd'.$uniqueId.'\',\'showLink'.$uniqueId.'\',\'hideLink'.$uniqueId.'\');return false;">read more</a></b></div>';
			$description.='<div class="cards_float90 hidden" id="hideLink'.$uniqueId.'"><b>- <a href="#ppj" onclick="seeLess(\'fd'.$uniqueId.'\',\'showLink'.$uniqueId.'\',\'hideLink'.$uniqueId.'\');return false;">hide full description</a></b></div>';
			$decription='<br /><br />';
		}else{
			$description.='<div class="cards_float90">'.$shortCaption.'</div>';
		}
		$content.=$description;
		
		if (trim($msg)!=''){$msg='<div class="cards_float90 cards_margintop">'.$msg.'</div>';}
		// add from: before the msg
		if ($fromStr<>'') {$msg='<div class="cards_float90 cards_margintop">'.$fromStr.'</div>'.$msg;}
		// add to: before the msg
		if ($rxList<>'') {$msg='<div class="cards_float90 cards_margintop">'.$rxList.'</div>'.$msg;}
		$content.=$msg.'</div></div>';
		return $content;
	}

	function makeOneCard($id=0,$postMsg='',$rxList='',$fromFbId=0){
		if ($postMsg!=''){$postMsg='<h2>Message</h2>'.$postMsg;}else{$postMsg='';}	
		if ($rxList!=''){$rxList='<h2>To</h2>'.$rxList;}
		if ($fromFbId<>0) {$fromStr='<h2>From</h2> <fb:name ifcantsee="Anonymous" uid="'.$fromFbId.'" capitalize="true" linked="true" />';} else $fromStr='';
		$query=$this->db->query("SELECT * FROM Cards WHERE id=$id;");
		while ($data=$this->db->readQ($query)) {
			$img='<img class="cards_thumb" src="'.URL_BASE.'/images/gifts/'.$data->slug.'.png">';
			$code.=$this->makeCardDisplay($data->id,$img,$data->name,$data->shortCaption,$data->longCaption,$postMsg,$rxList,$fromStr);
		}
		return $code;
	}

	function listCardsReceived($fbId) {
		$query=$this->db->query("SELECT Log.*,Cards.slug,Cards.name,Cards.shortCaption,Cards.longCaption,UserInfo.fbId as senderFbId,(SELECT txt FROM LogExtra WHERE logid=Log.id) as msg FROM Log LEFT JOIN Cards ON (Log.itemid=Cards.id) LEFT JOIN UserInfo ON (Log.userid1=UserInfo.userid) WHERE action='sendCard' AND userid2=$fbId ORDER BY Log.t DESC;");
		$temp='<div class="cards_floatwide">';
		if ($this->db->count()==0) {
			$temp.='<p>You have not received any '.CARDS_NAME.' yet.&nbsp;&nbsp;<a href="?p=cards&o=send" requirelogin="1">Send one now</a>.</p>';
		} else {
			$counter=0; //use to create unique div ids for FBJS hide/show
			while ($data=$this->db->readQ($query)) {			
		        $timeSince=$this->utilObj->date_diff($data->t,date("Y-m-d H:i:s"),false).' ago';	
		    	$content='<div classes="cards_floatwide cards_marginbottom" ><h1><fb:name uid="'.$data->senderFbId.'" capitalize="true" /> sent you a '.$data->name.' '.$timeSince.' ... <a href="?p=cards&o=send&prefillId='.$data->senderFbId.' requirelogin="1">Reply</a></h1> </div>';		    	
		    	if ($data->msg!=''){$msg='<h2>Message</h2>'.$data->msg;}else{$msg='';}	
		    	$img='<img class="thumb" class="cards_thumb_noborder" src="'.URL_BASE.'/images/gifts/'.$data->slug.'.png">';
		    	$content.=$this->makeCardDisplay($counter,$img,$data->name,$data->shortCaption,$data->longCaption,$msg);
		    	$temp.=$content;
		    	$counter++;		    	
		    }
		}
		$temp.='</div>';
		return $temp;
	}
	
	function listCardsSent($userid) {
		$query=$this->db->query("SELECT Log.*,Cards.slug,Cards.name,Cards.shortCaption,Cards.longCaption,UserInfo.fbId as senderFbId,(SELECT txt FROM LogExtra WHERE logid=Log.id) as msg FROM Log LEFT JOIN Cards ON (Log.itemid=Cards.id) LEFT JOIN UserInfo ON ($userid=UserInfo.userid)  WHERE action='sendCard' AND userid1=$userid ORDER BY Log.t DESC;");

		$temp='<div class="cards_floatwide">';
		if ($this->db->count()==0) {
			$temp.='<p>You have not sent any '.CARDS_NAME.' yet.&nbsp;&nbsp;<a href="?p=cards&o=send" requirelogin="1">Send one now</a>.</p>';
		} else {
			$counter=0; //use to create unique div ids for FBJS hide/show
			while ($data=$this->db->readQ($query)) {			
		        $timeSince=$this->utilObj->date_diff($data->t,date("Y-m-d H:i:s"),false).' ago';		    	
		    	$content='<div class="cards_floatwide cards_marginbottom"><h1>You sent <fb:name uid="'.$data->userid2.'" capitalize="true" /> a '.$data->name.' '.$timeSince.'</h1></div>';
		    	
		    	if ($data->msg!=''){$msg='<h2>Message</h2>'.$data->msg;}else{$msg='';}	
		    	$img='<img class="cards_thumb cards_noborder" src="'.URL_BASE.'/images/gifts/'.$data->slug.'.png">';
		    	$content.=$this->makeCardDisplay($counter,$img,$data->name,$data->shortCaption,$data->longCaption,$msg);
		    	$temp.=$content;
		    	$counter++;
		    }
		}
		$temp.='</div>';
		return $temp;
	}

	function makeFancyTitle($text='',$width='100%'){
		$title='<div class="cards_fancyTitle cards_floatwide"><img src="'.URL_BASE.'/images/cardGraphic.jpg" width="13" height="14" border="0" align="top"><span class="cards_fancyTitleText">'.$text.'</span></div>';
		return $title;
	}

	function buildFBJS($paneWidth=500){
		$js.="<script>\n <!--\n ";
		$js.="\nfunction readMore(stage,sLink,hLink) {\n Animation(document.getElementById(stage)).to('height', 'auto').from('0px').to('width', 'auto').from('0px').to('opacity', 1).from(0).blind().show().go(); document.getElementById(sLink).setStyle('display', 'none'); document.getElementById(hLink).setStyle('display', 'inline');  } ";
		$js.="\n\nfunction seeLess(stage,sLink,hLink) {\n Animation(document.getElementById(stage)).to('height', '0px').to('width', '0px').to('opacity', 0).hide().go(); document.getElementById(hLink).setStyle('display', 'none'); document.getElementById(sLink).setStyle('display', 'inline');  } ";
		$js.="\nfunction changeCard(id) {\n var fb='card'+id; document.getElementById('selCardStage').setInnerFBML(details[fb]); document.getElementById('pickCard').setValue(id);  \n} ";
		$js.="\nfunction movePanel(pg,numPages) {\n var newX=".$paneWidth."-(pg*".$paneWidth.");  Animation(document.getElementById('thumbPanel')).to('left', newX+'px').go(); \n} ";
		$js.="\nfunction selectForAttach(id) {\n var fb='card'+id; document.getElementById('selCardStage').setInnerFBML(details[fb]); document.getElementById('id').setValue(id);  \n} ";		
		$js.="\n //--> \n</script>\n";
		return $js;
	}

    function checkResubmit($userid=0,$cardid,$fbIdList){
		// checks to see if they are resending to someone
    	$check=false;
    	$numSubmitted=sizeof($fbIdList);
    	$query=$this->db->query("SELECT * FROM Log WHERE action='sendCard' AND userid1=$userid ORDER BY t DESC;");
		while ($data=$this->db->readQ($query)) {
			if ($cardid==$data->itemid && in_array($data->userid2, $fbIdList)){
				$check=true;
			}
		}
     	return $check;
    }		
	
}
?>