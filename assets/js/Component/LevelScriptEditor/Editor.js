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

                if (componentElement.attr('data-layout') == "SIDEBAR_RIGHT"){


                    var simpleTab = require('./../Simple')(componentElement);
                    var tab = new LayoutTab(componentElement.attr('data-tab-title'), simpleTab);

                    window.layoutRightTabs.addTab( tab );

                    self._components.push( tab.element );
                    self._components.push( simpleTab.element );
                }

                if (componentElement.find('[data-editor]').length){

                    var editor = ace.edit(componentElement.find('[data-editor]').get(0));
                    editor.setTheme("ace/theme/twilight");
                    editor.session.setMode("ace/mode/manhunt");

                    //
                    //
                    // self._editor.push(
                    //     CodeMirror.fromTextArea(componentElement.find('textarea').get(0), {
                    //         lineNumbers: true,
                    //         matchBrackets: true,
                    //         lineWrapping: true,
                    //         mode: 'simplemode',
                    //         theme: 'darcula'
                    //     })
                    // );
                }
            });

        },

        remove : function () {

            $.each(self._components, function (index, componentElement) {
                componentElement = $(componentElement);
                componentElement.remove();
            });

            $.each(self._editor, function (index, editor) {
                editor.toTextArea();
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