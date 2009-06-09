<?php
// Load session, database and global functions
require('global.php');

// TEST

// Get Action and Group GET variables
// Group simply corresponds to the tab from the console menu
//  -this is to clean up the controller admin and keep them separated
// Action is the desired function to be performed
if (isset($_GET['group']) && $_GET['group'] != '')
	$group = $_GET['group'];
else
	$group = 'main';

if (isset($_GET['action']) && $_GET['action'] != '')
	$action = $_GET['action'];
else
	$action = 'index';

$group = strtolower($group);
$action = strtolower($action);

// This will setup the appropriate db class for you group/admin
$db = init_db($group, $action);

// Array containing messages
// Populate $flash['notice'] to display notices such as successfully saving a change
// Or populate $flash['error'] to display error messages
// Afterwards run set_flash($flash) to save the data
$flash = array();
$flash['notice'] = '';
$flash['error'] = '';
set_flash($flash);


// Utility functions
// Set the Title variable for the page, override this in action function
// to change the title for the given page. This is used in header.php
 $title = "Newscloud Management Console";
function disp_header($title = "Newscloud Management Console", $action = 'index') {
	global $init;
	require_once (PATH_PHP.'/classes/page.class.php');
	$page=new XHTMLpage();
	$page->pkgScripts(CACHE_PREFIX.'nrConsole',array(PATH_PHP_SCRIPTS.'/template.js'));
	//$template_src = '<script src="http://hotdish.newsreel.org/?p=cache&type=js&cf=template.js&v=1.002"></script>';
	//$page->addScript(PATH_PHP_SCRIPTS.'/template.js');

	// hack: djm - dont know correct way to incorporate this
	//$page->addScript('http://ajax.googleapis.com/ajax/libs/prototype/1.6.0.2/prototype.js');
			
	$templateBuilder = false;
	if ($action == 'template_builder')
		$templateBuilder = true;

	$flash = get_flash();
	include_once('header.php');
}

function disp_footer($action = '') {
	$templateBuilder = false;
	if ($action == 'template_builder')
		$templateBuilder = true;

	require_once('footer.php');
}

// Load Dashboard
// This will load up the various info for the dashboard
function load_dashboard($db) {
	$data = array();
	$data['new_contact_emails'] = $db->load_all("SELECT * FROM ContactEmails WHERE is_read = 0 ORDER BY date DESC");

	return $data;
}

/*
// HAAAXXX
define("URL_BASE","http://callback.newsreel.org/sites/climate/facebook");

// TODO: problem: management console needs these as well
define('URL_UPLOADS', URL_BASE.'/uploads');
define('URL_THUMBNAILS', URL_UPLOADS.'/images');
define('URL_SUBMITTED_IMAGES', URL_UPLOADS.'/submissions');

define('PATH_UPLOAD_IMAGES', PATH_SITE.'/../facebook/uploads/images/'); // TODO: FIX THIS HACK	
*/




function handle_image_upload($fieldname, $prefix)
{			
	
	//echo '<pre> '. print_r($_POST, true) . '</pre>';
	//echo '<pre> '. print_r($_FILES, true) . '</pre>';
	if (is_uploaded_file($_FILES[$fieldname]['tmp_name']))
	{
		//echo 'uploaded temp: ' .$_FILES[$fieldname]['tmp_name'];
	   
		$uploaddir = PATH_UPLOAD_IMAGES;
		$filename="{$prefix}_" . basename($_FILES[$fieldname]['name']);
	    $uploadfile = $uploaddir . $filename;
	    move_uploaded_file($_FILES[$fieldname]['tmp_name'], $uploadfile);
	    copy($uploadfile, $uploaddir. 'thumbnail_'. $filename); // TODO: implement resizing for bandwidth savings
	    
	    return $filename;
	} else

	return false;    
}


