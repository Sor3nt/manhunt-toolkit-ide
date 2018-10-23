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
require('./vendor/ace/ext-searchbox');
require('./vendor/ace/theme-twilight');
require('./Component/LevelScriptEditor/Mode');


var Tokenizer = require('./Tokenizer/Tokenizer')();
//
// console.log(Tokenizer.tokenize('scriptmain LevelScript; entity A01_Escape_Asylum : et_level;'));
console.log(Tokenizer.tokenize(localStorage.getItem('test')));

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


