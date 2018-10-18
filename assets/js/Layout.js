module.exports = function() {

    var $ = require('jquery');

    var self = {

        _component: {},

        _elements : {
            SIDEBAR_LEFT: $('#left-panel .list'),
            SIDEBAR_RIGHT: $('#right-panel .list'),
            SIDEBAR_RIGHT_TABS : $('#right-panel .tab-holder'),
            MAIN_TABS : $('#center-panel .tab-holder'),
            MAIN_CONTENT : $('#canvas')

        },

        add: function ( layoutComponent, section ) {

            self._component[ layoutComponent.getName() ] = layoutComponent;

            self._elements[section].append( layoutComponent.getElement() );
        },

        get: function (componentName) {
            if (typeof self._component[ componentName ] !== "undefined")
                return self._component[ componentName ];

            return false;
        }


    };

    return {
        add: self.add,
        get: self.get
    };
};