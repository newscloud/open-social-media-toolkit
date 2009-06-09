<?php
/*
 * autoPost class support 

provides supportive functions for autoposting behavior, for quick posting by moderators and such
called from ajax requests mostly

 */

class autoPost
{
	var $app;
	var $session;
	var $db;
	var $facebook;	
	
	function __construct(&$app=NULL)
	{
		if (!is_null($app)) {
			$this->app=&$app;
			$this->session=&$app->session;
			$this->facebook = &$app->session->facebook;
			$this->db=&$app->db;			
		}	
	}

	function fetchPostForm($id=0) {
		// fetch story info
		$q=$this->db->query("SELECT * FROM Newswire WHERE id=$id;");
		$story=$this->db->readQ($q);		
		// send form with title and caption
			$code.='<p>Title: <br /><input type="text" name="title" id="title_'.$id.'" value="'.$story->title.'" /></p>';
			$code.='<p>Caption: <br /><textarea name="caption" id="caption_'.$id.'" class="formfield" id="caption" cols="80" rows="8">'.strip_tags($story->caption).'</textarea></p>';
			$code.='<p><input type="button" name="submit" value="Submit" onclick="postPublish('.$id.');" /><a href="#" onclick="hidePreviewPublish('.$id.');return false;">Cancel</a></p>';
			return $code;
	}
}
?>