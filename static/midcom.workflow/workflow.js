$(document).ready(function()
{
    $('body').on('click', '[data-dialog="delete"]', function(event)
    {
        event.preventDefault();
        var button = $(this),
            options = {
                title:  button.data('dialog-heading'),
                resizable: false,
                modal: true,
                width: 'auto',
                buttons: {}
            },
            label = button.text(),
            action = button.attr('href') || button.data('action');

        if (label.trim() === '')
        {
            label = button.data('dialog-heading');
        }

        options.buttons[label] = function() {
            $('<form action="' + action + '" method="post">')
                .append($('<input type="submit" name="' + button.data('form-id') + '">'))
                .append($('<input type="hidden" name="referrer" value=' + location.pathname + '">'))
                .hide()
                .prependTo('body');
            $('input[name="' + button.data('form-id') + '"]').click();
        };
        options.buttons[button.data('dialog-cancel-label')] = function() {
            $(this).dialog( "close" );
        };
        $('<div>')
            .css('min-width', '300px') // This should be handled by dialog's minWidth option, but that doesn't work with width: "auto"
                                       // Should be fixed in https://github.com/jquery/jquery-ui/commit/643b80c6070e2eba700a09a5b7b9717ea7551005
            .append($('<p>' + button.data('dialog-text') + '</p>'))
            .appendTo($('body'))
            .dialog(options);
    });

    $('body').on('click', '[data-dialog="datamanager"]', function(event)
    {
        event.preventDefault();
        if (!$(this).hasClass('active'))
        {
            create_dialog($(this), $(this).find('.toolbar_label').text() || $(this).attr('title') || '', $(this).attr('href'));
        }
    });
});

function create_dialog(control, title, url)
{
    if ($('.midcom-workflow-dialog').length > 0)
    {
        $('.midcom-workflow-dialog .ui-dialog-content').dialog('close');
    }

    var dialog, iframe,
        config = {
            dialogClass: 'midcom-workflow-dialog',
            buttons: [],
            title: title,
            close: function() {
                control.removeClass('active');
                iframe.css('visibility', 'hidden');
                if (iframe[0].contentWindow)
                {
                    iframe[0].contentWindow.stop();
                }
            },
            open: function() {
                dialog.closest('.ui-dialog').focus();
            }};

    if (control.data('dialog-cancel-label'))
    {
        config.buttons.push({
            text: control.data('dialog-cancel-label'),
            click: function() {
                $(this).dialog( "close" );
            }
        });
    }

    if ($('#midcom-dialog').length > 0)
    {
        dialog = $('#midcom-dialog');
        iframe = dialog.find('> iframe');
    }
    else
    {
        iframe = $('<iframe name="datamanager-dialog"'
                   + ' frameborder="0"'
                   + ' marginwidth="0"'
                   + ' marginheight="0"'
                   + ' width="100%"'
                   + ' height="100%"'
                   + ' scrolling="auto" />')
            .on('load', function()
            {
                $(this).css('visibility', 'visible');
            });

        dialog = $('<div id="midcom-dialog"></div>')
            .append(iframe)
            .insertAfter(control);

        config.height = Math.min(550, $(window).height());
        config.width = Math.min(700, $(window).width());
    }

    if (url)
    {
        iframe.attr('src', url);
    }

    control.addClass('active');
    if (   control.parent().attr('role') === 'gridcell'
        && control.closest('.jqgrow').hasClass('ui-state-highlight') === false)
    {
        //todo: find out why the click doesn't bubble automatically
        control.parent().trigger('click');
    }
    dialog.dialog(config);
}
