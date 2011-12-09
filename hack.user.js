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
            var script = document.createElement("script");
            script.textContent = "(" + callback.toString() + ")();";
            ( document.body || head ).appendChild( script );
        }
    };

    // use body if available. more safe in IE
    (document.body || head).appendChild(s); 
}

scriptTag('https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', function(){
    scriptTag( 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js', function(){
        scriptTag('http://sandbox.localhost/glue-hack.js', function(){
            console.log('hack loaded');
        });    
    });
})
