<?php

require_once(PATH_CORE.'/classes/dbRowObject.class.php');

/*
 * New approach: Row object and Table object explicitly separate. Row objects are now dynamic
 * so the serialize functionality is not subsumed into Prize, and the general operations are all in PrizeTable
 * 
 */


class Prize extends dbRowObject 
{

  function __construct($db, $tablename, $fieldnames, $idname) // could create directly, but better to ask the PrizeTable object for it!
  {
  	parent::__construct( $db, $tablename,
	    $fieldnames, $idname );
  
  }
  
	// TODO-maybe: put all field data in this class as static members in the fundamental row object, then build a the table based on it.
  
	// base class uses dynamic fields and query generation, custom business logic goes in here
  
  	// example
  	function decreaseStock()
  	{
  		$this->currentStock--;
  		$this->update();
  	
  	}
  	
}



class PrizeTable 
{
	var $db;
	
	static $tablename="Prizes";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "Prize";
	
	static $fields = array(		
			"title"				=>"VARCHAR(255) default ''",
			"shortName"			=>"VARCHAR(25) default ''",
			"description"		=>"TEXT default ''",
			"sponsor"			=>"VARCHAR (150) default ''",
			"sponsorUrl"		=>"VARCHAR (255) default ''",
			"dateStart"			=>"DATETIME",
			"dateEnd"			=>"DATETIME",
			"initialStock"		=>"INT(4) default 0",
			"currentStock"		=>"INT(4) default 0",
			//"category"			=>"ENUM ('default','weekly','final') default 'default'",
			"pointCost"			=>"INT(4) default 1000",
			"eligibility"		=>"ENUM ('team','general') default 'team'",
			"userMaximum"		=>"INT(4) default 0", // a limit on the number of times a single user may redeem this particular prize (0= nolimit)				
			"status"			=>"ENUM ('enabled','disabled','hold') default 'enabled'",
			"orderFieldsNeeded"	=>"VARCHAR(150) default 'name address phone email'",  // string flags to help us build an appropriate order page. i.e. dont want to ask for shipping address for virtual prizes
			"thumbnail" 		=>"VARCHAR(255) default 'default_prize_thumb.png'", // filename in the directory of thumbnail images
			"isWeekly"			=>"TINYINT(1) default 0",
			"isGrand"			=>"TINYINT(1) default 0",
			"isFeatured"		=>"TINYINT(1) default 0",
			"dollarValue"		=>"INT(6) default 0",
	
			);
	static $keydefinitions = array(); 
	
