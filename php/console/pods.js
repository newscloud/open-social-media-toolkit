var Dom = YAHOO.util.Dom;
var Event = YAHOO.util.Event;
var DDM = YAHOO.util.DragDropMgr;
var podmodcontainer = {};
var ClassIntervalCalendar
var currTime = 'day';
var currStartDate = false;
var currEndDate = false;
var foo;
var BASE_URL = "index.php?p=console";
var SITE_FILTERS_ENABLED = false;
if (CURR_SITE_ID === 0) {
	SITE_FILTERS_ENABLED = true;
}


var customDatePicker = function(podid) {
	function updateDateHandler() {
		var start = Dom.get('startDate');
		var end   = Dom.get('endDate');
		caldialog.hide();
		updateStats(podid, 'custom', start.value, end.value);
	}

	function closeDateHandler() {
		caldialog.hide();
	}

	var caldialog = new YAHOO.widget.Dialog("calPanel",
							{ width: "420px", 
								//height: "500px",
								context:["custom-button", "tl", "bl"],
								buttons:[ {text: "Update", handler: updateDateHandler, isDefault:true}, {text:"Cancel", handler: closeDateHandler}],
								//fixedcenter: true, 
								close: true, 
								draggable: false, 
								zindex:27,
								modal: false,
								visible: false
							} );
	caldialog.setHeader('Select a date or range');
	caldialog.setBody('<div id="calSelect"></div><div id="dates"><p><label for="startDate">Start Date:</label><input type="text" name="startDate" id="startDate" /></p><p><label for="endDate">End Date:</label><input type="text" name="endDate" id="endDate" /></p></div>');
	caldialog.render(document.body);
    var inTxt = YAHOO.util.Dom.get("startDate"),
        outTxt = YAHOO.util.Dom.get("endDate"),
        inDate, outDate, interval;

    inTxt.value = "";
    outTxt.value = "";

    //var cal = new YAHOO.example.calendar.IntervalCalendar("cal1Container", {pages:2});
    var cal = new ClassIntervalCalendar("calSelect", {pages:2});

    cal.selectEvent.subscribe(function() {
        interval = this.getInterval();

        if (interval.length == 2) {
            inDate = interval[0];
            var inDay = String(inDate.getDate());
            var inMonth = String(inDate.getMonth() + 1);
            if (inDay.length < 2) {
            	inDay = "0" + inDay;
						}
						if (inMonth.length < 2) {
							inMonth = "0" + inMonth;
						}
            //inTxt.value = (inDate.getMonth() + 1) + "-" + inDate.getDate() + "-" + inDate.getFullYear();
            //inTxt.value = inDate.getFullYear() + "-" + (inDate.getMonth() + 1) + "-" + inDate.getDate();
            inTxt.value = inDate.getFullYear() + "-" + inMonth + "-" + inDay;

            if (interval[0].getTime() != interval[1].getTime()) {
							outDate = interval[1];
							var outDay = String(outDate.getDate());
							var outMonth = String(outDate.getMonth() + 1);
							if (outDay.length < 2) {
								outDay = "0" + outDay;
							}
							if (outMonth.length < 2) {
								outMonth = "0" + outMonth;
							}
							outTxt.value =  outDate.getFullYear() + "-" + outMonth + "-" + outDay;
            } else {
                outTxt.value = "";
            }
        }
    }, cal, true);
    
    cal.render();
	caldialog.show();
}	


var chart = function() {
	var chartcontainer = {
		//dataTable : new YAHOO.util.DataSource.TYPE_JSON,
		//jsonData : new YAHOO.util.DataSource('index.php?pod='+podid+'&action=load_statistics&time='+time+'&view='+view),
		jsonData : new YAHOO.util.DataSource(BASE_URL+'&pod='+podid+'&pod_action=load_statistics&time='+time+'&view='+view),
		barChart : new YAHOO.widget.BarChart('chart', jsonData,
		{
			//series: seriesDef,
			xField: "count",
			yField: "label"
		}),
	};
	chartcontainer.jsonData.connMethodPost = true,

	chartcontainer.jsonData.responseSchema =
	{
		resultsList : "Stats.Totals",
		//resultsList : "Results",
		fields : [
			"name",
			{key : "count", parser: "number"},
			"label"
		],
		metaFields : {
			actionName : "Stats.ActionName",
			chartType  : "Stats.ChartType"
			//totalActions : "Stats.Total"
		}
	};

};


