module.exports = function() {

    var $ = require('jquery');

    var LayoutComponent = require('./../LayoutComponent');
    var LayoutTab = require('./../LayoutTab');
    var LevelScriptEditor = require('./LevelScriptEditor/Editor');

    var self = {

        load: function ( level, callback ) {
            var link = window.routes.component.level.levelScriptListing.replace('--level--', level);

            $.get(link, function (levelScripts) {

                var element = $(levelScripts);

                element.on('click', '.name', self._openLevelScript);

                var component = new LayoutComponent('levelscript_listing', element);
                window.layout.add(component, 'SIDEBAR_LEFT');

                if (typeof callback !== "undefined") callback();

            });

        },

        _openLevelScript: function () {

            var script = $(this).attr('data-script');
            var link = window.routes.component.level.levelScript
                .replace('--level--', $(this).attr('data-level'))
                .replace('--script--', script);

            $.get(link, function (levelScript) {

                var componentHandler = new LevelScriptEditor(levelScript);
                var tab = new LayoutTab(script, componentHandler);

                window.layoutTabs.addTab( tab );
            });


        }

    };

    return {
        load: self.load
    };
};