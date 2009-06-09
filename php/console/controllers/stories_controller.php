<?php

Class StoriesController extends AppController {
	var $name = 'Stories';
	public function featured() {
				disp_header('Template Builder', 'template_builder');
				//require('views/template_builder.php');
				disp_footer('template_builder');
	}

			/****************************************************************
			 *  Comments section
			 ***************************************************************/
	public function comments() {
				$this->set('comments', $this->db->load_all());
				$this->render();
	}

	public function view_comment() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
				if (($comment = $this->db->load($id))) {
					$this->set('comment', $comment);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
	}

	public function new_comment() {
				$this->render();
	}

	public function create_comment() {
				if (isset($_POST['comment']['comments'])) {
					if (($id = $this->db->insert($_POST['comment'])) > 0) {
						set_flash(array('notice' => 'Successfully created comment!'));
						redirect(url_for($this->name, 'view_comment', $id));
					} else {
						set_flash(array('error' => 'Could not create your comment! Please try again. '.$id));
						redirect(url_for($this->name, 'new_comment'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for($this->name, 'new_comment'));
				}
	}

	public function modify_comment() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
				if (($comment = $this->db->load($id))) {
					$this->set('comment', $comment);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for('stories', 'comments'));
				}
	}

	public function update_comment() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
				if (isset($_POST['comment']['comments'])) {
					if (($result = $this->db->update($_POST['comment'])) == 1) {
						set_flash(array('notice' => 'Successfully updated comment.'));
						redirect(url_for('stories', 'view_comment', $id));
					} else {
						set_flash(array('error' => 'Could not update your comment! Please try again. '.$result));
						redirect(url_for('stories', 'modify_comment', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'modify_comment', $id));
				}
	}

	public function block_comment() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
				if (($result = $this->db->block($id, 1)) == 1) {
					set_flash(array('notice' => 'Successfully blocked comment.'));
					redirect(url_for('stories', 'comments'));
				} else {
					set_flash(array('error' => 'Could not block comment. Please try again. '.$result));
					redirect(url_for('stories', 'comments'));
				}
	}

	public function unblock_comment() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
				if (($result = $this->db->block($id, 0)) == 1) {
					set_flash(array('notice' => 'Successfully unblocked comment.'));
					redirect(url_for('stories', 'comments'));
				} else {
					set_flash(array('error' => 'Could not unblock comment. Please try again. '.$result));
					redirect(url_for('stories', 'comments'));
				}
	}

	public function destroy_comment() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current comment for id: $id"));
					redirect(url_for($this->name, 'comments'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted comment.'));
					redirect(url_for('stories', 'comments'));
				} else {
					set_flash(array('error' => 'Could not delete comment. Please try again. '.$result));
					redirect(url_for('stories', 'comments'));
				}
	}