var updateStats = function(podid, time, startDate, endDate) {
	if (typeof(time) == 'undefined' || time == null || time == 'none') {
		time = currTime;
		startDate = currStartDate;
		endDate = currEndDate;
	} else {
		currTime = time;
		currStartDate = startDate;
		currEndDate   = endDate;
	}
	var title = Dom.get(podid+'title-full');
	var useMemberFilters = false;
	var useSiteFilters = false;
	if (podmodcontainer[podid].useMemberFilters) {
		useMemberFilters = true;
		if (podmodcontainer[podid].useSiteFilters) {
			useSiteFilters = true;
		}
	}
	if (title != null) {
		title.innerHTML = '<h3>Loading...</h3>';
	}
	function fullMonth(date) {
		var m = String(date.getMonth() + 1);
		if (m.length < 2) {
			m = "0" + m;
		}
		return m;
	}
	function fullDay(date) {
		var d = String(date.getDate());
		if (d.length < 2) {
			d = "0" + d;
		}
		return d;
	}
	var startTime = new Date();
	var start     = '';
	var endTime   = false;
	var end       = false;
	switch (time) {
		case 'week':
			startTime.setDate(startTime.getDate() - 7); 
			start = startTime.getFullYear()+'-'+fullMonth(startTime)+'-'+fullDay(startTime);
		break;
		case 'month':
			start = startTime.getFullYear()+'-'+fullMonth(startTime)+'-01';
		break;
		case 'custom':
			start = startDate;
			end   = endDate;
		break;
		case 'all':
			start = '1984-05-22';
		break;
		case 'day':
		case 'default':
			if (podid == 'pod33') {
				start = '2009-01-01';
			} else {
				start = startTime.getFullYear()+'-'+fullMonth(startTime)+'-'+fullDay(startTime);
			}
	}
	currStartDate = startDate;

	//var dataSourceUrl = 'index.php?pod='+podid+'&action=load_statistics&view=full&startDate='+start;
	var dataSourceUrl = BASE_URL+'&pod='+podid+'&pod_action=load_statistics&view=full&startDate='+start;
	var dataSourceParams = 'startDate=' + start;
	var match_pod = podid.match(/^(pod([0-9]+))/);
	var dopid = match_pod[2];
	var atag_href = BASE_URL+"&pod="+dopid+"&pod_action=load_all&view=csv&startDate="+start;
	if (end) {
		currEndDate = endDate;
		dataSourceUrl = dataSourceUrl + '&endDate=' + end;
		dataSourceParams = dataSourceParams + '&endDate=' + end;
		atag_href = atag_href + '&endDate=' + end;
	}
	if (useMemberFilters) {
		var membersOnly = Dom.get('membersOnly');
		var teamOnly = Dom.get('teamEligibleOnly');
		var siteId = Dom.get('siteId');
		//if (podmodcontainer[podid].podnameheader != 'Session Statistics' && podmodcontainer[podid].podnameheader != 'Story Report') {
		if (podid != 'pod13' && podid != 'pod37' && podid != 'pod38' && podid != 'pod40') {
			if (membersOnly.checked) {
				dataSourceUrl = dataSourceUrl + '&membersOnly=true';
				dataSourceParams = dataSourceParams + '&membersOnly=true';
				atag_href = atag_href + '&membersOnly=true';
			}
			if (teamOnly.checked) {
				dataSourceUrl = dataSourceUrl + '&teamEligibleOnly=true';
				dataSourceParams = dataSourceParams + '&teamEligibleOnly=true';
				atag_href = atag_href + '&teamEligibleOnly=true';
			}
		}
		if (useSiteFilters && siteId.selectedIndex != 0) {
			dataSourceUrl = dataSourceUrl + '&siteid=' + siteId.value;
			dataSourceParams = dataSourceParams + '&siteid=' + siteId.value;
			atag_href = atag_href + '&siteid=' + siteId.value;
		}
	}

	var jsonData = new YAHOO.util.DataSource(dataSourceUrl);
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
			actionName : "Stats.ActionName",
			chartType  : "Stats.ChartType"
			//totalActions : "Stats.Total"
		}
	};
	var pod = podmodcontainer[podid];
	var dt = pod.podDataTable;
	var ct = pod.podchart;
	//pod.podDataTable.getDataSource().replace(jsonData);
	//pod.podDataTable.render();
	//dt.getDataSource().sendRequest(dataSourceParams, dt.onDataReturnInitializeTable, dt);
	dt.getDataSource().sendRequest(dataSourceParams, {
		success: dt.onDataReturnInitializeTable,
		scope: dt,
		argument: dt.getState()
	});
	var atag_ele = Dom.get(podid+'-csv-export');
	if (podmodcontainer[podid].useCSVExport) {
		atag_ele.href = atag_href;
	}
	if (pod.isDataTable) {
		//pod.fulldt.getDataSource().sendRequest(dataSourceParams, pod.fulldt.onDataReturnInitializeTable, pod.fulldt);
		pod.fulldt.getDataSource().sendRequest(dataSourceParams, {
			success: pod.fulldt.onDataReturnInitializeTable,
			scope: pod.fulldt,
			argument: pod.fulldt.getState()
		});
	}
	//ct.set('dataSource', dt.getDataSource().sendRequest('time='+time, dt.onDataReturnInitializeTable, dt));
	if (pod.isChart) {
		ct.set('dataSource', jsonData);
	}
	//ct.dataSource.sendRequest('time='+time, ct.onDataReturnInitializeTable, ct);
}

var updateStatsFN = function(podid, time) {
	return function (e) {
		updateStats(podid, time);
	}
}

var customDatePickerFN = function(podid) {
	return function (e) {
		customDatePicker(podid);
	}
}

