    
    var fxfo_patternmethods = {};
    
    //required
    fxfo_patternmethods["fxfo_required"] = function(element) {

           return (element.attr('type') == 'checkbox' && element.prop('checked')) 
                  || (element.attr('type') != 'checkbox' && element.val() != '');    
    };

    //numeric
    fxfo_patternmethods["fxfo_numeric"] = function(element) {

        return $.isNumeric( element.val().replace( ',', '.' ) );
    }

    //min
    fxfo_patternmethods["fxfo_min"] = function(element, params) {

      return (element.val().length >= params[0]);
    }

    //max
    fxfo_patternmethods["fxfo_max"] = function(element, params) {

      return (element.val().length <= params[0]);
    }

    //email
    fxfo_patternmethods["fxfo_email"] = function(element) {

        var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);

        return pattern.test(element.val());
    }

    //confirm
    fxfo_patternmethods["fxfo_confirm"] = function(element, params) {

        return (element.val() == $('[name=' + params[0] + ']').val());
    }

    //confirm
    fxfo_patternmethods["fxfo_logic"] = function(element, params) {

        var logic = params[0];
        var value2 = params[1];
        var value1 = element.val();

        if (logic == '==') {

            var result = (value1 == value2);
        }

        else if (logic == '!=') {

            var result = (value1 != value2);
        }

        else if (logic == '>') {

            var result = (value1 > value2);
        }

        else if (logic == '<') {

            var result = (value1 < value2);
        }

        else if (logic == '>=') {

            var result = (value1 >= value2);
        }

        else if (logic == '<=') {

            var result = (value1 <= value2);
        }

        else {

            console.error('[fxfo]: logic function' + logic + 'couldn\'t be found');
        }

        return result;    
    }