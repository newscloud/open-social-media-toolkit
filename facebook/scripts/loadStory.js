var ajaxUrl=document.getElementById('ajaxNode').getValue();

var nums = ['thumbnail1', 'thumbnail2', 'thumbnail3', 'thumbnail4', 'thumbnail5', 'thumbnail6', 'thumbnail7', 'thumbnail8', 'thumbnail9', 'thumbnail10', 'thumbnail11', 'thumbnail12', 'thumbnail13', 'thumbnail14', 'thumbnail15', 'thumbnail16', 'thumbnail17', 'thumbnail18', 'thumbnail19', 'thumbnail20', 'thumbnail21', 'thumbnail22', 'thumbnail23', 'thumbnail24', 'thumbnail25', 'thumbnail26', 'thumbnail27', 'thumbnail28', 'thumbnail29', 'thumbnail30', 'thumbnail31', 'thumbnail32', 'thumbnail33', 'thumbnail34', 'thumbnail35', 'thumbnail36', 'thumbnail37', 'thumbnail38', 'thumbnail39', 'thumbnail40', 'thumbnail41', 'thumbnail42', 'thumbnail43', 'thumbnail44', 'thumbnail45', 'thumbnail46', 'thumbnail47', 'thumbnail48', 'thumbnail49', 'thumbnail50'];
var index = 0;
var total = 3;
var includeImage = true;
var images;

function loadStoryData() {
	var caption = document.getElementById('caption');
	var title = document.getElementById('title');
	var imageUrl = document.getElementById('imageUrl');
	var url = document.getElementById('url');
	var queryParams = { 'url' : url.getValue() };
	var ajax = new Ajax();
	if (url.getValue()!='') {
		//caption.setClassName('process');
		title.setClassName('process');
		url.setClassName('process');
		//imageUrl.setClassName('process');
		ajax.requireLogin = true;
		ajax.responseType = Ajax.JSON;
		//ajax.responseType = Ajax.RAW;
		ajax.ondone = function(data) { updateStoryData(data,'link'); }
		ajax.post(ajaxUrl+"?p=ajax&m=parseStory", queryParams);
	}
}

function loadBlogData() {
	var caption = document.getElementById('caption');
	var title = document.getElementById('title');
	var imageUrl = document.getElementById('imageUrl');
	var url = document.getElementById('url');
	var queryParams = { 'url' : url.getValue() };
	var ajax = new Ajax();
	if (url.getValue()!='') {		
		url.setClassName('process');
		//imageUrl.setClassName('process');
		ajax.requireLogin = true;
		ajax.responseType = Ajax.JSON;
		//ajax.responseType = Ajax.RAW;
		ajax.ondone = function(data) { updateStoryData(data,'blog'); }
		ajax.post(ajaxUrl+"?p=ajax&m=parseStory", queryParams);
	}
}

function previewBlog() {

	if (typeof(images) =='undefined' || typeof(images.length) == 'undefined' || images.length == 0) total = 0;
	var story_box = document.getElementById('story_selector_box');
	story_box.setStyle('display', 'block');

	var story_title = document.getElementById('story_title');
	var story_summary = document.getElementById('story_summary');
	var blog_preview = document.getElementById('blog_preview');
	var title = document.getElementById('title');
	var caption = document.getElementById('caption');
	var entry = document.getElementById('entry');
	if (title.getValue() != '') {
		story_title.setTextValue(title.getValue());
	}
	if (caption.getValue() != '') {
		story_summary.setTextValue(caption.getValue());
	}
	if (entry.getValue() != '') {
		blog_preview.setInnerXHTML('<div>'+entry.getValue()+'</div>');
	}
}

function updateStoryData(data,mode) {
	var caption = document.getElementById('caption');
	var story_summary = document.getElementById('story_summary');
	var title = document.getElementById('title');
	var url = document.getElementById('url');
	var story_title = document.getElementById('story_title');
	var imageUrl = document.getElementById('imageUrl');
	var story_box = document.getElementById('story_selector_box');
	images = data.images;

	if (title.getValue() == '') {
		title.setValue(data.title);
		story_title.setTextValue(data.title);
	} 
	if (caption.getValue() == '') {
		caption.setTextValue(data.description);
		story_summary.setTextValue(data.description);
	}
	if (mode=='blog') {
		updateTitle();	
		var entry = document.getElementById('entry');
		var blog_preview = document.getElementById('blog_preview');
		if (entry.getValue() != '') {
			blog_preview.setInnerXHTML('<div>'+entry.getValue()+'</div>');
		}
	}
	//init_images(data.images);
	init_images();

	//caption.setClassName('');
	title.setClassName('');
	url.setClassName('');
	//imageUrl.setClassName('');

	if (images.length > 0) {
		story_box.setStyle('display', 'block');
	}
}

