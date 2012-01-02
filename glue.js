var glue_me = (function(){
    var selectors = 'a:visible,p,h1,h2,h3,h4,h5';
    console.log($(selectors).collectCSS());

    $(document.body).append(
        $('<img onclick="glue_me.now()" id="glueme" '
          +'src="https://github.com/mvhenten/hotglue2/raw/master/img/hotglue-logo.png" '
          + 'style="position:absolute; top:10%; right:10%; z-index:999999; cursor:pointer; " '
          +'alt="hotglue me">').hide()
    );


    $('#glueme').draggable().fadeIn(1000);

    return {
        collect: function(){
            this.sanitizeImages();

            var collection = $( $(selectors).collectCSS() ).map(function(i, obj ){
                var offset = $(el).offset();

                obj.style.top  = offset.top + 'px';
                obj.style.left = offset.left + 'px';

                $(obj.element).find('*').inlineCSS();

                obj.tagName = obj.element.tagName.toLowerCase();

                switch( obj.tagName ){
                    case 'img':
                        obj.src  = obj.element.src;
                        break;
                    case 'a':
                        obj.href = obj.element.href;
                        break;
                }

                return obj;
            });

            //this.save( collection );
        },

        sanitizeImages: function(){
            $('img').each(function( i, el ){
                $(el).attr('src', el.src );
            });
        }
    }



    //var target    = 'http://hotglue2.localhost/jsonp.php';
    //

    //
    //
    //return {
    //    now: function(){
    //        var self = this;
    //
    //        $(selectors).each( function(i, el){
    //            var offset = $(el).offset();
    //            var position = $(el).position();
    //            self.glue(el);
    //        });
    //    },
    //    save: function( el_data ){
    //        $.getJSON( target + "?action=create&callback=?",
    //            function(data) {
    //                var id = parseInt(data['#data'].name.split('.').pop());
    //                $.getJSON( target + "?&callback=?",{
    //                    action: 'save',
    //                    id: id,
    //                    css: JSON.stringify( el_data.css ),
    //                    content: el_data.content
    //                },
    //                function(data){
    //                    //console.log(data);
    //            });
    //        });
    //    },
    //    absolutize: function(el){
    //        //    $(document.body).append( el.parentNode.removeChild(el) );
    //        $(el).css({
    //            position: 'absolute',
    //            top: css.top + 'px',
    //            left: css.top + 'px'
    //        });
    //    }
    //};
})();
