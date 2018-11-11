module.exports = function( content, level, script ) {

    var $ = require('jquery');
    var LayoutTab = require('./../../LayoutTab');

    var self = {

        _content : $(content),
        _editor : {},
        _components : [],

        _init : function () {
            self._components = self._content.find('[data-layout]');
        },

        init : function () {

            var editorElement = self._components.find('[data-editor]');

            var editor = ace.edit(editorElement.get(0));
            editor.setTheme("ace/theme/twilight");
            editor.session.setMode("ace/mode/manhunt");
            editor.on('change', self._onChange);

            self._editor = editor;

            self._createEvents();
        },

        _createEvents: function () {

            self._components.find('[data-action="save"]').click(self._doSave);
        },

        _doSave: function () {

            var link = window.routes.component.level.levelScriptSave
                .replace('--level--', level)
                .replace('--script--', script);


            $.post(link, {
                srce: self._editor.getValue()
            }, function (response) {

                console.log(response);

            });

        },

        _onChange: function () {
            var tokens = window.tokenizer.tokenize(self._editor.getValue());

console.log(tokens);

            console.log("ok", window.tokenInspector.inspect(tokens, [
                'script_references',
                'defined_scripts'
            ]));

        },

        remove : function () {

            $.each(self._editor, function (index, editor) {
                editor.destroy();
            });

            $.each(self._components, function (index, componentElement) {
                componentElement = $(componentElement);
                componentElement.remove();
            });

        },

        hide: function () {
            $.each(self._components, function (index, componentElement) {
                componentElement = $(componentElement);
                componentElement.hide();
            });
        },

        show: function () {
            $.each(self._components, function (index, componentElement) {
                componentElement = $(componentElement);
                componentElement.show();
            });
        }
    };

    self._init();

    return {
        components: self._components,
        remove: self.remove,
        hide: self.hide,
        show: self.show,
        init: self.init
    };
};