function updateTitle() {
	var title = document.getElementById('title');
	if (title.getValue() != '') {
		var story_title = document.getElementById('story_title');
		story_title.setTextValue(title.getValue());
	}
}

function updateCaption() {
	var caption = document.getElementById('caption');
	if (caption.getValue() != '') {
		var story_caption = document.getElementById('story_summary');
		story_caption.setTextValue(caption.getValue());
	}
}

function updateBlogEntry() {
	var caption = document.getElementById('entry');
	if (caption.getValue() != '') {
		var story_caption = document.getElementById('story_summary');
		story_caption.setTextValue(caption.getValue());
	}
}

function toggleImage() {
	var imgUrl = document.getElementById('imageUrl');
	if (includeImage) {
		includeImage = false;
		imgUrl.setValue('');
	} else {
		includeImage = true;
		imgUrl.setValue(images[index]);
	}
}

function getEl(el) {
	return el;
	//return document.getElementById(el);
}

function trim(text) {
    try {
        return String(text.toString()).trim();
    } catch (ignored) {
        return "";
    }
}

function init_images() {
    //total = total + images.length;
    total = images.length;
		var imgdiv = document.getElementById('thumb_container');
		var children = imgdiv.getChildNodes().length;
		while (children-- > 0) {
			imgdiv.removeChild(imgdiv.getLastChild());
		}
    var html = ["<div class=\"thumbnail_stage\"><h4>" + _tx("Choose a Thumbnail") + "</h4><div class=\"selector clearfix\"><div class=\"arrows clearfix\">", "<span class=\"left\"><a href=\"#\" class=\"arrow disabled\">&nbsp;</a></span>", "<span class=\"right\"><a href=\"#\" class=\"arrow ", images.length > 1 ? "enabled" : "disabled", "\">&nbsp;</a></span>", "</div><div class=\"counter\"><span>1 of ", images.length, "</span></div></div>"];
    for (var k = 0; k < images.length; k++) {
    		var div = document.createElement('div');
    		div.setId("thumbnail"+(k+1));
    		div.setClassName("thumbnail" + (k == 0 ? " thumbnail_selected" : " thumbnail_unselected"));
    		var img = document.createElement('img');
    		img.setSrc(images[k]);
    		img.setStyle('maxWidth', '95px');
    		img.setStyle('maxHeight', '95px');
    		div.appendChild(img);
    		imgdiv.appendChild(div);
        //html.push("<div class=\"thumbnail", k == 0 ? " thumbnail_selected" : " thumbnail_unselected", "\">", "<img class=\"img_loading\" src=\"", images[k], "\" onload=\"adjustImage(this);\" />", "</div>");
        //html.push("<div class=\"thumbnail", k == 0 ? " thumbnail_selected" : " thumbnail_unselected", "\">", "<img class=\"img_loading\" src=\"", images[k], "\" onload=\"adjustImage(this);\" />", "</div>");
    }
    html.push("<label style=\"white-space:nowrap\"><input name=\"no_picture\" type=\"checkbox\" >No Picture</label></div>");
    //html.push("<label style=\"white-space:nowrap\"><input name=\"no_picture\" type=\"checkbox\" onclick=\"this.parentNode.parentNode.parentNode.thumbnail.use_thumbnail(this.checked)\" />" + _tx("No Picture") + "</label></div>");
		move_selection(0);
}

function use_thumbnail(checkbox) {
	if (!checkbox) {
		this.move_selection(0);
		CSS.removeClass(this.obj, "thumbnail_dont_use");
	} else {this.input.value = "";
		CSS.addClass(this.obj, "thumbnail_dont_use");
	}
}

