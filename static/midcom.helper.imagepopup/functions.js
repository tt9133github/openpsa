$(document).ready(function() {
    $('.content-area')
        .on('click', 'a[href], .preview-image', function(event) {
            event.preventDefault();
            var url = this.href || this.src,
                title = '';

            if ($(this).parent('td').length > 0) {
                title = $(this).parent('td').next().find('input').val();
            }

            parent.tinymce.activeEditor.windowManager.getParams().oninsert(url, {alt: title});
            parent.tinymce.activeEditor.windowManager.close();
        })
        .on('mouseover', 'a[href], .preview-image', function() {
            this.title = 'Click to insert';
        });

    if ($('#links').length > 0) {
        $('#links').fancytree({
            click: function(event, data) {
                if (data.targetType === 'title') {
                    parent.tinymce.activeEditor.windowManager.getParams().oninsert(data.node.data.href, {title: data.node.title});
                    parent.tinymce.activeEditor.windowManager.close();
                }
            },
            extensions: ['glyph'],
            glyph: {
                preset: "awesome4"
            }
        });
    }
});
