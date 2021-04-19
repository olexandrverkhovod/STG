jQuery(document).on('tinymce-editor-setup', function (event, editor) {
    editor.settings.toolbar1 += ',widget_mce_button';
    editor.addButton('widget_mce_button', {
        icon: false,
        text: 'Button',
        title: 'Add button shortcode',
        onclick: function () {
            editor.windowManager.open({
                title: 'Insert button',
                body: [
                    {
                        type: 'textbox',
                        name: 'buttonTitle',
                        label: 'Title',
                    },
                    {
                        type: 'textbox',
                        name: 'buttonUrl',
                        label: 'URL',
                    }
                ],
                onsubmit: function (e) {
                    editor.insertContent('[button title="' + e.data.buttonTitle + '" link="' + e.data.buttonUrl + '"]');
                }
            });
        }
    });
});