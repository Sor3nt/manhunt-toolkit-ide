module.exports = function(  ) {

    var Token = require('./Token');
    var $ = require('jquery');

    var tokenHandlers = [


        // Sample: { this is a comment }
        {
            regex: /^((\{([^{^}])*)*\{([^{^}])*\}(([^{^}])*\})*)/,
            map: [Token.T_COMMENT]
        },



        // Sample: true
        { regex: /^(true)/i, map: [ Token.T_TRUE ] },
        //
        // Sample: false
        { regex: /^(false)/i, map: [ Token.T_FALSE ] },


        // Sample: 1.00
        {
            regex: /^(-?\d+.?\d+)/,
            map: [Token.T_FLOAT]
        },

        // Sample: 123
        {
            regex: /^(-?\d+)/,
            map: [Token.T_INT]
        },


        // Sample: ,
        {
            regex: /^(\,)/,
            map: [Token.T_SEPERATOR]
        },

        // Sample: :=
        {
            regex: /^(\:\=)/,
            map: [
                Token.T_DEFINE
            ]
        },

        // Sample: ;
        {
            regex: /^(\;)/,
            map: [Token.T_LINEEND]
        },

        // Sample: (
        {
            regex: /^(\()/,
            map: [Token.T_BRACKET_OPEN]
        },

        // Sample: )
        {
            regex: /^(\))/,
            map: [Token.T_BRACKET_CLOSE]
        },

        // Sample: :
        {
            regex: /^(\:)/,
            map: [Token.T_DEFINE]
        },

        // Sample: scriptmain LevelScript;
        {
            regex: /^(scriptmain)\s*([\w\(\)]+)/i,
            map: [
                Token.T_SCRIPTMAIN,
                Token.T_SCRIPTMAIN_NAME
            ]
        },

        //sample: if
        {
            regex: /^(if)\s?/i,
            map: [Token.T_IF]
        },

        //sample: then
        {
            regex: /^(then)\s?/i,
            map: [Token.T_THEN]
        },

        //sample : lDebuggingFlag = TRUE
        {
            regex: /^(\w+)\s*([=><])\s*(\w+)/,
            map: [
                Token.T_VARIABLE,
                Token.T_CONDITION,
                Token.T_VARIABLE
            ]
        },




        // Sample: tElevatorLevel = (ElevatorUp, ElevatorDown );
        {
            regex: /^(\w+)\s*(=)\s*(\()((\s*\w+,?)+)\s*(\))\s*(;)/,
            map: [
                Token.T_VARIABLE,
                Token.T_IS_EQUAL,
                Token.T_BRACKET_OPEN,
                function( entries ){
                    var result = [];

                    var parts = entries.split(',');
                    $.each(parts, function(index, entry){
                        entry = $.trim(entry);
                        result.push({ type: Token.T_VARIABLE, value: entry})
                    });

                    return result;
                },
                false,
                Token.T_BRACKET_CLOSE,
                Token.T_LINEEND
            ]
        },


        // Sample : var
        {
            regex: /^(var)\s+/i ,
            map: [Token.T_DEFINE_SECTION_VAR]
        },




        // Sample : pos, pos2 : Vec3D;
        {
            regex: /^((\w+, *)+(\w+))\s*(:)\s*(\w+)(;)/ ,
            map: [
                function( entries, matches ){
                    var result = [];

                    var parts = entries.split(',');
                    $.each(parts, function(index, entry){
                        entry = $.trim(entry);

                        result.push({ type: Token.T_VARIABLE, value: entry});
                        result.push({ type: Token.T_DEFINE, value: ':'});
                        result.push({ type: Token.T_DEFINE_TYPE, value: matches[4]});
                    });

                    result.push({ type: Token.T_LINEEND, value: ';' });

                    return result;
                },
                false,
                false,
                false,
                false
            ]
        },


        // Sample : lLevelState : tLevelState;
        {
            regex: /^(\w+)\s*(:)\s*([\w\[\]]+)\s*(;)/ ,
            map: [
                Token.T_VARIABLE,
                Token.T_DEFINE,
                Token.T_DEFINE_TYPE,
                Token.T_LINEEND
            ]
        },






        // Sample: script OnCreate;
        {
            regex: /^(script)\s*(\w+)\s*(;)/i,
            map: [
                Token.T_SCRIPT,
                Token.T_SCRIPT_NAME,
                Token.T_LINEEND
            ]
        },

        // Sample: lLevelState := StartOfLevel;
        {
            regex: /^(\w+)\s*(:)\s*(\w+)\s*(;)/,
            map: [
                Token.T_VARIABLE,
                Token.T_ASSIGN,
                Token.T_FUNCTION,
                Token.T_LINEEND
            ]
        },

        // Sample (strings): "test"
        { regex: /^(['"].*['"])/, map: [ Token.T_STRING ] },

        // Sample : &lt;&gt;
        { regex: /^(\&lt\;\&gt\;)/, map: [ Token.T_IS_NOT_EQUAL ] },
        { regex: /^(\<\>)/, map: [ Token.T_IS_NOT_EQUAL ] },


        // Sample: "end;"
        { regex: /^(end;)\s/i, map: [ Token.T_SCRIPT_END ] },

        // Sample: ":="
        { regex: /^(\:\=)\s/i, map: [ Token.T_ASSIGN ] },

        // Sample: "begin"
        { regex: /^(begin)\s+/i, map: [ Token.T_BEGIN ] },

        // Sample: "end."
        { regex: /^\s(end\.)/i, map: [ Token.T_END_CODE ] },

        // Sample: "function("
        { regex: /^(\w+)(\()/, map: [ Token.T_FUNCTION, Token.T_BRACKET_OPEN ] },

        // Sample: "function;"
        { regex: /^(\w+)(;)/, map: [ Token.T_FUNCTION, Token.T_LINEEND ] },

        // Sample : procedure InitAI; FORWARD;
        { regex: /^(procedure)\s*(\w+)(;)\s*(forward)\s*(;)/i, map: [
            Token.T_PROCEDURE,
            Token.T_FUNCTION,
            Token.T_LINEEND,
            Token.T_FORWARD,
            Token.T_LINEEND
        ] },




        // Sample: entity
        {
            regex: /^(entity)/i,
            map: [Token.T_DEFINE_SECTION_ENTITY]
        },

        {
            regex: /^(type)/i,
            map: [Token.T_DEFINE_SECTION_TYPE]
        },


        // Sample: "varname "
        { regex: /^([\w_]+)/, map: [ Token.T_VARIABLE ] },




        {
            regex: /^(\n)/,
            map: [Token.T_NEWLINE]
        },

        {
            regex: /^(\s+)/,
            map: [Token.T_SPACE]
        }

    ];

    var results = [];

    var lineNumber = 1;

    function match(code) {

        var result = [];

        for(var i in tokenHandlers){
            if (!tokenHandlers.hasOwnProperty(i)) continue;


            var handler = tokenHandlers[i];
// console.log(code, handler.regex);
            var matches = code.match(handler.regex);
            if (matches !== null){
                // console.log("MAAATCH");
                var consumed;

                $.each(matches, function (index, match) {
                    if (index == 0){
                        consumed = match.length;
                        return true;
                    }


                    if (typeof match == "undefined") return true;
                    if (typeof handler.map[ index - 1] == "undefined") return true;
                    if (handler.map[ index - 1] == false) return true;

                    if (typeof handler.map[ index - 1] == "function"){
                        var _matches = handler.map[ index - 1](match, matches);

                        $.each(_matches, function (index, match) {
                            console.log(match);
                            result.push(match);
                        });

                    }else{
                        console.log({
                            type: handler.map[ index - 1],
                            value: match
                        });
                        result.push({
                            type: handler.map[ index - 1],
                            value: match
                        });

                    }
                });

                $.each(result, function(index, entry){
                    if (entry.type == Token.T_NEWLINE){
                        lineNumber++;
                        return true;
                    }else if (entry.type != Token.T_SPACE){
                        result[index].lineNumber = lineNumber;
                    }
                });

                return {
                    tokens : result,
                    consumed: consumed
                };
            }

        }

        console.log('Unable to parse code: ' + code);
        exit;
        return false;
    }


    return {



        tokenize: function ( sourceCode ) {

            var tonkens = [];

            while(sourceCode.length) {
                var result = match(sourceCode);

                $.each(result.tokens, function (index, token) {
                    tonkens.push(token);
                });

                sourceCode = sourceCode.substr(result.consumed);
            }

            console.log(tonkens);
            return tonkens;
        }
    }



};