<?php

class parseStory {
	var $url;

	function parseStory($url) {
		if (!preg_match('/^http:\/\//', $url))
			$url = 'http://'.$url;
		$this->url = $url;
	}

	function parse() {
		require_once(PATH_CORE.'/classes/remotefile.class.php');
		$rfObj = new remotePageProperty($this->url);
		$data = array();
		$data['title'] = $rfObj->getPageTitle();

		$page = $rfObj->page_content;
		if (preg_match('/<meta name="description"[^>]*content="([^"]+)"/i', $page, $match))
			$data['description'] = $match[1];
		else
			$data['description'] = '';

		$imgArr = $rfObj->getPageImages();
		$images = array();
		foreach ($imgArr as $index => $image) {
			$size = getimagesize($image);
			if ($size[0] < 75 || $size[1] < 50)
				continue;
			$images[] = $image;
		}
		$data['images'] = $images;

		return $this->jsonData($data);
	}

	function jsonData($data) {
		return json_encode($data);
	}

}

?>
