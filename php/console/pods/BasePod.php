<?php

class BasePod {
	var $BASE_URL;
	var $fields;
	var $currFields;
	var $labels;
	var $currLabels;
	var $podData;
	var $filters = false;
	var $id;
	var $name;
	var $numPodRows = 5;
	var $numFullRows = 50;
	var $model = false;

	function __construct($podData) {
		global $BASE_URL;
		global $db;
		$this->BASE_URL = $BASE_URL;
		$this->db = $db;
		$this->podData = $podData['data'];
		$this->fields =& $podData['data']['fields'];
		$this->id = $podData['id'];
		$this->name = $podData['data']['name'];
		if (isset($podData['data']['model']) && $podData['data']['model'] != 'none') {
			$model = $podData['data']['model'];
			require_once(PATH_CONSOLE.'/models/'.$model.'.php');
			$this->model = new $model();
		}
	}

	function gen_pod() {
		$p =& $this->podData;
		$pod = array();
		$pod['id'] = $this->id;
		if (isset($p['header']) && $p['header'])
			$pod['header'] = $p['header'];
		if (isset($p['body']) && $p['body'])
			$pod['body'] = $p['body'];
		if (isset($p['footer']) && $p['footer'])
			$pod['footer'] = $p['footer'];
		if ($this->filters)
			$pod['filters'] = $this->filters;

		if (isset($p['type'])) {
			$this->load_schema('pod', $p['type']);
			switch ($p['type']) {
				case 'dataTable':
					$pod['datasource'] = $this->gen_url($p['type'], 'pod');
					$pod['fields'] = $this->currFields;
					$pod['labels'] = $this->currLabels;
				break;
				case 'ajax':
					$pod['ajax'] = $this->gen_url($p['type'], 'pod');
				break;
				case (preg_match('/^chart-(.*)$/', $p['type'], $match) ? $p['type'] : !$p['type']):
					$pod['chart'] = $this->gen_url('chart', 'pod');
					$pod['chartType'] = $match[1];
					if (isset($p['useButtons']))
						$pod['useButtons'] = $p['useButtons'];
					else
						$pod['useButtons'] = true;
					if (isset($this->useMemberFilters) && $this->useMemberFilters)
						$pod['useMemberFilters'] = $this->useMemberFilters;
					else
						$pod['useMemberFilters'] = false;
					if (isset($this->useSiteFilters) && $this->useSiteFilters)
						$pod['useSiteFilters'] = $this->useSiteFilters;
					else
						$pod['useSiteFilters'] = false;
				break;
				default:
					$pod[$p['type']] = $this->gen_url($p['type'], 'pod');
			}
		}

		if (isset($this->useMemberFilters))
			$pod['useMemberFilters'] = $this->useMemberFilters;
		if (isset($this->useButtons))
			$pod['useButtons'] = $this->useButtons;
		if (isset($this->useSiteFilters))
			$pod['useSiteFilters'] = $this->useSiteFilters;
		if (isset($this->useCSVExport))
			$pod['useCSVExport'] = $this->useCSVExport;

		return $pod;
	}

	function gen_url($type, $view) {
		$url = '';
		switch ($type) {
			case 'dataTable':
				$url = "{$this->BASE_URL}&pod={$this->id}&pod_action=load_all&view={$view}";
			break;
			case 'ajax':
				$url = "{$this->BASE_URL}&pod={$this->id}&pod_action=render_ajax&view={$view}";
			break;
			case 'chart':
				$url = "{$this->BASE_URL}&pod={$this->id}&pod_action=load_statistics&view={$view}&time=daily";
			break;
			default:
				$url = "{$this->BASE_URL}&pod={$this->id}&pod_action={$type}&view={$view}";
			break;
		}
		return $url;
	}

	function load_schema($view, $type = 'dataTable', $numRows = 10) {
		$fields = '[';
		$labels = '[';
		if (!count($this->fields))
			return false;

		foreach ($this->fields as $name => $field) {
			if ($field['enabled'] && $field[$view]) {
				$fieldtype = $field['type'];
				// **TODO** Add in column types and associated parsers
				$label = addslashes($field['label']);
				switch ($fieldtype) {
					case 'default':
					case 'timestamp':
					case 'text':
					case 'date':
					case 'enum':
					case 'string':
						$fields .= "\"$name\",";
						$labels .= "{key: \"{$name}\", label: \"{$field['label']}\"},";
					break;
					case 'boolean': // **TODO** Set up proper boolean parsing
					case 'number':
						$fields .= "{key: \"{$name}\", parser: \"number\"},";
						$labels .= "{key: \"{$name}\", label: \"{$field['label']}\"},";
					break;
				}
			}
		}
		$fields = substr($fields, 0, -1);
		$labels = substr($labels, 0, -1);
		$fields .= ']';
		$labels .= ']';

		if ($view == 'full')
			$numRows = $this->numFullRows;
		else if ($view == 'pod')
			$numRows = $this->numPodRows;

		$datasource = $this->gen_url($type, $view);
		//$fields = addslashes($fields);
		$this->currFields = $fields;
		//$labels = addslashes($labels);
		$this->currLabels = $labels;
		$data = array(
			'fields' => $fields,
			'labels' => $labels,
			'datasource' => $datasource,
			'numRows' => $numRows
		);
		return json_encode($data);
		//return "{ \"fields\" : \"$fields\", \"labels\" : \"$labels\", \"datasource\" : \"$datasource\", \"numRows\": \"$numRows\" }";
	}

	function load_all($conditions = false) {
		return $this->model->load_all($conditions);
	}

	function load_item($id = 0, $view = 'pod') {
		if ($id == 0)
			return false;
		else
			return json_encode($this->model->load_item($id, $view, $this->podData));
	}

	function load($view) {
		return json_encode(array('body' => $this->podData['body']));
	}

	function render_ajax() {
		return "<h1>".$this->podData['body']."</h1>";
	}
}

?>

