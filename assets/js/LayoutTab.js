module.exports = function( name, componentHandler ) {

    var $ = require('jquery');

    var self = {

        _id : 'tab_' + Math.floor(Math.random() * 10000000),

        _element : {},

        _componentHandler : componentHandler,

        _init: function ( name ) {
            var tab = $('<div>').addClass('tab');
            var title = $('<span>').addClass('title').html( name );
            var close = $('<span>').addClass('close') ;

            close.click( self.remove );

            tab.append(title);
            tab.append(close);

            self._element = tab;
        },

        onPostAppend: function () {
            self._componentHandler.init();
        },

        getId: function () {
            return self._id;
        },

        hide: function () {
            self._componentHandler.hide();
            self._element.removeClass('active');
        },

        show: function () {
            self._componentHandler.show();
            self._element.addClass('active');
        },

        remove: function () {
            self._element.remove();
            self._componentHandler.remove();
        }

    };

    self._init(name);

    return {
        element: self._element,
        componentHandler: self._componentHandler,
        show: self.show,
        hide: self.hide,
        getId: self.getId,
        remove: self.remove,
        onPostAppend: self.onPostAppend
    };
};