/* Edit Templates */
public function edittemplates() {
	redirect(URL_CANVAS. '/?p=admin&o=editTemplates');
}


			/****************************************************************
			 *  Videos section
			 ***************************************************************/
	public function video_posts() {
				$this->set('video_posts', $this->db->load_all());
				$this->render();
	}

	public function view_video() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current video for id: $id"));
					redirect(url_for($this->name, 'videos'));
				}
				if (($video = $this->db->load($id))) {
					$this->set('video', $video);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current video for id: $id"));
					redirect(url_for('stories', 'videos'));
				}
	}

	public function new_video() {
				$this->render();
	}

	public function create_video() {
				if (isset($_POST['video']['title'])) {
					if (($id = $this->db->insert($_POST['video'])) > 0) {
						set_flash(array('notice' => 'Successfully created video!'));
						redirect(url_for('stories', 'view_video', $id));
					} else {
						set_flash(array('error' => 'Could not create your video! Please try again. '.$id));
						redirect(url_for('stories', 'new_video'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'new_video'));
				}
	}

	public function modify_video() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current video for id: $id"));
					redirect(url_for($this->name, 'videos'));
				}
				if (($video = $this->db->load($id))) {
					$this->set('video', $video);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current video for id: $id"));
					redirect(url_for('stories', 'video_posts'));
				}
	}

	public function update_video() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current video for id: $id"));
					redirect(url_for($this->name, 'videos'));
				}
				if (isset($_POST['video']['title'])) {
					if (($result = $this->db->update($_POST['video'])) == 1) {
						set_flash(array('notice' => 'Successfully updated video.'));
						redirect(url_for('stories', 'view_video', $id));
					} else {
						set_flash(array('error' => 'Could not update your video! Please try again. '.$result));
						redirect(url_for('stories', 'modify_video', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'modify_video', $id));
				}
	}
			
	public function destroy_video() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current video for id: $id"));
					redirect(url_for($this->name, 'videos'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted video.'));
					redirect(url_for('stories', 'video_posts'));
				} else {
					set_flash(array('error' => 'Could not delete video. Please try again. '.$result));
					redirect(url_for('stories', 'video_posts'));
				}
	}

			/****************************************************************
			 *  Stories section
			 ***************************************************************/
	public function widgets() {
				$widgets = $this->db->load_all();
				$this->set('widgets', $widgets);
				$this->render();
	}

	public function assign_widget() {
				if (isset($_POST['id']) && preg_match('/^[0-9]+$/', $_POST['id'])) {
					$id = $_POST['id'];
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- widgetid: $widgetid."));
					redirect(url_for('stories', 'widgets'));
				}
				if (isset($_POST['siteContentId']) && preg_match('/^[0-9]+$/', $_POST['siteContentId'])) {
					$siteContentId = $_POST['siteContentId'];
					$this->run_assign_widget($id, $siteContentId);
					redirect(url_for('stories', 'widgets'));
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- widgetid: $widgetid."));
					redirect(url_for('stories', 'widgets'));
				}
	}
			 
	public function view_widget() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (($widget = $this->db->load($id))) {
					$this->set('widget', $widget);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current widgetfor id: $id"));
					redirect(url_for('stories', 'widgets'));
				}
	}
			 
	public function new_widget() {
				$this->render();
	}

	public function create_widget() {
				if (isset($_POST['widget']['title'])) {
					//$id = $this->db->insert($_POST['widget']);
					if (($id = $this->db->insert($_POST['widget'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created widget!'));
						redirect(url_for('stories', 'view_widget', $id));
					} else {
						set_flash(array('error' => 'Could not create your widget! Please try again. '.$id));
						redirect(url_for('stories', 'new_widget'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'new_widget'));
				}
	}

	public function update_widget_location() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (isset($_POST['locale'])) {
					$locale = $_POST['locale'];
					$this->run_update_widget_location($id, $locale);
					redirect(url_for('stories', 'widgets'));
				} else {
					set_flash(array('error' => "Invalid $group -- $action -- locale: $locale."));
					redirect(url_for('stories', 'widgets'));
				}
	}

	public function add_story_widget() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (($widget = $this->db->load($id))) {
					$this->set('widget', $widget);
					$this->set('db', $this->db);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for('stories', 'widgets'));
				}
	}

	public function reset_widget_cover() {
		// set widget to story
		set_flash(array('notice' => "Removing featured widget from cover page<br />"));
		$this->db->query("DELETE FROM FeaturedWidgets WHERE locale='homeFeature'");
		redirect(url_for('stories', 'widgets'));
		// clear out the cache for the cover widget area
		/*
			 require_once(PATH_CORE.'/classes/template.class.php');
			 $templateObj=new template($this->db);
			 $templateObj->resetCache('read',$siteContentId);
		 */  
	}

	public function reset_widget_sidebar() {
    // set widget to story
    set_flash(array('notice' => "Removing featured widget from cover page<br />"));
    $this->db->query("DELETE FROM FeaturedWidgets WHERE locale='homeSidebar'");
		redirect(url_for('stories', 'widgets'));
    // clear out the cache for the cover widget area
    /*
    require_once(PATH_CORE.'/classes/template.class.php');
    $templateObj=new template($db);
    $templateObj->resetCache('read',$siteContentId);
    */  
	}

	public function remove_widget_from_stories() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
    // set widget to story
        require_once(PATH_CORE.'/classes/template.class.php');
        $templateObj=new template($db);
        $q=$this->db->query("SELECT siteContentId FROM Content WHERE widgetid=$id;");
        while ($data=$this->db->readQ($q)) {
            $this->db->query("UPDATE Content SET widgetid=0 WHERE widgetid=$id AND siteContentId=".$data->siteContentId); 
            $templateObj->resetCache('read',$data->siteContentId);      
        }       
    		set_flash(array('notice' => "Removing WidgetId:".$id." from all stories<br />"));
				redirect(url_for($this->name, 'widgets'));
	}

	public function modify_widget() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (($widget = $this->db->load($id))) {
					$this->set('widget', $widget);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for('stories', 'widgets'));
				}
	}

	public function place_widget() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (($widget = $this->db->load($id))) {
					$this->set('widget', $widget);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for('stories', 'widgets'));
				}
	}

	public function update_widget() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (isset($_POST['widget']['title'])) {
					if (($result = $this->db->update($_POST['widget'])) == 1) {
						$this->set('result', $result);
						set_flash(array('notice' => 'Successfully updated widget.'));
						redirect(url_for('stories', 'view_widget', $id));
					} else {
						set_flash(array('error' => 'Could not update your widget! Please try again. '.$result));
						redirect(url_for('stories', 'modify_widget', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'modify_widget', $id));
				}
			 
	}

	public function destroy_widget() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current widget for id: $id"));
					redirect(url_for($this->name, 'widgets'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted widget.'));
					redirect(url_for('stories', 'widgets'));
				} else {
					set_flash(array('error' => 'Could not delete story. Please try again. '.$result));
					redirect(url_for('stories', 'widgets'));
				}
	}

	public function story_posts() {
				$story_posts = $this->db->load_all();
				$this->set('story_posts', $story_posts);
				$this->render();
	}

	public function view_story() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for($this->name, 'story_posts'));
				}
				if (($story = $this->db->load($id))) {
					$this->set('story', $story);
					$comment_sql = "select userid, concat(comments) as comments, date, postedByName FROM Comments WHERE siteContentId = $id order by date desc";
					$comments_res = $this->db->query($comment_sql);
					$comments = array();
					while (($comment = mysql_fetch_assoc($comments_res)) !== false)
						$comments[] = $comment;
					$this->set('comments', $comments);

					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for('stories', 'storys'));
				}
	}

	public function new_story() {
				$this->render();
	}

	public function create_story() {
				if (isset($_POST['story']['title'])) {
					//$id = $this->db->insert($_POST['story']);
					if (($id = $this->db->insert($_POST['story'])) > 0) {
					//if (preg_match('/^[0-9]+$/', $id)) {
						set_flash(array('notice' => 'Successfully created story!'));
						redirect(url_for('stories', 'view_story', $id));
					} else {
						set_flash(array('error' => 'Could not create your story! Please try again. '.$id));
						redirect(url_for('stories', 'new_story'));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'new_story'));
				}
	}

	public function modify_story() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for($this->name, 'story_posts'));
				}
				if (($story = $this->db->load($id))) {
					$this->set('story', $story);
					$this->render();
				} else {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for('stories', 'story_posts'));
				}
	}

	public function update_story() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for($this->name, 'story_posts'));
				}
				if (isset($_POST['story']['title'])) {
					if (($result = $this->db->update($_POST['story'])) == 1) {
						set_flash(array('notice' => 'Successfully updated story.'));
						redirect(url_for('stories', 'view_story', $id));
					} else {
						set_flash(array('error' => 'Could not update your story! Please try again. '.$result));
						redirect(url_for('stories', 'modify_story', $id));
					}
				} else {
					set_flash(array('error' => 'Form data not submitted properly, please try again.'));
					redirect(url_for('stories', 'modify_story', $id));
				}
	}

	public function block_story() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for($this->name, 'story_posts'));
				}
				if (($result = $this->db->block($id, 1)) == 1) {
					set_flash(array('notice' => 'Successfully blocked story.'));
					redirect(url_for('stories', 'story_posts'));
				} else {
					set_flash(array('error' => 'Could not block story. Please try again. '.$result));
					redirect(url_for('stories', 'story_posts'));
				}
	}

	public function unblock_story() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for($this->name, 'story_posts'));
				}
				if (($result = $this->db->block($id, 0)) == 1) {
					set_flash(array('notice' => 'Successfully unblocked story.'));
					redirect(url_for('stories', 'story_posts'));
				} else {
					set_flash(array('error' => 'Could not unblock story. Please try again. '.$result));
					redirect(url_for('stories', 'story_posts'));
				}
	}

	public function destroy_story() {
				$id = $this->params['id'];
				if ($id === 0) {
					set_flash(array('error' => "Sorry no current story for id: $id"));
					redirect(url_for($this->name, 'story_posts'));
				}
				if (($result = $this->db->delete($id)) == 1) {
					set_flash(array('notice' => 'Successfully deleted story.'));
					redirect(url_for('stories', 'story_posts'));
				} else {
					set_flash(array('error' => 'Could not delete story. Please try again. '.$result));
					redirect(url_for('stories', 'story_posts'));
				}
	}

	public function index() {
				$this->render();
	}

	/******************************************************************************
	 * Private functions
	 *****************************************************************************/
	 private function run_assign_widget($id, $siteContentId) {
		 // set widget to story                                                               
		 set_flash(array('notice' => "Assigning WidgetId:".$id." to story id:
		 ".$siteContentId."...<br/> <a
		 href=\"".URL_CANVAS."?p=read&cid=".$siteContentId."\"
		 target=\"_blank\">Preview the story</a>"));
		 $this->db->query("UPDATE Content SET widgetid=$id WHERE siteContentId=$siteContentId");
		 // clear out the cache for the story
		 require_once(PATH_CORE.'/classes/template.class.php');                               
		 $templateObj=new template($this->db);
		 $templateObj->resetCache('read',$siteContentId);                                     
	 }

	 private function run_update_widget_location($id, $locale) {
		 // set widget to locale
		 set_flash(array('notice' => "Assigning WidgetId:".$id." to locale: ".$locale."...<br/> "));
		 $q=$this->db->query("SELECT * FROM FeaturedWidgets WHERE locale='$locale';");
		 if ($this->db->countQ($q)>0) {
			 $this->db->query("UPDATE FeaturedWidgets SET widgetid=$id WHERE locale='$locale'");
			 // echo "UPDATE FeaturedWidgets SET widgetid=$id WHERE locale='$locale'";
		 } else {
			 $this->db->query("INSERT INTO FeaturedWidgets (widgetid,locale,position) VALUES ($id,'$locale',1);");
			 //  echo "INSERT INTO FeaturedWidgets (widgetid,locale,position) VALUES ($id,'$locale',1);";
		 }
		 // clear out the cache for the story
		 // require_once(PATH_CORE.'/classes/template.class.php');
		 //$templateObj=new template($this->db);
		 //$templateObj->resetCache('read',$siteContentId);  
	 }
	 	 
	 	 

}

?>
