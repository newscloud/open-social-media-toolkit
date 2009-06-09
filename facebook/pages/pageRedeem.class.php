<?php

class pageRedeem 
{

	var $page;
	var $db;
	var $facebook;
	var $app;
	var $templateObj;
		
	function __construct(&$page) 
	{
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->app = &$page->app;
		$this->setupLibraries();
	}

	function setupLibraries() 
	{
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
	}

	function fetch($mode='fullPage') {
		// build the prizes page
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;	
		
		if (isset($_GET['id']))
			$id=$_GET['id'];
		else
			$id=NULL;	
		if (isset($_GET['step']))
			$step=$_GET['step'];
		else
			$step=NULL;	
			
		if (isset($_GET['message']))
			$message = $_GET['message'];
		else			
			$message = '';
			
		if ($_GET['debug']) echo '<pre>' . print_r($_GET, true) . '</pre>';
			
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		$tabs.=$this->teamObj->buildSubNav('rewards');	 
		$inside='<div id="col_left"><!-- begin left side -->';
		
		
		require_once(PATH_CORE.'/classes/prizes.class.php');
		$rewards = new rewards($this->db);

		require_once(PATH_CORE .'/classes/prizes.class.php');
		$pt = new PrizeTable($this->db);
		$prize = $pt->getRowObject();
	
		
		if ($step=='submit') $id = $_POST['prizeid'];
		
		if (!$id || !$prize->load($id)) 
		{
			$inside.='Error: cannot redeem a prize with no id specified';	
		} else
		{
			$inside .= $rewards->fetchRewardDetail($id, true);
			
			if ($step != 'submit') // not the result of a submit
			{			
				if (!$this->checkOrderPossible(&$prize, &$this->page->session->u, &$this->page->session->ui, $message))		
				{
					$inside .= $this->page->buildMessage('error', "There is a problem redeeming this prize", $message);
			
				} else
				{
					// order form					
					$inside .= $this->buildOrderForm(&$prize, &$this->page->session->u, &$this->page->session->ui);
				}
			} else // if process sent us back here, that means there was an error, since the success condition sends us to profile
			{
				// repopulate form with get vars
				
				$user=&$this->page->session->u;
				$userinfo=&$this->page->session->ui;
				
				//$user->email = $_GET['email'];
				
				$uipostfields = array('address1', 'address2', 'city', 'state', 'phone', 'zip');
				//echo '<pre>'.print_r($_POST, true). '</pre>';
				foreach ($uipostfields as $field)
				{
					if (isset($_POST[$field]))
						$userinfo->{$field}=$_POST[$field];					
				}
				$userinfo->update(); // sync updated address info to database				
				//echo '<pre>'.print_r($userinfo, true). '</pre>';				
				$message = '';
				if (!$this->validateOrderFields($prize, $user, $userinfo, $message))
				{			
					$inside .= $this->page->buildMessage('error', "There was a problem with your submission", $message);
					$inside .= $this->buildOrderForm(&$prize, &$this->page->session->u, &$this->page->session->ui);	
				} else
				{
					// success
					$orderid =$this->processPrizeOrder($prize->id, $user->userid, $message); // TODO: make this function a bit faster by using whats stored in the session 
					if (!$orderid)
					{
						$inside .= $this->page->buildMessage('error', "There was a problem redeeming this prize", $message);
						
					} else
					{		

						
						$inside .= $this->page->buildMessage('success', "Order received", $message);

						
						require_once(PATH_FACEBOOK .'/pages/pageOrders.class.php');
						$orders = new pageOrders($this->page);
 
						
						$inside .='<div class="panel_1">'.		
							'<div class="panelBar clearfix">
								<h2>Order Details</h2>
								<div class="bar_link"><a href="?p=orders" onclick="setTeamTab(\'orders\'); return false;">See all</a></div>
								</div><!__end "panelBar"__>'.
								$orders->fetchOrderDetail($orderid)
							.'</div><!-- end panel_1 -->';
				
					}
				}
		
			}
			
		}
		
		$inside.='</div><!-- end left side --><div id="col_right">';
	
		$inside.=$this->teamObj->fetchSidePanel('challenges');
		
		$inside.='</div> <!-- end right side -->';
		
		//$inside.='<input type="hidden" id="pagingFunction" value="fetchChallenges">';		
	
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'');		
		return $code;
	}
	
	
	
	function buildOrderForm(&$prize,&$user,&$userinfo) 
	{

		
		
		//$code.="To redeem $prize->title at a cost of $prize->pointCost you will need to fill in the form below:<br>";
		
		$code.="<h3>We need to confirm your information before we can send you the goods!</h3>";
		//$code.="Needed fields: $prize->orderFieldsNeeded<br>";
		// need a form here of some kind
		
		$code .= '<fb:editor action="?p=redeem&step=submit" labelwidth="100">';

		$code.='<fb:editor-custom label="Name" name="name">'.$user->name.'</fb:editor-custom>';
		//$code .='<input type="hidden" name="name" value="'.$user->name.'"/>';
	  
	  
	   	//$code .= '<fb:editor-text label="Email address" name="email" value="'.$user->email.'"/>';
		$code.='<fb:editor-custom label="Email" name="email">'.$user->email.'</fb:editor-custom>';
		
/*
		"phone" => "VARCHAR(255) default ''",
		"address1" => "VARCHAR(255) default ''",		
		"address2" => "VARCHAR(255) default ''",
		"city" => "VARCHAR(255) default 'Unknown'",	
		"state" => "VARCHAR(255) default ''",
		"country" => "VARCHAR(255) default ''",
		"zip" => "VARCHAR(255) default ''",
	*/	
		       	
		if (preg_match("/address/i", $prize->orderFieldsNeeded) )
		{
			$code .= '<fb:editor-text label="Address" name="address1" value="'.$userinfo->address1.'" /> ';	
			$code .= '<fb:editor-text label="" 		  name="address2" value="'.$userinfo->address2.'" /> ';	

			$code .= '<fb:editor-text label="City" 	  name="city" value="'.$userinfo->city.'" /> ';	
			$code .= '<fb:editor-text label="State/Province" 		  name="state" value="'.$userinfo->state.'" /> ';
			$code .= '<fb:editor-text label="Zip"     name="zip" value="'.$userinfo->zip.'" /> ';
			
		}
		
		if (preg_match("/phone/i", $prize->orderFieldsNeeded) )
		{
			$code .= '<fb:editor-text label="Phone Number" name="phone" value="'.$userinfo->phone.'" /> ';	
		}
		
		$code .= '<input name="prizeid" type="hidden" value="'. $prize->id . '"/>'.
				 '<input name="pointcost" type="hidden" value="'. $prize->pointCost . '"/>';
		
		
		$code.='<fb:editor-buttonset>  
	           <fb:editor-button value="Place Order"/> <fb:editor-cancel href="?p=rewards"/>  
	           </fb:editor-buttonset>';
	
		$code .= '</fb:editor>';
		/*$code.='<form action="?p=redeem&step=submitted" method="post">'. 
				'<textarea name="orderformtext" cols="40" rows="5">'.
				'type stuff here'.
				'</textarea><br>'.
				'<input name="prizeid" type="hidden" value="'. $prize->id . '"/>'.
				'<input name="pointcost" type="hidden" value="'. $prize->pointCost . '"/>'.
		
				'<fb:submit>Order</fb:submit>'.
				'</form>';
*/
		
		//$code.='<br>';
		
		$code ='<div class="panel_1">'.		
				'<div class="panelBar clearfix">
					<h2>Confirm Information</h2>
					<!-- <div class="bar_link"><a href="#">I did this too!</a></div> -->
					</div><!__end "panelBar"__>'.
					$code
				.'</div><!-- end panel_1 -->';
		
		return $code;
	}		
	
	function validateOrderFields(&$prize, &$user, &$userinfo, &$message)
	{
			
		
		if (preg_match("/address/i", $prize->orderFieldsNeeded) )
		{
			if (!$userinfo->address1 <> '')
			{
				$message = 'Please enter your street address'; return false;
			}
						
			//	$userinfo->city <> '' &&
			if (!$this->validateZip($userinfo->zip, $userinfo->country))
			{ $message='Please enter a valid zip code'; return false; }

			
		}
		
		if (preg_match("/phone/i", $prize->orderFieldsNeeded) )
		{			
			if (!$this->validatePhone($userinfo->phone))
			{ $message='Please enter a valid 10 digit phone number';  return false; }
			
		}
		
		return true;
	}
	
	function validatePhone($phone)
	{
		/* fancier phone regex?
		 * ^(?:\([2-9]\d{2}\)\ ?|[2-9]\d{2}(?:\-?|\ ?|\.?))[2-9]\d{2}[- \.]?\d{4}$
		 */
	
		$phone = preg_replace('/[^0-9]/', '', $phone); # remove non-numbers

		if (preg_match('/^1?[0-9]{10}$/', $phone)) {
			return true;
		}
		
	}
	
	function validateZip($zip, $country)
	{
		/*
		 *   'Zip or Zip+4
                Public Const rgxZIP_US = "(?:\d{5}(?:-\d{4})?)"                

                'Canadian postal codes
                Public Const rgxZip_CA = "(?:[A-Z]\d[A-Z] \d[A-Z]\d)"
		 */
		
		switch ($country)
		{
			default: 
			case 'United States': $rgx="/^([0-9]{5})(-[0-9]{4})?$/i";break;
			case 'Canada': $rgx="/^([a-ceghj-npr-tv-z]){1}[0-9]{1}[a-ceghj-npr-tv-z]{1}[0-9]{1}[a-ceghj-npr-tv-z]{1}[0-9]{1}$/i"; break;
		}
		
		return preg_match($rgx, $zip);
		
	}
	
	
	function fetchSubmitted()
	{
		
		
		
		// render a submission status page, validate inputs and have the user retry if fields were missing
		$code.="Submitting....\n";
		
		$code .= 'POST: <pre>' .print_r( $_POST, true) .'</pre>';		
		
		// TODO: update user/user info records with order info collected
//		$this->db->setDebug(true);
		//$log = $this->app->getActivityLog();
		//$log->add($log->serialize(0, $this->app->session->userid, 'signup', 0, 0));
	
	/*	// try this:
		$lt = new LogTable($this->db);
		$logrow = $lt->getRowObject();
		$logrow->userid1 = $this->session->userid;
		$logrow->action='redeemed';
		
		$logrow->insert();
		*/
		$code .= 'LOG: <pre>' .print_r( $log, true) .'</pre>';		
		
		
		// user has submitted an order so lets record it!				
		
		$code .= $this->processPrizeOrder($_POST['prizeid'], $this->page->session->userid);
		$code .= 'See your <a href="?p=orders">Order History</a>.';
		$code .= 'Return to the <a href="?p=team">'.SITE_TEAM_TITLE.'</a>.';
		
		return $code;
	}

	// precheck its possible for user to even order this
	function checkOrderPossible($prize, $user, $userinfo, &$message)
	{
		require_once(PATH_CORE.'/classes/prizes.class.php');
		require_once(PATH_CORE . '/classes/orders.class.php');
		$orderTable 	= new OrderTable($this->db);
		$prizeTable	 	= new PrizeTable($this->db);
		
		$prizeid=$prize->id;
		$userid=$user->userid;
		
		if ($user->ncUid==0) 
		{
            $message = 'We do not have a record of you verifying your email address. 
            			Please look in your email and spam folder for a verification request link. 
            			If you can\'t find one, <a href="#" onclick="requestVerify();return false;">request another here</a>.';
            return false;
        }

       	if ($prize->isWeekly || $prize->isGrand)
		{
			// the only criterion we need to verify besides their order info is whether there is a wonPrize to match...
			
			if (!$this->userWonPrize($userid, $prizeid))
			{	 $message = "Sorry, you can't claim this prize because you didn't actually win it!"; return false; }

		} else
		{
			
			if ($userinfo->rxConsentForm==0 AND $userinfo->age>0 AND $userinfo->age<18 AND ENABLE_MINOR_CONSENT) 
			{	$message ="Sorry, we haven't received your consent form yet!"; return false; }
						
	    	if (!($user->cachedPointTotal >= $prize->pointCost))
			{	 $message = "Sorry, you don't have enough points to order this."; return false; }
	
			if ($prize->currentStock <= 0)
			{	$message = "Sorry, we don't have any more of these in stock right now."; return false; }
							
			if (!$prizeTable->userIsEligible($prize->eligibility, $user->eligibility))
			{	$message = "Sorry, you are not eligible to order this prize. "; return false; } 
				
			//if () //  TODO: now is between date start and end
			if (!(strtotime($prize->dateStart) < time() && time() < strtotime($prize->dateEnd) ) )
			{ 	 $message = "Sorry, this reward is not redeemable right now."; return false;}
	
	
			
						
		}
		
		// 0=unlimited
		if ($prize->userMaximum && $prize->userMaximum < $orderTable->getNumberUserOutstandingOrdersForPrize($prizeid, $userid))
		{ 	$message = "Sorry, you aren't allowed to order this prize again (per-user limit of {$prize->userMaximum})"; return false; }


		$maxOrdersPer24Hours = 3;
		if ($orderTable->getNumberUserOrdersLast24Hours($userid) >= $maxOrdersPer24Hours)
		{ 	$message = "Sorry, you aren't allowed to place another order right now, 
										you have already placed $maxOrdersPer24Hours orders in the last 24 hours."; 
			return false; }
		
		
        return true;
		
	}
	
	// TODO: refactor, move this to prizes.class.php
	function processPrizeOrder($prizeid, $userid, &$message)
	{
		require_once(PATH_CORE.'/classes/user.class.php');
		require_once(PATH_CORE.'/classes/prizes.class.php');
		require_once(PATH_CORE . '/classes/orders.class.php');
		$orderTable 	= new OrderTable($this->db);
		$userTable 		= new UserTable($this->db);
		$userInfoTable 	= new UserInfoTable($this->db);
		$prizeTable	 	= new PrizeTable($this->db);
		
		$user 		= $userTable->getRowObject();
		$userInfo 	= $userInfoTable->getRowObject();
		$prize 		= $prizeTable->getRowObject();
		$order 		= $orderTable->getRowObject();
		
		if (!$user->load($userid) || !$userInfo->load($userid) || !$prize->load($prizeid))
		{	 $message = "There was an error loading prize and/or user records."; return false; }
	
		$userInfoTable->updateUserCachedPointsAndChallenges($userid, $user, $userInfo); // slightly inefficient, calls load again		
				
	   	// final check, in case something else happened
		if (!$this->checkOrderPossible($prize,$user,$userinfo,$message))
		{
			return false;
		}
		// everythings ok:

		if (!($prize->isWeekly || $prize->isGrand))
		{
			$user->cachedPointTotal -= $prize->pointCost;
		}
		
		$prize->currentStock--;
			
		$order->userid = $user->userid;
		$order->prizeid = $prize->id;
		$order->pointCost = $prize->pointCost; // cache points spent in here for proper recordkeeping.
		$phpnow = time();
		$order->dateSubmitted = date('Y-m-d H:i:s', $phpnow);
		$order->status = 'submitted';
		
			
		if (!$order->insert())
		{ 	$message = 'Error submitting your order, please email support.'; return false; }
		$user->update();
		$prize->update();	
			
		$message .= 'Your order number is #'. $order->id . '.';
		
		// debatable -- should this show up in the log if its a weekly or grand prize?
		$log = $this->app->getActivityLog();
		$log->add($log->serialize(0, $this->page->session->userid, 'redeemed', $_POST['prizeid'], 0));
	
		
		return $order->id;
	}

	function userWonPrize($userid, $prizeid)
	{
		require_once (PATH_CORE .'/classes/log.class.php');
		$lt = new LogTable($this->db);
		$log = $lt->getRowObject();
		
		return $log->loadWhere("action='wonPrize' AND userid1=$userid AND itemid=$prizeid");
	
	}
	
	
}

?>