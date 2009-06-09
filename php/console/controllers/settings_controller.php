<?php

Class SettingsController extends AppController {
	var $name = 'Settings';
	public function index() {
		$this->render();
	}
	public function initialize_tables() {
		$tables = array();
		$sql = "SHOW TABLES";
		//if ($this->dbname)
		if (isset($_REQUEST['dbname']) && $_REQUEST['dbname'])
			$this->db->selectdb($_REQUEST['dbname']);
		$tableresults = $this->db->query($sql);

		while (($row = mysql_fetch_assoc($tableresults)) !== false) {
			$tablename = array_shift($row);
			if (preg_match('/^Admin/', $tablename))
				continue;
			$tables[$tablename] = array();
			$tables[$tablename]['name'] = $tablename;
			$tables[$tablename]['fields'] = array();
			$tsql = "DESCRIBE $tablename";
			$fieldresults = mysql_query($tsql);

			while (($trow = mysql_fetch_assoc($fieldresults)) !== false) {
				$type = 'string';
				switch ($trow['Type']) {
					case (preg_match('/^varchar/i', $trow['Type']) ? $trow['Type'] : !$trow['Type']):
						$type = 'string';
					break;
					case (preg_match('/^int/i', $trow['Type']) ? $trow['Type'] : !$trow['Type']):
						$type = 'number';
					break;
					case (preg_match('/^tinyint/i', $trow['Type']) ? $trow['Type'] : !$trow['Type']):
						$type = 'boolean';
					break;
					case (preg_match('/^enum/i', $trow['Type']) ? $trow['Type'] : !$trow['Type']):
						$type = 'enum';
					break;
					case (preg_match('/^(datetime|timestamp)/i', $trow['Type']) ? $trow['Type'] : !$trow['Type']):
						$type = 'date';
					break;
					case (preg_match('/^text/i', $trow['Type']) ? $trow['Type'] : !$trow['Type']):
						$type = 'text';
					break;
					default:
						$type = 'string';
					break;
				}
				$tables[$tablename]['fields'][$trow['Field']] = array(
						'fieldtype'	=> $trow['Type'],
						'type'			=> $type,
						'null'			=> $trow['Null'],
						'key'				=> $trow['Key'],
						'default'		=> $trow['Default'],
						'extra'			=> $trow['Extra']
				);

				if ($trow['Key'] == 'PRI')
					$tables[$tablename]['fields'][$trow['Field']]['primary_key'] = true;

			}
		}
		//print_r($tables);
		//echo "<h1>Hello..</h1>";
		//$skip_render = true;
	}

	public function save_table() {
		$json = array();
		$tablename = $_POST['tablename'];
		$data = json_decode(stripslashes($_POST['table']), true);
		$skip_render = true;

		$json['name'] = $tablename;
		$json['fields'] = array();

		for ($i = 1; $i < count($data); $i++) {
			$json['fields'][$data[$i]['field']] = $data[$i];
		}

		$sql = sprintf("REPLACE INTO Admin_DataStore (type, name, data) VALUES (\"%s\", \"%s\", \"%s\")", 'model', $tablename, mysql_real_escape_string(json_encode($json)));
		$this->db->query($sql);
	}

	public function new_pod() {
		$this->db->db->selectDB('research');
		$results = $this->db->query("SELECT * FROM Admin_DataStore WHERE type = 'model'");
		$models = array();
		while (($model = mysql_fetch_assoc($results)) !== false)
			$models[] = $model;

		$this->set('models', $models);
		$this->render();
	}

	public function create_pod() {
		$pod = $_POST['podform'];
		$this->db->db->selectDB('research');
		mysql_select_db('research', $this->db->db);
		$results = $this->db->query("SELECT * FROM research.Admin_DataStore WHERE type = 'model' AND name = '{$pod['model']}'");
		$model = mysql_fetch_assoc($results);
		$modeldata = json_decode($model['data'], true);
		$json = array();
		
		$json['header'] = $pod['header'];
		$json['body'] = $pod['body'];
		$json['footer'] = $pod['footer'];
		$json['model'] = $pod['model'];
		$json['name'] = $pod['name'];
		$json['type'] = $pod['type'];
		$json['podfile'] = $pod['podfile'];
		$json['fields'] = $modeldata['fields'];

		$sql = sprintf("INSERT INTO research.Admin_DataStore (type, name, data) VALUES (\"%s\", \"%s\", \"%s\")", 'pod', $pod['name'], mysql_real_escape_string(json_encode($json)));
		$this->db->query($sql);
		
		$this->skip_render = true;
		set_flash(array('notice' => 'Successfully created pod!'));
		redirect(url_for($this->name, 'index'));
		//header('Location: index.php');
	}
}


?>
