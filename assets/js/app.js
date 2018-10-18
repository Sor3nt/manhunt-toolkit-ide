/**
 * Include the needed CSS
 */
require('../css/bootstrap.min.css');
require('../css/app.css');

/**
 * Boot application
 */
require('./bootstrap.min');
var $ = require('jquery');

//boot ACE Editor
require('./vendor/ace/ace');
require('./vendor/ace/ext-language_tools');
require('./vendor/ace/theme-twilight');
require('./Component/LevelScriptEditor/Mode');


/**
 * boot application
 */

window.layout = require('./Layout')();

var LayoutTabs = require('./LayoutTabs');
window.layoutTabs = new LayoutTabs('MAIN_TABS');
window.layoutRightTabs = new LayoutTabs('SIDEBAR_RIGHT_TABS');

$(document).ready(function() {

    //fill the level dropdown
    var levelsDropdown = require('./Component/LevelsDropdown')();
    levelsDropdown.load();


    // create simple toggler for tree structure
    $( document ).on( "click", "span.item-folder i", function() {
        var next = $(this).parent().next();

        if (next.hasClass('subtree')){
            $(this).toggleClass('down', 'right');
            next.toggle();
        }
    });

});