// Core admin
switch ($group) {

	/****************************************************************
	 *  Functions for Main section
	 ***************************************************************/
	case 'main':
		switch ($action) {
			case 'editorial':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			case 'membership':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			case 'site':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			case 'foo':
				$flash['error'] = "Invalid foo action.";
				$flash['notice'] = "But we still have a notice :D";
				set_flash($flash);
				header('Location: console.php');
			break;
			case 'index':
				disp_header();
				$data = load_dashboard($db);
				$new_contact_emails = $data['new_contact_emails'];
				require('views/dashboard.php');
				disp_footer();
			break;

			/****************************************************************
			 *  Test example functions
			 ***************************************************************/
			case 'new_test':
				if (isset($_SESSION['test']) && $_SESSION['test'] != '')
					$test = $_SESSION['test'];
				disp_header('New Test -- '.$title);
				require('views/new_test.php');
				disp_footer();
			break;

			case 'create_test':
				$errors = array();
				if (isset($_POST['test']['name'])) {
					if ($_POST['test']['name'] == '')
						$errors['name'] = 'Name cannot be blank';
					if (strlen($_POST['test']['foo']) < 4)
						$errors['foo'] = 'Foo must be more than 4 chars in length';
					if (!preg_match('/^[0-9]{4}$/', $_POST['test']['bar']))
						$errors['bar'] = 'Bar must be a year.';
					if (!preg_match('/true|false/', $_POST['test']['asdf']))
						$errors['asdf'] = 'Asdf must be a boolean.';
					if (count($errors) > 0) {
						$flash['error'] = '<p>There were '.count($errors).' errors in your posting.';
						$flash['error'] .= '<ul>';
						foreach ($errors as $field => $error)
							$flash['error'] .= "<li>$field: $error</li>";
						$flash['error'] .= '</ul>';
						$flash['error'] .= '<p>Please correct these errors and resubmit.</p>';

						set_flash($flash);
						$_SESSION['test'] = $_POST['test'];
						header('Location: index.php?p=console&group=main&action=new_test');
					} else {
						$_SESSION['test'] = $_POST['test'];
						$flash['notice'] = 'Successfully created test!';
						set_flash($flash);
						header('Location: index.php?p=console&group=main&action=view_test');
					}
				} else {
					$flash['error'] = 'There were errors creating your test.';
					header('Location: index.php?p=console&group=main&action=new_test');
				}
			break;

			case 'modify_test':
				$test = $_SESSION['test'];
				disp_header('Modify Test -- '.$title);
				require('views/modify_test.php');
				disp_footer();
			break;

			case 'update_test':
				if (isset($_POST['test']['name'])) {
					$flash['notice'] = 'Successfully updated test!';
					set_flash($flash);
					$_SESSION['test'] = $_POST['test'];
					header('Location: index.php?p=console&group=main&action=view_test');
				} else {
					$flash['error'] = 'Error updating test.';
					set_flash($error);
					header('Location: index.php?p=console&group=main&action=modify_test');
				}
			break;

			case 'view_test':
				if (isset($_SESSION['test']) && $_SESSION['test'] != '')
					$test = $_SESSION['test'];
				disp_header('View Test -- '.$title);
				require('views/view_test.php');
				disp_footer();
			break;

			case 'destroy_test':
				$_SESSION['test'] = '';
				$flash['notice'] = 'Deleted current test.';
				set_flash($flash);
				header('Location: index.php?p=console&group=main&action=view_test');
			break;
			/****************************************************************
			 *  /Test example functions
			 ***************************************************************/


			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=main');
			break;
		}
	break;

	/****************************************************************
	 *  Functions for Stories section
	 ***************************************************************/
	case 'stories':
		switch ($action) {
			case 'featured':
				disp_header('Template Builder', 'template_builder');
				//require('views/template_builder.php');
				disp_footer('template_builder');
			break;
			/****************************************************************
			 *  Comments section
			 ***************************************************************/
			case 'comments':
				$comments = $db->load_all();
				disp_header();
				require('views/comments.php');
				disp_footer();
			break;
			case 'view_comment':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
				if (($comment = $db->load($id))) {
					disp_header();
					require('views/view_comment.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current comment for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
			break;
			case 'new_comment':
				disp_header();
				require('views/new_comment.php');
				disp_footer();
			break;
			case 'create_comment':
				if (isset($_POST['comment']['comments'])) {
					//$id = $db->insert($_POST['comment']);
					if (($id = $db->insert($_POST['comment'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created comment!';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_comment&id='.$id);
					} else {
						$flash['error'] = 'Could not create your comment! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=new_comment');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=new_comment');
				}
			break;
			case 'modify_comment':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
				if (($comment = $db->load($id))) {
					disp_header();
					require('views/modify_comment.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current comment for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
			break;
			case 'update_comment':
				if (isset($_POST['comment']['id']) && preg_match('/^[0-9]+$/', $_POST['comment']['id'])) {
					$id = $_POST['comment']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
				if (isset($_POST['comment']['comments'])) {
					if (($result = $db->update($_POST['comment'])) == 1) {
						$flash['notice'] = 'Successfully updated comment.';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_comment&id='.$id);
					} else {
						$flash['error'] = 'Could not update your comment! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=modify_comment&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=modify_comment&id='.$id);
				}
			break;
			case 'block_comment':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
				if (($result = $db->block($id, 1)) == 1) {
					$flash['notice'] = 'Successfully blocked comment.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				} else {
					$flash['error'] = 'Could not block comment. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
			break;
			case 'unblock_comment':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
				if (($result = $db->block($id, 0)) == 1) {
					$flash['notice'] = 'Successfully unblocked comment.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				} else {
					$flash['error'] = 'Could not unblock comment. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
			break;
			case 'destroy_comment':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted comment.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				} else {
					$flash['error'] = 'Could not delete comment. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=comments');
				}
			break;
			/****************************************************************
			 *  Videos section
			 ***************************************************************/
			case 'video_posts':
				$video_posts = $db->load_all();
				disp_header();
				require('views/video_posts.php');
				disp_footer();
			break;
			case 'view_video':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=videos');
				}
				if (($video = $db->load($id))) {
					disp_header();
					require('views/view_video.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current video for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=videos');
				}
			break;
			case 'new_video':
				disp_header();
				require('views/new_video.php');
				disp_footer();
			break;
			case 'create_video':
				if (isset($_POST['video']['title'])) {
					//$id = $db->insert($_POST['video']);
					if (($id = $db->insert($_POST['video'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created video!';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_video&id='.$id);
					} else {
						$flash['error'] = 'Could not create your video! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=new_video');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=new_video');
				}
			break;
			case 'modify_video':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=video_posts');
				}
				if (($video = $db->load($id))) {
					disp_header();
					require('views/modify_video.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current video for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=video_posts');
				}
			break;
			case 'update_video':
				if (isset($_POST['video']['id']) && preg_match('/^[0-9]+$/', $_POST['video']['id'])) {
					$id = $_POST['video']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=video_posts');
				}
				if (isset($_POST['video']['title'])) {
					if (($result = $db->update($_POST['video'])) == 1) {
						$flash['notice'] = 'Successfully updated video.';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_video&id='.$id);
					} else {
						$flash['error'] = 'Could not update your video! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=modify_video&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=modify_video&id='.$id);
				}
			break;			
			case 'destroy_video':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=video_posts');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted video.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=video_posts');
				} else {
					$flash['error'] = 'Could not delete video. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=video_posts');
				}
			break;
			/****************************************************************
			 *  Stories section
			 ***************************************************************/
			 case 'widgets':
				$widgets = $db->load_all();
				disp_header();
				require('views/widgets.php');
				disp_footer();
			 break;
			case 'assign_widget':
				if (isset($_POST['id']) && preg_match('/^[0-9]+$/', $_POST['id'])) {
					$id = $_POST['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- widgetid: $widgetid.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (isset($_POST['siteContentId']) && preg_match('/^[0-9]+$/', $_POST['siteContentId'])) {
					$siteContentId = $_POST['siteContentId'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- widgetid: $widgetid.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
			
				// actually update the database	
				disp_header();
				require('actions/assign_widget.php');
				disp_footer();
				
				break;			 
			case 'view_widget':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (($widget= $db->load($id))) {
					disp_header();
					require('views/view_widget.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current widgetfor id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
			break;			 
			 case 'new_widget':
				disp_header();
				require('views/new_widget.php');
				disp_footer();
			 break;
			 case 'create_widget':
				if (isset($_POST['widget']['title'])) {
					//$id = $db->insert($_POST['widget']);
					if (($id = $db->insert($_POST['widget'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created widget!';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_widget&id='.$id);
					} else {
						$flash['error'] = 'Could not create your widget! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=new_widget');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=new_widget');
				}
			break;
			case 'update_widget_location':
				if (isset($_POST['id']) && preg_match('/^[0-9]+$/', $_POST['id'])) {
					$id = $_POST['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (isset($_POST['locale'])) {
					$locale = $_POST['locale'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- locale: $locale.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				disp_header();
				require('actions/update_widget_location.php');
				disp_footer();			
			break;
			 case 'add_story_widget':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				disp_header();
				require('views/add_story_widget.php');
				disp_footer();
			 break;
			 case 'reset_widget_cover':
				disp_header();
				require('actions/reset_widget_cover.php');
				disp_footer();
			 break;
			 case 'reset_widget_sidebar':
				disp_header();
				require('actions/reset_widget_sidebar.php');
				disp_footer();
			 break;
			 case 'remove_widget_from_stories':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				disp_header();
				require('actions/remove_widget_from_stories.php');
				disp_footer();
			break;
			 case 'modify_widget':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (($widget= $db->load($id))) {
					disp_header();
					require('views/modify_widget.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current widget for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
			break;
			 case 'place_widget':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (($widget= $db->load($id))) {
					disp_header();
					require('views/place_widget.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current widget for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
			break;
			 case 'update_widget':
				if (isset($_POST['widget']['id']) && preg_match('/^[0-9]+$/', $_POST['widget']['id'])) {
					$id = $_POST['widget']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (isset($_POST['widget']['title'])) {
					if (($result = $db->update($_POST['widget'])) == 1) {
						$flash['notice'] = 'Successfully updated widget.';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_widget&id='.$id);
					} else {
						$flash['error'] = 'Could not update your widget! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=modify_widget&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=modify_widget&id='.$id);
				}
			 
			 break;
			 case 'destroy_widget':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted widget.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				} else {
					$flash['error'] = 'Could not delete story. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=widgets');
				}
			 break;
			case 'story_posts':
				$story_posts = $db->load_all();
				disp_header();
				require('views/story_posts.php');
				disp_footer();
			break;
			case 'view_story':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=storys');
				}
				if (($story = $db->load($id))) {
					disp_header();
					require('views/view_story.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current story for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=storys');
				}
			break;
			case 'new_story':
				disp_header();
				require('views/new_story.php');
				disp_footer();
			break;
			case 'create_story':
				if (isset($_POST['story']['title'])) {
					//$id = $db->insert($_POST['story']);
					if (($id = $db->insert($_POST['story'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created story!';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_story&id='.$id);
					} else {
						$flash['error'] = 'Could not create your story! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=new_story');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=new_story');
				}
			break;
			case 'modify_story':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
				if (($story = $db->load($id))) {
					disp_header();
					require('views/modify_story.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current story for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
			break;
			case 'update_story':
				if (isset($_POST['story']['id']) && preg_match('/^[0-9]+$/', $_POST['story']['id'])) {
					$id = $_POST['story']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
				if (isset($_POST['story']['title'])) {
					if (($result = $db->update($_POST['story'])) == 1) {
						$flash['notice'] = 'Successfully updated story.';
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=view_story&id='.$id);
					} else {
						$flash['error'] = 'Could not update your story! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=stories&action=modify_story&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=modify_story&id='.$id);
				}
			break;
			case 'block_story':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
				if (($result = $db->block($id, 1)) == 1) {
					$flash['notice'] = 'Successfully blocked story.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				} else {
					$flash['error'] = 'Could not block story. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
			break;
			case 'unblock_story':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
				if (($result = $db->block($id, 0)) == 1) {
					$flash['notice'] = 'Successfully unblocked story.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				} else {
					$flash['error'] = 'Could not unblock story. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
			break;
			case 'destroy_story':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted story.';
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				} else {
					$flash['error'] = 'Could not delete story. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=stories&action=story_posts');
				}
			break;
			case 'index':
				disp_header();
				echo "<h1>Index page for Stories.</h1>";
				disp_footer();
			break;
			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=stories');
			break;
		}
	break;
		
	/****************************************************************
	 *  Functions for Street Team section
	 ***************************************************************/

	case 'street_team':
		switch ($action) {
			case 'feature_panel':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;

			/****************************************************************
			 *  Leaders section
			 ***************************************************************/
			case 'licensePlateEligibleLeaders':

				
				disp_header();
				
				$week=12;
				//$week = $_GET['week'];
				
				echo '<script type="text/javascript">';
				require('views/leaders.js');
				echo '</script>';
			
				$leaders=array();
				$leadersQ = $db->query(
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

				echo "<h2>Members from up to the end of week $week that were not awarded license plate frames</h2>";
				require('views/leaders.php');
				
				
				break;
			case 'leaders':
				//echo '<pre>'.print_r($db, true) .'</pre>';
				disp_header();
				
				echo '<script type="text/javascript">';
				require('views/leaders.js');
				echo '</script>';
				
				$leadersQ = $db->query(
				"SELECT (DATE(weekOf)) AS week, WEEK(weekOf,1) as weekNum, name, pointTotal, eligibility, fbId,User.userid 
					FROM WeeklyScores, User,UserInfo 
					WHERE UserInfo.userid=WeeklyScores.userid 
						AND User.userid=WeeklyScores.userid AND User.isBlocked=0 AND pointTotal>0
					ORDER BY weekOf,eligibility, pointTotal DESC;");
				while ($row=mysql_fetch_assoc($leadersQ))
				{
					$leaders []= $row;
				}
				
				require('views/leaders.php');
				$leaders=array();
				$leadersQ = $db->query(
				"SELECT name, cachedPointsEarned AS pointTotal, eligibility, fbId,User.userid 
					FROM User,UserInfo 
					WHERE UserInfo.userid=User.userid
						 AND User.isBlocked=0 AND cachedPointsEarned>0
					ORDER BY eligibility, cachedPointsEarned DESC LIMIT 250;"); // TODO: may shorten this after first week scoring
				while ($row=mysql_fetch_assoc($leadersQ))
				{
					$leaders []= $row;
				}

				echo '<h2>All-time leaders</h2>';
				require('views/leaders.php');
				
				disp_footer();
			break;
			case 'assign_prize':
				
				/*
				if ((isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id']))
					OR (isset($_GET['multiple']))) {
					$userid = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- userid: $userid.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=leaders');
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
				
				disp_header();
				//echo '<pre>' . print_r($_POST,true) . '</pre>';
				require('views/assign_prize.php');
				disp_footer();
		

			break;
			case 'award_prize':
				if (isset($_POST['prizeid']) && preg_match('/^[0-9]+$/', $_POST['prizeid'])) {
					$prizeid = $_POST['prizeid'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- prizeid: $prizeid.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=leaders');
				}

				if (isset($_POST['userids']) /*&& preg_match('/^[0-9]+$/', $_POST['userids'])*/) {
					$userids = $_POST['userids'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- userid: $userid.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=leaders');
				}
				
				// actually update the database	
				
			
				
				disp_header();
				//echo $message;
					//	echo '<pre>' . print_r($_POST,true) . '</pre>';
		
				require('views/award_prize.php');
				disp_footer();
	
											
				
				break;
	
			
			/****************************************************************
			 *  Challenges section
			 ***************************************************************/
			case 'challenges':
				$challenges = $db->load_all();
				disp_header();
				require('views/challenges.php');
				disp_footer();
			break;
			case 'view_challenge':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
				if (($challenge = $db->load($id))) {
					disp_header();
					require('views/view_challenge.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current challenge for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
			break;
			case 'new_challenge':
				disp_header();
				require('views/new_challenge.php');
				disp_footer();
			break;
			case 'create_challenge':
				if (isset($_POST['challenge']['title'])) {
					//$id = $db->insert($_POST['challenge']);
					if (($id = $db->insert($_POST['challenge'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created challenge!';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_challenge&id='.$id);
					} else {
						$flash['error'] = 'Could not create your challenge! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=new_challenge');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=new_challenge');
				}
			break;
			case 'modify_challenge':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
				if (($challenge = $db->load($id))) {
					disp_header();
					require('views/modify_challenge.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current challenge for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
			break;
			case 'update_challenge':
				if (isset($_POST['challenge']['id']) && preg_match('/^[0-9]+$/', $_POST['challenge']['id'])) {
					$id = $_POST['challenge']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
				if (isset($_POST['challenge']['title'])) {
					
					// djm: take care of image upload /////////
					$newthumb =  
						handle_image_upload('thumbnail', "prize_{$_POST['challenge']['id']}_");
						
					if ($newthumb)
						$_POST['challenge']['thumbnail'] = $newthumb;
	
					///////////////////////////////////////////
					
					if (($result = $db->update($_POST['challenge'])) == 1) {
						$flash['notice'] = 'Successfully updated challenge.';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_challenge&id='.$id);
					} else {
						$flash['error'] = 'Could not update your challenge! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=modify_challenge&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=modify_challenge&id='.$id);
				}
			break;
			case 'destroy_challenge':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted challenge.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				} else {
					$flash['error'] = 'Could not delete challenge. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=challenges');
				}
			break;
			/****************************************************************
			 *  Prizes section
			 ***************************************************************/
			case 'prizes':
				$prizes = $db->load_all();
				disp_header();
				require('views/prizes.php');
				disp_footer();
			break;
			case 'view_prize':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
				if (($prize = $db->load($id))) {
					disp_header();
					require('views/view_prize.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current prize for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
			break;
			case 'new_prize':
				disp_header();
				require('views/new_prize.php');
				disp_footer();
			break;
			case 'create_prize':
				if (isset($_POST['prize']['title'])) {
					//$id = $db->insert($_POST['prize']);
					$newthumb = handle_image_upload('thumbnail', "prize_".time());
						
					if ($newthumb)
						$_POST['prize']['thumbnail'] = $newthumb;
					$flash['notice'] = 'Successfully created prize!';
					if (($id = $db->insert($_POST['prize'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_prize&id='.$id);
					} else {
						$flash['error'] = 'Could not create your prize! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=new_prize');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=new_prize');
				}
			break;
			case 'modify_prize':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
				if (($prize = $db->load($id))) {
					disp_header();
					require('views/modify_prize.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current prize for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
			break;
			case 'update_prize':
				if (isset($_POST['prize']['id']) && preg_match('/^[0-9]+$/', $_POST['prize']['id'])) {
					$id = $_POST['prize']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
				if (isset($_POST['prize']['title'])) {
					// djm: take care of image upload ///
					$newthumb =  
						handle_image_upload('thumbnail', "prize_{$_POST['prize']['id']}_");
						
					if ($newthumb)
						$_POST['prize']['thumbnail'] = $newthumb;
					/////////////////////////////////////
					if (($result = $db->update($_POST['prize'])) == 1) {
						$flash['notice'] = 'Successfully updated prize.';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_prize&id='.$id);
					} else {
						$flash['error'] = 'Could not update your prize! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=modify_prize&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=modify_prize&id='.$id);
				}
			break;
			case 'destroy_prize':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted prize.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				} else {
					$flash['error'] = 'Could not delete prize. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=prizes');
				}
			break;
			case 'winners':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			/****************************************************************
			 *  Orders Section
			 ***************************************************************/
			case 'orders':
				$orders = $db->load_all();
				disp_header();
				require('views/orders.php');
				disp_footer();
			break;
			case 'view_order':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
				if (($order = $db->load($id))) {
					disp_header();
					require('views/view_order.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current order for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
			break;
			case 'new_order':
				disp_header();
				require('views/new_order.php');
				disp_footer();
			break;
			case 'create_order':
				if (isset($_POST['order']['userid'])) {
					//$id = $db->insert($_POST['order']);
					if (($id = $db->insert($_POST['order'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created order!';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_order&id='.$id);
					} else {
						$flash['error'] = 'Could not create your order! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=new_order');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=new_order');
				}
			break;
			case 'modify_order':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
				if (($order = $db->load($id))) {
					disp_header();
					require('views/modify_order.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current order for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
			break;
			case 'update_order':
				if (isset($_POST['order']['id']) && preg_match('/^[0-9]+$/', $_POST['order']['id'])) {
					$id = $_POST['order']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
				if (isset($_POST['order']['userid'])) {
					if (($result = $db->update($_POST['order'])) == 1) {
						$flash['notice'] = 'Successfully updated order.';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_order&id='.$id);
					} else {
						$flash['error'] = 'Could not update your order! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=modify_order&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=modify_order&id='.$id);
				}
			break;
			case 'destroy_order':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted order.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				} else {
					$flash['error'] = 'Could not delete order. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=orders');
				}
			break;
			/****************************************************************
			 *  Completed Challenges Section
			 ***************************************************************/
			case 'completed_challenges':
				
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
				$completed_challenges = $db->load_all("SELECT * FROM ChallengesCompleted WHERE status='submitted' AND evidence!='Automatic!' ORDER BY dateSubmitted DESC LIMIT 1000");
				require('views/completed_challenges.php');
								
				echo '<h1>Auto Challenges</h1>';
				$completed_challenges = $db->load_all("SELECT * FROM ChallengesCompleted WHERE evidence='Automatic!' ORDER BY dateSubmitted DESC LIMIT 100");
				require('views/completed_challenges.php');
				
				disp_footer();
			break;
			case 'view_completed_challenge':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
				if (($completed_challenge = $db->load($id))) {
					disp_header();
					require('views/view_completed_challenge.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current completed_challenge for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
			break;
			case 'new_completed_challenge':
				disp_header();
				require('views/new_completed_challenge.php');
				disp_footer();
			break;
			case 'create_completed_challenge':
				if (isset($_POST['completed_challenge']['userid'])) {
					//$id = $db->insert($_POST['completed_challenge']);
					if (($id = $db->insert($_POST['completed_challenge'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created completed_challenge!';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_completed_challenge&id='.$id);
					} else {
						$flash['error'] = 'Could not create your completed_challenge! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=new_completed_challenge');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=new_completed_challenge');
				}
			break;
			case 'modify_completed_challenge':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
				if (($completed_challenge = $db->load($id))) {
					disp_header();
					require('views/modify_completed_challenge.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current completed_challenge for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
			break;
			case 'update_completed_challenge':
				if (isset($_POST['completed_challenge']['id']) && preg_match('/^[0-9]+$/', $_POST['completed_challenge']['id'])) {
					$id = $_POST['completed_challenge']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
				if (isset($_POST['completed_challenge']['userid'])) {
					if (($result = $db->update($_POST['completed_challenge'])) == 1) {
						$flash['notice'] = 'Successfully updated completed_challenge.';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_completed_challenge&id='.$id);
					} else {
						$flash['error'] = 'Could not update your completed_challenge! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=modify_completed_challenge&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=modify_completed_challenge&id='.$id);
				}
			break;
			case 'destroy_completed_challenge':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted completed_challenge.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				} else {
					$flash['error'] = 'Could not delete completed_challenge. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
			break;
			
			case 'approve_completed_challenge': // slight hack, since this now handles rejection as well
				if (isset($_POST['completed_challenge']['id']) && preg_match('/^[0-9]+$/', $_POST['completed_challenge']['id'])) 
				{
					$id = $_POST['completed_challenge']['id'];
				} else 
				{
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=completed_challenges');
				}
				
				if ($_POST['reject']) // user pressed reject button
				{
					/*
					 * 
					//$_POST['completed_challenge[status]']='rejected'; // set status to rejected
					// update to set comment text
					if (($result = $db->update($_POST['completed_challenge'])) == 1) 
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
						
						$flash['notice'] = "Challenge submission $id rejected.";
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_completed_challenge&id='.$id);
											
					} else {
						$flash['error'] = 'Could not update your completed_challenge! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=modify_completed_challenge&id='.$id);
					}
					
				} elseif (isset($_POST['pointsAwarded'])) // user presumably pressed approve and assigned nonzero points
				{
					
					// update to set comment text
					if (($result = $db->update($_POST['completed_challenge'])) == 1) 
					{

					} else {
						$flash['error'] = 'Could not update your completed_challenge! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=modify_completed_challenge&id='.$id);
					}
		
					
					
					// djm: 
					require_once( PATH_CORE .'/classes/challenges.class.php');
					$cct = new ChallengeCompletedTable();
					$cct->approveChallenge($id, $_POST['pointsAwarded'], &$code);
					/*
					if (($result = $db->update($_POST['completed_challenge'])) == 1) 
					{
						$flash['notice'] = 'Successfully updated completed_challenge.';
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=view_completed_challenge&id='.$id);
					} else 
					{
						$flash['error'] = 'Could not update your completed_challenge! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=street_team&action=review_completed_challenge&id='.$id);
					}*/
					$flash['notice'] = $code;
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=view_completed_challenge&id='.$id);
					
				} else 
				{
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=street_team&action=view_completed_challenge&id='.$id);
				}
			break;
			
			/****************************************************************
			 *  General Main section
			 ***************************************************************/
			case 'index':
				disp_header();
				echo "<h1>Index page for Street Team.</h1>";
				disp_footer();
			break;
			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=street_team');
			break;
		}
	break;

	/****************************************************************
	 *  Functions for Members section
	 ***************************************************************/
	case 'members':
		switch ($action) {
			/****************************************************************
			 *  Manage Members section
			 ***************************************************************/
			case 'members':
				$members = $db->load_all();
				disp_header();
				require('views/members.php');
				disp_footer();
			break;
			case 'view_member':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				$sql = "SELECT User.*, UserInfo.* FROM User, UserInfo WHERE User.userid = $id AND User.userid = UserInfo.userid";
				if (($member_res = $db->query($sql))) {
					$member = mysql_fetch_assoc($member_res);
				//if (($member = $db->load($id))) {
					disp_header();
					require('views/view_member.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current member for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
			break;
			case 'new_member':
				disp_header();
				require('views/new_member.php');
				disp_footer();
			break;
			case 'create_member':
				if (isset($_POST['member']['name'])) {
					if ($_POST['member']['dateRegistered'] == '')
						$_POST['member']['dateRegistered'] = date("Y-m-d H:i:s", time());
					//$id = $db->insert($_POST['member']);
					if (($id = $db->insert($_POST['member'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created member!';
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=view_member&id='.$id);
					} else {
						$flash['error'] = 'Could not create your member! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=new_member');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=new_member');
				}
			break;
			
			
			case 'modify_member':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				if (($member = $db->load($id))) {
					disp_header();
					require('views/modify_member.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current member for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
			break;
			case 'update_member':
				if (isset($_POST['member']['id']) && preg_match('/^[0-9]+$/', $_POST['member']['id'])) {
					$id = $_POST['member']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				if (isset($_POST['member']['name'])) {
					if (($result = $db->update($_POST['member'])) == 1) {
						$flash['notice'] = 'Successfully updated member.';
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=view_member&id='.$id);
					} else {
						$flash['error'] = 'Could not update your member! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=modify_member&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=modify_member&id='.$id);
				}
			break;
			case 'block_member':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				if (($result = $db->block($id, 1)) == 1) {
					$flash['notice'] = 'Successfully blocked member.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				} else {
					$flash['error'] = 'Could not block member. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
			break;
			case 'unblock_member':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				if (($result = $db->block($id, 0)) == 1) {
					$flash['notice'] = 'Successfully unblocked member.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				} else {
					$flash['error'] = 'Could not unblock member. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
			break;
			case 'destroy_member':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted member.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				} else {
					$flash['error'] = 'Could not delete member. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
			break;
			case 'authorize_editing':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				// GRRRRRRR RUSSSELLLL !!!1
				$sql = "SELECT User.*, UserInfo.* FROM User, UserInfo WHERE User.userid = $id AND User.userid = UserInfo.userid";
				if (($member_res = $db->query($sql))) {
					$member = mysql_fetch_assoc($member_res);
				//if (($member = $db->load($id))) {
					disp_header();
					//require('views/modify_member.php');
					require_once(PATH_CORE . '/classes/dynamicTemplate.class.php');
					require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');						
					$dynTemp = dynamicTemplate::getInstance();
					$authorized = $dynTemp->authorizeFbIdForEditing($member['fbId']);
					echo "Authorizing $fbId...authorized fbIds now $authorized";
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current member for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
			break;
			case 'show_friend_invite_credits':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=members');
				}
				
				$query = 
				"SELECT name, User.userid, dateRegistered, WEEK(dateRegistered,1) AS week, email, fbId 
				FROM User,UserInfo,Log 
				WHERE User.userid=UserInfo.userid 
					AND Log.userid1=$id 
					AND Log.action='friendSignup' 
					AND User.userid=Log.userid2
				ORDER BY dateRegistered DESC;";
				
				$res = $db->query($query);
				while ($row=mysql_fetch_assoc($res))
				{
					$friends []= $row;										
				}
								
				disp_header();
				require('views/friend_invite_credits.php');
				//echo '<pre>' . print_r($friends,true) . '</pre>';
				disp_footer();
				
	
			break;
			
			/****************************************************************
			 *  Manage Outbound Messages section
			 ***************************************************************/
			case 'outboundmessages':
				$outboundmessages = $db->load_all();
				disp_header();
				require('views/outboundmessages.php');
				disp_footer();
			break;
			case 'view_outboundmessage':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
				if (($outboundmessage = $db->load($id))) {
					disp_header();
					require('views/view_outboundmessage.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current outboundmessage for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
			break;
			case 'new_outboundmessage':
				disp_header();
				require('views/new_outboundmessage.php');
				disp_footer();
			break;
			case 'create_outboundmessage':
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
					//$id = $db->insert($_POST['outboundmessage']);
					if (($id = $db->insert($_POST['outboundmessage'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						$flash['notice'] = 'Successfully created outboundmessage!';
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=view_outboundmessage&id='.$id);
					} else {
						$flash['error'] = 'Could not create your outboundmessage! Please try again. '.$id;
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=new_outboundmessage');
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=new_outboundmessage');
				}
			break;
			case 'modify_outboundmessage':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
				if (($outboundmessage = $db->load($id))) {
					disp_header();
					require('views/modify_outboundmessage.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current outboundmessage for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
			break;
			case 'update_outboundmessage':
				if (isset($_POST['outboundmessage']['id']) && preg_match('/^[0-9]+$/', $_POST['outboundmessage']['id'])) {
					$id = $_POST['outboundmessage']['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
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
					if (($result = $db->update($_POST['outboundmessage'])) == 1) {
						$flash['notice'] = 'Successfully updated outboundmessage.';
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=view_outboundmessage&id='.$id);
					} else {
						$flash['error'] = 'Could not update your outboundmessage! Please try again. '.$result;
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=modify_outboundmessage&id='.$id);
					}
				} else {
					$flash['error'] = 'Form data not submitted properly, please try again.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=modify_outboundmessage&id='.$id);
				}
			break;
			case 'send_outboundmessage':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = 'Invalid Outbound Message ID.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
				if (isset($_GET['preview']) && $_GET['preview'] == 'true') {
					$preview = true;
				} else {
					$preview = false;
				}
				if (($outboundmessage = $db->load($id))) {
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
						$flash['error'] = "This message is marked as 'hold' and cannot be sent. Change the status of this message and try again.";
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=outboundmessages');
						exit;
					} else if ($outboundmessage['status'] == 'sent') {
						$flash['error'] = "This message has already been sent and cannot be resent.";
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=outboundmessages');
						exit;
					} else if ($outboundmessage['status'] == 'incomplete') {
						$received = $outboundmessage['usersReceived'];
						if ($received != '')
							$extraWhere = " AND UserInfo.fbId NOT IN ($received)";
					} else {
					}
					include_once PATH_FACEBOOK.'/lib/facebook.php';
					$facebook = new Facebook($init['fbAPIKey'], $init['fbSecretKey']);
					//$usersResult = $db->query("SELECT User.userid, fbId FROM User LEFT JOIN UserInfo ON User.userid = UserInfo.userid WHERE optInEmail = 1 $extraWhere");
					$usersResult = $db->query("SELECT User.userid, UserInfo.fbId FROM User, UserInfo WHERE $optInWhere $userGroupWhere User.userid = UserInfo.userid $extraWhere");
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
						$flash['error'] = "No current opted in users to send emails to.";
						set_flash($flash);
						header('Location: index.php?p=console&group=members&action=outboundmessages');
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
							require('views/send_outboundmessage.php');
							$msg = ob_get_contents();
						ob_end_clean();
						if ($outboundmessage['msgType'] == 'announce') {
							$result = $facebook->api_client->notifications_sendEmail($currEmailUsers, $outboundmessage['subject'], '', $msg);
						} else if ($outboundmessage['msgType'] == 'notification') {
							$result = $facebook->api_client->notifications_send($currEmailUsers, $msg, 'app_to_user');
						} else {
							$flash['error'] = 'Invalid message type!';
							set_flash($flash);
							header('Location: index.php?p=console&group=members&action=outboundmessages');
							exit;
						}
						if (preg_match('/^[0-9]+/', $result)) {
							$usersReceived .= $result.',';
							$totalSent += substr_count($result, ',') + 1;
							$flash['notice'] = "Successfully sent your outbound message to $totalSent users out of $totalUsers total.";
						} else {
							$flash['error'] = "Failed to send your outbound message: $result";
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
						$flash['error'] = "Incomplete notifications, only $totalSent out of $totalUsers messages were sent!";
						if (!$preview)
							$status = 'incomplete';
						else
							$status = 'pending';
					}
					$db->query("UPDATE OutboundMessages set usersReceived = '$usersReceived', numUsersReceived = '$totalSent', numUsersExpected = '$totalUsers', status = '$status' WHERE id = '{$outboundmessage['id']}'");
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				} else {
					$flash['error'] = "Sorry no current outboundmessage for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
			break;
			case 'block_outboundmessage':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
				if (($result = $db->block($id, 1)) == 1) {
					$flash['notice'] = 'Successfully blocked outboundmessage.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				} else {
					$flash['error'] = 'Could not block outboundmessage. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
			break;
			case 'unblock_outboundmessage':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
				if (($result = $db->block($id, 0)) == 1) {
					$flash['notice'] = 'Successfully unblocked outboundmessage.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				} else {
					$flash['error'] = 'Could not unblock outboundmessage. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
			break;
			case 'destroy_outboundmessage':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
				if (($result = $db->delete($id)) == 1) {
					$flash['notice'] = 'Successfully deleted outboundmessage.';
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				} else {
					$flash['error'] = 'Could not delete outboundmessage. Please try again. '.$result;
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=outboundmessages');
				}
			break;
			/****************************************************************
			 *  Manage Member Emails section
			 ***************************************************************/
			case 'member_emails':
				disp_header();
				$new_contact_emails = $db->load_all();
				require('views/member_emails.php');
				disp_footer();
			break;
			case 'clean_up_member_emails':
				$result = $db->query("TRUNCATE ContactEmails");
				if (preg_match('/query/i', $result))
					$flash['error'] = $result;
				else if (preg_match('/^[0-9]+$/', $result))
					$flash['notice'] = 'Successfully deleted all member contact emails.';
				else
					$flash['notice'] = $result;
				set_flash($flash);
				header('Location: index.php?p=console&group=members&action=member_emails');
			break;
			case 'view_member_email':
				if (isset($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id'])) {
					$id = $_GET['id'];
				} else {
					$flash['error'] = "Invalid $group -- $action -- id: $id.";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=member_emails');
				}
				if (($member_email = $db->load($id))) {
					disp_header();
					require('views/view_member_email.php');
					disp_footer();
				} else {
					$flash['error'] = "Sorry no current member_email for id: $id";
					set_flash($flash);
					header('Location: index.php?p=console&group=members&action=member_emails');
				}
			break;
			case 'index':
				disp_header();
				echo "<h1>Index page for Members.</h1>";
				disp_footer();
			break;
			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=members');
			break;
		}
	break;

	/****************************************************************
	 *  Functions for Statistics section
	 ***************************************************************/
	case 'statistics':
		switch ($action) {
			case 'statistics':
				disp_header();
				require('views/statistics.php');
				disp_footer();
			break;
			case 'index':
				disp_header();
				echo "<h1>Index page for Statistics.</h1>";
				disp_footer();
			break;
			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=statistics');
			break;
		}
	break;

	/****************************************************************
	 *  Functions for Actions section
	 ***************************************************************/
	case 'admin':
		switch ($action) {
			case 'cronjobs':
				$cronJobs = $db->load_all();
				disp_header();
				require('views/cronJobs.php');
				disp_footer();
			break;
			case 'run_cronjob':
				disp_header();
				require('actions/runCronJobs.php');
				disp_footer();			
			break;
			case 'initcronjobs':
				disp_header();
				require('actions/initCronJobs.php');
				disp_footer();			
			break;
			case 'cloud_properties':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			case 'database':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			case 'feed_list':
				disp_header();
				require('views/not_implemented.php');
				disp_footer();
			break;
			case 'index':
				disp_header();
				echo "<h1>Index page for Actions.</h1>";
				disp_footer();
			break;
			case 'flushfeeds':
				disp_header();
				require('actions/flushFeedList.php');
				disp_footer();			
			break;
			case 'sitestatus':
				disp_header();
				require('actions/siteStatus.php');
				disp_footer();
			break;
			case 'edittemplates':
				require('actions/editTemplates.php');				
			break;
			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=admin');
			break;
		}
	break;

	/****************************************************************
	 *  Functions for Facebook section
	 ***************************************************************/
	case 'facebook':
		switch ($action) {
			case 'registerfeedtemplates':
				disp_header();
				require('actions/registerFeedTemplates.php');
				disp_footer();
			break;
			case 'deletefeedtemplates':
				disp_header();
				require('actions/deleteFeedTemplates.php');
				disp_footer();
			break;
			case 'initprofilebox':
				disp_header();
				require('actions/initProfileBox.php');
				disp_footer();
			break;
			case 'downloadsettings':
				disp_header();
				require('actions/downloadSettings.php');
				disp_footer();
			break;
			case 'syncallocations':
				disp_header();
				require('actions/syncAllocations.php');
				disp_footer();
			break;
			case 'index':
				disp_header();
				echo "<h1>Index page for Facebook</h1>";
				disp_footer();
			break;
			default:
				set_flash(array('error' => 'Unknown action: '.$action));
				header('Location: index.php?p=console&group=facebook');
			break;
		}
	break;

	/****************************************************************
	 *  Default Group Actions
	 ***************************************************************/
	default:
		set_flash(array('error' => 'Unknown group: '.$group));
		header('Location: console.php');
	break;
}

?>
