module.exports = function() {

    var $ = require('jquery');

    var levelScript = require('./LevelScriptListing')();
    var LayoutComponent = require('./../LayoutComponent');

    var self = {

        get: function ( callback ) {
            $.get(window.routes.component.levels, callback);
        },

        load: function () {

            self.get(function (levels) {

                var element = $(levels);

                element.find('select').change(self._onLevelChange);

                var component = new LayoutComponent('levels_dropdown', element);
                window.layout.add(component, 'SIDEBAR_LEFT');
            });

        },

        _onLevelChange: function () {
            levelScript.load($(this).val());
        }

    };

    return {
        get: self.get,
        load: self.load
    };
};