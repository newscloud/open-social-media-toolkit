var slots = [], players = [],
    Event = YAHOO.util.Event, DDM = YAHOO.util.DDM;

var loading = '<div class="loadingStatus"><img src="images/loading.gif"></div>';
var http_request = false;

// YUI Drag and Drop functions
(function() {

YAHOO.example.DDPlayer = function(id, sGroup, config) {
    YAHOO.example.DDPlayer.superclass.constructor.apply(this, arguments);
    this.initPlayer(id, sGroup, config);
};

YAHOO.extend(YAHOO.example.DDPlayer, YAHOO.util.DDProxy, {

    TYPE: "DDPlayer",

    initPlayer: function(id, sGroup, config) {
        if (!id) { 
            return; 
        }

        var el = this.getDragEl()
        YAHOO.util.Dom.setStyle(el, "borderColor", "transparent");
        YAHOO.util.Dom.setStyle(el, "opacity", 0.76);

        // specify that this is not currently a drop target
        this.isTarget = false;

        this.originalStyles = [];

        this.type = YAHOO.example.DDPlayer.TYPE;
        this.slot = null;

        this.startPos = YAHOO.util.Dom.getXY( this.getEl() );
        YAHOO.log(id + " startpos: " + this.startPos, "info", "example");
    },

    startDrag: function(x, y) {
        YAHOO.log(this.id + " startDrag", "info", "example");
        var Dom = YAHOO.util.Dom;

        var dragEl = this.getDragEl();
        var clickEl = this.getEl();

        dragEl.innerHTML = clickEl.innerHTML;
        dragEl.className = clickEl.className;

        Dom.setStyle(dragEl, "color",  Dom.getStyle(clickEl, "color"));
        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor"));

        Dom.setStyle(clickEl, "opacity", 0.1);

        var targets = YAHOO.util.DDM.getRelated(this, true);
        YAHOO.log(targets.length + " targets", "info", "example");
        for (var i=0; i<targets.length; i++) {
        YAHOO.log("Target: " + targets[i], "info", "example");
            
            var targetEl = this.getTargetDomRef(targets[i]);
            if (targetEl == null) {
            	continue;
						}

            if (!this.originalStyles[targetEl.id]) {
                this.originalStyles[targetEl.id] = targetEl.className;
            }

            targetEl.className = "target";
        }
    },

    getTargetDomRef: function(oDD) {
        if (oDD.player) {
            return oDD.player.getEl();
        } else {
            return oDD.getEl();
        }
    },

    endDrag: function(e) {
        // reset the linked element styles
        YAHOO.util.Dom.setStyle(this.getEl(), "opacity", 1);

        this.resetTargets();
    },

    resetTargets: function() {

        // reset the target styles
        var targets = YAHOO.util.DDM.getRelated(this, true);
        for (var i=0; i<targets.length; i++) {
            var targetEl = this.getTargetDomRef(targets[i]);
            if (targetEl == null) {
            	continue;
						}
            var oldStyle = this.originalStyles[targetEl.id];
            if (oldStyle) {
                targetEl.className = oldStyle;
            }
        }
    },

    onDragDrop: function(e, id) {
        // get the drag and drop object that was targeted
        var oDD;
        
        if ("string" == typeof id) {
            oDD = YAHOO.util.DDM.getDDById(id);
        } else {
            oDD = YAHOO.util.DDM.getBestMatch(id);
        }

        var el = this.getEl();

        // check if the slot has a player in it already
        if (oDD.player) {
            // check if the dragged player was already in a slot
            if (this.slot) {
                // check to see if the player that is already in the
                // slot can go to the slot the dragged player is in
                // YAHOO.util.DDM.isLegalTarget is a new method
                if ( YAHOO.util.DDM.isLegalTarget(oDD.player, this.slot) ) {
                    YAHOO.log("swapping player positions", "info", "example");
                    YAHOO.util.DDM.moveToEl(oDD.player.getEl(), el);
                    //this.slot.player = oDD.player;
                    oDD.player.slot = this.slot;
                } else {
                    YAHOO.log("moving player in slot back to start", "info", "example");
                    //YAHOO.util.Dom.setXY(oDD.player.getEl(), oDD.player.startPos);
                    //this.slot.player = null;
                    //oDD.player.slot = null
                }
            } else {
                // the player in the slot will be moved to the dragged
                // players start position
                oDD.player.slot = null;
                //YAHOO.util.DDM.moveToEl(oDD.player.getEl(), el);
            }
        } else {
            // Move the player into the emply slot
            // I may be moving off a slot so I need to clear the player ref
            if (this.slot) {
                this.slot.player = null;
            }
        }

        //YAHOO.util.DDM.moveToEl(el, oDD.getEl());
        updateTemplateData(el, oDD.getEl());
        this.resetTargets();

        this.slot = oDD;
        this.slot.player = this;
    },

    swapfu: function(el1, el2) {
        var Dom = YAHOO.util.Dom;
        var pos1 = Dom.getXY(el1);
        var pos2 = Dom.getXY(el2);
        Dom.setXY(el1, pos2);
        Dom.setXY(el2, pos1);
    },

    onDragOver: function(e, id) {
    },

    onDrag: function(e, id) {
    }

});

Event.onDOMReady(function() { 
		//registerItems();

    //DDM.mode = document.getElementById("ddmode").selectedIndex;

    //Event.on("ddmode", "change", function(e) {
    //       YAHOO.util.DDM.mode = this.selectedIndex;
    //    });
});

})();

