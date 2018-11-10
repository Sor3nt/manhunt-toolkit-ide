module.exports = function( content ) {

    var $ = require('jquery');
    var LayoutTab = require('./../../LayoutTab');

    var self = {

        _content : $(content),
        _editor : [],
        _components : [],

        _init : function () {
            self._components = self._content.find('[data-layout]');
        },

        init : function () {

            $.each(self._components, function (index, componentElement) {

                componentElement = $(componentElement);
                //
                // console.log(componentElement);
                // if (componentElement.attr('data-layout') == "MAIN_TOOLBAR"){
                //     var target = $('#tool-bar');
                //     target.append(componentElement);
                //
                //     // self._components.push( componentElement );
                // }

                // if (componentElement.attr('data-layout') == "SIDEBAR_RIGHT"){
                //
                //
                //     var simpleTab = require('./../Simple')(componentElement);
                //     var tab = new LayoutTab(componentElement.attr('data-tab-title'), simpleTab);
                //
                //     window.layoutRightTabs.addTab( tab );
                //
                //     self._components.push( tab.element );
                //     self._components.push( simpleTab.element );
                // }


                $.each(componentElement.find('[data-editor]'), function (index, element) {


                    var editor = ace.edit(element);
                    editor.setTheme("ace/theme/twilight");
                    editor.session.setMode("ace/mode/manhunt");

                    editor.on('change', function () {
                        self._onChange(editor);
                    });


                    self._editor.push(editor);

                });

            });

        },

        _onChange: function (editor) {
            var tokens = window.tokenizer.tokenize(editor.getValue());

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