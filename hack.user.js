// ==UserScript==
// @match http://*.transmediale.de/*
// ==/UserScript==

function scriptTag(src, callback) {

	var s = document.createElement('script');
	s.type = 'text/' + (src.type || 'javascript');
	s.src = src.src || src;
	s.async = false;

    s.onreadystatechange = s.onload = function() {

        var state = s.readyState;

        if (!callback.done && (!state || /loaded|complete/.test(state))) {
            callback.done = true;
            callback();
        }
    };

    // use body if available. more safe in IE
    (document.body || head).appendChild(s);
}


scriptTag('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', function(){
    scriptTag( 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', function(){
        scriptTag( 'http://tm12.hotglue.org/js/json2.min.js', function(){
            var s = document.createElement('script');
            s.src = "http://tm12.hotglue.org/js/hotglue-hack.js";
    //	    s.innerHTML = "$('body').children().draggable().children().draggable(); $('td').draggable(); $('img').draggable();";
            (document.body || head).appendChild(s);
        });
    });
})
