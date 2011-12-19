/**


*/


$('img').each(function( i, el ){
    $(el).attr('src', el.src );
});

var selectors = '#block-block-119 .content,p,h1,h2,h3,h4,h5,.teamblock';
//var selectors = '#block-block-146';

//var containment = $( ".selector" ).draggable( "option", "containment" );

/*
$(selectors).draggable({ containment: 'window' }).each(function(i,el){
    $(el).css('position', 'absolute');
});
*/
$(selectors).each( function(i, el){
    var offset = $(el).offset();
    var position = $(el).position();

    console.log(offset, 'offset');
    console.log(position, 'position');

    create_glue({
        css: {
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
        },
        content: el.innerHTML
    });

});


function create_glue( el_data ){
//    console.log( element_css );

    $.getJSON("http://hotglue2.localhost/jsonp.php?action=create&callback=?",
        function(data) {
            var id = parseInt(data['#data'].name.split('.').pop());
            $.getJSON("http://hotglue2.localhost/jsonp.php?&callback=?",{
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


//$.getJSON('http://hotglue2.localhost/jsonp.php', function(data) {});

//print_glue(nodes);
