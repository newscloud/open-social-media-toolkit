<?php

$html = <<<HTML
<span class="chart_title">Monthly Expenses</span>
<div id="chart-{$view}">Unable to load Flash content. The YUI Charts Control requires Flash Player 9.0.45 or higher. You can download the latest version of Flash Player from the <a href="http://www.adobe.com/go/getflashplayer">Adobe Flash Player Download Center</a>.</p></div>
HTML;
$js = <<<JS

	YAHOO.widget.Chart.SWFURL = "http://yui.yahooapis.com/2.7.0/build/charts/assets/charts.swf";

//--- data

	var monthlyExpenses =
	[
		{ month: "January", rent: 880.00, utilities: 894.68 },
		{ month: "February", rent: 880.00, utilities: 901.35 },
		{ month: "March", rent: 880.00, utilities: 889.32 },
		{ month: "April", rent: 880.00, utilities: 884.71 },
		{ month: "May", rent: 910.00, utilities: 879.811 },
		{ month: "June", rent: 910.00, utilities: 897.95 }
	];

	var myDataSource = new YAHOO.util.DataSource( monthlyExpenses );
	myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSARRAY;
	myDataSource.responseSchema =
	{
		fields: [ "month", "rent", "utilities" ]
	};
//--- chart

	var seriesDef =
	[
		{ displayName: "Rent", yField: "rent" },
		{ displayName: "Utilities", yField: "utilities" }
	];

	var formatCurrencyAxisLabel = function( value )
	{
		return YAHOO.util.Number.format( value,
		{
			prefix: "$",
			thousandsSeparator: ",",
			decimalPlaces: 2
		});
	}

	var getDataTipText = function( item, index, series )
	{
		var toolTipText = series.displayName + " for " + item.month;
		//toolTipText += "\\n" + formatCurrencyAxisLabel( item[series.yField] );
		return toolTipText;
	}

	var currencyAxis = new YAHOO.widget.NumericAxis();
	currencyAxis.minimum = 800;
	currencyAxis.labelFunction = formatCurrencyAxisLabel;

	var mychart = new YAHOO.widget.LineChart( "chart-{$view}", myDataSource,
	{
		series: seriesDef,
		xField: "month",
		yAxis: currencyAxis,
		dataTipFunction: getDataTipText,
		//only needed for flash player express install
		expressInstall: "assets/expressinstall.swf"
	});

JS;
?>