function left_arrow_press() {
	//CSS.addClass(this.left, "active");
	move_selection(-1);
	return false;
}
function right_arrow_press() {
	//CSS.removeClass(this.right, "active");
	move_selection(1);
	return false;
}
function move_selection(offset) {
    var tmpindex = index + offset;
    if (tmpindex >= 0 && tmpindex < total) {
        var counter = document.getElementById('image_counter');
        var left_arrow = document.getElementById('left_arrow');
        var right_arrow = document.getElementById('right_arrow');
        var imgUrl = document.getElementById('imageUrl');
        //var divs = document.getElementsByTagName("div");
        var divs = [];
        //var nums = ['thumbnail1', 'thumbnail2', 'thumbnail3', 'thumbnail4', 'thumbnail5'];
        var j = 0;
        index = tmpindex;
        //for (var i = 0; i < divs.length; i++) {
        for (var i = 0; i < total; i++) {
        	var elf = document.getElementById(nums[i]);
            //var className = divs[i].className;
            //var className = document.getElementById(nums[i]).getClassName();
            var className = elf.getClassName();
            //if (!CSS.hasClass(divs[i], "thumbnail ")) {
            if (className.indexOf("thumbnail ") == -1) {
                continue;
            }
            var selected = j == index;
            if (className.indexOf(selected ? "_unselected" : "_selected") != -1) {
            		elf.setClassName(className.replace(/thumbnail_(?:un)?selected/, selected ? "thumbnail_selected" : "thumbnail_unselected"));
                //CSS.setClass(elf, className.replace(/thumbnail_(?:un)?selected/, selected ? "thumbnail_selected" : "thumbnail_unselected"));
                //CSS.setClass(divs[i], className.replace(/thumbnail_(?:un)?selected/, selected ? "thumbnail_selected" : "thumbnail_unselected"));
            }
            j++;
        }
        //this.label.innerHTML = _tx("{selected} of {total}", {selected: index + 1, total: j});
        //counter.innerHTML = "" + (index + 1) + " of " + j;
        if (includeImage) {
        	imgUrl.setValue(images[index]);
				}
        counter.setTextValue("" + (index + 1) + " of " + j);
        left_arrow.setClassName(left_arrow.getClassName().replace(/[^ ]+abled/, index == 0 ? "disabled" : "enabled"));
        //CSS.setClass(left_arrow, left_arrow.className.replace(/[^ ]+abled/, index == 0 ? "disabled" : "enabled"));
        right_arrow.setClassName(right_arrow.getClassName().replace(/[^ ]+abled/, index == total - 1 ? "disabled" : "enabled"));
        //CSS.setClass(right_arrow, right_arrow.className.replace(/[^ ]+abled/, index == total - 1 ? "disabled" : "enabled"));
        //CSS.setClass(this.left, this.left.className.replace(/[^ ]+abled/, index == 0 ? "disabled" : "enabled"));
        //CSS.setClass(this.right, this.right.className.replace(/[^ ]+abled/, index == this.images.length - 1 ? "disabled" : "enabled"));
        //this.input.value = this.images[index].src;
    }

}

function tx(str, args) {
    if (typeof _string_table == "undefined") {
        return;
    }
    str = _string_table[str];
    return _tx(str, args);
}

function _tx(str, args) {
    if (args) {
        if (typeof args != "object") {
            Util.error("intl.js: the 2nd argument must be a keyed array (not a string) for tx(" + str + ", ...)");
        } else {
            var regexp;
            for (var key in args) {
                if (intl_ends_in_punct(args[key])) {
                    regexp = new RegExp("{" + key + "}" + intl_ends_in_punct.punct_char_class + "*", "g");
                } else {
                    regexp = new RegExp("{" + key + "}", "g");
                }
                str = str.replace(regexp, "\x01" + args[key] + "\x01");
            }
            str = intl_phonological_rules(str);
        }
    }
    return str;
}

function intl_phonological_rules(str) {
    var rules = window.intl_locale_rewrites;
    var regexp;
    if (rules) {
        var pats = [];
        var reps = [];
        for (var p in rules.patterns) {
            var pat = p;
            var rep = rules.patterns[p];
            for (var m in rules.meta) {
                regexp = new RegExp(m.slice(1, -1), "g");
                pat = pat.replace(regexp, rules.meta[m]);
                rep = rep.replace(regexp, rules.meta[m]);
            }
            regexp = new RegExp("\\+", "g");
            pats[pats.length] = pat.replace(regexp, "\x01");
            reps[reps.length] = rep.replace(regexp, "\x01");
        }
        for (var ii = 0; ii < pats.length; ii++) {
            regexp = new RegExp(pats[ii].slice(1, -1), "g");
            str = str.replace(regexp, reps[ii]);
        }
    }
    regexp = new RegExp("\x01", "g");
    str = str.replace(regexp, "");
    return str;
}