var loadStats = function(podid, view, time, url) {
	var times = {
		'all' : 'All Time',
		'day' : 'Daily',
		'week' : 'Weekly',
		'month' : 'Month',
		'custom' : 'Custom'
	}

	if (view == 'full' && podmodcontainer[podid].useMemberFilters) {
		var filters = {
			'membersOnly'				: ' All Members',
			'teamEligibleOnly'	: ' Inside Study',
		}
		var selectFilters = {
			'siteId' : {'name' : 'Select Site ', 'options' : {'0' : 'All Sites', '1' : 'Hot Dish', '2' : 'MnDaily', '3' : 'Chi-Town Daily', '4' : 'In:Site'}}
		}
		foo = selectFilters;
		var filtersdiv = Dom.get(podid+'-filters-full');
		var fields = document.createElement('fieldset');
		fields.className = 'filters-fieldset';
		var legend = document.createElement('legend');
		legend.appendChild(document.createTextNode('Members Filters'));
		fields.appendChild(legend);
		var ul = document.createElement('ul');

		if (view == 'full' && podmodcontainer[podid].useSiteFilters && SITE_FILTERS_ENABLED) {
			for (i in selectFilters) {
				var li = document.createElement('li');
				var select = document.createElement('select');
				select.id = i;
				Event.addListener(select, 'change', updateStatsFN(podid, 'none') );
				//Event.addListener(select, 'change', function() {alert('Select: '+Dom.get('siteId').selectedIndex);});
				var label = document.createElement('label');
				label.htmlFor = i;
				label.appendChild(document.createTextNode(selectFilters[i]['name']));
				for (j in selectFilters[i]['options']) {
					var option = document.createElement('option');
					option.value = j;
					option.text = selectFilters[i]['options'][j];
					select.appendChild(option);
				}
				li.appendChild(label);
				li.appendChild(select);
				ul.appendChild(li);
			}
		}

		//if (podmodcontainer[podid].podnameheader != 'Session Statistics' && podmodcontainer[podid].podnameheader != 'Story Report') {
		if (podid != 'pod13' && podid != 'pod37' && podid != 'pod38' && podid != 'pod40') {
			for (i in filters) {
				var li = document.createElement('li');
				var checkbox = document.createElement('input');
				checkbox.type = 'checkbox';
				checkbox.value = i;
				checkbox.id = i;
				Event.addListener(checkbox, 'click', updateStatsFN(podid, 'none') );
				var label = document.createElement('label');
				label.htmlFor = i;
				label.appendChild(document.createTextNode(filters[i]));
				li.appendChild(checkbox);
				li.appendChild(label);
				ul.appendChild(li);
				//fields.appendChild(document.createElement('br'));
			}
		}

		fields.appendChild(ul);
		filtersdiv.appendChild(fields);
	}

	if (view == 'full' && podmodcontainer[podid].useButtons) {
		var buttons = Dom.get(podid+'-buttons-full');

		for (i in times) {
			var span = document.createElement('span');
			span.id = i+'-button';
			span.className = "yui-button yui-push-button";
			var button = document.createElement('button');
			button.type = 'button';
			button.id = i+'-statbutton';
			button.className = 'first-child';
			button.name = i+'button';
			button.value = times[i];
			button.appendChild(document.createTextNode(times[i]));

			Event.addListener(button, 'mouseover', function (e) {
								var el = Event.getTarget(e).parentNode;
								Dom.addClass(el, 'yui-button-hover yui-push-button-hover');
							});

			Event.addListener(button, 'mouseout', function (e) {
								var el = Event.getTarget(e).parentNode;
								Dom.removeClass(el, 'yui-button-hover yui-push-button-hover');
							});

			if (i != 'custom') {
				Event.addListener(button, 'click', updateStatsFN(podid, i) );
			} else {
				Event.addListener(button, 'click', customDatePickerFN(podid) );
			}

			span.appendChild(button);
			buttons.appendChild(span);
		}
	}

	if (time == null) {
		if (podid == 'pod33') {
			time = 'all';
		} else {
			time = 'daily';
		}
	}
	if (view == null)
		view = 'pod';
	//setBackgroundColors(true);
	//currType = type;
	//currTime = time;
	var type = false;
	var chartid = podid+'-chart-'+view;
	var podmod = podmodcontainer[podid];
	if (typeof(podmod.chartType) != 'undefined')
		type = podmod.chartType;

	YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.7.0/build/charts/assets/charts.swf";

//--- data

	//var jsonData = new YAHOO.util.DataSource('index.php?p=load_statistics&type='+type+'&time='+time);
	//var jsonData = new YAHOO.util.DataSource('index.php?pod='+podid+'&action=load_statistics&time='+time+'&view='+view);
	//var jsonData = new YAHOO.util.DataSource('index.php?pod='+podid+'&action=load_statistics&view='+view);
	var jsonData = new YAHOO.util.DataSource(BASE_URL+'&pod='+podid+'&pod_action=load_statistics&view='+view);
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
			actionName : "Stats.ActionName",
			chartType  : "Stats.ChartType"
			//totalActions : "Stats.Total"
		}
	};
	jsonData.doBeforeCallback = function(oRequest, oFullResponse, oParsedResponse) {
		//var header = document.getElementById(podid+'-chartsHeader');
		if (view == 'full') {
			var title = Dom.get(podid+'title-full');
			title.innerHTML = '<h2>'+oParsedResponse.meta.actionName+'</h2>';
		}
		//header.innerHTML = oParsedResponse.meta.actionName;
		jsonData.type = oParsedResponse.meta.chartType;

		return oParsedResponse;
	}

//--- chart
	if (type == 'pie') {
		var config = 
			{
				dataField: "count",
				categoryField: "label",
				style:
				{
					padding: 5,
				}
			};
		if (view == 'full') {
			config.style.padding = 20;
			config.style.legend = 
					{
						display: "right",
						padding: 10,
						spacing: 5,
						font:
						{
							family: "Arial",
							size: 13
						}
					};
		}
		var mychart = new YAHOO.widget.PieChart(chartid, jsonData, config);
		/*
		var mychart = new YAHOO.widget.PieChart(chartid, jsonData,
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
		*/
	} else if (type == 'bar') {
		//var seriesDef = [{xField: "count"}];

		var mychart = new YAHOO.widget.BarChart(chartid, jsonData,
		{
			//series: seriesDef,
			xField: "count",
			yField: "label"
		});
	} else if (type == 'line') {
		var mychart = new YAHOO.widget.LineChart(chartid, jsonData,
		{
			xField: "name",
			yField: "count",
			//dataTipFunction: getDataTipText
		});
	} else {
		var mychart = false;
	}


	if (view == 'full') {
		//var dataTableid = podid+'-dataTable-full';
		var dataTableid = '';
		if (podmodcontainer[podid].isDataTable) {
			dataTableid = 'full-view-content';
		} else {
			dataTableid = podid+'-dataTable-full';
		}

		var dataTable = new YAHOO.widget.DataTable(dataTableid,
						[ {key: 'label', sortable: true},
						  {key: 'count', sortable: true}
						],
						jsonData,
						{} );
	}
	podmodcontainer[podid].podchart = mychart;
	podmodcontainer[podid].podDataTable = dataTable;

	/*
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


	//setBackgroundColors();
	*/

}

var podschemacallbacks = function (podid) {
	return {
	success : function(o) {
				try {
						var data = YAHOO.lang.JSON.parse(o.responseText);
				}
				catch (x) {
						alert("JSON Parse failed!");
						return;
				}

				var dt = genDataTable('full-view', data.datasource, data.fields, data.labels, data.numRows);
				podmodcontainer[podid].fulldt = dt;
	}
	}
}

var fullloadcallbacks = function (podid) {
	return {
	success : function(o) {
				var podmod = podmodcontainer[podid];
				try {
						var data = YAHOO.lang.JSON.parse(o.responseText);
				}
				catch (x) {
						alert("JSON Parse failed!");
						return;
				}

				//var pod = data.podid;
				//var podmod = podmodcontainer.pod;
				//alert(podid);
				var content = Dom.get('full-view-content');
				if (podmod.isAjaxMulti) {
					content.innerHTML = data.html;
					eval(data.js);
				} else if (podmod.isChart) {
					content.innerHTML = data.html;
					loadstats(podid, 'full');
				} else {
					content.innerHTML = data.body;
				}
	}
	}
}

function addNav(name, podid) {
	var dashpodnav = Dom.get('dashpod-nav');
	var li = document.createElement('li');
	var atag = document.createElement('a');
	atag.href = "#";
	//atag.onclick = "maxView("+podid+"); return false;";
	var fnCallback = function(e, obj) {
		Event.preventDefault(e);
		maxView(podid);
	}
	Event.addListener(atag, 'click', fnCallback);
	var linktext = document.createTextNode(name);
	atag.appendChild(linktext);
	li.appendChild(atag);
	dashpodnav.appendChild(li);
}

