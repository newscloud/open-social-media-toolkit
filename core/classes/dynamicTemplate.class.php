<?php

//--------------------------------------------------------------
// new stuff for db-dynamic templates

require_once(PATH_CORE.'/classes/dbRowObject.class.php');

class TemplateObj extends dbRowObject
{
	
}

class TemplateTable
{
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table fields
	var $db;	
	static $tablename="Templates";
	static $idname = "id";
	static $idtype = "INT(11) unsigned NOT NULL auto_increment";
	static $dbRowObjectClass = "TemplateObj";
		
	static $fields = array(		
		"shortName" => 		"VARCHAR(255)",
		"code" 		=>		"BLOB",
		"category"  =>		"VARCHAR(128)",
		"helpString" =>		"BLOB"
		
	);

	static $keydefinitions = array(); 		
	///////////////////////////////////////////////////////////////////////////////////////////////////////
	// standard table functions
	function __construct(&$db=NULL) 
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;		
	}	
	// although many functions will be duplicated between table subclasses, having a parent class gets too messy	
	function getRowObject()
	{	
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
	///////////////////////////////////////////////////////////////////////////////////////////////////////

	
	function loadTemplates()
	{
		$q=$this->db->queryC("
			SELECT shortName, code 
			FROM Templates;");
			
		$dbtemplates = array();

		if ($q)
		{
			while($data=$this->db->readQ($q))
			{
				$dbtemplates[$data->shortName] = $data->code;
			}
		
		}		
		
		return $dbtemplates;
	}
	
	function loadAll()
	{
	$q=$this->db->queryC("
			SELECT * 
			FROM Templates ORDER BY category ASC;");
			
		$dbtemplates = array();

		if ($q)
		{
			while($data=$this->db->readQ($q))
			{
				$dbtemplates[$data->shortName]['code'] = $data->code;
				$dbtemplates[$data->shortName]['category'] = $data->category;
				$dbtemplates[$data->shortName]['helpString'] = $data->helpString;
			}
		
		}		
		
		return $dbtemplates;
		
	}
	
}



class dynamicTemplate
{
	static $singleton = null;
	var $db;
	var $dbtemplates = null;
	var $editEnabled = false;
	var $forceAjaxEdit = false; // for master edit page, force ajax edit wrappers even if template cannot use them normally
	private function __construct($db)
	{
		if (is_null($db)) 
		{ 
			require_once('db.class.php');
			$this->db=new cloudDatabase();
		} else
			$this->db=$db;			
	}
	
	static function getInstance($db=null)
	{
		if (!self::$singleton)
			self::$singleton = new dynamicTemplate($db); 
			
		return self::$singleton;
	}
	
	function authEnableEditMode($session=null)
	{
	
		if ($session AND ($session->isAdmin OR $session->u->isSponsor))
		{
			$this->editEnabled = true;
			return true; 
		}
		
		if ($_POST['fb_sig_added']) { 
			$fbId=$_POST['fb_sig_user'];
		} else {
			$fbId=$_POST['fb_sig_canvas_user'];
		}					

		//echo "Checking fbid $fbId...";
		// check this user authorized to edit app
		require_once(PATH_CORE .'/classes/systemStatus.class.php');
		// check system status field for 
		$ssObj=new systemStatus($this->db);
		
		$authorizedEditors = explode(',',$ssObj->getState('template_editor_fbIds'));
		
		//echo 'Authorized editors: <pre>'.print_r($authorizedEditors, true).'</pre>';
		if (array_search($fbId, $authorizedEditors) !== false)
		{
					
			$this->editEnabled = true;
			//echo '...Editing enabled!';
			return true;
		} else
		{
			//echo '...Sorry, editing not enabled.';
		}
		
		return false;
	}
	
	function authorizeFbIdForEditing($fbId)
	{
		require_once(PATH_CORE .'/classes/systemStatus.class.php');
		// check system status field for 
		$ssObj=new systemStatus($this->db);
		
		$authorizedEditors = explode(',',$ssObj->getState('template_editor_fbIds'));
		
		$authorizedEditors []=$fbId;
		
		$ssObj->setState('template_editor_fbIds', implode(',', $authorizedEditors));
		
		return implode(',', $authorizedEditors);
	}	
	
	function fetchDBTemplate($templateShortName) // just load it, no definition capability
	{
	    if (!$this->dbtemplates)
	        $this->loadDBTemplates();
	    //if ($this->dbtemplates[$templateShortName]<>'')
	     //   $code= $this->dbtemplates[$templateShortName];
		//return $code;
		return $this->dbtemplates[$templateShortName]['code'];
		
	}
	
	function fetchDBTemplateHelpString($templateShortName) // just load it, no definition capability
	{
	    if (!$this->dbtemplates)
	        $this->loadDBTemplates();
	    //if ($this->dbtemplates[$templateShortName]<>'')
	     //   $code= $this->dbtemplates[$templateShortName];
		//return $code;
		return $this->dbtemplates[$templateShortName]['helpString'];
		
	}
	
	
	function useDBTemplate($templateShortName, $templateDefaultCode, $templateHelpString = '', $refreshPage = false, $category='', $ajaxEdit=true)
	{
	
	    if (!$this->dbtemplates)
	        $this->loadDBTemplates();
	    if (array_key_exists($templateShortName, $this->dbtemplates))//($this->dbtemplates[$templateShortName]<>'')
	        $code= $this->dbtemplates[$templateShortName]['code'];
	    else
	    {
	    	$code= $templateDefaultCode;
	    	// add to db and update loaded templates
	    	$this->updateAddDBTemplate($templateShortName, $templateDefaultCode, $templateHelpString, $category);
	    }
	    
	    if ($this->editEnabled && ($ajaxEdit || $this->forceAjaxEdit))
	        $code = $this->wrapCodeForEditMode($code, $templateShortName, $templateHelpString, $refreshPage);
	       
	    return $code;
	    
	}
	function loadDBTemplates()
	{
		$tt = new TemplateTable($this->db);
		$this->dbtemplates= $tt->loadAll();
		return $this->dbtemplates;
	}
	
	function updateAddDBTemplate($shortName, $code,$helpString='', $category='')
	{
		if (/*ENABLE_TEMPLATE_EDITS &&*/ $this->editEnabled) // extra auth check to make sure this isnt being invoked by an unauthorized user
		{		
			$tt = new TemplateTable($this->db);
			$to = $tt->getRowObject();
			$to->shortName = $shortName;
			$to->code = $code;
			if ($helpString <>'') $to->helpString = $helpString;
			if ($category <> '') $to->category = $category;
			if ($to->loadWhere("shortName='$shortName'"))
			{
				$to->code=$code; 
				$to->update();
			} else
				$to->insert();
				
		}
	}
	
	function deleteDBTemplate($shortName)
	{
		if (/*ENABLE_TEMPLATE_EDITS &&*/ $this->editEnabled) // extra auth check to make sure this isnt being invoked by an unauthorized user
		{
		
			$tt = new TemplateTable($this->db);
			$to = $tt->getRowObject();
			$to->shortName = $shortName;
			if ($to->loadWhere("shortName='$shortName'"))
			{ 
				$to->delete();
				return true;
			} else
				return false;
				
		}
		return false;
	}
	
	function wrapCodeForEditMode($code, $shortName, $helpString, $refreshPage)
	{
		$refreshPageText = $refreshPage ? 'true' : 'false';
		$nodeId = "ajax_template_edit_$shortName";
		$code = '<span onmouseover="document.getElementById(\''.$nodeId.'\').addClassName(\'templateEditHover\');"' // //.setStyle(\'backgroundColor\',\'green\'});"
					.'onmouseout="document.getElementById(\''.$nodeId.'\').removeClassName(\'templateEditHover\');"'
					//."onclick=\"editTemplate('$nodeId','$shortName','$helpString',$refreshPageText); return false;\""
					."onclick=\"editTemplate('$nodeId','$shortName',$refreshPageText); return false;\""
					//.' style="background-color: blue;"
					.'>'.
					"<span id='$nodeId'>".
						"$code".
					"</span>".
					
					//"<a onclick=\"editTemplate('$nodeId','$shortName','$helpString',$refreshPageText); return false;\">e</a>".
				"</span>";
		return $code;					
	}
	
}
?>