(function () {
    tinymce.PluginManager.add('ba_mce_button', function (editor, url) {
        editor.addButton('ba_mce_button', {
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
})();