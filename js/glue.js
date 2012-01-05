var glue_me = (function(){
    var selectors = 'a:visible,img,p,h1,h2,h3,h4,h5';
    var selectors = '#festival-menu-1,#block-block-238 img';
    var target    = 'http://hotglue2.localhost/tm-glue.php';

    $(document.body).append(
        $('<img onclick="glue_me.go()" id="glueme" '
          +'src="https://github.com/mvhenten/hotglue2/raw/master/img/hotglue-logo.png" '
          + 'style="position:absolute; top:10%; right:10%; z-index:999999; cursor:pointer; " '
          +'alt="hotglue me">').hide()
    );

    $('#glueme').draggable().fadeIn(1000);

    return {
        go: function(){
            var page = {
                title: $('title').text(),
                style: $('body').collectCSS()[0].style,
                elements: this.collect()
            }

            console.log(page);

            $.post(target, {data: JSON.stringify(page)}, function(data){
                console.log(data);
            });
        },

        collect: function(){
            this.sanitizeImages();

            var collect = $( $(selectors).collectCSS() ).map(function(i, obj ){
                var offset = $(obj.element).offset();

                obj.style.top  = offset.top + 'px';
                obj.style.left = offset.left + 'px';
                obj.properties = {};

                switch( obj.element.tagName.toLowerCase() ){
                    case 'img':
                        obj.properties.src  = obj.element.src;
                        obj.type = 'image';
                        obj.text = '';
                        break;
                    case 'a':
                        obj.properties.href = obj.element.href;
                        obj.type = 'link';
                        obj.text = $(obj.element).text();
                        break;
                    default:
                        $(obj.element).find('*').inlineCSS();
                        obj.type = 'text';
                        obj.text = $(obj.element).html();
                }

                delete(obj.element);
                return obj;
            });

            return $.makeArray(collect);
        },

        sanitizeImages: function(){
            /* need to set src attribute explicitly */
            $('img').each(function( i, el ){
                $(el).attr('src', el.src );
            });
        }
    }
})();
