<?php
class pageOrders
{

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	var $templateObj;
		
	function __construct(&$page) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
	}

	
	function fetch($mode='fullPage') 
	{
		// build the orders page
		if (isset($_GET['currentPage']))
			$currentPage=$_GET['currentPage'];
		else
			$currentPage=1;			
			
		if (isset($_GET['id']))
			$id = $_GET['id'];
		else
			$id = null;
		
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$this->teamObj=new actionTeam($this->page);
		$tabs=$this->teamObj->buildSubNav('rewards');
			 
						
		if ($id) $inside.=$this->fetchOrderDetail($id);		
		$inside.=$this->fetchOrderSummaryBox();
		
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';		
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'fetchRewardsPage');		
		return $code;
	}
	
	function fetchOrderDetail($id)
	{
		
		require_once(PATH_CORE.'/classes/orders.class.php');		
		require_once(PATH_CORE.'/classes/prizes.class.php');		
		$orderTable = new OrderTable($this->db);
		$order = $orderTable->getRowObject();
		$prizeTable = new PrizeTable($this->db);
		$prize = $prizeTable->getRowObject();
		
		if ($order->load($id) && $prize->load($order->prizeid) && $order->userid==$this->page->session->u->userid)
		{

			switch ($order->status)
			{	
				default:
				case 'refunded': 	$tablecode .= '<tr><td>refunded</td><td>'.$order->dateRefunded.'</td></tr>';			
				case 'canceled': 	$tablecode .= '<tr><td>canceled</td><td>'.$order->dateCanceled.'</td></tr>';			
				case 'shipped': 	$tablecode .= '<tr><td>shipped</td><td>'.$order->dateShipped.'</td></tr>';			
				case 'approved': 	$tablecode .= '<tr><td>approved</td><td>'.$order->dateApproved.'</td></tr>';			
				case 'submitted': 	$tablecode .= '<tr><td>submitted</td><td>'.$order->dateSubmitted.'</td></tr>';			
			}
			$code = '
			<div id="readStoryList">
			  <div class="panel_block">    
			    <div class="thumb">'.template::buildLinkedRewardPic($prize->id, $prize->thumbnail, 180).'</div>
			    <div class="storyBlockWrap clearfix">
			      <p class="storyHead">Order #'.$id . ': '. template::buildRewardLink($prize->title, $prize->id).'</p>
					    <div class="storyBlockMeta">
					    	<p class="pointValue">Redeemed '.$order->pointCost.' <span class="pts">pts</span></p>
					    			     	
			     	     </div><!--end "storyBlockMeta"-->'
			     	     .'<p>Reviewed by: '. $order->reviewedBy .'</p>' // TODO: make this nice!      	    
			     	     
			     	     .'<!--<p class="storyCaption">--><p>'.
							'<table>
								<tr><th>Status</th><th>Date</th></tr>'
								.$tablecode.
							'</table>'
						.'</p>'
			    .'</div><!--end "storyBlockWrap"-->
						    
			    
			  </div><!--end "panel_block"-->
			</div><!--end "readStoryList"-->
			';
			
			
			
		} else
		{
			$code .= $this->page->buildMessage('error',
			"There was a problem retreiving your order record", 
			"Invalid order id=$id, bad prizeid=$prize->id, or unauthorized userid=$userid");
			
		}
		$code = '<div class="">'.$code.'</div>';
		//$code .= 'TODO: We did this panel goes here:';		
		return $code;
		
	}
		
	function fetchOrderSummaryBox()
	{
		require_once (PATH_FACEBOOK .'/pages/pageProfile.class.php');
		$pageProfile = new pageProfile($this->page);
		$pageProfile->isProfileOwner = true; // hack, otherwise we wont see the order list
		return $pageProfile->fetchOrderList();
		
	}

	function fetchOrderList($userid=0,$currentPage=1)
	{
		if (!$userid)
		{
			return "No valid user\n";
		}
		
		
		// to do - take out rows per page
		$rowsPerPage=10;
		// userid is passed in because there is no session when refreshed with Ajax
		$code='';
		$startRow=($currentPage-1)*$rowsPerPage; // replace rows per page
		/*$challengeList=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS * 
				FROM Orders ORDER BY dateSubmitted ASC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
		*/
		$challengeList=$this->templateObj->db->query(
		"SELECT SQL_CALC_FOUND_ROWS 
				Orders.dateSubmitted,
				Prizes.title,
				Orders.prizeid,  
				Orders.id AS orderid 
				FROM Orders,Prizes WHERE Orders.prizeid=Prizes.id AND Orders.userid=$userid 
				ORDER BY Orders.dateSubmitted ASC LIMIT $startRow,".$rowsPerPage.";"); // $this->page->rowsPerPage
				
				
				$code.='<div>';
		// to do - later we'll move these template defs
		if ($this->templateObj->db->countQ($challengeList)>0) 
		{
			$listTemplate='<ol>{items}</ol>';
		/*	$itemTemplate='<p>
				<a href="?p=prizeInfo&id={Prizes.id}">
				#{Orders.id} submitted on {Orders.dateSubmitted} for {Prizes.title} - 
				<a href="?p=orderInfo&id={Orders.id}">details</a></p>'; /*{pointCost}*/
			$itemTemplate='<p>
				Order #{orderid} submitted on {dateSubmitted} for  
				<a href="?p=prizeInfo&id={prizeid}">{title}</a> - 
				<a href="?p=orderInfo&id={orderid}">details</a></p>'; 
		
			$rowTotal=$this->templateObj->db->countFoundRows();
			$pagingHTML=$this->page->paging($currentPage,$rowTotal,$rowsPerPage,'?userid='.$userid.'&p=orders&currentPage='); // later put back page->rowsPerPage			
			// $this->templateObj->db->setTemplateCallback('comments', array($this, 'decodeComment'), 'comments');
			//$code.=$this->templateObj->mergeTemplate($this->templateObj->templates['commentList'],$this->templateObj->templates['commentItem']);
			$code.=$this->templateObj->mergeTemplate($listTemplate,$itemTemplate);
		} else {
			$code.='You have not placed any orders.';
		}			
		$code.=$pagingHTML;
		return $code;
	}		

		
}

?>
