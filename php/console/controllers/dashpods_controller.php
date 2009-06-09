<?php

Class DashpodsController extends AppController {
	var $name = 'Dashpods';
	public function index() {
		$this->render();
	}

	public function main() {
		$pod_list = array('ActionStats', 'SessionLength', 'UserReport', 'MemberStats', 'MostReadStories');

		echo $this->build_pods_json($pod_list);
	}

	public function stats() {
		$pod_list = array('MemberStats', 'ActionStats', 'SessionStatistics', 'SessionLength');

		echo $this->build_pods_json($pod_list);
	}

	public function stories() {
		$pod_list = array('TopRatedStories', 'MostReadStories', 'MostDiscussedStories', 'MostShared', 'StoryReport');

		echo $this->build_pods_json($pod_list);
	}

	public function geo() {
		$pod_list = array('CityDistribution', 'CountryDistribution', 'StateDistribution');

		echo $this->build_pods_json($pod_list);
	}

	public function members() {
		$pod_list = array('ActionStats', 'MemberStats', 'AgeDistribution', 'GenderDistribution', 'EmailDistribution', 'InterestDistribution', 'UserReport', 'TotalMembers');

		echo $this->build_pods_json($pod_list);
	}

	public function challenges() {
		$pod_list = array('ChallengeReport', 'ChallengeChart');

		echo $this->build_pods_json($pod_list);
	}

	public function misc() {
		$pod_list = array('ActionStats');

		echo $this->build_pods_json($pod_list);
	}

	private function build_pods_json($pod_list = false) {
		if (!$pod_list || !is_array($pod_list)) return false;


		global $poddb;
		$jsonData = array();
		$pods = $poddb->get_pods();
		foreach ($pods as $podname => $pod) {
			if (in_array($podname, $pod_list)) {
				$podh = load_pod($pod);
				$jsonData[] = $podh->gen_pod();
			} else {
				continue;
			}
		}
		if (count($jsonData)) {
			if (isset($new_js)) {
				$fullData = array('Pods' => $jsonData);
				$fullData['filters'] = $filters;
				$fullData['groups'] = $groups;
				return json_encode($fullData);
			} else {
				return json_encode($jsonData);
			}
		}
	}

	protected function before_filter() {
		if (!isset($this->params['load']))
			$this->base_page();
	}

	protected function base_page() {
		$this->render('base_page');
		exit;
	}

}

?>
