module.exports = function( content ) {

    var $ = require('jquery');

    var self = {

        _element : $(content),

        init : function () {
        },

        remove : function () {
            self._element.remove();
        },

        hide: function () {
            self._element.hide();
        },

        show: function () {
            self._element.show();
        }
    };

    return {
        element: self._element,
        remove: self.remove,
        hide: self.hide,
        show: self.show,
        init: self.init
    };
};