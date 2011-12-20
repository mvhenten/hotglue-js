$('img').each(function( i, el ){
    $(el).attr('src', el.src );
});

//var selectors = 'a,#block-block-119 .content,p,h1,h2,h3,h4,h5,.teamblock';
var selectors = 'a:visible,p,h1,h2,h3,h4,h5,table';

$(selectors).each( function(i, el){
    var offset = $(el).offset();
    var position = $(el).position();

    var css = {
        'z-index': 100,
        width: $(el).width() + 'px',
        height: $(el).height() + 'px',
        top: offset.top + 'px',
        left: offset.left + 'px',
        'background-color': $(el).css('background-color'),
        'color': $(el).css('color'),
        'line-height': $(el).css('line-height'),
        'font-family': $(el).css('font-family'),
        'font-size': $(el).css('font-size'),
        'font-style':$(el).css('font-style'),
        'font-weight':$(el).css('font-weight')
    }


    create_glue({
        css: css,
        content: el.innerHTML
    });


//    absolutize( el, css );
//    $(document.body).append( el.parentNode.removeChild(el) );

});

function absolutize( el, css ){
    $(el).css({
        position: 'absolute',
        top: css.top + 'px',
        left: css.top + 'px'
    });
}


function create_glue( el_data ){
    $.getJSON("http://tm12.hotglue.org/hotglue2/jsonp.php?action=create&callback=?",
        function(data) {
            var id = parseInt(data['#data'].name.split('.').pop());
            $.getJSON("http://tm12.hotglue.org/hotglue2/jsonp.php?&callback=?",{
                action: 'save',
                id: id,
                css: JSON.stringify( el_data.css ),
                content: el_data.content
            },
            function(data){
                console.log(data);
        });
    });
}
