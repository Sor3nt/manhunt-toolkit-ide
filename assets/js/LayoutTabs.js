module.exports = function( target ) {

    var $ = require('jquery');

    var LayoutComponent = require('./LayoutComponent');

    var self = {

        _tabs : {},

        addTab: function ( tab ) {

            self.hide();
            tab.show();

            tab.element.click( function () {
                self.hide();
                tab.show();
            });

            // add the tab
            var tabComponent = new LayoutComponent('tab', tab.element);
            window.layout.add(tabComponent, target);

            //append any given components
            $.each(tab.componentHandler.components, function (index, component) {
                component = $(component);

                var editorComponent = new LayoutComponent('', component);
                window.layout.add(editorComponent, component.attr('data-layout'));
            });

           //
            self._tabs[ tab.getId() ] = tab;
            tab.onPostAppend();
        },

        hide: function () {
            for(var i in self._tabs){
                if (!self._tabs.hasOwnProperty(i)) continue;
                self._tabs[i].hide();
            }
        }

    };

    return {
        hide: self.hide,
        show: self.show,
        addTab: self.addTab
    };
};