function maxView(id) {
	var tmp = Dom.get('full-view');
	var match_pod = id.match(/^(pod([0-9]+))/);
	var dopid = match_pod[2];
	if (tmp != null) {
		var childhd = tmp.firstChild;
		var match = childhd.id.match(/^(pod([0-9]+))/);
		minView(match[1]);
		//tmp.parentNode.removeChild(tmp);
	}
	var podmod = podmodcontainer[id];
	var dashpods = Dom.get('dashpods');
	Dom.setStyle(id+'-pod-menu', 'display', 'none');
	Dom.setStyle(id+'-full-menu', 'display', 'block');
	var idiv = document.createElement('div');
	var menu = Dom.get(id+'header');
	idiv.id = 'full-view';
	idiv.style.width = 'auto';
	idiv.style.height = 'auto';
	idiv.appendChild(menu);
	var pagetopdiv = document.createElement('div');
	pagetopdiv.id = 'full-view-paging-top';
	pagetopdiv.style.textAlign = 'center';
	var pagebottomdiv = document.createElement('div');
	pagebottomdiv.id = 'full-view-paging-bottom';
	pagebottomdiv.style.textAlign = 'center';
	var cdiv = document.createElement('div');
	cdiv.id = 'full-view-content';
	if (podmod.isChart || podmod.isDataTable) {
		var buttondiv = document.createElement('div');
		buttondiv.id = id+'-buttons-full';
		buttondiv.className = 'buttons-full';
		cdiv.appendChild(buttondiv);
		var filterdiv = document.createElement('div');
		filterdiv.id = id+'-filters-full';
		filterdiv.className = 'filters-full';
		cdiv.appendChild(filterdiv);
		if (podmod.useCSVExport) {
			var infodiv = document.createElement('div');
			infodiv.id = id+'info-full';
			infodiv.className = 'info-full';
			var atag = document.createElement('a');
			atag.id = id+'-csv-export';
			//atag.href = "http://research.newscloud.com/index.php?pod="+dopid+"&action=load_all&view=csv";
			atag.href = BASE_URL+"&pod="+dopid+"&pod_action=load_all&view=csv";
			//atag.href = "#";
			atag.target = "_csv";
			atag.appendChild(document.createTextNode('Export file to csv'));
			infodiv.appendChild(atag);
			//infodiv.appendChild(atag.appendChild(document.createTextNode('Export file to csv.')));
			//cdiv.appendChild(infodiv);
		}
		var titlediv = document.createElement('div');
		titlediv.id = id+'title-full';
		titlediv.className = 'title-full';
		titlediv.appendChild(document.createTextNode('Loading...'));
		cdiv.appendChild(titlediv);
		if (podmod.isChart) {
			var chartdiv = document.createElement('div');
			chartdiv.id = id+'-chart-full';
			chartdiv.className = 'chart-full';
			cdiv.appendChild(chartdiv);
		}
		var dataTablediv = document.createElement('div');
		dataTablediv.id = id+'-dataTable-full';
		dataTablediv.className = 'dataTable-full';
		cdiv.appendChild(dataTablediv);
	}
	idiv.appendChild(pagetopdiv);
	idiv.appendChild(cdiv);
	idiv.appendChild(pagebottomdiv);
	var full = document.createElement('iframe');
	Dom.setStyle('dashpods', 'display', 'none');
	//full.src = 'foo.php';
	//full.src = 'index.php?action=load&view=full&id='+id;
	full.src = BASE_URL+'&pod_action=load&view=full&id='+id;
	full.style.width = '100%';
	full.style.height = '1000px';
	//idiv.appendChild(full);
	Dom.insertAfter(idiv, dashpods);
	//alert('Found pod '+id)
	//YAHOO.util.Connect.asyncRequest('GET',"index.php?ctrl=main&action=load_schema&id="+id, podschemacallbacks(id));
	if (podmod.isDataTable) {
		Dom.insertAfter(titlediv, Dom.get('full-view').firstChild);
		if (podmod.useCSVExport) {
			Dom.insertAfter(infodiv, Dom.get('full-view').firstChild);
		}
		Dom.insertAfter(filterdiv, Dom.get('full-view').firstChild);
		Dom.insertAfter(buttondiv, Dom.get('full-view').firstChild);
		//YAHOO.util.Connect.asyncRequest('GET',"index.php?pod="+id+"&action=load_schema&id="+id+"&view=full", podschemacallbacks(id));
		YAHOO.util.Connect.asyncRequest('GET',BASE_URL+"&pod="+id+"&pod_action=load_schema&id="+id+"&view=full", podschemacallbacks(id));
		loadStats(id, 'full');
	} else if (podmod.isChart) {
		loadStats(id, 'full');
	} else {
		YAHOO.util.Connect.asyncRequest('GET', BASE_URL+"&pod="+id+"&pod_action=load&view=full", fullloadcallbacks(id));
	}
}

function minView(id) {
	var dashpods = Dom.get('dashpods');
	var iframeview = Dom.get('full-view');
	var header = Dom.get(id+'header');
	var pod = Dom.get(id);
	Dom.insertBefore(header, pod.firstChild);
	iframeview.parentNode.removeChild(iframeview);
	Dom.setStyle('dashpods', 'display', 'block');
	Dom.setStyle(id+'-full-menu', 'display', 'none');
	Dom.setStyle(id+'-pod-menu', 'display', 'block');
}

function genDataTable(id, datasource, fields, labels, numRows) {
	var match = false;
	var podid = 0;
	if ((match = id.match(/^(pod[0-9]+)/))) {
		podid = match[1];
	}
	if (numRows == null) {
		numRows = 8;
	}
	var labelsObj = eval(labels);
	var fieldsObj = eval(fields);
	//  create the datasource and point it at the webservice's FetchAll method
	var myDataSource = new YAHOO.util.DataSource(datasource, { connMethodPost: true });

	//  set the content-type to JSON
	myDataSource.connMgr = YAHOO.util.Connect;
	//myDataSource.connMgr.initHeader('Content-Type', 'application/json; charset=utf-8', true);
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;            

	//  setup the response schema
	myDataSource.responseSchema = 
	{ 
			resultsList : 'Results.data',
			fields: fieldsObj,
			//metaFields : { responseCode: 'responseCode', responseText: 'responseText' }                 
	};

	//  configure the data pager
	var myPaginator = new YAHOO.widget.Paginator({
			//  2 data pagers - both top and bottom
			containers : [id+'-paging-top', id+'-paging-bottom'],
			pageLinks : 5,
			rowsPerPage : numRows,
			template : "{PreviousPageLink} {PageLinks} {NextPageLink}"
	});                       

	//  setup the table settings
	var myTableConfig = {
			//  JSON object that maps to the parameters of my WebMethod
			//initialRequest : '{startIndex:0, pageSize:15, tableName:"<%= this.table.Name %>"}',
			initialRequest : '{startIndex:0, pageSize:15}',
			// A custom function to translate the js paging request into a JSON
			/*
			generateRequest : function(state, dt){
					return '{startIndex:' + state.pagination.recordOffset + ', pageSize:' + state.pagination.rowsPerPage + ', tableName:"<%= this.table.Name %>"}'
			},
			*/
			paginator : myPaginator,
			paginationEventHandler : YAHOO.widget.DataTable.handleDataSourcePagination
	};

	//  create the datatable
	//this.myDataTable = new YAHOO.widget.DataTable(id, labelsObj, myDataSource, myTableConfig);
	var dt = new YAHOO.widget.DataTable(id+'-content', labelsObj, myDataSource, myTableConfig);
	/*
	dt.on('rowClickEvent', function (rArgs) {
		var target = rArgs.target;
		var record = this.getRecord(target);
		genLoadItemPanel(podid, record.getData('id'));
	});
	*/

	return dt;
}