	function __construct(&$db=NULL) 
	{

		if (is_null($db)) { 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;
			
	//	$this->fields = 
		//echo "PrizeTable Constructor -- is this ever necessary?\n";
		//echo (self::$fields);
			 
	}
	
	// although many functions will be duplicated between table subclasses, having a parent class gets too messy
	
	function getRowObject()
	{
		// TODO: optimize row object creation by caching field names in a local static var
		// TODO: pass the fields array by ref OR move it to the row class
		//echo (self::$fields);
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

	///////////////////////////////////////////////////////////////////////////////////////////
	// queries and operations involving multiple prize row objects would be implemented here, i.e. GetPrizeStockSummary or somesuch
	

	
	function testPopulate()
	{
		
		echo '<p>PrizeTable::testPopulate()</p>';
		$prize = $this->getRowObject();
		

		$prize->title = '1 acre of Brazilian Rainforest';
		$prize->sponsor = 'SaveTheTrees.org';
		$prize->pointCost = 100;
		$prize->currentStock = 1000;
		$prize->dateStart = date('Y-m-d H:i:s', time());
		$prize->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
	
		if (!self::checkPrizeExistsByTitle($prize->title)) $prize->insert();

		$prize->title = '3 acres of Brazilian Rainforest';
		$prize->sponsor = 'SaveTheTrees.org';
		$prize->pointCost = 250;
		$prize->currentStock = 1000;
		$prize->dateStart = date('Y-m-d H:i:s', time());
		$prize->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		
		if (!self::checkPrizeExistsByTitle($prize->title)) $prize->insert();
		
		$prize->title = 'Protect 1 tree in your state';
		$prize->sponsor = 'SaveTheTrees.org';
		$prize->pointCost = 25;
		$prize->currentStock = 1000;
		$prize->dateStart = date('Y-m-d H:i:s', time());
		$prize->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		
		if (!self::checkPrizeExistsByTitle($prize->title)) $prize->insert();
		
		$prize->title = 'Kitten';
		$prize->description = 'A cute orphaned kitten, in a box, delivered directly to your doorstep';
		$prize->sponsor = 'PetRescue.com';
		$prize->pointCost = 300;
		$prize->currentStock = 1000;
		$prize->dateStart = date('Y-m-d H:i:s', time());
		$prize->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		
		if (!self::checkPrizeExistsByTitle($prize->title)) $prize->insert();

		$prize->title = 'Mean Kitten';
		$prize->description = 'A cute but mean orphaned kitten, in a box, delivered directly to your doorstep';
		$prize->sponsor = 'PetRescue.com';
		$prize->pointCost = 200;
		$prize->currentStock = 1000;
		$prize->dateStart = date('Y-m-d H:i:s', time());
		$prize->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
		
		
		if (!self::checkPrizeExistsByTitle($prize->title)) $prize->insert();
		
		
		$prize->title = 'Dumb Kitten';
		$prize->description = 'A cute but slightly stupid kitten, in a box, delivered directly to your doorstep';
		$prize->sponsor = 'PetRescue';
		$prize->sponsorUrl = 'http://www.petrescue.com';
		$prize->pointCost = 275;
		$prize->currentStock = 1000;
		$prize->dateStart = date('Y-m-d H:i:s', time());
		$prize->dateEnd = date('Y-m-d H:i:s', time()+3600*24*7);
	
		
		if (!self::checkPrizeExistsByTitle($prize->title)) $prize->insert();
		
		
	}
	
	function checkPrizeExistsByTitle($title)
  	{
  		
  		$chkDup=$this->db->queryC("SELECT ".self::$idname." FROM ".self::$tablename." WHERE title='$title'");
		return $chkDup;
  		
  	}
  		
	static function userIsEligible($prize_el, $user_el)
	{
 		// this implies that 'general' can only redeem prizes marked 'general', and 'team' can only redeem prizes marked 'team'. 
 		// might also want that team can redeem general prizes...
		return $prize_el==$user_el || ($prize_el == 'general' && $user_el=='team');
		
	}
  	
	static function getSQLEligibilityClause($eligibility) // return a sql cause exactly matching the logic above in userIsEligible so that prize displays can be filtered consistently
	{
		return "(eligibility='$eligibility' OR (eligibility='general' AND '$eligibility'='team'))";
	}
	
	// helper function to add eligibility criterion to a possibly nonempty wherestring already being constructed by the caller
	static function addEligibilityToWhereString($whereString, $eligibility)
	{
		if ($eligibility=='' || $eligibility=='ineligible') return $whereString; // ineligible users can still see prizes, so do nothing
		return $whereString =='' ? ("WHERE ".self::getSQLEligibilityClause($eligibility)) : // wherestring is empty, so add WHERE keyword on the front
									("$whereString AND ".self::getSQLEligibilityClause($eligibility)); // wherestring not empty, add AND keyword and return the clause 
				
	}
	
	function getPrizeList($where='1', $orderby='id')
	{
		$prizes =array();
		$q= $this->db->queryC("SELECT title,id,dateEnd FROM Prizes WHERE $where ORDER BY $orderby;");

		if ($q)
		{
			
			while ($data = $this->db->readQ($q))
			{
			
				$prizes[$data->id]=$data->title;
			}
		}
		return $prizes;	
		
		
	}
	
	// helper to retrieve a list of weekly prizes, used right now for the leaderboard header
	function getWeeklyPrizesByDate($date,$orderby)
	{
		return $this->getPrizeList("(dateStart<$date) AND (dateEnd>$date) AND isWeekly=1",$orderby );
		
	}
  	
	
}


class rewards {
	
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
		
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'rewards');               
      
	}
	
	
	function fetchRewards($sort='title'/*REDEEM: 'pointCost'*/, $filter='weekly'/*REDEEM:'redeemable'*/, $currentPage=1, $isAjax=false, $eligibility='team')
	{
		
		/* $code .=  '<div class="subFilter">Sort by:
            <a href="#" class="feedFilterButton selected">Points</a> <a href="#" style="" class="feedFilterButton">Name</a> <a href="#" style="" class="feedFilterButton">Number Available</a>
    </div><!--end "subtitle"-->';
		 */
			if (!$isAjax)
			{
				$code .= $this->fetchSubSort($sort, $filter);
			}
		
			//$inside.=$rewards->fetchRewards('default',$currentPage);
		
		$code.='<div id="rewardGrid">';		
		
        $code.=$this->fetchRewardsPage($sort,$filter,$currentPage,true, '', $eligibility);
        $code.='<!-- end rewardGrid --></div>';
		
        return $code;
   	
	}
	
