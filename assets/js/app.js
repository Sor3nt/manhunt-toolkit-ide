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


window.tokenizer = require('./Tokenizer/Tokenizer')();
//
// console.log(window.tokenizer.tokenize('runscript(\'bla\', \'bla\');'));
// console.log(Tokenizer.tokenize(localStorage.getItem('test')));
// exit;
/**
 * boot application
 */

window.layout = require('./Layout')();

var LayoutTabs = require('./LayoutTabs');
window.layoutTabs = new LayoutTabs('MAIN_TABS');
window.layoutRightTabs = new LayoutTabs('SIDEBAR_RIGHT_TABS');

window.levelScript = require('./Component/LevelScriptListing')();
window.tokenInspector = require('./Tokenizer/Inspector')();


$(document).ready(function() {

    //fill the level dropdown
    var levelsDropdown = require('./Component/LevelsDropdown')();
    levelsDropdown.load(function () {

        window.levelScript.load('A01_Escape_Asylum', function () {

            /**
             *
             * D E B U G
             *
             */

            var link = window.routes.component.level.levelScript
                .replace('--level--', "A01_Escape_Asylum")
                .replace('--script--', "A01_Escape_Asylum");

            $.get(link, function (levelScript) {

                var LevelScriptEditor = require('./Component/LevelScriptEditor/Editor');
                var LayoutTab = require('./LayoutTab');

                var componentHandler = new LevelScriptEditor(levelScript, 'A01_Escape_Asylum', 'A01_Escape_Asylum');
                var tab = new LayoutTab("A01_Escape_Asylum", componentHandler);

                window.layoutTabs.addTab( tab );
            });

            /**
             *
             * D E B U G END
             *
             */


        });


    });


    // create simple toggler for tree structure
    $( document ).on( "click", "span.item-folder i", function() {
        var next = $(this).parent().next();

        if (next.hasClass('subtree')){
            $(this).toggleClass('down', 'right');
            next.toggle();
        }
    });

});


