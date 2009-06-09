YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.6.0/build//charts/assets/charts.swf";


function loadStats(type, time) {
	var jsonData = new YAHOO.util.DataSource('http://hotdish.newsreel.org/?p=load_statistics&type='+type+'&time='+time);
	jsonData.responsType = YAHOO.util.DataSource.TYPE_JSON;
	jsonData.responseSchema =
	{
		resultsList : "Stats.Totals",
		fields : [
			"name",
			{key : "count", parser: "number"},
			"label"
		],
		metaFields : {
			totalActions : "Stats.Total"
		}
	};
