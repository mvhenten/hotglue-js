/**


*/


var childs = $(document.body).children().toArray();

stack = [];
nodes = [];

// create the stack, in such a format we can record
// the 'depth'
for( var i = 0, len = childs.length; i < len; i++ ){
    stack.push( { depth: 0, element: childs[i] } );
}

$('div').draggable();

while( stack.length ){
    var el = stack.pop();
    var childs = $(el.element).children().toArray();

    if( childs.length == 0 ) continue;
    if( el.element.tagName !== 'DIV' ) continue;
    if( el.depth > 6 ) continue;

    // add children, record depth
    for( var i = 0, len = childs.length; i < len; i++ ){
        stack.push({ depth: el.depth + 1 , element: childs[i] });
    }

    // process the element: retrieve offset, widht, height
    el.properties = {
        offset: $(el.element).offset(),
        zIndex: el.depth,
        width: $(el.element).width(),
        height: $(el.element).height()
    };
    
    nodes.push(el);
}

function absolutize_nodes( nodes ){
    for( var i = 0, len = nodes.length; i < len; i++ ){
        var node = nodes[i], el = node.element;
        
        $(el).css({
            position: 'absolute',
            top: node.properties.offset.top + 'px',
            left: node.properties.offset.left + 'px',
            width: node.properties.width + 'px',
            height: node.properties.height + 'px',
            zIndex: node.depth
        });
        
//        console.log(el);
 //      break;
    }
}


function make_draggable_nodes( nodes ){
    for( var i = 0, len = nodes.length; i < len; i++ ){
        $(nodes[i].element ).draggable();
    
    }
}
//absolutize_nodes( nodes );

//make_draggable_nodes(nodes);
//$('p').draggable();


function print_glue( nodes ){
    var n = $('<pre></pre>');
    for( var i = 0, len = nodes.length; i < len; i++ ){
        var prop = nodes[i].properties

        n.append(
            [ 'object-zindex: ' + prop.zIndex ,
              'object-width: ' + prop.width,
              'object-height: ' + prop.height,
              'object-left: ' + prop.offset.left,
              'object-top: ' + prop.offset.top,
              "\n\n",
            ].join("\n")
        );
    }
    $(document.body).append(n);
}

print_glue(nodes);
    

