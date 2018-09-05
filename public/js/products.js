$(document).ready(function() {
    $(function(){
        $('textarea.froala-editor').froalaEditor({
			key: '8sxslestE4gjk=='
		})
    });
    "use strict";

    App.init(); // Init layout and core plugins
    Plugins.init(); // Init all plugins
    FormComponents.init(); // Init all form-specific plugins
});
