module.exports = function() {

    var $ = require('jquery');

    var self = {

        createCategory: function ( name, clickCallback ) {

            var container = $('<span>');
            container.addClass('item-folder');

            var icon = $('<i>');
            icon.addClass('right');

            var text = $('<b>');
            text.addClass('name');
            text.html( name );

            container.append(icon);
            container.append(text);

            if (typeof clickCallback == "function") text.click( function () {
                clickCallback( name );
            } );

            return container;
        },

        createSubTree: function ( entries, clickCallback) {

            var container = $('<div>');
            container.addClass('subtree');

            $.each(entries, function (index, entry) {

                var item = self.createCategory(entry, clickCallback);
                container.append(item);
            });

            return container;
        }

    };

    return {
        createCategory: self.createCategory,
        createSubTree: self.createSubTree
    };
};