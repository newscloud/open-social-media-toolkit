<?php

Class MembersController extends AppController {
	var $name = 'Members';
			/****************************************************************
			 *  Manage Members section
			 ***************************************************************/
	public function members() {
				$members = $this->db->load_all();
				$this->set('members', $members);
				$this->render();
	}

	public function view_member() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				$sql = "SELECT User.*, UserInfo.* FROM User, UserInfo WHERE User.userid = $id AND User.userid = UserInfo.userid";
				if (($member_res = $this->db->query($sql))) {
					$member = mysql_fetch_assoc($member_res);
					$this->set('member', $member);

					$comment_sql = "select title,Content.siteContentId,concat(comments) as comments,Comments.date FROM Comments LEFT JOIN Content ON Content.siteContentId=Comments.SiteContentId WHERE Comments.userid={$member['userid']}";
					$comments_res = $this->db->query($comment_sql);
					$comments = array();
					while (($comment = mysql_fetch_assoc($comments_res)) !== false)
						$comments[] = $comment;
					$this->set('comments', $comments);

					$challenges_sql = "select challengeid,title,concat(evidence) as evidence,ChallengesCompleted.status,dateSubmitted,pointsAwarded,concat(comments) as comments from ChallengesCompleted LEFT JOIN Challenges ON Challenges.id=ChallengesCompleted.challengeid WHERE userid = {$member['userid']} ORDER BY dateSubmitted";
					$challenges_res = $this->db->query($challenges_sql);
					$challenges = array();
					while (($challenge = mysql_fetch_assoc($challenges_res)) !== false)
						$challenges[] = $challenge;
					$this->set('challenges', $challenges);

					$blogs_sql = "select * from Content where isBlogEntry=1 and userid = {$member['userid']}";
					$blogs_res = $this->db->query($blogs_sql);
					$blogs = array();
					while (($blog = mysql_fetch_assoc($blogs_res)) !== false)
						$blogs[] = $blog;
					$this->set('blogs', $blogs);

					$stories_sql = "select * from Content where isBlogEntry=0 and userid = {$member['userid']}";
					$stories_res = $this->db->query($stories_sql);
					$stories = array();
					while (($story = mysql_fetch_assoc($stories_res)) !== false)
						$stories[] = $story;
					$this->set('stories', $stories);

					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for('members', 'members'));
				}
	}

	public function new_member() {
				$this->render();
	}

	public function create_member() {
				if (isset($_POST['member']['name'])) {
					if ($_POST['member']['dateRegistered'] == '')
						$_POST['member']['dateRegistered'] = date("Y-m-d H:i:s", time());
					//$id = $this->db->insert($_POST['member']);
					if (($id = $this->db->insert($_POST['member'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created member!'));
						redirect(url_for('members', 'view_member', $id));
					} else {
						set_flash(array('error' => 'Could not create your member! Please try again. '.$id));
						redirect(url_for('members', 'new_member'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('members', 'new_member'));
				}
	}

			
			
	public function modify_member() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				if (($member = $this->db->load($id))) {
					$this->set('member', $member);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for('members', 'members'));
				}
	}

	public function update_member() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				if (isset($_POST['member']['name'])) {
					if (($result = $this->db->update($_POST['member'])) == 1) {
						set_flash(array('notice' => 'Successfully updated member.'));
						redirect(url_for('members', 'view_member', $id));
					} else {
						set_flash(array('error' => 'Could not update your member! Please try again. '.$result));
						redirect(url_for('members', 'modify_member', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('members', 'modify_member', $id));
				}
	}

	public function block_member() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				if (($result = $this->db->block($id, 1)) == 1) {
					set_flash(array('notice' => 'Successfully blocked member.'));
					redirect(url_for('members', 'members'));
				} else {
					set_flash(array('error' => 'Could not block member. Please try again. '.$result));
					redirect(url_for('members', 'members'));
				}
	}

	public function unblock_member() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				if (($result = $this->db->block($id, 0)) == 1) {
					set_flash(array('notice' => 'Successfully unblocked member.'));
					redirect(url_for('members', 'members'));
				} else {
					set_flash(array('error' => 'Could not unblock member. Please try again. '.$result));
					redirect(url_for('members', 'members'));
				}
	}

	public function destroy_member() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted member.'));
					redirect(url_for('members', 'members'));
				} else {
					set_flash(array('error' => 'Could not delete member. Please try again. '.$result));
					redirect(url_for('members', 'members'));
				}
	}

	public function authorize_editing() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				$sql = "SELECT User.*, UserInfo.* FROM User, UserInfo WHERE User.userid = $id AND User.userid = UserInfo.userid";
				if (($member_res = $this->db->query($sql))) {
					$member = mysql_fetch_assoc($member_res);
				//if (($member = $this->db->load($id))) {
					//require('views/modify_member.php');
					require_once(PATH_CORE . '/classes/dynamicTemplate.class.php');
					require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');						
					$dynTemp = dynamicTemplate::getInstance();
					$authorized = $dynTemp->authorizeFbIdForEditing($member['fbId']);
					set_flash(array('notice' => "Authorizing $fbId...authorized fbIds now $authorized"));
					redirect(url_for('members', 'members'));
				} else {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for('members', 'members'));
				}
	}

	public function show_friend_invite_credits() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member for id: $id"));
					redirect(url_for($this->name, 'members'));
				}
				
				$query = 
				"SELECT name, User.userid, dateRegistered, WEEK(dateRegistered,1) AS week, email, fbId 
				FROM User,UserInfo,Log 
				WHERE User.userid=UserInfo.userid 
					AND Log.userid1=$id 
					AND Log.action='friendSignup' 
					AND User.userid=Log.userid2
				ORDER BY dateRegistered DESC;";
				
				$res = $this->db->query($query);
				while ($row=mysql_fetch_assoc($res))
				{
					$friends []= $row;										
				}

				$this->set('friends', $friends);
								
				$this->render();
				
	
	}

			
			/****************************************************************
			 *  Manage Outbound Messages section
			 ***************************************************************/
	public function outboundmessages() {
				$outboundmessages = $this->db->load_all();
				$this->set('outboundmessages', $outboundmessages);
				$this->render();
	}

	public function view_outboundmessage() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (($outboundmessage = $this->db->load($id))) {
					$this->set('outboundmessage', $outboundmessage);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current outboundmessage for id: $id"));
					redirect(url_for('members', 'outboundmessages'));
				}
	}

	public function new_outboundmessage() {
				$this->render();
	}

	public function create_outboundmessage() {
				if (isset($_POST['outboundmessage']['msgBody'])) {
					if ($_POST['outboundmessage']['t'] == '')
						$_POST['outboundmessage']['t'] = date("Y-m-d H:i:s", time());
					$userGroup = '';
					switch($_POST['outboundmessage']['userGroup']) {
						case 'all':
							$userGroup = '';
						break;
						case 'members':
							$userGroup = 'User.isMember = 1';
						break;
						case 'nonmembers':
							$userGroup = 'User.isMember = 0';
						break;
						case 'teampotential':
							$userGroup = "User.isMember = 0 AND UserInfo.age<=25 AND UserInfo.age>=16 AND User.eligibility<>'ineligible'";
						break;
						case 'team':
							$userGroup = "User.eligibility='team'";
						break;
						case 'general':
							$userGroup = "User.eligibility='general'";
						break;
						case 'admin':
							$userGroup = 'User.isAdmin = 1';
						break;
						default:
							$userGroup = '';
						break;
					}
					$_POST['outboundmessage']['userGroup'] = $userGroup;
					//$id = $this->db->insert($_POST['outboundmessage']);
					if (($id = $this->db->insert($_POST['outboundmessage'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created outboundmessage!'));
						redirect(url_for('members', 'view_outboundmessage', $id));
					} else {
						set_flash(array('error' => 'Could not create your outboundmessage! Please try again. '.$id));
						redirect(url_for('members', 'new_outboundmessage'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('members', 'new_outboundmessage'));
				}
	}

	public function modify_outboundmessage() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (($outboundmessage = $this->db->load($id))) {
					$this->set('outboundmessage', $outboundmessage);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current outboundmessage for id: $id"));
					redirect(url_for('members', 'outboundmessages'));
				}
	}

	public function update_outboundmessage() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (isset($_POST['outboundmessage']['msgBody'])) {
					$userGroup = '';
					switch($_POST['outboundmessage']['userGroup']) {
						case 'all':
							$userGroup = '';
						break;
						case 'members':
							$userGroup = 'User.isMember = 1';
						break;
						case 'nonmembers':
							$userGroup = 'User.isMember = 0';
						break;
						case 'teampotential':
							$userGroup = "User.isMember = 0 AND UserInfo.age<=25 AND UserInfo.age>=16 AND User.eligibility<>'ineligible'";
						break;
						case 'team':
							$userGroup = "User.eligibility='team'";
						break;
						case 'general':
							$userGroup = "User.eligibility='general'";
						break;
						case 'admin':
							$userGroup = 'User.isAdmin = 1';
						break;
						default:
							$userGroup = '';
						break;
					}
					$_POST['outboundmessage']['userGroup'] = $userGroup;
					if (($result = $this->db->update($_POST['outboundmessage'])) == 1) {
						set_flash(array('notice' => 'Successfully updated outboundmessage.'));
						redirect(url_for('members', 'view_outboundmessage', $id));
					} else {
						set_flash(array('error' => 'Could not update your outboundmessage! Please try again. '.$result));
						redirect(url_for('members', 'modify_outboundmessage', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('members', 'modify_outboundmessage', $id));
				}
	}

	public function send_outboundmessage() {
				global $init;
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (isset($_GET['preview']) && $_GET['preview'] == 'true') {
					$preview = true;
				} else {
					$preview = false;
				}
				if (($outboundmessage = $this->db->load($id))) {
					$extraWhere = '';
					$optInWhere = '';
					$userGroupWhere = '';
					if ($outboundmessage['msgType'] == 'announce') {
						$optInWhere = ' User.optInEmail = 1 AND ';
					}

					if ($outboundmessage['userGroup'] != '' || $preview) {
						if (!$preview) {
							$userGroupWhere = ' '.$outboundmessage['userGroup'].' AND ';
						} else {
							$userGroupWhere = ' User.isAdmin = 1 AND ';
						}
					}

					if ($outboundmessage['status'] == 'hold') {
						set_flash(array('error' => "This message is marked as 'hold' and cannot be sent. Change the status of this message and try again."));
						redirect(url_for('members', 'outboundmessages'));
						exit;
					} else if ($outboundmessage['status'] == 'sent') {
						set_flash(array('error' => "This message has already been sent and cannot be resent."));
						redirect(url_for('members', 'outboundmessages'));
						exit;
					} else if ($outboundmessage['status'] == 'incomplete') {
						$received = $outboundmessage['usersReceived'];
						if ($received != '')
							$extraWhere = " AND UserInfo.fbId NOT IN ($received)";
					} else {
					}
					include_once PATH_FACEBOOK.'/lib/facebook.php';
					$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
					//$usersResult = $this->db->query("SELECT User.userid, fbId FROM User LEFT JOIN UserInfo ON User.userid = UserInfo.userid WHERE optInEmail = 1 $extraWhere");
					$usersResult = $this->db->query("SELECT User.userid, UserInfo.fbId FROM User, UserInfo WHERE $optInWhere $userGroupWhere User.userid = UserInfo.userid $extraWhere");
					$users = '';
					while (($row = mysql_fetch_row($usersResult)) !== false)
						$users .= "{$row[1]},";
					$users = substr($users, 0, -1);
					$usersArr = array();
					$totalUsers = 0;
					if (preg_match('/^[0-9]+/', $users)) {
						//$totalUsers = substr_count($users, ',') + 1;
						$users = preg_replace('/,{2,}/', ',', $users);
						$usersArr = split(',', $users);
						$totalUsers = count($usersArr);
					} else {
						set_flash(array('error' => "No current opted in users to send emails to."));
						redirect(url_for('members', 'outboundmessages'));
						exit;
					}
					$userCount = 0;
					$totalSent = 0;
					$usersReceived = '';
					while ($userCount < $totalUsers) {
						$sendCount = 0;
						$currEmailUsers = '';
						$emailsLeft = min(99, $totalUsers - $totalSent);
						while ($sendCount++ < $emailsLeft)
							if (isset($usersArr[$userCount]) && $usersArr[$userCount] != '')
								$currEmailUsers .= $usersArr[$userCount++].',';
						$currEmailUsers = substr($currEmailUsers, 0, -1);
						$msg = '';
						ob_start();
							require(PATH_CONSOLE.'/views/members/send_outboundmessage.php');
							$msg = ob_get_contents();
						ob_end_clean();
						if ($outboundmessage['msgType'] == 'announce') {
							$result = $facebook->api_client->notifications_sendEmail($currEmailUsers, $outboundmessage['subject'], '', $msg);
						} else if ($outboundmessage['msgType'] == 'notification') {
							$result = $facebook->api_client->notifications_send($currEmailUsers, $msg, 'app_to_user');
						} else {
							set_flash(array('error' => 'Invalid message type!'));
							redirect(url_for('members', 'outboundmessages'));
							exit;
						}
						if (preg_match('/^[0-9]+/', $result)) {
							$usersReceived .= $result.',';
							$totalSent += substr_count($result, ',') + 1;
							set_flash(array('notice' => "Successfully sent your outbound message to $totalSent users out of $totalUsers total."));
						} else {
							set_flash(array('error' => "Failed to send your outbound message: $result"));
							break;
						}
					}
					$usersReceived = substr($usersReceived, 0, -1);
					// Update stats
					if ($totalSent == $totalUsers) {
						if (!$preview)
							$status = 'sent';
						else
							$status = 'pending';
					} else {
						set_flash(array('error' => "Incomplete notifications, only $totalSent out of $totalUsers messages were sent!"));
						if (!$preview)
							$status = 'incomplete';
						else
							$status = 'pending';
					}
					$this->db->query("UPDATE OutboundMessages set usersReceived = '$usersReceived', numUsersReceived = '$totalSent', numUsersExpected = '$totalUsers', status = '$status' WHERE id = '{$outboundmessage['id']}'");
					redirect(url_for('members', 'outboundmessages'));
				} else {
					set_flash(array('error' => "Sorry no current outboundmessage for id: $id"));
					redirect(url_for('members', 'outboundmessages'));
				}
	}

	public function block_outboundmessage() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (($result = $this->db->block($id, 1)) == 1) {
					set_flash(array('notice' => 'Successfully blocked outboundmessage.'));
					redirect(url_for('members', 'outboundmessages'));
				} else {
					set_flash(array('error' => 'Could not block outboundmessage. Please try again. '.$result));
					redirect(url_for('members', 'outboundmessages'));
				}
	}

	public function unblock_outboundmessage() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (($result = $this->db->block($id, 0)) == 1) {
					set_flash(array('notice' => 'Successfully unblocked outboundmessage.'));
					redirect(url_for('members', 'outboundmessages'));
				} else {
					set_flash(array('error' => 'Could not unblock outboundmessage. Please try again. '.$result));
					redirect(url_for('members', 'outboundmessages'));
				}
	}

	public function destroy_outboundmessage() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current outbound message for id: $id"));
					redirect(url_for($this->name, 'outboundmesages'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted outboundmessage.'));
					redirect(url_for('members', 'outboundmessages'));
				} else {
					set_flash(array('error' => 'Could not delete outboundmessage. Please try again. '.$result));
					redirect(url_for('members', 'outboundmessages'));
				}
	}

			/****************************************************************
			 *  Manage Member Emails section
			 ***************************************************************/
	public function member_emails() {
				$new_contact_emails = $this->db->load_all();
				$this->set('new_contact_emails', $new_contact_emails);
				$this->render();
	}

	public function clean_up_member_emails() {
				$result = $this->db->query("TRUNCATE ContactEmails");
				if (preg_match('/query/i', $result))
					set_flash(array('error' => $result));
				else if (preg_match('/^[0-9]+$/', $result))
					set_flash(array('notice' => 'Successfully deleted all member contact emails.'));
				else
					set_flash(array('notice' => $result));
				redirect(url_for('members', 'member_emails'));
	}

	public function view_member_email() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current member email for id: $id"));
					redirect(url_for($this->name, 'member_emails'));
				}
				if (($member_email = $this->db->load($id))) {
					$this->set('member_email', $member_email);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current member_email for id: $id"));
					redirect(url_for('members', 'member_emails'));
				}
	}

			/****************************************************************
			 *  Manage Forum Topics section
			 ***************************************************************/
	public function forumtopics() {
				$forumtopics = $this->db->load_all();
				$this->set('forumtopics', $forumtopics);
				$this->render();
	}

	public function view_forumtopics() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current forumtopic for id: $id"));
					redirect(url_for($this->name, 'forumtopics'));
				}
				if (($forumtopic = $this->db->load($id))) {
					$this->set('forumtopic', $forumtopic);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current forumtopic for id: $id"));
					redirect(url_for('members', 'forumtopics'));
				}
	}

	public function new_forumtopic() {
				$this->render();
	}

	public function create_forumtopic() {
				if (isset($_POST['forumtopic']['title'])) {
					if ($_POST['forumtopic']['lastChanged'] == '')
						$_POST['forumtopic']['lastChanged'] = date("Y-m-d H:i:s", time());
					if (($id = $this->db->insert($_POST['forumtopic'])) > 0) {
						set_flash(array('notice' => 'Successfully created forumtopic!'));
						redirect(url_for('members', 'forumtopics', $id));
					} else {
						set_flash(array('error' => 'Could not create your forumtopic! Please try again. '.$id));
						redirect(url_for('members', 'new_forumtopic'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('members', 'new_forumtopic'));
				}
	}

	public function modify_forumtopic() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current forumtopic for id: $id"));
					redirect(url_for($this->name, 'forumtopics'));
				}
				if (($forumtopic = $this->db->load($id))) {
					$this->set('forumtopic', $forumtopic);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current forumtopic for id: $id"));
					redirect(url_for('members', 'forumtopics'));
				}
	}

	public function update_forumtopic() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current forumtopic for id: $id"));
					redirect(url_for($this->name, 'forumtopics'));
				}
				if (isset($_POST['forumtopic']['title'])) {
					if (($result = $this->db->update($_POST['forumtopic'])) == 1) {
						set_flash(array('notice' => 'Successfully updated forumtopic.'));
						redirect(url_for('members', 'forumtopics', $id));
					} else {
						set_flash(array('error' => 'Could not update your forumtopic! Please try again. '.$result));
						redirect(url_for('members', 'modify_forumtopic', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('members', 'modify_forumtopic', $id));
				}
	}

	public function destroy_forumtopic() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current forumtopic for id: $id"));
					redirect(url_for($this->name, 'forumtopics'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted forumtopic.'));
					redirect(url_for('members', 'forumtopics'));
				} else {
					set_flash(array('error' => 'Could not delete forumtopic. Please try again. '.$result));
					redirect(url_for('members', 'forumtopics'));
				}
	}


	public function index() {
				$this->render();
	}

}


?>