function load_ajax() {
http_request = false;
  if (http_request == false) {
	  if (window.XMLHttpRequest) { // Mozilla, Safari,...
		  http_request = new XMLHttpRequest();
		  if (http_request.overrideMimeType) {
			  // set type accordingly to anticipated content type
			  //http_request.overrideMimeType('text/xml');
			  http_request.overrideMimeType('text/html');
		  }
	  } else if (window.ActiveXObject) { // IE
		  try {
			  http_request = new ActiveXObject("Msxml2.XMLHTTP");
		  } catch (e) {
			  try {
				  http_request = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (e) {}
		  }
	  }
	  if (!http_request) {
		  alert('Cannot create XMLHTTP instance');
		  return false;
	  }
  }
}


function setLoading(el) {
	el.innerHTML = loading;
}

function loadTemplate(template) {
	load_ajax();
	var main = document.getElementById('yui-main');
	setLoading(main);

	http_request.onreadystatechange = function() {
		if (http_request.readyState == 4) {
			if (http_request.status == 200) {
				main.innerHTML = http_request.responseText;
				if (template != 'select_templates') {
					loadStories();
				}
			} else {
				alert('There was a problem loading the template. '+http_request.responseText);
			}
		}
	}
	http_request.open('GET', '?p=load_template&template='+template, true);
	http_request.send(null);


	return false;
}

function loadStories() {
	load_ajax();
	var nav = document.getElementById('yui-nav');
	setLoading(nav);

	http_request.onreadystatechange = function() {
		if (http_request.readyState == 4) {
			if (http_request.status == 200) {
				var stories = http_request.responseText.match(/story_\d+/g);
				nav.innerHTML = http_request.responseText;
				registerItems(stories);
			} else {
				alert('There was a problem loading the stories.');
			}
		}
	}
	http_request.open('GET', '?p=load_stories', true);
	http_request.send(null);

	return false;
}

function updateTemplateData(storyEl, dropEl) {
	load_ajax();
	var story_id = dropEl.id.replace(/(story-\d+)-image.*/, '$1');
	document.getElementById(story_id+'-story-id').value = storyEl.id;
	http_request.overrideMimeType('text/xml');
	var story_image = document.getElementById(story_id+'-image');
	var story_title = document.getElementById(story_id+'-title');
	var story_caption = document.getElementById(story_id+'-caption');
	var story = new Array();

	http_request.onreadystatechange = function() {
		if (http_request.readyState == 4) {
			if (http_request.status == 200) {
				var xml = http_request.responseXML;
				var story = xml.getElementsByTagName('story').item(0);
				if (story_image != null && !dropEl.id.match('/blurb/')) {
					story_image.innerHTML = xml.childNodes.item(0).childNodes.item(1).childNodes[0].data;
				}
				if (story_title != null) {
					story_title.innerHTML = xml.childNodes.item(0).childNodes.item(3).childNodes[0].data;
				}
				if (story_caption != null && !dropEl.id.match('/blurb|mini/')) {
					story_caption.innerHTML = xml.childNodes.item(0).childNodes.item(5).childNodes[0].data;
				}

				//dropEl.innerHTML = http_request.responseText;
				//tmp.innerHTML = http_request.responseText;
			} else {
				alert('There was a problem updating the story. '+http_request.responseText);
			}
		}
	}
	http_request.open('GET', '?p=load_story&id='+storyEl.id+'&dropElId='+dropEl.id, true);
	http_request.send(null);
}

function registerItems(stories) {
    // slots
    slots[0] = new YAHOO.util.DDTarget("story-1-image", "topslots");
    slots[1] = new YAHOO.util.DDTarget("story-2-image", "topslots");
    slots[2] = new YAHOO.util.DDTarget("story-3-image", "topslots");
    slots[3] = new YAHOO.util.DDTarget("story-4-image", "topslots");
    slots[4] = new YAHOO.util.DDTarget("story-2-image-blurb", "topslots");
    slots[5] = new YAHOO.util.DDTarget("story-3-image-blurb", "topslots");
    slots[6] = new YAHOO.util.DDTarget("story-2-image-mini", "topslots");
    slots[7] = new YAHOO.util.DDTarget("story-3-image-mini", "topslots");
    slots[8] = new YAHOO.util.DDTarget("story-5-image-mini", "topslots");
    slots[9] = new YAHOO.util.DDTarget("story-6-image-mini", "topslots");
    //slots[1] = new YAHOO.util.DDTarget("t2", "topslots");
    //slots[2] = new YAHOO.util.DDTarget("b1", "bottomslots");
    //slots[3] = new YAHOO.util.DDTarget("b2", "bottomslots");
    //slots[4] = new YAHOO.util.DDTarget("b3", "bottomslots");
    //slots[5] = new YAHOO.util.DDTarget("b4", "bottomslots");
    
    // players
    for (i = 0; i < stories.length; i++) {
    	players[i] = new YAHOO.example.DDPlayer(stories[i], "topslots");
		}
    //players[0] = new YAHOO.example.DDPlayer("story_1", "topslots");
    //players[1] = new YAHOO.example.DDPlayer("story_2", "topslots");
    //players[2] = new YAHOO.example.DDPlayer("story_3", "topslots");
    //players[3] = new YAHOO.example.DDPlayer("story_4", "topslots");
    //players[4] = new YAHOO.example.DDPlayer("story_5", "topslots");
    //players[5] = new YAHOO.example.DDPlayer("story_6", "topslots");
    //players[6] = new YAHOO.example.DDPlayer("story_7", "topslots");
    //players[7] = new YAHOO.example.DDPlayer("story_8", "topslots");
    //players[2] = new YAHOO.example.DDPlayer("story_3", "bottomslots");
    //players[3] = new YAHOO.example.DDPlayer("story_4", "bottomslots");
    //players[5].addToGroup("bottomslots");
}

function saveTemplate(template) {
	load_ajax();

	var fields = []
	fields['template_1'] = ['story-1'];
	fields['template_2'] = ['story-1', 'story-2', 'story-3'];
	fields['template_3'] = ['story-1', 'story-4'];
	fields['template_4'] = ['story-1', 'story-4'];
	fields['template_5'] = ['story-1', 'story-2', 'story-3', 'story-4', 'story-5', 'story-6'];

	var curr_fields = fields[template];
	var story_ids = [];
	var params = '';
	for (var i = 0; i < curr_fields.length; i++) {
		story_ids[i] = document.getElementById(curr_fields[i]+'-story-id').value;
		params += '&'+curr_fields[i]+'='+story_ids[i];
	}
	params += '&template='+template;

	for (var i = 0; i < curr_fields.length; i++) {
		var alert_str = 'Please select a story for: ';
		var error = false;
		if (story_ids[i] == 0) {
			error = true;
			if (curr_fields[i] == 'story-1') {
				alert_str += ' Primary Story 1.';
			} else if (curr_fields[i] == 'story-2') {
				alert_str += ' Sub Story 1.';
			} else if (curr_fields[i] == 'story-3') {
				alert_str += ' Sub Story 2.';
			} else if (curr_fields[i] == 'story-4') {
				alert_str += ' Primary Story 2.';
			} else if (curr_fields[i] == 'story-5') {
				alert_str += ' Sub Story 3.';
			} else if (curr_fields[i] == 'story-6') {
				alert_str += ' Sub Story 4.';
			} else {
				alert_str += ' unknown story.';
			}
		}

		if (error) {
			alert_str += ' Please select all stories and try saving again.';
			alert(alert_str);
			return false;
		}
	}

	http_request.onreadystatechange = function() {
		if (http_request.readyState == 4) {
			if (http_request.status == 200)   
			{
				// djm: this is disabled until the console page can embed these constants
				/*var homelink = document.createElement('a');
				homelink.href = document.getElementById('URL_CANVAS').getValue(); //'http://apps.facebook.com/hotdish/?p=home';
				var homelinktext = document.createTextNode('Return to the '+document.getElementById('SITE_TITLE').getValue()+' home page to view your new featured stories template.');
				homelink.appendChild(homelinktext);
				var container = document.getElementById('template-container');
				container.appendChild(homelink);
				*/
				alert('Saved template successfully.');
			} else {
				alert('There was a problem loading the stories.');
			}
		}
	}
	http_request.open('GET', '?p=save_template'+params, true);
	http_request.send(null);

	return false;
}
