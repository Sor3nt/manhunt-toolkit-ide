module.exports = function() {

    var $ = require('jquery');

    var LayoutComponent = require('./../LayoutComponent');

    var self = {

        get: function ( callback ) {
            $.get(window.routes.component.levels, callback);
        },

        load: function ( onLoad ) {

            self.get(function (levels) {

                var element = $(levels);

                element.find('select').change(self._onLevelChange);

                var component = new LayoutComponent('levels_dropdown', element);
                window.layout.add(component, 'SIDEBAR_LEFT');

                if (typeof onLoad != "undefined") onLoad();
            });

        },

        _onLevelChange: function () {
            window.levelScript.load($(this).val(), undefined);
        }

    };

    return {
        get: self.get,
        load: self.load
    };
};