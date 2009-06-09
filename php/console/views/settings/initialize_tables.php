<script type="text/javascript">
// Shortcuts
var DataSource = YAHOO.util.DataSource,
    Event = YAHOO.util.Event,
    Dom   = YAHOO.util.Dom;

YAHOO.example.data = null; // this is where the data will go

// Add a new parser to DataSource to parse numbers that are prefixed with
// currency symbols (or any other non-numeric characters)
DataSource.Parser.currency = function (cur) {
    if (YAHOO.lang.isString(cur)) {
        var neg = !cur.indexOf('-');
        cur = (neg?-1:1) * cur.slice(neg).replace(/^[^0-9.]+|,/g,'');
    } else if (!YAHOO.lang.isNumber(cur)) {
        return 0;
    }

    return cur;
};
DataSource.Parser.checkbox = function(val) {
	//alert(val);
	var match = val.match(/id="([^"]+)"/);
	if (match == null) {
		return false;
	} else {
		var id = match[1];
	}
	var item = Dom.get(id);
	if ( item.checked) {
		return 1;
	} else {
		return 0;
	}
}

DataSource.Parser.textinput = function(val) {
	var match = val.match(/id="([^"]+)"/);
	if (match == null) {
		return false;
	} else {
		var id = match[1];
	}
	var item = Dom.get(id);
	return item.value;
}

DataSource.Parser.select = function(val) {
	var match = val.match(/id="([^"]+)"/);
	if (match == null) {
		return false;
	} else {
		var id = match[1];
	}
	var options = Dom.getChildren(id);
	for (var i = 0; i < options.length; i++) {
		var option = options[i];
		if (option.selected) {
			return option.value;
		}
	}

	return 'none';
}

//Event.on('save','click',function (e) {
function save(id) {
	var match = id.match(/table-(.*)/);
	var tablename = match[1];
YAHOO.example.data = null; // this is where the data will go

    // Here's the pass-thru DataSource.  Instantiate and immediately call
    // sendRequest, passing a simple assignment function as the callback's
    // success handler
    new DataSource(Dom.get(id), {
        responseType : DataSource.TYPE_HTMLTABLE,
        responseSchema : {
            // Describe the object keys each record will have and what type
            // of data to parse into the respective values
            fields : [
				{ key: 'field'},
				{ key: 'label', parser: 'textinput'},
				{ key: 'fieldtype'},
				{ key: 'type'},
				{ key: 'null'},
				{ key: 'default'},
				{ key: 'extra'},
				{ key: 'key'},
				{ key: 'enabled', parser: 'checkbox'},
				{ key: 'full', parser: 'checkbox'},
				{ key: 'pod', parser: 'checkbox'},
				{ key: 'sortby', parser: 'checkbox'},
				{ key: 'validate', parser: 'select'},
				{ key: 'new', parser: 'checkbox'},
				{ key: 'modify', parser: 'checkbox'},
				{ key: 'usedefault', parser: 'checkbox'}
				/*
                { key: 'due',      parser: 'date' },
                { key: 'account' },
                { key: 'quantity', parser: 'number' },
                { key: 'amount',   parser: 'currency' } // use our new parser
				*/
            ]
        }
    }).sendRequest(null,{
        success : function (req,res) {
            YAHOO.example.data = res.results;
			Dom.get('results').innerHTML = res.results;
			YAHOO.util.Connect.asyncRequest('POST', "index.php?ctrl=settings&action=save_table", { success: function(req, res) { alert('Sucess!! maybe?'); }}, "tablename="+tablename+"&table="+YAHOO.lang.JSON.stringify(res.results));
        }
    });

	return false;

}
    
</script>
<div id="results"></div>
<?php foreach ($tables as $table): ?>
<div id="stable-<? echo $table['name']; ?>" class="dbtable">
<h1>Table '<? echo $table['name']; ?>' config</h1>
<br /><br />
<table id="table-<? echo $table['name']; ?>">
	<tr>
		<th>Field</th>
		<th>Label</th>
		<th>Field Type</th>
		<th>Type</th>
		<th>Null?</th>
		<th>Default</th>
		<th>Extra</th>
		<th>Key</th>
		<th>Enabled</th>
		<th>Full View</th>
		<th>Pod View</th>
		<th>Sortable</th>
		<th>Validate</th>
		<th>New</th>
		<th>Modify</th>
		<th>Use Default</th>
	</tr>
	<?php foreach ($table['fields'] as $name => $field): ?>
		<tr>
			<td <? if ($name == 'artist') echo 'id="artist"';?>><? echo $name; ?></td>
			<td><input id="<?php echo uniqid(); ?>" type="text" value="<? echo $name; ?>" style="width: 7em;" /></td>
			<td><? echo $field['fieldtype']; ?></td>
			<td><? echo $field['type']; ?></td>
			<td><? echo $field['null']; ?></td>
			<td><? echo $field['default']; ?></td>
			<td><? echo $field['extra']; ?></td>
			<td><? echo $field['key']; ?></td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" checked/></td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" checked/></td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" checked/></td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" /></td>
			<td><select id="<? echo uniqid(); ?>">
				<option value="none" selected>None</option>
				<option value="notempty">Not Empty</option>
				<option value="email">Email</option>
				<option value="number">Number</option>
				<option value="string">String</option>
				</select>
			</td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" checked/></td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" checked/></td>
			<td><input id="<? echo uniqid(); ?>" type="checkbox" checked/></td>
		</tr>
	<?php endforeach; ?>
</table>
<a href="#" id="save" onclick="save('table-<?php echo $table['name']; ?>'); return false;">Save</a>
<br /><br />
<?php endforeach; ?>
</div>