   function fetchSubSort($sort='pointCost', $filter='redeemable') 
   {
   		$sortlist = array(/* REDEEM: 'pointCost'=>'Points', */'title'=>'Name', 'currentStock'=>'# Available'); //dateStart' =>'Date Added', 'title' => 'Title');
   		$filterlist = array(/* REDEEM: 'redeemable'=>'Redeemable by points', */'weekly'=>'Weekly rewards', 'grand'=>'Grand &amp; Runners Up rewards');

   	//	if ($sort == 'default') $sort = 'pointCost';
   	
        $code.='<div class="subFilter">';
		$code .= '<input type="hidden" id="sort" value="'.$sort.'" />';        	
		$code .= '<input type="hidden" id="filter" value="'.$filter.'" />';        	
        
        
        $code .= 'Filter by:';
        foreach ($filterlist as $field => $name) 
        {
         //   $code.='<option value="'.$field.'" '.($sort==$field?'SELECTED':'').'>'.$catlist[$field].'</option>';
         	$code .= '<a href="#" id="'.$field.'Filter" class=" '.($filter==$field?'selected':'').'" 
         						onclick="setRewardFilter(\''.$field.'\'); return false;">'.$name.'</a>';
        }
        
        
        $code .= '&nbsp;&nbsp;&nbsp;Sort by:'; // TODO: hack, this style should be changed to subSort
          //	'<select name="sort" id="sort" onChange="refreshRewards();">';
        foreach ($sortlist as $field => $name) 
        {
         //   $code.='<option value="'.$field.'" '.($sort==$field?'SELECTED':'').'>'.$catlist[$field].'</option>';
         	$code .= '<a href="#" id="'.$field.'Sort" class=" '.($sort==$field?'selected':'').'" 
         						onclick="setRewardSort(\''.$field.'\'); return false;">'.$name.'</a>';
        }
         
        //$code.='</select></div><br clear="all" />';
        //$code .='<a class="bar_link" href="?p=rewards&o=summary">Summary</a>';
		$code .= '</div>';
   	  		
        return $code;       
    }
	
	
	function fetchRewardsPage($sort='weekly'/*REDEEM: 'redeemable'*/,$filter='redeemable', $currentPage=1, $paging=true, $whereString ='',$eligibility) 
	{ 
		
		$cacheName=$this->templateObj->safeFilename(
			"rewards_{$sort}_{$filter}_{$currentPage}_".($paging?'p':'np')."_{$whereString}_{$eligibility}");
	    if ($this->templateObj->checkCache($cacheName,7)) {
	        // still current, get from cache
	        
	        $code=$this->templateObj->fetchCache($cacheName);
	       // $code .='fromcache';
	        
	    } else {

			
			
			// to do - take out rows per page
			$rowsPerPage=32;
			// userid is passed in because there is no session when refreshed with Ajax
			$code='';
			//if ($sort == 'default') $sort = 'pointCost';
			
			//if ($whereString =='')
			{
				if ($whereString <> '') $whereString .= ' AND ';
				switch ($filter)
				{
					default: $wherestring .= '1'; break; // do nothing, but dont break syntax
					case 'redeemable': $whereString .= 'isGrand=0 AND isWeekly=0'; break;
					case 'weekly': $whereString .= 'isWeekly=1'; $pointClass = "hidden"; break;  
					//$this->templateObj->setTemplateCallback('pointClass', array($this, 'pointClassHidden')); break;
					case 'grand': $whereString .= 'isGrand!=0'; $pointClass = "hidden"; break;					 
					//$this->templateObj->setTemplateCallback('pointClass', array($this, 'pointClassHidden')); break;					
				}
			}
			
			//$eligString = ($eligibility=='' || $eligibility=='ineligible') ? '' : PrizeTable::getSQLEligibilityClause($eligibility);
			$whereString=PrizeTable::addEligibilityToWhereString($whereString, $eligibility); 
			
			$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
			
			$query = "SELECT SQL_CALC_FOUND_ROWS *, '$pointClass' AS pointClass,
						DATE_FORMAT(dateStart, '%c/%e') AS shortDateStart,
						DATE_FORMAT(dateEnd, '%c/%e') AS shortDateEnd,
						IF(isGrand>0,'hidden','') AS dateClass
						FROM Prizes 
						WHERE status='enabled' AND $whereString
						GROUP BY shortName 
						ORDER BY $sort DESC LIMIT $startRow,".$rowsPerPage.";";
				
			$this->templateObj->db->log($query);
			$prizeList=$this->templateObj->db->query($query); // $this->page->rowsPerPage
			//$code.='<div>';
			$this->templateObj->db->log($query);		
	
			// to do - later we'll move these template defs
			if ($this->templateObj->db->countQ($prizeList)>0) {
				$rowTotal=$this->templateObj->db->countFoundRows();
				$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'?&p=rewards&sort='.$sort.'&currentPage='); // later put back page->rowsPerPage			
				$this->templateObj->db->setTemplateCallback('description', array($this->templateObj, 'cleanString'), array('description', 80));
				$code.=$this->templateObj->mergeTemplate(
					$this->templateObj->templates['rewardList'],$this->templateObj->templates['rewardItem']);           
			} else {
				$code.='There are no prizes yet.';
			}			
			//$code.='</div>';
			// jr - not sure if we'll need paging
			// if ($paging) $code.=$pagingHTML;
			
			$this->templateObj->cacheContent($cacheName,$code);
			
	    }
	    return $code;
	}	

	function fetchRewardsPanelList($sort='default', $limit=3, $whereString='', $eligibility) 
	{ 
		//$this->db->setTemplateCallback('linkedThumbnail',array($this, 'buildVideos') ,'completedid');
		//$this->db->setTemplateCallback('linked',array($this, 'buildPhotos') ,'completedid');
		
		$code='';
		if ($sort == 'default') $sort = 'pointCost';
		
	
		$whereString = PrizeTable::addEligibilityToWhereString($whereString,$eligibility);
				
		$prizeList=$this->templateObj->db->query(
			"SELECT SQL_CALC_FOUND_ROWS *, 
				IF(isGrand>0 OR isWeekly=1,'hidden','') AS pointClass 
				FROM Prizes 
			$whereString ORDER BY $sort DESC LIMIT 0,$limit;"); // $this->page->rowsPerPage

		if ($this->templateObj->db->countQ($prizeList)>0) {
			$rowTotal=$this->templateObj->db->countFoundRows();
			
			$code.=$this->templateObj->mergeTemplate(
				$this->templateObj->templates['rewardPanelList'],$this->templateObj->templates['rewardPanelItem']);           
		} else {
			$code.='There are no prizes yet.';
		}			
		
		return $code;
	}	

	function fetchWinners($whereString = '', $currentPage=1)
	{
		if ($whereString <> '') $whereString .= "AND $whereString";
		
		$rowsPerPage = 50;
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		
		
		$prizeList=$this->templateObj->db->query(
			"SELECT SQL_CALC_FOUND_ROWS
				Prizes.id AS prizeid,
				fbId, title, thumbnail
				
				FROM Log,User,UserInfo,Prizes
				WHERE  	User.userid = Log.userid1 AND UserInfo.userid=User.userid 
						AND Log.action='wonPrize' AND Prizes.id=Log.itemid
					$whereString ORDER BY prizeid ASC LIMIT $startRow,$rowsPerPage;"); 


		if ($this->templateObj->db->countQ($prizeList)>0) {
			$rowTotal=$this->templateObj->db->countFoundRows();
			$pagingHTML=$this->templateObj->paging($currentPage,$rowTotal,$rowsPerPage,'?&p=rewards&o=winners&currentPage='); // later put back page->rowsPerPage			
		
			$code.=$this->templateObj->mergeTemplate(
				$this->templateObj->templates['winnerList'],$this->templateObj->templates['winnerItem']);

			$code .=$pagingHTML;
		} else {
			$code.='No winners yet.';
		}			
		
		$inside = $code;
		
		// hack: wrap in refresh-pageable div here, rather than in a sort wrapper
		$code =  ' <div id="storyList" class="list_stories">'; // id="challengeList
		$code .= '<input type="hidden" id="pagingFunction" value="fetchWinners" />';
	
		$code.=$inside;
        //$code.='<!-- end rewardGrid --></div>';
		$code.='</div><!--end "winnerList list_stories"-->';
		
		
		
		return $code; 
	}

	function fetchPrizesWithWinners()
	{
		/*
		$prizeList=$this->templateObj->db->query(
			"SELECT SQL_CALC_FOUND_ROWS
				Prizes.id AS prizeid,
				title, thumbnail				
				FROM Prizes				
				ORDER BY dateEnd ASC;"); 
		
		//$this->templateObj->db->setTemplateCallback('winners', array(&$this, 'fetchWinnersProfilePicsByPrize'), 'prizeid');

		if ($this->templateObj->db->countQ($prizeList)>0) {
			$listTemplate = '<ul>{items}</ul>';
			$itemTemplate = '<li><div class="thumb">'.template::buildLinkedRewardPic('{prizeid}', '{thumbnail}', 30).
		                '</div>'.
		                   '<p class="storyHead">'.template::buildRewardLink('{title}', '{prizeid}') .' </p>'.
							'<p class="storyCaption">{winners}</p></li>';         
			$code.=$this->templateObj->mergeTemplate($listTemplate,$itemTemplate);


			} else {
			$code.='No winners yet.';
		}			
		*/
		$where = '1';
		$orderby = 'dateEnd ASC';
		
		$prizes =array();
		$q= $this->db->queryC("SELECT title,id,dateEnd,thumbnail FROM Prizes WHERE $where ORDER BY $orderby;");

		if ($q)
		{
			
			while ($data = $this->db->readQ($q))
			{			
				$prizes[$data->id] = $data;				
			}
		}
		
		foreach ($prizes as $id => $data)
		{
			
			$code .= '<li><div class="thumb">'.template::buildLinkedRewardPic($data->id, $data->thumbnail, 60).
		                '</div>'.
		                   '<p class="storyHead">'.template::buildRewardLink($data->title, $data->id) .' </p>'.
							'<br clear="all" />'.
							'<p class="storyCaption">'.$this->fetchWinnersProfilePicsByPrize($data->id).'</p></li>';  
		}
		
		
		return '<ul>'.$code.'</ul>';
	}
	
	function fetchWinnersProfilePicsByPrize($prizeid = '')
	{
				
		$prizeList=$this->templateObj->db->query(
			"SELECT SQL_CALC_FOUND_ROWS
				Prizes.id AS prizeid,
				fbId, title, thumbnail
				
				FROM Log,User,UserInfo,Prizes
				WHERE  	User.userid = Log.userid1 AND UserInfo.userid=User.userid 
						AND Log.action='wonPrize' AND Prizes.id=Log.itemid AND Prizes.id=$prizeid
					 ORDER BY Log.dateCreated ASC"); 


		if ($this->templateObj->db->countQ($prizeList)>0) {

			$listTemplate = '<ul>{items}</ul>';
			$itemTemplate = ' '.template::buildLinkedProfilePic("{fbId}",'size="square"  with="30" height="30"'). ' ';
			
			$code.=$this->templateObj->mergeTemplate(
				$listTemplate, $itemTemplate,'', 1000000); // f*ck the row limit hacked crap
				//$this->templateObj->templates['winnerList'],$this->templateObj->templates['winnerItem']);

		} else {
			$code.='No winners yet.';
		}			
				
		return $code; 
	}
	
	
	
	// might this belong in facebook/pages?
	function fetchRewardDetail($id, $noButtons=false,$shortSummary=false)
	{
		$prizeTable = new PrizeTable($this->db);
		$prize = $prizeTable->getRowObject();
		
		if ($id && $prize->load($id))
		{
			$pointText = !$shortSummary ? 
							($prize->pointCost==0 ? 
								(($prize->isGrand==1 ?
						     	     	'Grand Prize':'')
						     	     .($prize->isGrand>1 ?
						     	     	'Runner-up prize':'')
									.($prize->isWeekly>0 ?
						     	     	'Weekly prize':''))									
								: ($prize->pointCost.' <span class="pts">pts</span>')) 
							: '';

			
			$code = '
 
    <div class="thumb">'. template::buildLinkedRewardPic($prize->id, $prize->thumbnail, !$shortSummary ? 180 : 120)//<img src="' . URL_THUMBNAILS.'/'.$prize->thumbnail. '" width="180" alt="challenge thumbnail" />
						.'</div>
    <div class="storyBlockWrap" >
      <p class="storyHead">'.$prize->title.'</p>
		    <div class="storyBlockMeta">
		    	<p class="pointValue">'.$pointText.'</p>'
		    	.(!$shortSummary ? 
		    		('<p>Sponsored by <a target="_blank" onclick="quickLog(\'extLink\',\'rewards\','.$id.',\''. $prize->sponsorUrl.'\');" href="'. $prize->sponsorUrl.'">'.$prize->sponsor.'</a></p>') 
		    		: '').								     	
		    	
		    	(!$shortSummary ? 
		    		//('<p class="availabilityText">' . $prize->currentStock . '/'.$prize->initialStock.' available week ending '.date('F j, Y',strtotime($prize->dateEnd)).'</p>')		    		 
		    		('<p class="availabilityText">' . $prize->initialStock . ' available, week ending '.date('F j, Y',strtotime($prize->dateEnd)).'</p>')		    		 
		    		: '') .
     	     '</div><!--end "storyBlockMeta"-->
     	     <!--<p class="storyCaption">--><p>'.$prize->description.
		    		($shortSummary ? '<a href="?p=rewards&id='.$id.'" onclick="setTeamTab(\'rewards\','.$id.'); return false;" class="more_link">&hellip;&nbsp;more</a>': ''). 
		    		'</p>
     	     './*($prize->isGrand==1 ?
     	     	'<h3>This is the Grand Prize.</h3>':'')
     	     .($prize->isGrand>1 ?
     	     	'<h3>This a Runner-up prize.</h3>':'')
			.($prize->isWeekly>0 ?
     	     	'<h3>This is a weekly prize.</h3>':'').*/
	 	      	    
    '</div><!--end "storyBlockWrap"-->
    
    
    <p class="">'.
    
    	(($noButtons || $prize->isGrand || $prize->isWeekly)? '' : '<a class="btn_1" href="?p=redeem&id='.$id.'" onclick="setTeamTab(\'redeem\','.$id.'); return false;">'.
		 		'Get it! Redeem my points' . '</a>')
						
	.'</p>';


			if ($shortSummary) // client supplies containers
			{
				
				
			} else // default container divs
			{
			  $code = '<div id="readStoryList">
						  <div class="panel_block">'.$code. 
						  '</div><!--end "panel_block"-->
						</div><!--end "readStoryList"-->';

			}
		 
		} else
		{
			$code .= 'Invalid prize id';
		}
		
		return $code;
	}
	
	function fetchRewardsForPublisher($sort='default',$currentPage=1, $paging=true) 
	{ 
		$code='';
		$prizeList=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Prizes ORDER BY pointCost DESC LIMIT 10;"); // $this->page->rowsPerPage

		if ($this->templateObj->db->countQ($prizeList)>0) {
			$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');	
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['pubRewardsList'],$this->templateObj->templates['pubRewardsItem']); 
		} else {
			$code.='There are no prizes yet.';
		}			

		if ($paging) $code.=$pagingHTML;
		return $code;
	}
	
	function fetchPostedRewardInfo($id){
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');					

		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Prizes WHERE id=".$id." LIMIT 1"); // $this->page->rowsPerPage
		
		//need to set thumbnail
		$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['postedRewardsList'],$this->templateObj->templates['postedRewardsItem']);
			

		//shouldn't requery, fix this
		$this->templateObj->db->result=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS title,thumbnail FROM Prizes WHERE id=".$id." LIMIT 1");
		$prizeInfo=$this->templateObj->db->read();
		
		$retArray=array('title'=>trim($prizeInfo->title),
						'storyLink'=>URL_CANVAS.'?p=rewards&id='.$id.'&record',
						'image'=>URL_THUMBNAILS.'/'.$prizeInfo->thumbnail,
						'story'=>$code
						);		

		return $retArray;
	}

	function fetchRewardsForProfileBox() 
	{ 
		$code='';

		$prizeList=$this->templateObj->db->query("SELECT SQL_CALC_FOUND_ROWS * FROM Prizes ORDER BY RAND()LIMIT 1"); // $this->page->rowsPerPage

		if ($this->templateObj->db->countQ($prizeList)>0) {
			$this->templateObj->registerTemplates(MODULE_ACTIVE,'publisher');	
			$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['pubRewardsList'],$this->templateObj->templates['pubRewardsItem']); 
		} else {
			$code.='There are no prizes yet.';
		}			

		$code.='hry';
		return $code;
	}

}	

?>

