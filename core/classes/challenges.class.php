<?php
	
require_once(PATH_CORE.'/classes/dbRowObject.class.php');
class Challenge extends dbRowObject  
{
 
};

class ChallengeCompleted extends dbRowObject  
{
 
};



		
class ChallengeTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Challenges";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "Challenge";
		
	static $fields = array(		
		"title" => 			"VARCHAR(255) default ''",
		"shortName" => 		"VARCHAR(25) default ''",
		"description" => 	"TEXT default ''",
		"dateStart" => 		"DATETIME", // mysql does not accept intelligent functions or NOW() as default - LAME
		"dateEnd" => 		"DATETIME",
		"initialCompletions" => 	"INT(4) default 0", // it may make sense to limit the global number of redemptions possible for certain challenges (0=nolimit) 
		"remainingCompletions" => 	"INT(4) default 0",
		"maxUserCompletions" => 	"INT(4) default 0", // a limit on the number of times a single user may complete this challenge (0= nolimit)		
		"maxUserCompletionsPerDay" =>	"INT(4) default 0", // a limit on the number of times a single user may complete this challenge per day		
		"type" => 			"ENUM ('automatic','submission') default 'automatic'", // to differentiate those that require user submission. note that submission should imply a label of the sort 'up to $pointValue', so we dont end up with complaints
		"pointValue" => 	"INT(4) default 10",
		"eligibility" => 	"ENUM ('team','general') default 'team'", // who can receive points for this challenge
		"status" => 		"ENUM ('enabled','disabled') default 'enabled'",
		"thumbnail" => 		"VARCHAR(255) default 'default_challenge_thumb.png'", // filename in the directory of thumbnail images
		"requires" =>		"VARCHAR(25) default 'text'", // flags include text, photo, video
		"isFeatured"=>		"TINYINT(1) default 0"
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
	
	// generic table creation routine, same for all *Table classes 		
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
	
	function testPopulate()
	{
		
		
		$challenge = $this->getRowObject();
		

		$challenge->title = 'Rescue a cute animal from a politically incorrect predicament';
		$challenge->pointValue = 100;
		
		$challenge->initialCompletions = 100;
		$challenge->remainingCompletions = 100;
		$challenge->maxUserCompletions = 5;
		$challenge->type = 'submission';
		$challenge->dateStart = date('Y-m-d H:i:s', time());
		$challenge->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		$challenge->thumbnail = 'BlenderKitten.jpg';
		$challenge->requires = "text photo video";
		
		if (!self::checkChallengeExistsByTitle($challenge->title)) $challenge->insert();
		
		$challenge->title = 'Start an indignant thread on an SUV enthusiast forum';
	
		$challenge->pointValue = 100;
		
		$challenge->initialCompletions = 100;
		$challenge->remainingCompletions = 100;
		$challenge->maxUserCompletions = 5;
		$challenge->type = 'submission';
		$challenge->dateStart = date('Y-m-d H:i:s', time());
		$challenge->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		$challenge->thumbnail = 'zebrahummer.jpg';
		$challenge->requires = 'text';
		
		
		if (!self::checkChallengeExistsByTitle($challenge->title)) $challenge->insert();
		
		$challenge->title = 'Comment on an article';
	
		$challenge->pointValue = 10;
		
		$challenge->initialCompletions = 0; 
		$challenge->remainingCompletions = 0;
		$challenge->maxUserCompletions = 5;
		$challenge->type = 'automatic';
		$challenge->dateStart = date('Y-m-d H:i:s', time());
		$challenge->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		$challenge->thumbnail = 'comment.jpeg';
	
		
		if (!self::checkChallengeExistsByTitle($challenge->title)) $challenge->insert();
		
		
	}
	
	
	function populateCommonChallenges()
	{
		
	// create the common fields here
		$challenge = $this->getRowObject();
		
		$challenge->thumbnail = 'default_challenge_thumb.jpg';
		$challenge->status = 'enabled';
		$challenge->eligibility = 'team';
		$challenge->isFeatured= 0;
	//	$challenge->type = 'automatic';
		$challenge->dateStart = date('Y-m-d H:i:s', time());
		$challenge->dateEnd = date('Y-m-d H:i:s', time()+3600*24*365*10); // 10yr default
		$challenge->requires = "text";
					
		/*
		 * derived from export command:
		 * mysql -p**** hotdish -B -e "select type, shortName, title, description, initialCompletions, remainingCompletions, pointValue, maxUserCompletions, maxUserCompletionsPerDay,isFeatured,status from Challenges where shortName!='';" | sed 's/\t/","/g;s/^/"/;s/$/"/;s/\n//g' > ~/mysql_exported_challenges.csv
		 * note: doesnt deal well with "" quotes and \n 
		 */
		
		$fieldnames = array("type","shortName","title","description","initialCompletions","remainingCompletions","pointValue","maxUserCompletions","maxUserCompletionsPerDay,isFeatured");
		$challenges = array(
						array("automatic","vote","Vote on a story","Like a story someone's posted? Vote for it and let everyone know.","0","0","5","1000","10","1","enabled"),
						array("automatic","comment","Comment on an article","Comment on an article.","0","0","10","300","10","1","enabled"),
						array("automatic","postStory","Post a story","Post a news story that hasn't been posted yet.","0","0","10","100","0","1","enabled"),
						array("automatic","postBlog","Post a blog entry","Write your own story as a blog post","0","0","10","100","0","0","enabled"),
						array("automatic","shareStory","Share a story","Share a ".SITE_TITLE." story with your friends.","0","0","25","500","10","1","enabled"),
						array("automatic","invite","Invite friends","Earn points just by <a href=\"?p=invite\" onclick=\"switchPage('invite'); return false;\">inviting your friends to use ".SITE_TITLE."</a> ","0","0","25","1000","50","1","enabled"),
						array("automatic","addBookmarkTool","Add bookmark tool","Add the ".SITE_TITLE." bookmark tool to your browser to make it easy to post articles while browsing the web. It's easy: just go to the Post a Story tab and drag the orange \"Post to ".SITE_TITLE."\" button up to the links bar in your browser. (In IE, you may have to drag it to the actual bookmarks tab in the bar and hit save.)","0","0","25","1","0","0","disabled"),
						array("automatic","signup","".SITE_TITLE." sign up","As soon as you sign up to use ".SITE_TITLE.", voila! Instant points!","0","0","200","0","0","0","enabled"),
						array("automatic","friendSignup","Friend sign up","Invite a friend to add the ".SITE_TITLE." app...and then if they add it, we'll shower some points on you.","0","0","100","1000","50","0","enabled"),
						array("automatic","optInEmail","Receive email from ".SITE_TITLE."!","Allow ".SITE_TITLE." to send you occasional updates via email. Go to \"Settings\" and click on \"Would you like to receive email from us through facebook? (50 pts)\" and wait for the magic to happen.","0","0","50","1","1","0","enabled"),
						array("automatic","optInSMS","Receive SMS updates from ".SITE_TITLE."","Receive announcements and feature updates via SMS (aka text messages). Click on \"Settings\" and then \"Would you like to receive sms notifications from us through facebook? (50 pts)\" and follow the instructions.","0","0","50","1","1","0","enabled"),
						array("automatic","levelIncrease","Level up!","As you accumulate points, your User Level increases. When you reach a new level, we give you bonus points!","0","0","200","6","6","0","enabled"),
						array("automatic","referReader","Reader referral","Earn points when someone reads a story you shared!","0","0","5","300","10","0","enabled"),
						array("automatic","chatStory","Chat about a story in ".SITE_TITLE."","Strike up a conversation about a story with one of your Facebook friends using the chat widget on the story page sidebar. This is a great way to introduce friends to climate change issues! Fine print: Your friend must click through to read the story. If they are not ".SITE_TITLE." members, they will be required to authorize the application for you to receive credit.","0","0","25","1000","10","0","enabled"),
						
						array("submission","addAppTab","Add App Tab to profile","Add the ".SITE_TITLE." application tab to your Facebook profile so your friends can see what you've been up to. See <a href=\"".URL_CANVAS."?p=faq\">the FAQ</a> for details. Then send us a screenshot of the tab on your profile! File size must be under 2mb. (Don't know how to take a screenshot? Once again, <a href=\"".URL_CANVAS."?p=faq\">hit up the FAQ</a>.)","0","0","100","1","0","0","disabled"),
						array("submission","profileBoxWall","Add profile box to your wall",'To add the profile box to your profile, visit settings -> application settings, then Edit Settings for ".SITE_TITLE.". Click the Profile tab and "add" next to Box to add the profile box to your profile. Then, move the profile box to your wall page. (If you don\'t see the button, you may have already added this.) Send us a screenshot (file size under 2mb) of the box on your profile! (Don\'t know how to take a screenshot? <a href="'.URL_CANVAS.'?p=faq">Hit up the FAQ</a>.)
						<div><fb:add-section-button section="profile" /><br clear="all" /></div>',"0","0","100","1","1","0","disabled"),
						array("submission","blog","Blog about ".SITE_TITLE."","Forget blogging about what you had for lunch. We know you love ".SITE_TITLE." -- so why not write a post about it on your blog and send us the link?","0","0","75","25","1","0","disabled"),						
						
						);
//					array("automatic","betaTest","Beta Test ".SITE_TITLE."","This special challenge is awarded only to the beta-testers of ".SITE_TITLE.".","0","0","250","1","1","0","disabled"),

		echo "Populating common challenges if missing...<br />";
		foreach ($challenges as $cfields)
		{
			for ($i = 0; $i<count($fieldnames); $i++)
			{
				$fval = $cfields[$i];
				$fname = $fieldnames[$i];
				$challenge->{$fname}=$fval;
			}
			if (!$this->checkChallengeExistsByShortName($challenge->shortName)) 
			{
				$challenge->insert();
				echo "...created $challenge->shortName<br />";
				
			}
			
		}
						
	}
	
	function checkChallengeExistsByTitle($title)
  	{
  		
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE title='$title'");
		return $chkDup;
  		
  	}

  	function checkChallengeExistsByShortName($shortname)
  	{
  		
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE shortName='$shortname'");
		return $chkDup;
  		
  	}
  	
  	
	static function userIsEligible($challenge_el, $user_el)
	{
		// TODO: decide on EL logic
		// probably ok for now
		return true;	
	}
  	
  	function getTitlesAndPointsByShortName($shortNames)
  	{
  		$shortNameString = "'".implode("','",$shortNames)."'"; // construct '-delimiter, comma separated list of shortnames
  		$q = $this->db->queryC("SELECT SQL_CALC_FOUND_ROWS shortName, title, pointValue FROM Challenges 
									WHERE shortName IN($shortNameString); ");
		if ($q) 
		{
			$challenges=array();
			//$data=$this->db->readQ($q);
			//echo '<pre>'.print_r($data,true). '</pre>';
			
			while ($data=$this->db->readQ($q))
			{				
				//echo '<pre>'.print_r($data,true). '</pre>';
				$challenges[$data->shortName]['title'] = $data->title;// = array($data->title, $data->pointValue);=
				$challenges[$data->shortName]['pointValue'] = $data->pointValue;// = array($data->title, $data->pointValue);=
			}
			//echo '<pre>'.print_r($challenges,true). '</pre>';
		} else
		{ 
			//echo '<pre>'.$q. '</pre>';
			return null;	  		
		}
		return $challenges;
  	}
  	
 	
};
	



		
class ChallengeCompletedTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="ChallengesCompleted";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "ChallengeCompleted";
		
	static $fields = array(		
		"userid" 		=> "BIGINT(20) default 0", 
		"challengeid"	=> "INT(11) default 0",
		"dateSubmitted" => "DATETIME",
		"dateAwarded" 	=> "DATETIME",
		"evidence" 		=> "TEXT default ''", // could contain a url or some html linking to the letters, movies, photos, etc
		"comments"		=> "TEXT default ''", // field to contain comment text shown in 'we did this'. for now added/edited by the moderator
		"status" 		=> "ENUM ('submitted','awarded','rejected') default 'submitted'",		
		"pointsAwarded" => "INT(4) default 10", // bonus points can be computed locally since we will have to look up the challenge anyway to display it
		"logid"			=> "BIGINT(20) unsigned default 0" // added to track automatic CCs that need to be linked with their log entries to help render a score log
		);

	static $keydefinitions = array(); 		
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once(PATH_CORE .'/classes/db.class.php');
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
	
	// generic table creation routine, same for all *Table classes 		
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
	
	function approveChallenge($completedid, $pointsAwarded, &$code, $dontLog=false)
	{
		// changes completed record to approved, awards points, logs the completion
		$cc = $this->getRowObject();
		
		$ct = new ChallengeTable($this->db);
		$challenge = $ct->getRowObject();

		require_once (PATH_CORE .'/classes/user.class.php');
		$uit = new UserInfoTable($this->db);
		$ui = $uit->getRowObject();
		$ut = new UserTable($this->db);
		$u = $ut->getRowObject();
	
		if (!($cc->load($completedid) && $challenge->load($cc->challengeid) 
			&& $ui->load($cc->userid) && $u->load($cc->userid)) )
		{	
			$code .=  'Couldnt find submission matching id '. $completedid . 
					' or challenge matching id '.$cc->challengeid . 
					' or user matching id ' . $cc->userid; 
			return false;
		}

		if ($challenge->remainingCompletions < 1 && $challenge->initialCompletions != 0)
		{
			$code .= 'This challenge has no remaining global completions';
			return false;
			
		}
	
		// get total user completions of this challenges

		$q = $this->db->query(
		"SELECT SQL_CALC_FOUND_ROWS * 
			FROM ChallengesCompleted WHERE challengeid={$cc->challengeid} 
										AND userid={$cc->userid} 
										AND status='awarded'
										AND dateSubmitted>=DATE_SUB(CURDATE(),INTERVAL 1 DAY); 
										
										");
		$userCompletionsLast24Hours = $this->db->countQ($q);
	
		
		$q = $this->db->query(
		"SELECT SQL_CALC_FOUND_ROWS * 
			FROM ChallengesCompleted WHERE challengeid={$cc->challengeid} 
										AND userid={$cc->userid} 
										AND status='awarded'");
		$userCompletions = $this->db->countQ($q);
		$code .= 'User completed this challenge ' . $userCompletions . ' times total, '.$userCompletionsLast24Hours. ' times today...';
		if ($userCompletions >= $challenge->maxUserCompletions && ($challenge->maxUserCompletions != 0))
		{
			$code .= 'User not allowed to complete this challenge again';
			return false;
		}
		
		if ($userCompletionsLast24Hours >= $challenge->maxUserCompletionsPerDay && ($challenge->maxUserCompletionsPerDay != 0))
		{
			$code .= 'User not allowed to complete this challenge again today';
			return false;
		}
		
		if ($userCompletions==0) // first time they've completed this challenge
		{
			$ui->cachedChallengesCompleted++;
			$ui->update();
		}
	
		if (!$dontLog) // for challenges that coincide with events already being logged
		{
			require_once (PATH_CORE .'/classes/log.class.php');
			$lt = new LogTable($this->db);
			$log = $lt->getRowObject();
			$log->action='completedChallenge';
			$log->userid1 = $cc->userid;
			$log->itemid = $completedid;
			$log->dateCreated = date('Y-m-d H:i:s', time());
			$log->insert();
			$cc->logid=$log->id; // for consistency, link with the log entry	
		}		
		
		
		$cc->status='awarded';
		$cc->pointsAwarded = $pointsAwarded;
		$cc->dateAwarded = date('Y-m-d H:i:s', time());
		
		$cc->update();
		
		$challenge->remainingCompletions--;
		$challenge->update();
		
		$u->cachedPointTotal+= $pointsAwarded;
		$u->update();
		
				$code .= 'Challenge completion approved.';
		
		return true;
	}
	
	
	function submitAutomaticChallenge($userid, $shortname, &$code, $dontLog, $logid)
	{
		// create the cc object, then call the approval mechanism
		
				
  	
		$evidence = 'Automatic!';
		
		require_once(PATH_CORE.'/classes/user.class.php');
		$challengeTable	= new ChallengeTable($db);
		$userTable 		= new UserTable($db);
		$userInfoTable 	= new UserInfoTable($db);
		$completedTable	= $this;
		
		$user 		= $userTable->getRowObject();
		$userInfo 	= $userInfoTable->getRowObject();
		$challenge 	= $challengeTable->getRowObject();
		$completed 	= $completedTable->getRowObject();
		
		//dbRowObject::$debug =1;
		
		if (!(	$user->load($userid) &&
				$userInfo->load($userid) &&
				$challenge->loadWhere("shortName='$shortname'")))
				{
					$code .= "Bad user: $userid or bad challenge: $challengeid. ";
					return false;
				}
		
		// validate challenge submission info
		
		// validate eligibility, date, membership
		
			
			
		if ($challenge->remainingCompletions <= 0 && $challenge->initialCompletions!=0)
		{	$code = 'Insufficient completions.'; return false; }
						
		if (!ChallengeTable::userIsEligible($challenge->eligibility, $user->eligibility))
		{	$code = 'User not eligible.'; return false; }

		if (!$evidence <> '')
		{ 	$code = 'Evidence was blank'; return false; }
			
		//if () //  TODO: now is between date start and end
		$now = time();
		$dateStart 	= strtotime($challenge->dateStart);
		$dateEnd 	= strtotime($challenge->dateEnd);
		
		if ($now > $dateEnd)
		{ 	$code = 'Sorry, you are too late!'; return false; }

		if ($now < $dateStart)
		{	$code = 'Sorry, you are too early'; return false; }
			
		// if () TODO: check user maximum by querying order histor						
		// more...
		
			
		// everythings ok:
		
		$challenge->remainingCompletions--;
		
		$completed->userid = $user->userid;
		$completed->challengeid = $challenge->id;
		$completed->dateSubmitted = date('Y-m-d H:i:s', time());
		$completed->status = 'submitted';
		$completed->evidence = $evidence;
		$completed->logid = $logid;
		// have to have a completed id to attach to the media records...
		$completed->insert();
			
		$challenge->update();	
			
		$code .= 'Automatic Challenge Application #'. $completed->id . ' submitted.';
		
	
		dbRowObject::$debug =0;

		// now approve
	
		return $this->approveChallenge($completed->id, $challenge->pointValue, &$code, $dontLog);
		
	}
	
	function revokeAutomaticChallengeAward($userid,$shortname)
	{
		require_once(PATH_CORE.'/classes/user.class.php');
		$challengeTable	= new ChallengeTable($db);
		$userTable 		= new UserTable($db);
		$userInfoTable 	= new UserInfoTable($db);
		$completedTable	= $this;
		
		$user 		= $userTable->getRowObject();
		$userInfo 	= $userInfoTable->getRowObject();
		$challenge 	= $challengeTable->getRowObject();
		$completed 	= $completedTable->getRowObject();
		
		$ccid;
		
		$res = $this->db->query("SELECT SQL_CALC_FOUND_ROWS ChallengesCompleted.id AS ccid 
									FROM ChallengesCompleted,Challenges 
										WHERE userid=$userid 
											AND Challenges.id = challengeid 
											AND Challenges.shortName='$shortname'
											AND ChallengesCompleted.status='awarded';");
		if ($this->db->countQ($res)) 
		{
			$data=$this->db->readQ($res);
		} else 
			return false;	
	
		$ccid=$data->ccid;

		if(!$completed->load($ccid))
		{		
			return false;		
		}
		
		$completed->pointsAwarded=0;
		$completed->dateAwarded = date('Y-m-d H:i:s', time());
		$completed->update();
		return true;
		
	}
	  	
};



class challenges {
	
	var $db;
	var $templateObj;
		
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
			
		$this->setupLibraries();
			
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'challenges');               
      
	}
	
	function fetchChallengePanelList($sort='default', $limit=3, $whereString='',$shortCommands=false) 
	{
			// to do - take out rows per page
		// userid is passed in because there is no session when refreshed with Ajax
		$code='';
		//$startRow=($currentPage-1)*ROWS_PER_PAGE; // replace rows per page
		
		$this->templateObj->db->setTemplateCallback('description', array($this,'ellipseChallengePanelDescription'),'description');
		
		$challengeList=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS 
				thumbnail, title, IF(pointValue=0,'flex',pointValue) AS pointValue, id, 
				MONTHNAME(dateStart) AS monthstart,
				DAY(dateStart) AS daystart,
				MONTHNAME(dateEnd) AS monthend,
				DAY(dateEnd) AS dayend,
				description,
				(initialCompletions-remainingCompletions) AS completions 
				FROM Challenges $whereString ORDER BY $sort DESC LIMIT 0,$limit;"); 
		
		//$code.='<div>';
		// to do - later we'll move these template defs
		if ($shortCommands) 
			$itemTemplate=$this->templateObj->templates['challengePanelItemShort'];
		else
			$itemTemplate=$this->templateObj->templates['challengePanelItem'];
		if ($this->templateObj->db->countQ($challengeList)>0) 
		{		
			$rowTotal=$this->templateObj->db->countFoundRows();
			$code.=$this->templateObj->mergeTemplate(
				$this->templateObj->templates['challengePanelList'],$itemTemplate); 
		} else {
			$code.='No challenges found.';
		}			
		return $code;
		
	}		

	function ellipseChallengePanelDescription($desc)
	{
		return $this->templateObj->cleanEllipsis($desc,200);
	}
	//////////////////
	
	function fetchChallenges($sort='pointValue', $currentPage=1, $isAjax=false)
	{
		
		if (!$isAjax)
		{
			$code .= $this->fetchSubSort($sort);
			
		}
		
		//$code.='<div id="rewardGrid">';		
		$code .=  ' <div id="storyList" class="list_stories">'; // id="challengeList
		$code .= '<input type="hidden" id="pagingFunction" value="fetchChallenges" />';
	
		$code.=$this->fetchChallengeList($sort,$currentPage);
        //$code.='<!-- end rewardGrid --></div>';
		$code.='</div><!--end "challengeList list_stories"-->';
        
        return $code;
        /* old code		
		$code='<div id="navSort">'; //<input type="hidden" id="sort" value="'.$sort.'">'; // cant use this with select controls
        $code.=$this->fetchSubNav($sort);
        $code.='<!-- end navFilter --></div><div id="storyList">';
        $code.='</div><!-- end storyList --></div>';
        */
	
	}
	
	
   function fetchSubSort($sort='pointValue') 
   {
  		$sortlist = array(	'pointValue'			=>'Points', 
   							'title'					=>'Name',
   							'isFeatured' 			=>'Featured', 
   							'dateStart'				=>'Date'); 	
     	
        $code.='<div class="subFilter">';
		$code .= '<input type="hidden" id="sort" value="'.$sort.'" />';        	
	    
        $code .= 'Sort by:'; // TODO: hack, this style should be changed to subSort
        foreach ($sortlist as $field => $name) 
        {

           	$code .= '<a href="#" id="'.$field.'Sort" class=" '.($sort==$field?'selected':'').'" 
         						onclick="setChallengeSort(\''.$field.'\'); return false;">'.$name.'</a>';
        }
     
		$code .= '</div>';
   		
        return $code;       
    }
	
	
	
	
	//////////////////
	
	
	
	function fetchChallengeList($sort, $currentPage=1) 
	{
				
    $cacheName='chList_'.$sort.'_'.$currentPage;
    if ($this->templateObj->checkCache($cacheName,15)) {
        // still current, get from cache
        $code=$this->templateObj->fetchCache($cacheName);
    } else {
		// to do - take out rows per page
	
		$where= "WHERE status='enabled'";
		
		if ($sort=='isFeatured')
			$where.=' AND isFeatured=1';
		
		if ($sort =='pointValue')
			$sort = "$sort DESC";
		
		$code='';
		$rowsPerPage = 2*ROWS_PER_PAGE;
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		$challengeList=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS 
				thumbnail, title, pointValue, id, 
				MONTHNAME(dateStart) AS monthstart,
				DAY(dateStart) AS daystart,
				MONTHNAME(dateEnd) AS monthend,
				DAY(dateEnd) AS dayend,description,
				(CASE type WHEN 'automatic' THEN 'hidden' 
							WHEN 'submission' THEN ''
							END) AS submissionStyle
				FROM Challenges $where ORDER BY type DESC, $sort LIMIT $startRow,".$rowsPerPage.";"); 
		
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($challengeList)>0) 
		{		
			$rowTotal=$this->templateObj->db->countFoundRows();
			$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'&p=challenges&currentPage='); // later put back page->rowsPerPage
			// $this->templateObj->db->setTemplateCallback('comments', array($this, 'decodeComment'), 'comments');
			$this->templateObj->db->setTemplateCallback('pointValue', array($this, 'getPointValue'), 'pointValue');
			//$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['commentList'],$this->templateObj->templates['commentItem']);
			$code.=$pagingHTML;
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates[challengePanelList],$this->templateObj->templates[challengePanelItem]);
			$code.=$pagingHTML;
		} else {
			$code.='No challenges found.';
		}			

        $this->templateObj->cacheContent($cacheName,$code);
    }
		return $code;
		
	}
			
	function getPointValue($val=0) {
		if ($val==0)
			return 'flex';
		else
			return $val;
	}
	
	
	function fetchChallengesForPublisher($sort='default',$currentPage=1, $paging=true) 
	{ 
		
		$code='';
		$challengesList=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS thumbnail, title, pointValue, id, description, MONTHNAME(dateStart) AS monthstart,	DAY(dateStart) AS daystart,	MONTHNAME(dateEnd) AS monthend,	DAY(dateEnd) AS dayend FROM Challenges ORDER BY pointValue DESC LIMIT 10;"); // $this->page->rowsPerPage

		if ($this->templateObj->db->countQ($challengesList)>0) {
			$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');	

			//need to set thumbnail

			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['pubChallengesList'],$this->templateObj->templates['pubChallengesItem']);    
		} else {
			$code.='There are no challenges yet.';
		}			

		if ($paging) $code.=$pagingHTML;
		return $code;
	}
	
	function fetchPostedChallengeInfo($id){
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');					

		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS thumbnail, title, pointValue, id, description, MONTHNAME(dateStart) AS monthstart,	DAY(dateStart) AS daystart,	MONTHNAME(dateEnd) AS monthend,	DAY(dateEnd) AS dayend  FROM Challenges WHERE id=".$id." LIMIT 1"); // $this->page->rowsPerPage
		
		$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['postedChallengesList'],$this->templateObj->templates['postedChallengesItem']);
			
		//shouldn't requery, fix this
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS title,thumbnail FROM Challenges WHERE id=".$id." LIMIT 1");
		$challengeInfo=$this->templateObj->db->read();
		
		$retArray=array('title'=>trim($challengeInfo->title),
						'storyLink'=>URL_CANVAS.'?p=challenges&id='.$id.'&record',
						'story'=>$code
						);		
		if (trim($challengeInfo->thumbnail)!='')
			$retArray['image']=URL_THUMBNAILS.'/'.$challengeInfo->thumbnail;

		return $retArray;
	}
	
	function fetchChallengesforProfileBox(){		
		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'profile');
		
			
		$this->templateObj->db->result=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS 
				thumbnail, title, pointValue, id, 
				MONTHNAME(dateStart) AS monthstart,
				DAY(dateStart) AS daystart,
				MONTHNAME(dateEnd) AS monthend,
				DAY(dateEnd) AS dayend,
				description,
				(initialCompletions-remainingCompletions) AS completions 
				FROM Challenges WHERE status='enabled' AND isFeatured=1 ORDER BY pointValue DESC LIMIT 2;"); 
		
		   
		$code=$this->templateObj->mergeTemplate($this->templateObj->templates['cProfileBoxList'],$this->templateObj->templates['cProfileBoxItem']); 
						
		return $code;	
	}

};

?>