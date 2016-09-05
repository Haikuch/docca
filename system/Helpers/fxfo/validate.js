/**
 * ErrorMessage als bestanden markieren
 * 
 * @param {type} ruleId
 * @returns {undefined}
 */
function fxfo_setErrorMsg_passed(ruleId) {

    var element_errorMsg = $('.fxfo_errorMsg[ruleId=' + ruleId + ']');
        
    //Das Styleattribut der Regelnachricht setzen
    element_errorMsg.attr('isError', 'false');
}

/**
 * ErrorMessage als fehlgeschlagen markieren
 * 
 * @param {type} ruleId
 * @returns {undefined}
 */
function fxfo_setErrorMsg_failed(ruleId) {

    var element_errorMsg = $('.fxfo_errorMsg[ruleId=' + ruleId + ']');
    
    //Das Styleattribut der Regelnachricht setzen
    element_errorMsg.attr('isError','true');
}    

/**
 * Element als bestanden markieren
 * 
 * @param {type} elemName
 * @returns {undefined}
 */
function fxfo_setElem_passed(elemName) {

    var element = $('[name=' + elemName + ']');
        
    //Das Feld wird als korrekt markiert
    element .addClass('fxfo_noError')
            .removeClass('fxfo_error');
}   

/**
 * Element als fehlgeschlagen markieren
 * 
 * @param {type} elemName
 * @returns {undefined}
 */
function fxfo_setElem_failed(elemName) {

    var element = $('[name=' + elemName + ']');
        
    //Das Feld wird als korrekt markiert
    element .addClass('fxfo_error')
            .removeClass('fxfo_noError');
}

/**
 * Element prüfen
 * 
 * @param {type} elemName Name des Elements im Formular
 * @returns {Boolean}
 */
function checkElement(elemName) {
    
    //Rules des Elements durchgehen
    for (ruleId in rules[elemName]) {
    
        //Eine Rule falsch = Element falsch
        if (!checkRule(elemName, ruleId)) {
            
            //Element als fehlgeschlagen markieren
            fxfo_setElem_failed(elemName);
            
            return false;
        }
    }
    
    //Element als bestanden markieren
    fxfo_setElem_passed(elemName);
    
    //Falls kein Fehler gefunden wurde
    return true;
}

/**
 * Rule prüfen und Message markieren
 * 
 * @param {type} elemName
 * @param {type} ruleId
 * @returns {Boolean}
 */
function checkRule(elemName, ruleId) {
    
    //Patterns der Rule durchgehen
    for (patternId in rules[elemName][ruleId]) { 
        
        //Ein Pattern falsch = Rule falsch
        if (!checkPattern(elemName, ruleId, patternId)) {
            
            //MSG als fehlgeschlagen markieren
            fxfo_setErrorMsg_failed(ruleId);
            
            return false;
        }        
    }
    
    //MSG als bestanden markieren
    fxfo_setErrorMsg_passed(ruleId);
    
    //Falls kein Fehler gefunden wurde
    return true;
}

/**
 * Pattern prüfen
 * 
 * @param {type} elemName
 * @param {type} ruleId
 * @param {type} patternId
 * @returns {unresolved}
 */
function checkPattern(elemName, ruleId, patternId) {
    
    var methodname  = rules[elemName][ruleId][patternId]['method'];
    var invers      = rules[elemName][ruleId][patternId]['invers'];
    var params      = rules[elemName][ruleId][patternId]['params'];

    //Methode aufrufen
    //TODO: input besser callen
    var pattern_result = fxfo_patternmethods['fxfo_' + methodname]($('[name=' + elemName + ']'), params);

    //Resultat umdrehen
    if (invers) {

        pattern_result = !pattern_result;
    }

    //Ergebnis zurückgeben
    return pattern_result;
}

/**
 * Default SubmitStopper Funktion
 * 
 * @param {type} failedElements
 * @returns {Boolean}
 */
function fxfo_defaultSubmitStopper(failedElements) {
    
    var string = '';
    
    for (var i=0; failedElements.length>i; i++) {
        
        string = string + failedElements[i];
    }
    
    $('form.fxfo-form .submitStopper').show('fast');
    
    return false;
}

//Prüfung starten
$('input, select').bind('focus change keyup', function() {
    
    var elemName = $(this).attr('name');
    
    //Element prüfen
    checkElement(elemName);
});

//ErrorMsg hervorheben
$('input, select').bind('focusin', function() { 
    
    $('.fxfo_errorMsg[field=' + $(this).attr('name') + ']').attr('isFocus','true');    
});

//ErrorMsg verstecken
$('input, select').bind('focusout', function() {   
    
    $('.fxfo_errorMsg[field=' + $(this).attr('name') + ']').attr('isFocus','false');
});

//Submitstopper    
$('.fxfo-form [type=submit]').click(function () {
    
    //Falls Submitstop deaktiviert ist nichts unternehmen
    if (!fxfo_submitStop) {
        
        return true;
    }
    
    var failedElements = new Array();
    var i = 0;

    //Alle Elemente durchlaufen
    for (elemName in rules) {
        
        //Element fehlgeschlagen
        if (!checkElement(elemName)) { 

            failedElements[i] = elemName;

            i++;
        }
    }

    //Falls keine Elemente fehlgeschlagen
    if (failedElements.length === 0) {
        return true;
    }

    //Falls eine spezielle Funktion erstellt wurde diese aufrufen
    if (typeof(window.submitStopper) === "function") {
    
        return submitStopper(failedElements);
    }
    
    //Default Funktion aufrufen
    else {
        
        return fxfo_defaultSubmitStopper(failedElements);  
    }        
}); 

//Formular trotz Fehler senden
$('form.fxfo-form .submitStopper .submitAnyway').click(function() {
    
    //TODO: klappt nicht, aber liegts am fxfo?
    $(this).closest('form').submit();    
});

//Formular nochmals prüfen
$('form.fxfo-form .submitStopper .checkAgain').click(function() {
    
    $(this).closest('form.fxfo-form .submitStopper').hide('fast');    
});

//Rules-Datenobjekt initialisieren
var rules = new Object();


