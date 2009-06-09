<script type="text/javascript">
var currType = 'actions';
var currTime = 'day';

function mouseOverButton(el) {
	el.className = "yui-button yui-push-button yui-button-hover yui-push-button-hover";
	if (currType + '-button' == el.id || currTime + '-button' == el.id) {
		el.className = el.className + ' button-selected';
	}
}

function mouseOutButton(el) {
	el.className = "yui-button yui-push-button";
	if (currType + '-button' == el.id || currTime + '-button' == el.id) {
		el.className = el.className + ' button-selected';
	}
}

function setBackgroundColors(reset) {
	if (reset == null) {
		reset = false;
	}
	if (reset) {
		YAHOO.util.Dom.setStyle(currType+'-statbutton', "backgroundColor", "transparent");
		YAHOO.util.Dom.setStyle(currTime+'-statbutton', "backgroundColor", "transparent");
	} else {
		YAHOO.util.Dom.setStyle(currType+'-statbutton', "backgroundColor", "#1273F3");
		YAHOO.util.Dom.setStyle(currTime+'-statbutton', "backgroundColor", "#1273F3");
	}
}
</script>
<h1>Statistics</h1>

<div id="buttons">
	<span id="members-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="members-statbutton" name="button1" value="Member Statistics" onclick="loadStats('members', currTime); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">Member Statistics</button></span>
	<span id="actions-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="actions-statbutton" name="button2" value="Action Statistics" onclick="loadStats('actions', currTime); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">Action Statistics</button></span>
	<span id="sessions-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="sessions-statbutton" name="button2" value="Session Statistics" onclick="loadStats('sessions', currTime); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">Session Statistics</button></span>
	<span class="spacer" style="padding-left: 100px;">&nbsp;</span>
	<span id="day-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="day-statbutton" name="button3" value="Daily" onclick="loadStats(currType, 'day'); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">Daily</button></span>
	<span id="week-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="week-statbutton" name="button4" value="Weekly" onclick="loadStats(currType, 'week'); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">Weekly</button></span>
	<span id="month-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="month-statbutton" name="button5" value="Monthly" onclick="loadStats(currType, 'month'); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">Monthly</button></span>
	<span id="all-button" class="yui-button yui-push-button"><button class="first-child" type="button" id="all-statbutton" name="button6" value="All Time" onclick="loadStats(currType, 'all'); return false;" onmouseover="mouseOverButton(this.parentNode);" onmouseout="mouseOutButton(this.parentNode);">All Time</button></span>
</div>



<h1 id="chartsHeader"></h1>
<div id="chart"></div>
<div id="chart2"></div>
<div id="statistics">
<script type="text/javascript">
function loadStats(type, time) {
	setBackgroundColors(true);
	currType = type;
	currTime = time;

	YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.6.0/build//charts/assets/charts.swf";

//--- data

	var jsonData = new YAHOO.util.DataSource('index.php?p=load_statistics&type='+type+'&time='+time);
	jsonData.connMethodPost = true;
	jsonData.responseType = YAHOO.util.DataSource.TYPE_JSON;
	jsonData.responseSchema =
	{
		resultsList : "Stats.Totals",
		//resultsList : "Results",
		fields : [
			"name",
			{key : "count", parser: "number"},
			"label"
		],
		metaFields : {
			actionName : "Stats.ActionName"
			//totalActions : "Stats.Total"
		}
	};
	jsonData.doBeforeCallback = function(oRequest, oFullResponse, oParsedResponse) {
		var header = document.getElementById('chartsHeader');
		header.innerHTML = oParsedResponse.meta.actionName;

		return oParsedResponse;
	}

//--- chart

	if (type != 'sessions') {
		var chart1 = document.getElementById('chart');
		var chart2 = document.getElementById('chart2');
		chart1.style.width = "500px";
		chart1.style.height = "350px";
		chart2.style.width = "500px";
		chart2.style.height = "350px";
		var mychart = new YAHOO.widget.PieChart( "chart", jsonData,
		{
			dataField: "count",
			categoryField: "label",
			style:
			{
				padding: 20,
				legend:
				{
					display: "right",
					padding: 10,
					spacing: 5,
					font:
					{
						family: "Arial",
						size: 13
					}
				}
			}//,
			//only needed for flash player express install
			//expressInstall: "assets/expressinstall.swf"
		});

		var seriesDef = [{xField: "count"}];

		var mychart2 = new YAHOO.widget.BarChart("chart2", jsonData,
		{
			series: seriesDef,
			yField: "label"
		});
	} else {
		var chart1 = document.getElementById('chart');
		var chart2 = document.getElementById('chart2');
		chart1.style.width = "700px";
		chart1.style.height = "350px";
		chart2.innerHTML = '';
		chart2.style.width = "0px";
		chart2.style.height = "0px";

		getDataTipText = function( item, index, series )
		{
			var toolTipText = "Sessions statistics for " + item.name;
			toolTipText += "\n" + item["count"] + " active users.";
			return toolTipText;
		}
		
		var mychart = new YAHOO.widget.LineChart("chart", jsonData,
		{
			xField: "name",
			yField: "count",
			dataTipFunction: getDataTipText
		});
	}


	setBackgroundColors();

}

</script>

</div>
