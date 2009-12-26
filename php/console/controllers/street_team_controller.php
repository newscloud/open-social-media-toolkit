<?php


Class Street_teamController extends AppController {
	var $name = 'Street_team';
	public function feature_panel() {
			$this->render('not_implemented');
	}


			/****************************************************************
			 *  Leaders section
			 ***************************************************************/
	public function licensePlateEligibleLeaders() {
				$week=12;
				//$week = $_GET['week'];
				
				$leaders=array();
				$leadersQ = $this->db->query(
				"SELECT name, cachedPointsEarned AS pointTotal, eligibility, fbId,User.userid 
					FROM User,UserInfo 
					WHERE UserInfo.userid=User.userid
						 AND User.isBlocked=0 AND cachedPointsEarned>0
						 AND User.userid NOT IN(select userid1 from Log where action='wonPrize' AND itemid=20)
						 ". //(SELECT userid FROM Orders WHERE prizeid=20); // 
					"ORDER BY eligibility, cachedPointsEarned DESC;"); 
				while ($row=mysql_fetch_assoc($leadersQ))
				{
					$leaders []= $row;
				}

				$this->set('leaders', $leaders);
				$this->set('week', $week);
				$this->set('action_title', "<h2>Members from up to the end of week $week that were not awarded license plate frames</h2>");

				$this->render('leaders');
	}

	public function leaders() {
				require_once(PATH_CONSOLE.'/helpers/application_helper.php');
		// ::TODO:: FIX THIS BULLSHIT
				//echo '<pre>'.print_r($this->db, true) .'</pre>';
				disp_header();
				
				echo '<script type="text/javascript">';
				require(PATH_CONSOLE.'/views/leaders.js');
				echo '</script>';
				
				$leadersQ = $this->db->query(
				"SELECT (DATE(weekOf)) AS week, WEEK(weekOf,1) as weekNum, name, pointTotal, eligibility, fbId,User.userid 
					FROM WeeklyScores, User,UserInfo 
					WHERE UserInfo.userid=WeeklyScores.userid 
						AND User.userid=WeeklyScores.userid AND User.isBlocked=0 AND pointTotal>0
					ORDER BY weekOf,eligibility, pointTotal DESC;");
				while ($row=mysql_fetch_assoc($leadersQ))
				{
					$leaders []= $row;
				}
				
				require(PATH_CONSOLE.'/views/street_team/leaders.php');
				$leaders=array();
				$leadersQ = $this->db->query(
				"SELECT name, cachedPointsEarned AS pointTotal, eligibility, fbId,User.userid 
					FROM User,UserInfo 
					WHERE UserInfo.userid=User.userid
						 AND User.isBlocked=0 AND cachedPointsEarned>0
					ORDER BY eligibility, cachedPointsEarned DESC LIMIT 250;"); // TODO: may shorten this after first week scoring
				while ($row=mysql_fetch_assoc($leadersQ))
				{
					$leaders []= $row;
				}

				//$this->set('leaders', $leaders);
				//$this->set('action_title', '<h2>All-time leaders</h2>');
				require(PATH_CONSOLE.'/views/street_team/leaders.php');
				
				disp_footer();
				exit;
	}

	public function assign_prize() {
				
				/*
				if ((isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id']))
					OR (isset($_GET['multiple']))) {
					$userid = $_GET['id'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- userid: $userid."));
					redirect(url_for('street_team', 'leaders'));
				}
				*/
				
				// process single userid or extract multiple userids
				if (!isset($_GET['multiple']))
				{
					$userid_array = array($userid);
				} else
				{
					$userid_array = array();
					foreach ($_POST AS $val => $id) 
					{
						if (stristr($val,'check_')!==false) 
						{
							$userid_array []=$id;
						}
						
					}
				}				
				// make unique				
				$userid_array = array_unique($userid_array);
				$userids = implode(' ,', $userid_array);
				
				$this->render();
		

	}

	public function award_prize() {
				if (isset($_POST['prizeid']) && preg_match('/^[0-9]+$/', $_POST['prizeid'])) {
					$prizeid = $_POST['prizeid'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- prizeid: $prizeid."));
					redirect(url_for('street_team', 'leaders'));
				}

				if (isset($_POST['userids']) /*&& preg_match('/^[0-9]+$/', $_POST['userids'])*/) {
					$userids = $_POST['userids'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- userid: $userid."));
					redirect(url_for('street_team', 'leaders'));
				}
				
				// actually update the database	
				
			
				
				disp_header();
				//echo $message;
					//	echo '<pre>' . print_r($_POST,true) . '</pre>';
		
				require('views/award_prize.php');
				disp_footer();
	
											
				
	}

	
			
			/****************************************************************
			 *  Challenges section
			 ***************************************************************/
	public function challenges() {
				$challenges = $this->db->load_all();
				$this->set('challenges', $challenges);
				$this->render();
	}

	public function view_challenge() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for($this->name, 'challenges'));
				}
				if (($challenge = $this->db->load($id))) {
					$this->set('challenge', $challenge);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for('street_team', 'challenges'));
				}
	}

	public function challenge_detail_report() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for($this->name, 'challenges'));
				}
				if (($challenge = $this->db->load($id))) {
					//$results = $this->db->query("SELECT * FROM ChallengesCompleted WHERE challengeid = {$challenge['id']}");
					//$results = $this->db->query("SELECT userid, dateSubmitted, concat(evidence) as evidence,status, pointsAwarded, concat(comments) as comments FROM ChallengesCompleted WHERE challengeid = {$challenge['id']}");
					$results = $this->db->query("select challengeid,Challenges.title,concat(evidence) as evidence,ChallengesCompleted.status,dateSubmitted,pointsAwarded,concat(comments) as comments,concat('http://host.newscloud.com/sites/climate/facebook/uploads/submissions/',localFilename) as imgPath,embedCode as videoPath from ((ChallengesCompleted LEFT JOIN Challenges ON Challenges.id=ChallengesCompleted.challengeid) LEFT JOIN Photos ON ChallengesCompleted.id=Photos.challengeCompletedId) LEFT JOIN Videos ON ChallengesCompleted.id=Videos.challengeCompletedId  WHERE ChallengesCompleted.challengeid = {$challenge['id']} ORDER BY dateSubmitted");
					$completed_challenges = array();
					while (($cc = mysql_fetch_assoc($results)) !== false)
						$completed_challenges[] = $cc;
					$this->set('challenge', $challenge);
					$this->set('completed_challenges', $completed_challenges);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for('street_team', 'challenges'));
				}
	}

	public function new_challenge() {
				$this->render();
	}

	public function create_challenge() {
				if (isset($_POST['challenge']['title'])) {
					//$id = $this->db->insert($_POST['challenge']);
					if (($id = $this->db->insert($_POST['challenge'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created challenge!'));
						redirect(url_for('street_team', 'view_challenge', $id));
					} else {
						set_flash(array('error' => 'Could not create your challenge! Please try again. '.$id));
						redirect(url_for('street_team', 'new_challenge'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'new_challenge'));
				}
	}

	public function modify_challenge() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for($this->name, 'challenges'));
				}
				if (($challenge = $this->db->load($id))) {
					$this->set('challenge', $challenge);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for('street_team', 'challenges'));
				}
	}

	public function update_challenge() {
				if (isset($_POST['challenge']['id']) && preg_match('/^[0-9]+$/', $_POST['challenge']['id'])) {
					$id = $_POST['challenge']['id'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- id: $id."));
					redirect(url_for('street_team', 'challenges'));
				}
				if (isset($_POST['challenge']['title'])) {
					
					// djm: take care of image upload /////////
					$newthumb =  
						handle_image_upload('thumbnail', "prize_{$_POST['challenge']['id']}_");
						
					if ($newthumb)
						$_POST['challenge']['thumbnail'] = $newthumb;
	
					///////////////////////////////////////////
					
					if (($result = $this->db->update($_POST['challenge'])) == 1) {
						set_flash(array('notice' => 'Successfully updated challenge.'));
						redirect(url_for('street_team', 'view_challenge', $id));
					} else {
						set_flash(array('error' => 'Could not update your challenge! Please try again. '.$result));
						redirect(url_for('street_team', 'modify_challenge', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'modify_challenge', $id));
				}
	}

	public function destroy_challenge() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current challenge for id: $id"));
					redirect(url_for($this->name, 'challenges'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted challenge.'));
					redirect(url_for('street_team', 'challenges'));
				} else {
					set_flash(array('error' => 'Could not delete challenge. Please try again. '.$result));
					redirect(url_for('street_team', 'challenges'));
				}
	}

			/****************************************************************
			 *  Prizes section
			 ***************************************************************/
	public function prizes() {
				$prizes = $this->db->load_all();
				$this->set('prizes', $prizes);
				$this->render();
	}

	public function view_prize() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current prize for id: $id"));
					redirect(url_for($this->name, 'prizes'));
				}
				if (($prize = $this->db->load($id))) {
					$this->set('prize', $prize);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current prize for id: $id"));
					redirect(url_for('street_team', 'prizes'));
				}
	}

	public function new_prize() {
				$this->render();
	}

	public function create_prize() {
				if (isset($_POST['prize']['title'])) {
					//$id = $this->db->insert($_POST['prize']);
					$newthumb = handle_image_upload('thumbnail', "prize_".time());
						
					if ($newthumb)
						$_POST['prize']['thumbnail'] = $newthumb;
					if (($id = $this->db->insert($_POST['prize'])) > 0) {
						set_flash(array('notice' => 'Successfully created prize!'));
						redirect(url_for('street_team', 'view_prize', $id));
					} else {
						set_flash(array('error' => 'Could not create your prize! Please try again. '.$id));
						redirect(url_for('street_team', 'new_prize'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'new_prize'));
				}
	}

	public function modify_prize() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current prize for id: $id"));
					redirect(url_for($this->name, 'prizes'));
				}
				if (($prize = $this->db->load($id))) {
					$this->set('prize', $prize);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current prize for id: $id"));
					redirect(url_for('street_team', 'prizes'));
				}
	}

	public function update_prize() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current prize for id: $id"));
					redirect(url_for($this->name, 'prizes'));
				}
				if (isset($_POST['prize']['title'])) {
					// djm: take care of image upload ///
					$newthumb =  
						handle_image_upload('thumbnail', "prize_{$_POST['prize']['id']}_");
						
					if ($newthumb)
						$_POST['prize']['thumbnail'] = $newthumb;
					/////////////////////////////////////
					if (($result = $this->db->update($_POST['prize'])) == 1) {
						set_flash(array('notice' => 'Successfully updated prize.'));
						redirect(url_for('street_team', 'view_prize', $id));
					} else {
						set_flash(array('error' => 'Could not update your prize! Please try again. '.$result));
						redirect(url_for('street_team', 'modify_prize', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'modify_prize', $id));
				}
	}

	public function destroy_prize() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current prize for id: $id"));
					redirect(url_for($this->name, 'prizes'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted prize.'));
					redirect(url_for('street_team', 'prizes'));
				} else {
					set_flash(array('error' => 'Could not delete prize. Please try again. '.$result));
					redirect(url_for('street_team', 'prizes'));
				}
	}

	public function winners() {
				$this->render('not_implemented');
	}

			/****************************************************************
			 *  Orders Section
			 ***************************************************************/
	public function orders() {
				$orders = $this->db->load_all();
				$this->set('orders', $orders);
				$this->render();
	}

	public function view_order() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current order for id: $id"));
					redirect(url_for($this->name, 'orders'));
				}
				if (($order = $this->db->load($id))) {
					$this->set('order', $order);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current order for id: $id"));
					redirect(url_for('street_team', 'orders'));
				}
	}

	public function new_order() {
				$this->render();
	}

	public function create_order() {
				if (isset($_POST['order']['userid'])) {
					//$id = $this->db->insert($_POST['order']);
					if (($id = $this->db->insert($_POST['order'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created order!'));
						redirect(url_for('street_team', 'view_order', $id));
					} else {
						set_flash(array('error' => 'Could not create your order! Please try again. '.$id));
						redirect(url_for('street_team', 'new_order'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'new_order'));
				}
	}

	public function modify_order() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current order for id: $id"));
					redirect(url_for($this->name, 'orders'));
				}
				if (($order = $this->db->load($id))) {
					$this->set('order', $order);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current order for id: $id"));
					redirect(url_for('street_team', 'orders'));
				}
	}

	public function update_order() {
				if (isset($_POST['order']['id']) && preg_match('/^[0-9]+$/', $_POST['order']['id'])) {
					$id = $_POST['order']['id'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- id: $id."));
					redirect(url_for('street_team', 'orders'));
				}
				if (isset($_POST['order']['userid'])) {
					if (($result = $this->db->update($_POST['order'])) == 1) {
						set_flash(array('notice' => 'Successfully updated order.'));
						redirect(url_for('street_team', 'view_order', $id));
					} else {
						set_flash(array('error' => 'Could not update your order! Please try again. '.$result));
						redirect(url_for('street_team', 'modify_order', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'modify_order', $id));
				}
	}

	public function destroy_order() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current order for id: $id"));
					redirect(url_for($this->name, 'orders'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted order.'));
					redirect(url_for('street_team', 'orders'));
				} else {
					set_flash(array('error' => 'Could not delete order. Please try again. '.$result));
					redirect(url_for('street_team', 'orders'));
				}
	}

			/****************************************************************
			 *  Completed Challenges Section
			 ***************************************************************/
	public function completed_challenges() {
				require_once(PATH_CONSOLE.'/helpers/application_helper.php');
				
				// major hacks 
				function isAutomatic($cc) { return $cc['evidence'] == 'Automatic!'; } // bit of a hack, depends on all auto challenges setting evidence to 'Automatic!'
				function isNotAutomatic($cc) { return !isAutomatic($cc); }
				function awaitingReview($cc) { return $cc['status']=='submitted' && isNotAutomatic($cc); }
				function isReviewed($cc) { return $cc['status']=='awarded' || $cc['status']=='rejected' || isAutomatic($cc); }
				
				//$cc_types = array(	'Submission' => array_filter($completed_challenges, 'isNotAutomatic'),
					//				'Automatic' => array_filter($completed_challenges, 'isAutomatic'));
				
				disp_header();
				// haaacks - should really join on Challenge type rather than rely on evidence='Automatic!'
				
				echo '<h1>Submission Challenges</h1>';
				$completed_challenges = $this->db->load_all("SELECT * FROM ChallengesCompleted WHERE status='submitted' AND evidence!='Automatic!' ORDER BY dateSubmitted DESC LIMIT 1000");
				require(PATH_CONSOLE.'/views/street_team/completed_challenges.php');
								
				echo '<h1>Auto Challenges</h1>';
				$completed_challenges = $this->db->load_all("SELECT * FROM ChallengesCompleted WHERE evidence='Automatic!' ORDER BY dateSubmitted DESC LIMIT 100");
				require(PATH_CONSOLE.'/views/street_team/completed_challenges.php');
				
				disp_footer();
				exit;
	}

	public function view_completed_challenge() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current completed challenge for id: $id"));
					redirect(url_for($this->name, 'completed_challenges'));
				}
				if (($completed_challenge = $this->db->load($id))) {
					$this->set('completed_challenge', $completed_challenge);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current completed_challenge for id: $id"));
					redirect(url_for('street_team', 'completed_challenges'));
				}
	}

	public function new_completed_challenge() {
				$this->render();
	}

	public function create_completed_challenge() {
				if (isset($_POST['completed_challenge']['userid'])) {
					//$id = $this->db->insert($_POST['completed_challenge']);
					if (($id = $this->db->insert($_POST['completed_challenge'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created completed_challenge!'));
						redirect(url_for('street_team', 'view_completed_challenge', $id));
					} else {
						set_flash(array('error' => 'Could not create your completed_challenge! Please try again. '.$id));
						redirect(url_for('street_team', 'new_completed_challenge'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'new_completed_challenge'));
				}
	}

	public function modify_completed_challenge() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current completed challenge for id: $id"));
					redirect(url_for($this->name, 'completed_challenges'));
				}
				if (($completed_challenge = $this->db->load($id))) {
					$this->set('completed_challenge', $completed_challenge);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current completed_challenge for id: $id"));
					redirect(url_for('street_team', 'completed_challenges'));
				}
	}

	public function update_completed_challenge() {
				if (isset($_POST['completed_challenge']['id']) && preg_match('/^[0-9]+$/', $_POST['completed_challenge']['id'])) {
					$id = $_POST['completed_challenge']['id'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- id: $id."));
					redirect(url_for('street_team', 'completed_challenges'));
				}
				if (isset($_POST['completed_challenge']['userid'])) {
					if (($result = $this->db->update($_POST['completed_challenge'])) == 1) {
						set_flash(array('notice' => 'Successfully updated completed_challenge.'));
						redirect(url_for('street_team', 'view_completed_challenge', $id));
					} else {
						set_flash(array('error' => 'Could not update your completed_challenge! Please try again. '.$result));
						redirect(url_for('street_team', 'modify_completed_challenge', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'modify_completed_challenge', $id));
				}
	}

	public function destroy_completed_challenge() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current completed challenge for id: $id"));
					redirect(url_for($this->name, 'completed_challenges'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted completed_challenge.'));
					redirect(url_for('street_team', 'completed_challenges'));
				} else {
					set_flash(array('error' => 'Could not delete completed_challenge. Please try again. '.$result));
					redirect(url_for('street_team', 'completed_challenges'));
				}
	}

			
	public function approve_completed_challenge() { // slight hack, since this now handles rejection as well
				if (isset($_POST['completed_challenge']['id']) && preg_match('/^[0-9]+$/', $_POST['completed_challenge']['id'])) 
				{
					$id = $_POST['completed_challenge']['id'];
				} else 
				{
					set_flash(array('error' => "Invalid $group -- $action -- id: $id."));
					redirect(url_for('street_team', 'completed_challenges'));
				}
				
				if ($_POST['reject']) // user pressed reject button
				{
					/*
					 * 
					//$_POST['completed_challenge[status]']='rejected'; // set status to rejected
					// update to set comment text
					if (($result = $this->db->update($_POST['completed_challenge'])) == 1) 
					{
		
					 */
					
					// grrr russell
					require_once( PATH_CORE .'/classes/challenges.class.php');
					$cct = new ChallengeCompletedTable();
					$cc = $cct->getRowObject();
					if ($cc->load($id))
					{
						$cc->status='rejected';
						$cc->update();
						
						set_flash(array('notice' => "Challenge submission $id rejected."));
						redirect(url_for('street_team', 'view_completed_challenge', $id));
											
					} else {
						set_flash(array('error' => 'Could not update your completed_challenge! Please try again. '.$result));
						redirect(url_for('street_team', 'modify_completed_challenge', $id));
					}
					
				} elseif (isset($_POST['pointsAwarded'])) // user presumably pressed approve and assigned nonzero points
				{
					
					// update to set comment text
					if (($result = $this->db->update($_POST['completed_challenge'])) == 1) 
					{

					} else {
						set_flash(array('error' => 'Could not update your completed_challenge! Please try again. '.$result));
						redirect(url_for('street_team', 'modify_completed_challenge', $id));
					}
		
					
					
					// djm: 
					require_once( PATH_CORE .'/classes/challenges.class.php');
					$cct = new ChallengeCompletedTable();
					$cct->approveChallenge($id, $_POST['pointsAwarded'], &$code);
					/*
					if (($result = $this->db->update($_POST['completed_challenge'])) == 1) 
					{
						set_flash(array('notice' => 'Successfully updated completed_challenge.'));
						redirect(url_for('street_team', 'view_completed_challenge', $id));
					} else 
					{
						set_flash(array('error' => 'Could not update your completed_challenge! Please try again. '.$result));
						redirect(url_for('street_team', 'review_completed_challenge', $id));
					}*/
					set_flash(array('notice' => $code));
					redirect(url_for('street_team', 'view_completed_challenge', $id));
					
				} else 
				{
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('street_team', 'view_completed_challenge', $id));
				}
	}

	/****************************************************************
	 *  MISC Helper Tasks
	 ***************************************************************/
	public function updateScores() {
		global $init;
		require_once (PATH_CORE. '/classes/teamBackend.class.php');
		$teamObj = new teamBackend();
		$teamObj->updateScores();		
		redirect(url_for('street_team', 'index'));
	}

	public function prepareContest() {
		global $init;
		require_once (PATH_CORE. '/classes/teamBackend.class.php');
		$teamObj = new teamBackend();
		$teamObj->prepareContest();
		redirect(url_for('street_team', 'index'));
	}

	public function resetContestAdmins() {
		global $init;
		require_once (PATH_CORE. '/classes/teamBackend.class.php');
		$teamObj = new teamBackend();
		$teamObj->testResetAdmins();
		redirect(url_for('street_team', 'index'));
	}
	
	public function cleanupOrphans() {
		global $init;
		require_once (PATH_CORE. '/classes/teamBackend.class.php');
		$teamObj = new teamBackend();
		$teamObj->cleanupOrphanedUsers();
		redirect(url_for('street_team', 'index'));
	}

	/****************************************************************
	 *  General Main section
	 ***************************************************************/
	public function index() {
				disp_header();
				echo "<h1>Index page for Street Team.</h1>";
				disp_footer();
	}

}


?>
