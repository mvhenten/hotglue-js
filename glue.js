var glue_me = (function(){
    var selectors = 'a:visible,p,h1,h2,h3,h4,h5';
    var target    = 'http://hotglue2.localhost/jsonp.php';

    $(document.body).append('<img onclick="glue_me.now()" id="glueme" src="https://github.com/mvhenten/hotglue2/raw/master/img/hotglue-logo.png" style="position:absolute; top:10%; right:10%; z-index:999999; cursor:pointer;" alt="hotglue me">');
    $('#glueme').draggable();


    return {
        now: function(){
            var self = this;
            $('img').each(function( i, el ){
                $(el).attr('src', el.src );
            });

            $(selectors).each( function(i, el){
                var offset = $(el).offset();
                var position = $(el).position();
                self.glue(el);
            });
        },
        glue: function( el ){
            var offset = $(el).offset();
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

            $(el).find('*').inlineCSS();

            this.save({
                css: css,
                content: el.innerHTML
            });
        },
        save: function( el_data ){
            $.getJSON( target + "?action=create&callback=?",
                function(data) {
                    var id = parseInt(data['#data'].name.split('.').pop());
                    $.getJSON( target + "?&callback=?",{
                        action: 'save',
                        id: id,
                        css: JSON.stringify( el_data.css ),
                        content: el_data.content
                    },
                    function(data){
                        //console.log(data);
                });
            });
        },
        absolutize: function(el){
            //    $(document.body).append( el.parentNode.removeChild(el) );
            $(el).css({
                position: 'absolute',
                top: css.top + 'px',
                left: css.top + 'px'
            });
        }
    };
})();
