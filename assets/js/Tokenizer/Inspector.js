module.exports = function( ) {


    var Token = require('./Token');
    var $ = require('jquery');


    return {

        inspect: function ( tokens, scopes ) {

            var results = {};

            var checkScriptReferences = scopes.indexOf('script_references') !== -1;
            var checkDefinedScripts = scopes.indexOf('defined_scripts') !== -1;

            if (checkScriptReferences) results['script_references'] = {};
            if (checkDefinedScripts) results['defined_scripts'] = [];


            $.each(tokens, function (index, token) {

                if (
                    checkScriptReferences &&
                    token.type == Token.T_FUNCTION &&
                    //todo: use regex to match runscript?
                    token.value.toLowerCase() == "runscript"
                ){

                    var entityName = tokens[index + 2].value;
                    var scriptName = tokens[index + 3].value;

                    if (typeof results['script_references'][entityName] == "undefined")
                        results['script_references'][entityName] = [];

                    results['script_references'][entityName].push({
                        scriptName : scriptName,
                        lineNumber : token.lineNumber
                    });

                }


                if (checkDefinedScripts && token.type == Token.T_SCRIPT_NAME){

                    var type = "regular";
                    if (typeof window.manhuntEvents[token.value.toLowerCase()] != "undefined"){
                        type = "event";
                    }

                    results['defined_scripts'].push({
                        value : token.value,
                        type : type,
                        lineNumber : token.lineNumber
                    });

                }

            });

            return results;
        }
    }
};