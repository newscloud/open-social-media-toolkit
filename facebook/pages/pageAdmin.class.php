<?php


class pageAdmin {

	var $page;
	var $db;
	var $facebook;
	var $fbApp;
	var $templateObj;
	var $teamObj;
		
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

	function fetch($option='editTemplates') 
	{
		require_once(PATH_CORE .'/classes/dynamicTemplate.class.php');						
		$dynTemp = dynamicTemplate::getInstance();
			
		if (!($this->page->session->u->isAdmin || 
				$this->page->session->u->isSponsor || 
				$dynTemp->authEnableEditMode($this->page->session)))
		{
			$inside ='This page is only accessible to site administrators.';
		} else
		{
			$dynTemp->authEnableEditMode($this->page->session); // just make sure edit mode is enabled
			if ($option=='editTemplates')
			{

				$inside = '<p><a href="?p=admin&o=populateTemplates">Update Templates from source code</a>. This is a safe update of any new templates from the source code into the database. Does not overwrite previous changes you\'ve made</p>'; 
				
				$dynTemp->loadDBTemplates(); // need to load before we enumerate
				$dynTemp->forceAjaxEdit = true;  // force templates to be ajax-editable
								 	
				$inside .= '<fb:editor action="" labelwidth="200">';
			
				foreach ($dynTemp->dbtemplates as $shortName => $template)
				{
					$hackNodeName = "ajax_template_edit_$shortName";
//					$inside .= "<tr><td>$shortName</td><td>".htmlentities($code)."</td></tr>";
					$inside.='<fb:editor-custom label="'.$template['category'].' '.$shortName.'" name="name">'.
							'<div style="width: 500px;">'.// style="display:inline-block; clear: both;">'.
								$dynTemp->wrapCodeForEditMode(htmlentities($template['code']), $shortName, $template['helpString'],false).
							'<br clear="all" /></div>'.
							//htmlentities($code).
							'<a onclick="clearTemplate(\''.$hackNodeName.'\',\''.$shortName.'\',false); return false;">Clear</a>'.
							' | '.
							'<a onclick="clearTemplate(\''.$hackNodeName.'\',\''.$shortName.'\',true); return false;">Reset to default</a>'.
						'</fb:editor-custom>';
							
				}
				$inside .='</fb:editor>';
				
							
			} else if ($option=='populateTemplates')
			{
			
				$inside .= $this->templateObj->populateTemplates(); // minor hack to force all template files to be processed as though they were being invoked
				
			}
			
		}		
		
		if ($this->page->isAjax) return $inside;
		
	
		if ($mode=='teamWrap') return $inside;
		$inside=$tabs.'<div id="teamWrap">'.$inside.'<!-- end teamWrap --></div>';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('team',$inside,'');		
		return $code;
	}
	


}

?>