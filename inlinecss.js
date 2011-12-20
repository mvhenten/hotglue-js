(function($) {
    function getInlineTextCSS( el ){
        var props = (
            'font-size font-family font-weight color letter-spacing word-spacing '
            +'text-align padding-left padding-right padding-top padding-bottom'
        ).split(/\s/);

        var collect = [];

        for( var i = 0, len = props.length; i < len; i++ ){
            var prop_name = props[i];
            var value = $(el).css(prop_name);

            if( value ){
                collect.push( prop_name + ':' + value );
            }
        }

        return collect.join(';');
    }

    function getInlineBordersCSS( el ){
        var dirs    = 'top right bottom left'.split(' ');
        var collect = [];
        for( var i = 0, len = dirs.length; i < len; i++ ){
            collect.push(getInlineBorderCSS(el, dirs[i]));
        }

        return collect.join(';');
    }

    function getInlineBorderCSS( el, dir ){
        var borders = 'border-%s-width border-%s-style border-%s-color';
        var spec     = borders.replace(/%s/g, dir ).split(' ');
        var collect  = [];

        for( var j = 0, len = spec.length; j < len; j++ ){
            var value = $(el).css(spec[j]);
            collect.push(value);
        }

        return 'border-' + dir + ':' + collect.join(' ');
    }

	$.fn.inlineCSS = function() {
		this.each(function() {
            $(this).attr('style', [
                getInlineTextCSS(this),
                getInlineBordersCSS(this)
            ].join(';') + ';');
		});
		return this;
	};
})(jQuery);

