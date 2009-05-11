<?php

class pageAppTab { 

	var $page;
	var $db;
	var $facebook;
	var $app;
	var $fbUserPageId;
	var $siteUserId;
	var $templateObj;
	
	function __construct(&$page, $fbUserPageId) {
		$this->page=&$page;		
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->siteUserId = array_pop(mysql_fetch_row($this->db->query("SELECT userid FROM UserInfo WHERE fbId = $fbUserPageId")));
		$this->fbUserPageId = $fbUserPageId;
	}

	function changeFilterString($match) {
		//return preg_replace('/href="#"[^>]*>([^<]+)<\/a>/e', '\'href="index.php?p=ajax&m=appTab&fType=\'.((\'$1\' == \'All Actions\') ? "all" : strtolower(\'$1\')).\'">$1</a>\'', $match[0]);
		return preg_replace('/href="#"[^>]*>([^<]+)<\/a>/', 'href="'.URL_CANVAS.'/?p=profile&memberid='.$this->fbUserPageId.'">$1</a>', $match[0]);
	}

	function fetch() {
		if (isset($_GET['fType']) && $_GET['fType'] != '')
			$filterType = $_GET['fType'];
		else
			$filterType = 'all';
		//$code = '<link rel="stylesheet" href="'.URL_CALLBACK.'?p=cache&type=css&cf=hdFacebook_1235766581.css" type="text/css" charset="utf_8" />';
		//$code.='<style type="text/css">'.htmlentities(file_get_contents(PATH_FACEBOOK_STYLES.'/default.css', true)).'</style>';
		$code=$this->page->streamStyles();
		require_once(PATH_CORE.'/classes/home.class.php');
		$homeObj=new home($this->db);
		$code .= '<div id="pageBody">';
		$code .= '<div id="pageContent">';
		//$code .= '<script type="text/javascript">'.htmlentities(file_get_contents(PATH_SCRIPTS.'/newsroom.js')).'</script>';
		//$code .= '<h1>Abe\'s CLIMATE CHANGE APP!*!*!*!*!</h1>';		
		//$code .= '<a class="btn_1" href="'.SITE_URL.'">Visit the '.SITE_TITLE.' Application</a>';
		//$code .= '<br /><br /><hr />';
		$code .='<div id="col_left"><!-- begin left side -->';
		$code .= '<div id="featurePanel" class="clearfix">';
		$code .= $this->page->buildPanelBar('Featured Stories','<a href="?p=stories&o=sponsor">More from '.SITE_SPONSOR.' editors</a>');
		$code .= $homeObj->fetchFeature();
		$code .='</div><!--end "featurePanel"--><div class="panel_1">';		
		require_once(PATH_FACEBOOK.'/classes/actionFeed.class.php');
		$actionFeed = new actionFeed(&$this->db, true);
		require_once(PATH_FACEBOOK.'/classes/actionTeam.class.php');
		$actionTeam = new actionTeam(&$this->page);
		//$code .= $actionFeed->fetchFeed('all', 1, $this->siteUserId); 
		$feed = $actionFeed->fetchFeed($filterType, 1, $this->siteUserId); 
		$code .= preg_replace_callback('/<div class="subFilter">.*?<\/div>/s', array($this, 'changeFilterString'), $feed);
		$code .= '</div><!-- end panel_1 -->';
		$code .= '</div><!-- end col_left -->';
		$code .= '<div id="col_right">';
		// hack to give fbId to action team class session
		$actionTeam->setAppTabMode($this->fbUserPageId);
		$code.=$this->fetchPromo();
		$code .= $actionTeam->fetchSidePanel('appTab',3);
		$code .= '</div><!-- end col_right -->';
		$code .='</div><!-- end pageContent -->';
		$code .='</div><!-- end pageBody -->';

		// Hack this to the app tab
		$code = preg_replace('/on[cC]lick="[^"]+"/', '', $code);
		// $code = preg_replace('/<fb:profile-pic[^>]+>/', '', $code);
		$code = preg_replace('/href="\/?index.php([^"]+)/', 'href="'.URL_CANVAS.'/$1&referfbid='.$this->fbUserPageId, $code);
		$code = preg_replace('/href="\?p=([^"]+)/', 'href="'.URL_CANVAS.'/?p=$1&referfbid='.$this->fbUserPageId, $code);
		$code = preg_replace_callback('/<div class="pages">.*?<\/div>/s', array($this, 'changeFilterString'), $code);

		return $code;
	}

	function fetchPromo() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE, 'appTab');		
		// to do - get this from cache
		$code='<div id="introPanel">';
		$code.=$this->templateObj->templates['promo'];
		$code.='<!-- end of introPanel --></div>';
		return $code;			
	}


}

?>