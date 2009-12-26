<?php

/* Crowdsharing ideas class */
class pagePredict {
	
	var $page;
	var $db;
	var $session;
	var $teamObj;
	var $templateObj;
	var $pObj;
	var $isAppTab=false;

	function __construct(&$page) {
		$this->page=&$page;		
		$this->session=$this->page->session;
		$this->db=&$page->db;
		$this->facebook=&$page->facebook;
		$this->setupLibraries();
	}

	function setupLibraries() {
		require_once(PATH_CORE.'/classes/template.class.php');
		$this->templateObj=new template($this->db);
		$this->templateObj->registerTemplates(MODULE_ACTIVE,'predict');               				
		require_once(PATH_FACEBOOK.'/classes/predict.class.php');
		$this->pObj=new Predict($this->db,$this->templateObj,$this->session);
	}
	
	function fetch($option='browse') {
		if ($option=='') $option='browse'; // fix default pass thru for tabs
		//$inside.=$this->buildSubNav($option);	
		$inside.='<div id="col_left"><!-- begin left side --><br />';
		switch ($option) {
			default: // browse page
				$inside.='<h3>Predict the Outcome of Wired\'s Vanish</h3>';
				$inside.='<p>Compete with other trackers to see who can make the most accurate predictions. You cannot make changes to your predictions once you submit them.</p>';
				$inside.=$this->showQuestions();
			break;
			case 'add': // suggest an idea
			break;
			case 'addSubmit': // submit the idea form
			break;
			case 'me': // my ideas
			break;
			case 'view': // show a single idea
				$id=$_GET['id'];
				//$inside.=$this->iObj->buildIdeaDisplay($id,isset($_GET['share']));	// show or hide share 
			break;
		}
		// TO DO - SHOW FEEDBACK QUESTION COMMENT BOARD
		$inside.='<p><strong>If you could make one change to improve this contest, what would it be?</strong></p>';
		$inside.='<fb:comments xid="'.CACHE_PREFIX.'_predict" canpost="true" candelete="true" showform="false" send_notification_uid="693311688"></fb:comments>';			
		$inside.='</div><!-- end left side --><div id="col_right">';
		//$inside.=$this->buildSidebar();		
		$inside.='</div> <!-- end right side -->';
		if ($this->page->isAjax) return $inside;	
		$code=$this->page->constructPage('ideas',$inside);		
		return $code;
	}		
	
	function showQuestions() {
		$code='';
		$q=$this->db->query("SELECT * FROM Predict");
		while ($d=$this->db->readQ($q)) {
			// to do - check if answered
			// if answered, call answer function
			// else show question
			$code.=$this->displayQuestion($d);
		}
		return $code;
	}
	
	function displayQuestion($d) {
		$code='';
		$code.='<div><p><strong>'.$d->title.'</strong></p>';
		switch ($d->type) {
			default:
			$code.='<select id="p_'.$d->id.'" ><option value="yes">yes</option><option value="no">no</option></select>';
			break;
			case 'selection':
			$code.='<select id="p_'.$d->id.'" >';
			$options=explode(',',$d->options);
			foreach ($options as $item) {
				$code.='<option value="'.$item.'">'.$item.'</option>';
			}
			$code.='</select>';
			break;
			case 'state':
            $provinces=array('na'=>'Won\'t get caught','AK '=>'Alaska', 'AL'=>'Alabama', 'AR'=>'Arkansas', 'AS'=>'American Samoa', 'AZ'=>'Arizona', 'CA'=>'California', 'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware', 'FL'=>'Florida', 'GA'=>'Georgia', 'GU'=>'Guam', 'HI'=>'Hawaii', 'IA'=>'Iowa', 'ID'=>'Idaho', 'IL'=>'Illinois', 'IN'=>'Indiana', 'KS'=>'Kansas', 'KY'=>'Kentucky', 'LA'=>'Lousiana', 'MA'=>'Massachusetts', 'MD'=>'Maryland', 'ME'=>'Maine', 'MI'=>'Michigan', 'MN'=>'Minnesota', 'MO'=>'Missouri', 'MS'=>'Mississippi', 'MT'=>'Montana', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'NE'=>'Nebraska', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NV'=>'Nevada', 'NY'=>'New York', 'OH'=>'Ohio', 'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania', 'PR'=>'Puerto Rico', 'RI'=>'Rhode Island', 'SC'=>'South Carolina', 'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 'UT'=>'Utah', 'VA'=>'Virginia', 'VI'=>'Virgin Islands', 'VT'=>'Vermont', 'WA'=>'Washington', 'DC'=>'Washington D.C.', 'WI'=>'Wisconsin', 'WV'=>'West Virginia', 'WY'=>'Wyoming');
			$code.='<select id="p_'.$d->id.'" >';
			foreach ($provinces as $key => $item) {
				$code.='<option value="'.$key.'">'.$item.'</option>';
			}		
			$code.='</select>';	
			break;
		}
		$code.='<input class="btn_2" type="button" value="Submit" /><br />';
		$code.='<!-- end of question '.$d->id.' --></div><br />';
		return $code;
	}
	
	
	// option methods
	
	function buildBrowseIdeas() {
		if (isset($_GET['tagid'])) 
			$tagid=$_GET['tagid'];
	 	else
	 		$tagid=0;
		if (isset($_GET['view'])) 
			$view=$_GET['view'];
	 	else
	 		$view='recent';
		if (isset($_GET['type'])) 
			$type=$_GET['type'];
	 	else
	 		$type='share';
		if (isset($_GET['status'])) 
			$status=$_GET['status'];
	 	else
	 		$status='available';
		$code='<h1>Browse Ideas</h1>';
//		$code.=$this->iObj->fetchBrowseIdeas(false,$tagid,$view);
		return $code;
	}

}	
?>