function genLoadItemPanel(podid, id) {
	var loadItemCallbacks = {"success": newItemPanel};
	if (podid == 0) {
		alert('Pod id of 0!');
		return false;
	}
	YAHOO.util.Connect.asyncRequest('GET',"index.php?pod="+podid+"&action=load_item&id="+id+"&view=full", loadItemCallbacks);
}

var newItemPanel = function(o) {
	try {
			var data = YAHOO.lang.JSON.parse(o.responseText);
	}
	catch (x) {
			alert("JSON Parse failed!");
			return;
	}

	var item = new YAHOO.widget.Panel("item7",
							{ width: "240px", 
								height: "500px",
								//fixedcenter: true, 
								close: true, 
								draggable: true, 
								zindex:7,
								modal: false,
								visible: false
							} );
	item.setHeader(data.header);
	item.setBody(data.body);
	item.setFooter(data.footer);
	item.render(document.body);
	item.show();
};
	
(function() {

var podcount = 0;

var loadpods = function() {
var myLogReader = new YAHOO.widget.LogReader();
myLogReader.collapse();
var wait = false;

if (!wait) {
		wait = new YAHOO.widget.Panel("wait",  
							{ width: "240px", 
								fixedcenter: true, 
								close: false, 
								draggable: false, 
								zindex:7,
								modal: true,
								visible: false
							} 
			);

		//wait.setHeader("Loading, please wait...");
		wait.setHeader("Welcome to MC Yui");
		wait.setBody("Loading, please wait...<br /><br /><img src=\"loading_bar.gif\"/>");
		wait.render(document.body);


}
		wait.show();

var podloadcallbacks = function (podid) {
	return {
	success : function(o) {
				var podmod = podmodcontainer[podid];
				try {
						if (podmod.isAjaxMulti) {
							var data = YAHOO.lang.JSON.parse(o.responseText);
						}
				}
				catch (x) {
						alert("JSON Parse failed!");
						return;
				}

				//var pod = data.podid;
				//var podmod = podmodcontainer.pod;
				//alert(podid);
				if (podmod.isAjaxMulti) {
					podmod.setBody(data.html);
					podmod.render();
					eval(data.js);
					podmodcontainer[podid].chartjs = data.js;
				} else if (podmod.isChart) {
				} else {
					podmod.setBody(o.responseText);
					podmod.render();
				}
	}
	}
}


// Define the callbacks for the asyncRequest
var callbacks = {

		success : function (o) {
				//YAHOO.log("RAW JSON DATA: " + o.responseText);

				// Process the JSON data returned from the server
				var pods = [];
				try {
						pods = YAHOO.lang.JSON.parse(o.responseText);
						podcount = pods.length;
				}
				catch (x) {
						wait.hide()
						alert("JSON Parse failed!");
						return;
				}

				//YAHOO.log("PARSED DATA: " + YAHOO.lang.dump(pods));

				// The returned data was parsed into an array of objects.
				// Add a P element for each received message
				//var col1 = document.getElementById('pod-col-1');
				var cols = [YAHOO.util.Dom.get('pod-col-1'),YAHOO.util.Dom.get('pod-col-2'),YAHOO.util.Dom.get('pod-col-3')];
				var col1 = YAHOO.util.Dom.get('pod-col-1');
				var col2 = YAHOO.util.Dom.get('pod-col-2');
				var col3 = YAHOO.util.Dom.get('pod-col-3');
				if (col1 == null) {
					wait.hide();
					return false;
				}
				for (var i = 1, len = pods.length; i <= len; ++i) {
					var p = pods[i-1];
					// TODO: REMOVE THIS TEMPORARY HACK TO IGNORE DATA PODS
					if (typeof(p.datasource) != 'undefined') {
						if (p.id >= 25 && p.id <= 28) {
							;
						} else if (p.id >= 36) {
							;
						} else {
							continue;
						}
					}
					var podid = 'pod'+p.id;
					var pod = document.createElement('div');
					pod.id = podid;
					//pod.id = podid;
					pod.className = 'pod';
					var j = i % 3;
					//col1.appendChild(pod);
					cols[j].appendChild(pod);
					if (typeof(p.name) == 'undefined') {
						addNav(p.header, podid);
					} else {
						addNav(p.name, podid);
					}
					//var podmod = new YAHOO.widget.Panel('pod-col-1', {visible: true, draggable: true, width: '260px'});
					var podmod = new YAHOO.widget.Module(podid, {visible: true});
					podmod.podname = p.name;
					var header = '<span class="pod-title">'+p.header+'</span>';
					header += '<div id="'+podid+'-pod-menu" class="pod-menu" style="display: block;"><a href="#" class="pod-full-button" onclick="maxView(\''+podid+'\'); return false;"><span class="hidden-button">[^]</span></a></div>';
					header += '<div id="'+podid+'-full-menu" class="full-menu" style="display: none;"><a href="#" class="full-pod-button" onclick="minView(\''+podid+'\'); return false;"><span class="hidden-button-full">[v]</span></a></div>';
					podmod.setHeader(header);
					if (typeof(p.chart) != 'undefined') {
						podmod.setBody('<div id="'+podid+'-chart-pod" class="chart-pod">'+p.body+'</div>');
					} else {
						podmod.setBody(p.body);
					}
					if (typeof(p.footer) != 'undefined') {
						podmod.setFooter(p.footer);
					}
					//podmod.startDrag = startDrag;
					//podmod.onDragEnter = dragEnter;
					//podmod.onDragOut = dragOut;
					//podmod.endDrag = dragEnd;
					podmod.isDataTable = false;
					podmodcontainer[podid] = podmod;
					podmod.render();

					var hd = YAHOO.util.Dom.getFirstChild(pod);
					var bd = hd.nextSibling;
					if (YAHOO.util.Dom.hasClass(hd, 'hd')) {
						hd.id = podid+'header';
					}
					if (YAHOO.util.Dom.hasClass(bd, 'bd')) {
						bd.id = podid+'body';
					}

					if (typeof(p.ajax) != 'undefined') {
						YAHOO.util.Connect.asyncRequest('GET', p.ajax, podloadcallbacks(podid));
					}

					if (typeof(p.chart) != 'undefined') {
						podmodcontainer[podid].isChart = true;
						podmodcontainer[podid].chartType = p.chartType;
						podmodcontainer[podid].useButtons = p.useButtons;
						podmodcontainer[podid].useMemberFilters = p.useMemberFilters;
						podmodcontainer[podid].useSiteFilters = p.useSiteFilters;
						loadStats(podid, 'pod', 'daily', p.chart);
						//YAHOO.util.Connect.asyncRequest('GET', p.chart, podloadcallbacks(podid));
					}

					if (typeof(p.useButtons) != 'undefined') {
						podmodcontainer[podid].useButtons = p.useButtons;
					}

					if (typeof(p.useMemberFilters) != 'undefined') {
						podmodcontainer[podid].useMemberFilters = p.useMemberFilters;
					}

					if (typeof(p.useSiteFilters) != 'undefined') {
						podmodcontainer[podid].useSiteFilters = p.useSiteFilters;
					}

					if (typeof(p.useCSVExport) != 'undefined') {
						podmodcontainer[podid].useCSVExport = p.useCSVExport;
					}

					if (p.header == 'Statistics') {
						podmodcontainer[podid].isAjaxMulti = true;
					}

					if (typeof(p.datasource) != 'undefined') {
						if (podid == 'pod33' || podid == 'pod36' || podid == 'pod37') {
							currTime = 'all';
						} else {
							currTime = 'day';
						}
						podmodcontainer[podid].isDataTable = true;
						podmod.setBody('<div id="'+podid+'body-paging-top" style="text-align: center"></div><div id="'+podid+'body-content">'+p.body+'</div><div id="'+podid+'body-paging-bottom" style="text-align: center"></div>');
						var dt = genDataTable(podid+'body', p.datasource, p.fields, p.labels);
						podmodcontainer[podid].dt = dt;
						podmodcontainer[podid].podDataTable = dt;
					}
					podmodcontainer[podid].podnameheader = p.header;

					//podmod.show();
					//var drag = new YAHOO.util.DDProxy('pod'+i, 'group1');
					//drag.startDrag = startDrag;
					//drag.onDragEnter = dragEnter;
					//drag.onDragOut = dragOut;
					//drag.endDrag = dragEnd;
					//proxies[i] = new YAHOO.util.DDProxy('pod'+i, 'group1');
					//proxies[i] = new YAHOO.util.DDProxy('pod'+i+'header', 'group1');
					//proxies[i].setHandleElId('pod'+i+'header');
					//proxies[i].startDrag = startDrag;
					//proxies[i].onDragEnter = dragEnter;
					//proxies[i].onDragOut = dragOut;
					//proxies[i].endDrag = dragEnd;
				}
				//podcol1.onDragEnter = podcol2.onDragEnter = podcol3.onDragEnter = dragEnter;
				wait.hide();

/*
var mod2 = document.createElement('div');
mod2.id = 'module2';
mod2.className = 'pod';
YAHOO.util.Dom.insertAfter(mod2, mod1);
		var module2 = new YAHOO.widget.Module("module2", { visible: false });
		module2.setHeader("Module #2 from Script");
		module2.setBody("This is a dynamically generated Module. <a href=\"http://google.com\">foo</a>");
		//module2.setFooter("End of Module #2");
		module2.render();
ddmod1 = new YAHOO.util.DD('module1');
ddmod2 = new YAHOO.util.DD('module2');


						var m = messages[i];
						var p = document.createElement('p');
						var message_text = document.createTextNode(
										m.animal + ' says "' + m.message + '"');
						p.appendChild(message_text);
						msg_section.appendChild(p);
				}
*/
		foo();
		},

		failure : function (o) {
				if (!YAHOO.util.Connect.isCallInProgress(o)) {
						wait.hide();
						alert("Async call failed!");
				}
		},

		timeout : 3000
}

// Make the call to the server for JSON data
//YAHOO.util.Connect.asyncRequest('GET',"index.php?p=console&ctrl=dashpods&action="+pod_group+"&load=true", callbacks);
YAHOO.util.Connect.asyncRequest('GET',BASE_URL+"&ctrl=dashpods&action="+pod_group+"&load=true", callbacks);
//YAHOO.util.Connect.asyncRequest('GET',"index.php?ctrl=main&action=load", callbacks);
}
//////////////////////////////////////////////////////////////////////////////
// example app
//////////////////////////////////////////////////////////////////////////////
YAHOO.example.DDApp = {
    init: function() {

        var rows=2,cols=3,i,j;
        for (i=1;i<cols+1;i=i+1) {
        	//alert('Setting target pod-col-'+i);
            new YAHOO.util.DDTarget("pod-col-"+i);
        }

        //for (i = 1; i <= podcount; i++) {
        	for (key in podmodcontainer) {
        	new YAHOO.example.DDList(key);
        	//new YAHOO.example.DDList("pod" + i);
        	//alert('Finished setting list pod'+i);
				}
        /*
        for (i=1;i<cols+1;i=i+1) {
            for (j=1;j<rows+1;j=j+1) {
            	alert("Setting list pod"+i);
                new YAHOO.example.DDList("pod" + (i+j));
            	alert("Finished setting list pod"+i);
            }
        }
        */

        Event.on("showButton", "click", this.showOrder);
        Event.on("switchButton", "click", this.switchStyles);
    },

    showOrder: function() {
        var parseList = function(ul, title) {
            var items = ul.getElementsByTagName("li");
            var out = title + ": ";
            for (i=0;i<items.length;i=i+1) {
                out += items[i].id + " ";
            }
            return out;
        };

        var ul1=Dom.get("ul1"), ul2=Dom.get("ul2");
        alert(parseList(ul1, "List 1") + "\n" + parseList(ul2, "List 2"));

    },

    switchStyles: function() {
        Dom.get("ul1").className = "draglist_alt";
        Dom.get("ul2").className = "draglist_alt";
    }
};

//////////////////////////////////////////////////////////////////////////////
// custom drag and drop implementation
//////////////////////////////////////////////////////////////////////////////

YAHOO.example.DDList = function(id, sGroup, config) {

    YAHOO.example.DDList.superclass.constructor.call(this, id, sGroup, config);

    this.logger = this.logger || YAHOO;
    var el = this.getDragEl();
    //Begin changed code
    //var strong = this.getEl().getElementsByTagName('strong')[0];
    //YAHOO.util.Dom.generateId(strong);
    //this.setHandleElId(strong.id);
    this.setHandleElId(id+'header');
    //End changed code
    Dom.setStyle(el, "opacity", 0.67); // The proxy is slightly transparent

    this.goingUp = false;
    this.lastY = 0;
};

YAHOO.extend(YAHOO.example.DDList, YAHOO.util.DDProxy, {

    startDrag: function(x, y) {
        this.logger.log(this.id + " startDrag");

        // make the proxy look like the source element
        var dragEl = this.getDragEl();
        var clickEl = this.getEl();
        Dom.setStyle(clickEl, "visibility", "hidden");

        dragEl.innerHTML = clickEl.innerHTML;

        Dom.setStyle(dragEl, "color", Dom.getStyle(clickEl, "color"));
        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));
        Dom.setStyle(dragEl, "border", "2px solid gray");
    },

    endDrag: function(e) {

        var srcEl = this.getEl();
        var proxy = this.getDragEl();

        // Show the proxy element and animate it to the src element's location
        Dom.setStyle(proxy, "visibility", "");
        var a = new YAHOO.util.Motion( 
            proxy, { 
                points: { 
                    to: Dom.getXY(srcEl)
                }
            }, 
            0.2, 
            YAHOO.util.Easing.easeOut 
        )
        var proxyid = proxy.id;
        var thisid = this.id;

        // Hide the proxy and show the source element when finished with the animation
        var podid = this.id;
        a.onComplete.subscribe(function() {
                Dom.setStyle(proxyid, "visibility", "hidden");
                Dom.setStyle(thisid, "visibility", "");
        				if (podmodcontainer[podid].isChart) {
        					podmodcontainer[podid].podchart.refreshData();
								}
        				//eval(podmodcontainer[podid].chartjs);
            });
        a.animate();
        //alert(this.id);
        //podmodcontainer[this.id].render();
    },

    onDragDrop: function(e, id) {

        // If there is one drop interaction, the li was dropped either on the list,
        // or it was dropped on the current location of the source element.
        if (DDM.interactionInfo.drop.length === 1) {

            // The position of the cursor at the time of the drop (YAHOO.util.Point)
            var pt = DDM.interactionInfo.point; 

            // The region occupied by the source element at the time of the drop
            var region = DDM.interactionInfo.sourceRegion; 

            // Check to see if we are over the source element's location.  We will
            // append to the bottom of the list once we are sure it was a drop in
            // the negative space (the area of the list without any list items)
            if (!region.intersect(pt)) {
                var destEl = Dom.get(id);
                var destDD = DDM.getDDById(id);
                destEl.appendChild(this.getEl());
                destDD.isEmpty = false;
                DDM.refreshCache();
            }

        }
    },

    onDrag: function(e) {

        // Keep track of the direction of the drag for use during onDragOver
        var y = Event.getPageY(e);

        if (y < this.lastY) {
            this.goingUp = true;
        } else if (y > this.lastY) {
            this.goingUp = false;
        }

        this.lastY = y;
    },

    onDragOver: function(e, id) {
    
        var srcEl = this.getEl();
        var destEl = Dom.get(id);

        // We are only concerned with list items, we ignore the dragover
        // notifications for the list.
        if (destEl.nodeName.toLowerCase() == "div" && Dom.hasClass(destEl, 'pod')) {
            var orig_p = srcEl.parentNode;
            var p = destEl.parentNode;

            if (this.goingUp) {
                p.insertBefore(srcEl, destEl); // insert above
            } else {
                p.insertBefore(srcEl, destEl.nextSibling); // insert below
            }

            DDM.refreshCache();
        }
    }
});



    /**
    * IntervalCalendar is an extension of the CalendarGroup designed specifically
    * for the selection of an interval of dates.
    *
    * @namespace YAHOO.example.calendar
    * @module calendar
    * @since 2.5.2
    * @requires yahoo, dom, event, calendar
    */

    /**
    * IntervalCalendar is an extension of the CalendarGroup designed specifically
    * for the selection of an interval of dates, as opposed to a single date or
    * an arbitrary collection of dates.
    * <p>
    * <b>Note:</b> When using IntervalCalendar, dates should not be selected or
    * deselected using the 'selected' configuration property or any of the
    * CalendarGroup select/deselect methods. Doing so will corrupt the internal
    * state of the control. Instead, use the provided methods setInterval and
    * resetInterval.
    * </p>
    * <p>
    * Similarly, when handling select/deselect/etc. events, do not use the
    * dates passed in the arguments to attempt to keep track of the currently
    * selected interval. Instead, use getInterval.
    * </p>
    *
    * @namespace YAHOO.example.calendar
    * @class IntervalCalendar
    * @extends YAHOO.widget.CalendarGroup
    * @constructor
    * @param {String | HTMLElement} container The id of, or reference to, an HTML DIV element which will contain the control.
    * @param {Object} cfg optional The initial configuration options for the control.
    */
    function IntervalCalendar(container, cfg) {
        /**
        * The interval state, which counts the number of interval endpoints that have
        * been selected (0 to 2).
        * 
        * @private
        * @type Number
        */
        this._iState = 0;

        // Must be a multi-select CalendarGroup
        cfg = cfg || {};
        cfg.multi_select = true;

        // Call parent constructor
        IntervalCalendar.superclass.constructor.call(this, container, cfg);

        // Subscribe internal event handlers
        this.beforeSelectEvent.subscribe(this._intervalOnBeforeSelect, this, true);
        this.selectEvent.subscribe(this._intervalOnSelect, this, true);
        this.beforeDeselectEvent.subscribe(this._intervalOnBeforeDeselect, this, true);
        this.deselectEvent.subscribe(this._intervalOnDeselect, this, true);
    }

    /**
    * Default configuration parameters.
    * 
    * @property IntervalCalendar._DEFAULT_CONFIG
    * @final
    * @static
    * @private
    * @type Object
    */
    IntervalCalendar._DEFAULT_CONFIG = YAHOO.widget.CalendarGroup._DEFAULT_CONFIG;

    YAHOO.lang.extend(IntervalCalendar, YAHOO.widget.CalendarGroup, {

        /**
        * Returns a string representation of a date which takes into account
        * relevant localization settings and is suitable for use with
        * YAHOO.widget.CalendarGroup and YAHOO.widget.Calendar methods.
        * 
        * @method _dateString
        * @private
        * @param {Date} d The JavaScript Date object of which to obtain a string representation.
        * @return {String} The string representation of the JavaScript Date object.
        */
        _dateString : function(d) {
            var a = [];
            a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_MONTH_POSITION.key)-1] = (d.getMonth() + 1);
            a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_DAY_POSITION.key)-1] = d.getDate();
            a[this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.MDY_YEAR_POSITION.key)-1] = d.getFullYear();
            var s = this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.DATE_FIELD_DELIMITER.key);
            return a.join(s);
        },

        /**
        * Given a lower and upper date, returns a string representing the interval
        * of dates between and including them, which takes into account relevant
        * localization settings and is suitable for use with
        * YAHOO.widget.CalendarGroup and YAHOO.widget.Calendar methods.
        * <p>
        * <b>Note:</b> No internal checking is done to ensure that the lower date
        * is in fact less than or equal to the upper date.
        * </p>
        * 
        * @method _dateIntervalString
        * @private
        * @param {Date} l The lower date of the interval, as a JavaScript Date object.
        * @param {Date} u The upper date of the interval, as a JavaScript Date object.
        * @return {String} The string representing the interval of dates between and
        *                   including the lower and upper dates.
        */
        _dateIntervalString : function(l, u) {
            var s = this.cfg.getProperty(IntervalCalendar._DEFAULT_CONFIG.DATE_RANGE_DELIMITER.key);
            return (this._dateString(l)
                    + s + this._dateString(u));
        },

        /**
        * Returns the lower and upper dates of the currently selected interval, if an
        * interval is selected.
        * 
        * @method getInterval
        * @return {Array} An empty array if no interval is selected; otherwise an array
        *                 consisting of two JavaScript Date objects, the first being the
        *                 lower date of the interval and the second being the upper date.
        */
        getInterval : function() {
            // Get selected dates
            var dates = this.getSelectedDates();
            if(dates.length > 0) {
                // Return lower and upper date in array
                var l = dates[0];
                var u = dates[dates.length - 1];
                return [l, u];
            }
            else {
                // No dates selected, return empty array
                return [];
            }
        },

        /**
        * Sets the currently selected interval by specifying the lower and upper
        * dates of the interval (in either order).
        * <p>
        * <b>Note:</b> The render method must be called after setting the interval
        * for any changes to be seen.
        * </p>
        * 
        * @method setInterval
        * @param {Date} d1 A JavaScript Date object.
        * @param {Date} d2 A JavaScript Date object.
        */
        setInterval : function(d1, d2) {
            // Determine lower and upper dates
            var b = (d1 <= d2);
            var l = b ? d1 : d2;
            var u = b ? d2 : d1;
            // Update configuration
            this.cfg.setProperty('selected', this._dateIntervalString(l, u), false);
            this._iState = 2;
        },

        /**
        * Resets the currently selected interval.
        * <p>
        * <b>Note:</b> The render method must be called after resetting the interval
        * for any changes to be seen.
        * </p>
        * 
        * @method resetInterval
        */
        resetInterval : function() {
            // Update configuration
            this.cfg.setProperty('selected', [], false);
            this._iState = 0;
        },

        /**
        * Handles beforeSelect event.
        * 
        * @method _intervalOnBeforeSelect
        * @private
        */
        _intervalOnBeforeSelect : function(t,a,o) {
            // Update interval state
            this._iState = (this._iState + 1) % 3;
            if(this._iState == 0) {
                // If starting over with upcoming selection, first deselect all
                this.deselectAll();
                this._iState++;
            }
        },

        /**
        * Handles selectEvent event.
        * 
        * @method _intervalOnSelect
        * @private
        */
        _intervalOnSelect : function(t,a,o) {
            // Get selected dates
            var dates = this.getSelectedDates();
            if(dates.length > 1) {
                /* If more than one date is selected, ensure that the entire interval
                    between and including them is selected */
                var l = dates[0];
                var u = dates[dates.length - 1];
                this.cfg.setProperty('selected', this._dateIntervalString(l, u), false);
            }
            // Render changes
            this.render();
        },

        /**
        * Handles beforeDeselect event.
        * 
        * @method _intervalOnBeforeDeselect
        * @private
        */
        _intervalOnBeforeDeselect : function(t,a,o) {
            if(this._iState != 0) {
                /* If part of an interval is already selected, then swallow up
                    this event because it is superfluous (see _intervalOnDeselect) */
                return false;
            }
        },

        /**
        * Handles deselectEvent event.
        *
        * @method _intervalOnDeselect
        * @private
        */
        _intervalOnDeselect : function(t,a,o) {
            if(this._iState != 0) {
                // If part of an interval is already selected, then first deselect all
                this._iState = 0;
                this.deselectAll();

                // Get individual date deselected and page containing it
                var d = a[0][0];
                var date = YAHOO.widget.DateMath.getDate(d[0], d[1] - 1, d[2]);
                var page = this.getCalendarPage(date);
                if(page) {
                    // Now (re)select the individual date
                    page.beforeSelectEvent.fire();
                    this.cfg.setProperty('selected', this._dateString(date), false);
                    page.selectEvent.fire([d]);
                }
                // Swallow up since we called deselectAll above
                return false;
            }
        }
    });

    //YAHOO.namespace("example.calendar");
    //YAHOO.example.calendar.IntervalCalendar = IntervalCalendar;
    ClassIntervalCalendar = IntervalCalendar;








Event.onDOMReady(loadpods);
var foo = function() {
Event.onDOMReady(YAHOO.example.DDApp.init, YAHOO.example.DDApp, true);
}

})();
