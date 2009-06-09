<?php

class MainController extends AppController {
	var $name = 'Main';
	public function editorial() {
			$this->render('not_implemented');
	}

	public function membership() {
			$this->render('not_implemented');
	}

	public function site() {
			$this->render('not_implemented');
	}

	public function index() {
			//$this->render('not_implemented');
			$this->render('dashboard');
	}

	public function load() {
		global $filters;
		global $groups;

		$jsonData = array();
		$pods = $this->db->get_pods();
		foreach ($pods as $podname => $pod) {
			$podh = load_pod($pod);
			$jsonData[] = $podh->gen_pod();
		}
		if (count($jsonData)) {
			if ($new_js) {
				$fullData = array('Pods' => $jsonData);
				$fullData['filters'] = $filters;
				$fullData['groups'] = $groups;
				echo json_encode($fullData);
			} else {
				echo json_encode($jsonData);
			}
		} else {
		}
		$this->skip_render = true;
	}

}

?>
