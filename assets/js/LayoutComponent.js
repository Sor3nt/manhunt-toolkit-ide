module.exports = function( name, element) {

    var self = {
        _name : name,
        _element : element,

        remove: function () {
            if (self._element !== false) self._element.remove();
            self._element = false;
        },

        setElement: function (element) {
            self._element = element;
        },

        getElement: function () {
            return self._element;
        },

        getName: function () {
            return self._name;
        }
    };

    return {
        getName: self.getName,
        remove: self.remove,
        getElement: self.getElement,
        setElement: self.setElement
    }

};