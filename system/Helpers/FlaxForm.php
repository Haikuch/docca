<?php

/**
 * Erzeugen von Formularelementen
 * 
 * Formularelemente können einzeln erzuegt und gestaltet werden
 * Zudem können automatisch schon vorhandene Daten in die Felder eingetragen und �berpr�ft werden
 */
class FlaxForm {
    
    //JS Code der zusammengebaut wird
    private $html_jscode;
    
    //Rule-Speicherung
    private $rules = array();                                   
    private $rulesLast;
    private $isErrorForm   = false;                   //BOOL ob irgendwelche Formulareingaben fehlerhaft sind (Regeln verletzt wurden)
    private $rulesAlreadyChecked = false;
    
    //Filter
    private $filters = array();
    
    //Elemente
    private $elements;      
    private $elemNameId;                 
    private $lastElemName; 
    
    //Dropzones
    private $dropzones;
    private $lastDropzone;
    
    //Daten aus dem Formular
    private $data;
    private $dataRaw;
    private $defaultData;
    
    //Autofocus des Formulars
    private $autofocus = false;
    
    //Submitstopper
    private $submitStopper = true;

    /**
     * Bei Aufruf des Objekts wird das Form-Tag erstellt
     * 
     * @param mixed $nameId
     * @param mixed $action
     * @param string $method
     * @param string $moreParams
     * @return void
     */
    public function __construct( $nameId, array $data = NULL ) {
        
        //Übertragene Daten setzen
        $data ? $this->setData($data) : ''; 
        
        //Form-Tag bauen
        $this   ->loadElem('_form_', 'form')
                ->setId($this->makeId( $nameId ))
                ->setName($this->makeName( $nameId ))
                ->addClass('fxfo-form')
                ->setMethod('POST');
        
        return $this;
    }
    
    //TODO: bei jedem neuen Snippet wird datei neu geparst, wenn aber einmal komplett dann werden auch ungebrauchte in die klasse geschrieben
    /**
     * Html etc. Snippets abfragen
     * 
     * @param type $snippet
     * @return type
     */
    private function getSnippet($snippet) {
        
        //Falls Snippet schon extrahiert diesen ausgeben
        if (isset($this->{'snip_' . $snippet})) {

            return $this->{'snip_' . $snippet};
        }
        
        //Datei parsen
        $file = explode("\n", file_get_contents(__DIR__ . '/fxfo/snippets.tpl'));
        
        //Sucharray trimmen
        $search = array_map("trim", $file);
        
        //Snippet finden
        $start = array_search('<!-- start '.$snippet.' -->', $search);
        $end = array_search('<!-- end '.$snippet.' -->', $search);
        
        //Kein vollständiges Snippet gefunden
        if ($start === FALSE OR $end === FALSE) {
            
            $this->error("couldnt find Snippet '".$snippet."'", 'WARNING');        
        }
        
        //Snippet ausschneiden
        $file = array_slice($file, $start, $end-$start);
        
        //Snippet in Variable speichern
        $this->{'snip_' . $snippet} = implode("\n", $file);
        
        //Snippet ausgeben
        return $this->{'snip_' . $snippet};        
    }
    
    /**
     * Interne Fehlermeldungen
     * 
     * @param type $message
     * @param string $type
     */
    private function error($message, $type, $backtrace = NULL) {
    
        //mit Backtrace von bestimmter Tiefe
        if ($backtrace !== NULL) {
            
            //Tiefe speichern
            $distance = $backtrace;
            
            //Backtrace starten
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            
            //Trace-String
            $trace = " (in ".$backtrace[$distance]['file']." on line ".$backtrace[$distance]['line'].") later ";
        }
        
        //Kein Backtrace
        else {
        
            $trace = '';
        }
        
        //Default Types
        $default = array(
            'WARNING'   => E_USER_WARNING,
            'NOTICE'    => E_USER_NOTICE,
            'ERROR'     => E_USER_ERROR,
            'DEPRECATED'=> E_USER_DEPRECATED
        );
        
        //Default-Type übernehmen
        if (isset($default[$type])) {
            
            $type = $default[$type];
        }
        
        //Fehlermeldung ausführen
        trigger_error('[fxfo-internal] ' . $message . $trace, $type);        
    }

    /**
     * Datenarray aus dem Formular (meist $_POST)
     * 
     * Gibt true zurück wenn Formular abgeschickt wurde 
     * (hidden input aus übergebenen Daten wird gecheckt)
     * 
     * @return boolean
     */
    public function isSent( ) {

        $formName = $this->getElement('_form_', 'name');
        
        //Falls automatisch gesetztes Hidden Input Wert hat
        if ( isset( $this->data[$formName . '_sent'] ) ) {

            return true;
        }
    }

    /**
     * Das <form> Tag wird ausgegeben
     * 
     * @return    HTML String
     */
    public function putStart() {
        
        return $this->put('_formStart_');
    }

    /**
     * Ende des Forms (</form>) wird ausgegeben
     * 
     * @return    HTML-String
     */
    public function putEnd() {

        return $this->put('_formEnd_');
    }

    /**
     * Empf�ngt Array mit Daten des Formulars (meist _GET,_POST)
     * 
     * Die Daten werden in den Formularfeldern angezeigt und einer Pr�fung unterzogen (siehe setError())
     * 
     * @param mixed $datas
     * @return void
     */
    public function setData( array $data ) {

        //Ungefilterte Values aus den Formularfeldern
        $this->dataRaw = $data;
        
        //Gefilterte Values aus den Formularfeldern
        $this->data = $data;
        
        //Neue Daten heißt nochmal neu checken
        $this->rulesAlreadyChecked = false;
    }
    
    /**
     * Daten angeben, die beim Starten des Formulars in den Feldern angezeigt werden
     */
    public function setDefaultData( array $data ) {
        
        $this->defaultData = $data;
    }
    
    /**
     * Daten hinzufügen, die beim Starten des Formulars in den Feldern angezeigt werden
     */
    public function addDefaultData( $elemName, $value ) {
        
        $this->defaultData[$elemName] = $value;
        
        err($this->defaultData);        
    }
    
    /**
     * Ausgeben ob schon Daten für dieses ElemName vorhanden
     * 
     * @param type $elemName
     * @param type $key
     * @return type
     */
    public function elemIsset($elemName) {
        
        return isset($this->elemNameId[$elemName]);
    }
    
    /**
     * Elementeinträge initialisieren
     * 
     * @param type $elemName
     * @return type
     */
    public function initElement($elemName) {
    
        //Falls elemName noch nicht existiert wird es angelegt
        if (!isset($this->elemNameId[$elemName])) {
            
            $hashId = $this->randhash();
            
            $this->elemNameId[$elemName] = $hashId;
            $this->elements[$hashId]['name'] = $elemName;            
        }
        
        return $this->elemNameId[$elemName];
    }
    
    /**
     * Hash-ID eines Elements nach ElemName geben, falls elemName noch nicht gespeichert wird Eintrag erzeugt
     * 
     * @param type $elemName
     * @return type
     */
    public function getElemId($elemName) {
        
        //Falls Element noch nicht existiert
        if (!$this->elemIsset($elemName)) {
    
            //Element initialisieren
            $this->initElement($elemName);
        }
            
        return $this->elemNameId[$elemName];
    }
    
    /**
     * 
     * @param type $elemName
     */
    public function filterElem($elemName) {
        
        //Falls noch keine Daten vorhanden ruasspringen
        if (!$this->isSent()) {
            
            return;
        }
        
        //Alle Fehler ausgeben
        if ($elemName == 'ALL') {
            
            //Alle Elemente mit Regeln werden in das Array geschrieben
            $elemName = array_keys($this->data);
            
            //ALL löschen aus den Elementen da sonst endlossschleife wegen gleicher benennung (wenn filterElem('ALL'))
            //TODO: Erstmal-Lösung, doof 
            if (array_search('ALL', $elemName) !== false) { 
                
                unset($elemName[array_search('ALL', $elemName)]);
            }
        }
        
        //Falls über Array übergeben Rekursiv aufrufen
        if (is_array($elemName)) { 
            
            foreach ($elemNameArray = $elemName AS $elemName) { 
                
                $this->filterElem($elemName);
            }
            
            return;
        }  
        
        //Value aus gespeichertem Element
        $data = $this->data[$elemName];
                    
        //Allgemeine Filter
        if (isset($this->filters['ALL'])) {

            //Alle Filter durchlaufen
            foreach ( $this->filters['ALL'] AS $filter ) {

                $data = $filter( $data );                
            }
        }

        //Dann spezielle Filter
        if (isset($this->filters[$elemName])) {

            //Alle Filter des Elements durchlaufen
            foreach ( $this->filters[$elemName] AS $filter ) {

                $data = $filter( $data );                
            }
        }
        
        //Gefilterte Daten wieder für das Element speichern
        $this->data[$elemName] = $data;
    }

    
    /**
     * Das gefilterte value eines Elements ausgeben
     * 
     * @param type $elemName
     * @return type
     */
    public function getValue( $elemName ) {
        
        return $this->data[$elemName];
    }
    
    /**
     * Das ungefilterte value eines Elements ausgeben
     * 
     * @param type $elemName
     * @return type
     */
    public function getValueRaw( $elemName ) {
        
        return $this->dataRaw[$elemName];
    }  
    
    /**
     * Eine Dropzonedefinition hinzufügen
     * 
     * @param type $dpzId
     * @param array $configs
     */
    public function addDropzone($dpzId = false, array $configs = array()) {
        
        //Übergebe ID oder hash
        $dpzId ? : $dpzId = $this->randhash();
        
        //übergebene Configs speichern
        $this->setDpzConfig($configs, $dpzId);
        
        //Chain speichern
        $this->lastDropzone = $dpzId;
        
        return $this;
    }
    
    /**
     * Ein Config-Array für die Dropzone speichern
     * 
     * @param array $config
     * @param type $dpzId
     * @return \FlaxForm
     */
    public function setDpzConfig(array $configs, $dpzId = false) {
        
        //Übergebe ID oder hash
        $dpzId ? : $dpzId = $this->lastDropzone;
        
        //übergebene Configs speichern
        $this->dropzones[$dpzId]['config'] = $configs;
        
        return $this;
    }
    
    /**
     * Dropzone URL übergeben
     * 
     * @param array $config
     * @param type $dpzId
     * @return \FlaxForm
     */
    public function setDpzUrl($url, $dpzId = false) {
        
        //Übergebe ID oder hash
        $dpzId ? : $dpzId = $this->lastDropzone;
        
        //übergebene Configs speichern
        $this->dropzones[$dpzId]['url'] = $url;
        
        return $this;
    }
    
    /**
     * Alias für putDropzone()
     * 
     * @param type $dpzId
     * @return type
     */
    public function putDpz($dpzId = 'All') {
        
        return $this->putDropzone($dpzId);
    }
    
    /**
     * Dropzone HTML ausgeben
     * 
     * @param type $dpzId
     * @return type
     */
    public function putDropzone($dpzId) {
        
        //Alle Dropzones ausgeben bei ALL
        $dpzId = $this->changeAllToArray($dpzId, $this->dropzones);
        
        //Falls $dpzId ein Array rekursiv aufrufen
        if ($recursive = $this->recursiver($dpzId, __METHOD__)) {
            
            return $recursive;
        }
        
        //Kommentar
        $return = '<!-- Dropzone ' . $dpzId . '-->' . _N;
        
        //HTMl laden und Daten ersetzen
        $return .= str_replace( array('{dpzId}'), array($dpzId), $this->getSnippet('dropzone') );
        
        return $return;
    }
    
    /**
     * Alias für putDropzoneJs()
     * 
     * @param type $dpzId
     * @return type
     */
    public function putDpzJs($dpzId = 'ALL') {
        
        return $this->putDropzoneJs($dpzId);
    }
    
    /**
     * Javascript für Aktivierung der Dropzone ausgeben
     * 
     * @param type $dpzId
     */
    public function putDropzoneJs($dpzId = 'ALL') {
        
        //Alle Dropzones ausgeben bei ALL
        $dpzId = $this->changeAllToArray($dpzId, $this->dropzones);
        
        //Falls $dpzId ein Array rekursiv aufrufen
        if ($recursive = $this->recursiver($dpzId, __METHOD__)) {
            
            return $recursive;
        }
        
        //Kommentar
        $return = '//Dropzone ' . $dpzId . _N;
        
        $configs = '';
        
        //Config für jquery zusaammenbauen
        foreach ($this->dropzones[$dpzId]['config'] as $config => $value) {
            
            $configs .= $config . ' : "' . $value . '",';
        }
        
        //JQ Code
        $return .= '$("#' . $dpzId . '").dropzone({' . $configs . '});' . _N . _N;
        
        return $return;
    }   
    
    /**
     * Gibt das JS für ein schöneres File Input aus
     * 
     * @param type $elemName
     * @return string
     */
    public function putFileJs() {
        
        //TODO: demnächst linken
        return file_get_contents(__DIR__ . '/fxfo/file.js');
    }
    
    /**
     * Fallback Funktion für den Autofocus falls HTML5 nicht vorhanden
     * 
     * @return string
     */
    public function putAutofocusJs() {
        
        //Falls kein Autofocus gesetzt wurde nichts ausgeben
        if (!$this->autofocus) {
            
            return;
        }
        
        //JS Script ausgeben
        $return =   'if (!("autofocus" in document.createElement("input"))) {
                        
                        $("[name='.$this->autofocus.']").focus(); 
                     }' . _N . _N;
        
        return $return;    
    }


   /**
    * Speichert die Regel für ein Element
    * 
    * @param type $elemName
    * @param type $pattern
    * @param type $errorMessage
    * @return \directForm
    */
    public function addRule( $elemName, $pattern, $errorMessage = NULL ) {
        
        //Kommaseperierte Mehrfachfelder
        is_array($elemName) ? : $elemName = array_map('trim', explode(',', $elemName));
        
        //Falls mehrere rekursiver Aufruf
        if (count($elemName) > 1) { 
            
            foreach ($elemNameArray = $elemName AS $elemName) { 
                
                $this->addRule($elemName, $pattern, $errorMessage);
                
                //Die erzeugten ruleIds werden gespeichert
                $ruleIds[] = $this->rulesLast;
            }
            
            //Die erzeugten ruleIds werden zum Chainen weitergegeben
            $this->rulesLast = $ruleIds;
            
            return $this;
        }     
        
        //Array hat nur ein Element
        $elemName = $elemName[0];
        
        //RuleId erzeugen
        $ruleId = $this->randhash();
                
        //Regel wird gespeichert 
        $this->rules[$ruleId]['elemName']     = $elemName;
        $this->rules[$ruleId]['errorMessage'] = $errorMessage;
        $this->rules[$ruleId]['status']       = 'unchecked';
        
        $this->rules[$ruleId]['pattern']      = $this->parsePattern($pattern, $elemName);        

        //TODO: wieder hinzufügen irgendwo
        //$failed     = $this->rulesDefaultFailedStatus; 
        //in_array('required', $patterns) ? $this->setRequired($elemName) : '';            
        
        //Hash speichern zum chainen
        $this->rulesLast = $ruleId;
        
        return $this;
    } 
    
    /**
     * Das vom User eingegeben Pattern wird geparst auf multiple
     * Pattern, negative Pattern, logische Pattern
     * 
     * @param string $patterns_string
     * @return array
     */
    private function parsePattern($patterns_string, $elemName) {
        
        //Bools abfangen
        $patterns_string = $this->changeBoolToString($patterns_string);
        
        //Array aus den Pattern erstellen (falls nicht bereits Array übergeben)
        $pattern_array = is_array($patterns_string) ? $patterns_string : array_map('trim', explode(',', $patterns_string));
        
        //Pattern-Array aufbauen
        foreach ($pattern_array as $pattern_string) {
            
            //Pattern als nicht geprüft markieren
            $pattern['status'] = 'unchecked'; 
            
            //Patternstring auseinandernehmen
            $pattern_tmp = explode('|', $pattern_string);
            
            //Patternmethode speichern
            $pattern['method'] = $pattern_tmp[0];
            
            //Parameter speichern
            $pattern['params'] = array_slice($pattern_tmp, 1);
            
            //Negative Pattern markieren
            if ($pattern['method'][0] == '!') {
                
                //Pattern als invertiert markieren
                $pattern['invers'] = true;
                
                //löschen von !
                $pattern['method'] = mb_substr($pattern['method'], 1);
            }  
            
            //Kein negatives Pattern
            else {
                
                //Pattern als invertiert markieren
                $pattern['invers'] = false;
            }
            
            
            //---Spezielle Pattern abfangen und bearbeiten
            
            //Logische Regeln abfangen
            if (in_array($pattern['method'], array('==', '!=', '<=', '>='))) {
                
                //Logic als 1. Parameter speichern
                array_unshift($pattern['params'], $pattern['method'] );
                
                //Methode ist dann nur logic
                $pattern['method'] = 'logic';                
            }            
            
            //Required abfangen
            else if ($pattern['method'] == 'required') {
                
                $this->setRequired($elemName);
            }       
            
            //--
            
            //Pattern ID erzeugen
            $patternId = $this->randhash();
            
            //Daten in Return-Pattern speichern
            $patterns[$patternId] = $pattern;
        }
        
        return $patterns;
    }
    
    
    /**
     * Fehler setzen der auf jeden Fall wahr ist (externe Prüfung)
     * 
     * @param type $elemName
     * @param type $errorMessage    kann hier gesetzt oder über bindToGroup() von einer liveRule übernommen werden
     * @return \directForm
     */
    public function addError( $elemName, $errorMessage = false ) {
        
        $this->addRule($elemName, 'false', $errorMessage);
        
        return $this;        
    }
    
    /**
     * Setzt den Failed Status einer Rule
     * 
     * @param type $elemName
     * @return \directForm
     */
    private function setRuleFailed( $ruleId ) {
        
        $this->setRule($ruleId, 'status', 'failed');
    } 
    
    /**
     * Setzt den Failed Status einer Rule
     * 
     * @param type $elemName
     * @return \directForm
     */
    private function setRulePassed( $ruleId ) {
        
        $this->setRule($ruleId, 'status', 'passed');
    }   
    
    /**
     * Gibt aus ob die Regel fehlgeschlagen ist
     * 
     * @param type $elemName
     * @return \directForm
     */
    private function ruleHasFailed( $ruleId ) {
        
        return ($this->getRule($ruleId, 'status') == 'failed');
    }   
    
    
    /**
     * Gibt true zurück falls eine Regel des Elements verletzt wurde, ansonsten false
     * 
     * @param type $elemName
     * @return boolean
     */
    private function elemHasFailed($elemName) {
        
        //Alle Regeln durchgehen
        foreach ($this->rules AS $ruleId => $rule) {
            
            //Ist eine Rule des Elements
            if ($rule['elemName'] == $elemName) {
                
                //Sobald eine Regel fehlgeschlagen, ist das Elem fehlgeschlagen
                if ($this->ruleHasFailed($ruleId)) {
                    
                    return true;
                }
            }            
        }
        
        //Keine verletzte Regel gefunden
        return false;        
    }  

    /**
     * Fügt einen Filter für ein Element hinzu
     * 
     * @param type $elemName
     * @param type $filter
     */
    public function addFilter( $filter, $elemName = 'ALL' ) {
        
        //Falls mehrere Elemnames als Array
        if (is_array($elemName)) {
    
            foreach ($elemName as $elemName) {
                
                $this->addFilter( $filter, $elemName );
            }
            
            return;
        }

        //Filter wird für das Element gespeichert
        $this->filters[$elemName][] = $filter;

        //Filter werden auf das Element angewand
        #$this->filterElem($elemName);            
    }

    /**
     * Führt die Filter aus wenn Daten vorhanden sind
     */
    public function doFilter() {
        
        //Nur laufen lassen falls Daten vorhanden
        if (!is_array($this->data) OR count($this->data) == 0) {
            
            return;
        }
        
        //Alle Elemente durchgehen
        foreach ($this->filters as $elemName => $filters) {
            
            //Alle Filter des ELements durchgehen
            foreach ($filters AS $filter) {
                            
                //Falls Custom-Funktion nicht existiert
                if (!function_exists($filter)) {

                    $this->error('filter function "' . $filter . '" not defined', 'WARNING');
                    
                    continue;
                }
                
                //Filter auf alle Elemente anweńden
                if ($elemName == 'ALL') {
                    
                    $this->data = array_map($filter, $this->data);
                    
                    continue;
                }
                
                else if (!isset($this->data[$elemName])) {
                    
                    $this->error('no data to filter for element "' . $elemName . '" found', 'WARNING');
                    
                    continue;
                }
                
                //Filter auf einzelnes Element anwenden
                $this->data[$elemName] = $filter($this->data[$elemName]);
            }
        }
    }

    /**
     * Prüft die Regeln und gibt das Ergebnis zurück
     * 
     * @param type $forceCheck      true angeben falls auf jeden Fall die Regeln nochmal geparst werden sollen
     * @return boolean
     */
    public function validate( $forceCheck = FALSE ) {

        //Falls Regeln noch nicht geparst wurden
        if ( !$this->rulesAlreadyChecked AND !$forceCheck ) {

            //Regeln parsen
            $this->parseRules();
        }

        //Falls Fehler aufgetreten sind ist Formular fehlerhaft
        if ( $this->isErrorForm ) {

            return false;
        }

        //Falls keine Fehler vorhanden
        return true;
    }


    /**
     * Geht die einzelnen Regeln durch und speichert ob sie eingehalten wurden
     * 
     * @return type
     */
    private function parseRules() {
        
        //Die einzelnen Regeln werden geprüft
        foreach ( $this->rules as $ruleId => $rule ) {
            
            //Regel prüfen
            $result = $this->checkRule( $ruleId );
            
            //Falls Regel fehlerhaft ist
            if ( $result == 'failed') {
                
                //Falls irgendeine Regel einen Fehler erzeugt ist Form fehlerhaft
                $this->isErrorForm = true;
            }

            //Regel wurde nicht verletzt
            else {

                //Falls Rule als falsch gestartet wurde korrigieren
                $this->setRulePassed( $ruleId );
            }               
        } 
        
        $this->rulesAlreadyChecked = true;

        //Bool des Formfehlers wird ausgegeben
        return $this->isErrorForm;
    }
    
    /**
     * Rule-Prüfung durchführen
     * 
     * @param type $ruleId
     * @param type $form
     * @return string|boolean
     */
    private function checkRule($ruleId) {
        
        require_once(__DIR__ . '/fxfo/patterndata.php');
        
        //Datenobjekt für Patternmethods erzeugen
        $data = new PatternData();
        
        //Name des zu prüfenden Elements
        $data->setElemName($this->getRule($ruleId, 'elemName'));
        
        //Alle empfangenen Daten
        $data->setData($this->getData());
        
        //Alle ElementDaten (zu diesem Zeitpunkt meist noch keine Werte)
        $data->setElements($this->getElement());
        
        //Die vorhandenen Pattern werden geprüft und das Ergebnis gespeichert
        $this->checkPattern($ruleId, $data);
        
        //Failed Status zurückgeben
        return $this->ruleHasFailed( $ruleId );        
    }  
    
    /**
     * Die Prüfung der Pattern findet statt und das Ergebnis wird im Rule-Array gespeichert
     * 
     * @param type $ruleId
     * @param PatternData $data
     */
    private function checkPattern($ruleId, PatternData $data) {
        
        //Patterndaten der Rule
        $patterns = $this->getRule($ruleId, 'pattern');
        
        //Alle Pattern der Rule durchlaufen
        foreach ($patterns as $patternId => $pattern) {
            
            //Extra-Parameter für das Pattern übergeben
            $data->setParams($pattern['params']);
            
            //Falls Custom-Funktionen
            if ($pattern['method'] == 'custom') {
                
                $function_name = $data->getParam('0');
                    
                //Falls Custom-Funktion nicht existiert
                if (!function_exists($function_name)) {
                    
                    $this->error('custom function "' . $function_name . '" not defined', 'WARNING');
    
                    continue;
                }
                
                $result = $function_name($data);
            }
            
            //Falls normal angelegte Methode
            else {
                
                $result = PatternMethods::$pattern['method']($data);
            }
           
            //Invertieren des Ergebnisses
            $result = $pattern['invers'] ? !$result : $result;            
            
            //Pattern bestanden
            if ($result) {
                
                $patterns[$patternId]['status'] = 'passed';
            }
            
            //Pattern nicht bestanden
            else {
                
                $patterns[$patternId]['status'] = 'failed';
                
                //Regel ist fehlgeschlagen sobald ein Pattern nicht bestanden wird
                $this->setRuleFailed($ruleId);
            }
        }
        
        //Element-Daten werden manipuliert entgegengenommen und überschrieben
        //TODO: neu machen mit anderem elemetnsstruktur
        #$this->elements = $data->getElement();
    }

    /**
     * Zeigt die Fehler von getError in einem Format an das zuvor durch
     * setHtmlError festgelegt werden kann
     *          
     * @return string  
     */
    public function putError( $elemName = false, $showAllRules = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;
        
        //Alle Fehler ausgeben
        if ($elemName == 'ALL') {
            
            $elemName = array();
            
            //Alle Regeln durchlaufen
            foreach ($this->rules AS $ruleId => $rule) {
                
                //Nur normale Clientseitige Regeln
                if ($rule['elemName'] != 'ALL') { 
                    
                    $elemName[] = $rule['elemName']; 
                }
            }
        }
        
        //Falls über Array übergeben Rekursiv aufrufen
        if (is_array($elemName)) { 
            
            $return = '';
            
            foreach ($elemNameArray = $elemName AS $elemName) { 
                
                $errorMsg = $this->putError($elemName, $showAllRules);
                
                $return .= $errorMsg;
                
                $return .= ($errorMsg != '') ? $this->getSnippet('errorSeperator') . _N : '';
            }
            
            return $return;
        }          
                    
        //Falls Element nicht fehlerhaft ist aber nur Fehler angezeigt werden sollen
        //TODO: kann man das nicht der nächsten schleife alles überlassen?
        if ( !$this->elemHasFailed($elemName) AND !$showAllRules ) { 

            return;
        }
        
        $return = '';
        
        //Ale Regeln durchlaufen
        foreach ($this->rules AS $ruleId => $rule) {
            
            //Nur die Regeln dieses Elements anzeigen
            if ($rule['elemName'] != $elemName) {
                
                continue;
            }
            
            //Regel unverletzt und nur Fehler anzeigen
            if (!$this->ruleHasFailed($ruleId) AND !$showAllRules) {
                
                continue;
            }
                
            //Falls diese Regel verletzt wurde
            if ($this->ruleHasFailed($ruleId) ) { #OR $this->isBoundToFailedRule( $ruleId )

                //Style-Params setzen
                $tagParams = 'isError="true" isFocus="false"';
            }   

            //Falls Regel nicht verletzt wurde
            else {

                //Style-Params setzen
                $tagParams = 'isError="false" isFocus="false"';
            }

            //Fehlertext wird in Fehlerstyle gesetzt 
            //TODO: arrays schöner machen
            $return .= str_replace( array('{errorMsg}','{ruleId}','{name}','{value}','{pattern}','{params}'), 
                                    array(   $rule['errorMessage'],
                                        $ruleId,
                                        $rule['elemName'],
                                        @$this->data[$rule['elemName']], //TODO: nur temporär mit @
                                        'patterntofix', #TODO: ist das notwendig? $rule['pattern'],
                                        $tagParams
                                    ), 
                                    $this->getSnippet('error') );
            
            //und Seperator angehangen
            $return .= $this->getSnippet('errorSeperator');

        }
            
        //Fehlermeldung ausgeben
        if ($return != '') {
            
            return $this->cutSeperator($return, $this->getSnippet('errorSeperator'));
        }
        
        //Kein Fehler für dieses Element vorhanden
        else {
         
            return false;
        }
    }
        
    /**
     * Zeigt alle Regeln, nicht nur die verletzten 
     * 
     * @param type $elemName
     */
    public function putRule( $elemName = false ) {
        
        return $this->putError($elemName, true);
    }

    
    /**
     * Erzeugt Header des Formulars
     */
    private function createStartTag() {
        
        //Form aus Elements holen
        $form = $this->getElement('_form_');
        
        //Kommentar zum Start des Formulars
        $header = '<!-- START fxfo-Form ' . $form['name'] . ' -->' . _N;

        //Erzeugen des Form-StartTags
        $header .= '<form ';        
        
        //name
        $header .= 'name="' . $form['name'] . '" ';

        //id
        isset($form['id'])           ? $header .= 'id="' . $form['id'] . '" ' : '';

        //action
        isset($form['action'])       ? $header .= 'action="' . $form['action'] . '" ' : '';

        //method
        isset($form['method'])       ? $header .= 'method="' . $form['method'] . '" ' : '';

        //novalidate
        isset($form['novalidate'])   ? $header .= 'novalidate="novalidate" ' : '';

        //method
        isset($form['filePrepared']) AND $form['filePrepared'] ? $header .= 'enctype="multipart/form-data" ' : '';

        //css class
        isset($form['classes'])      ? $header .= 'class="' . implode( ' ', $form['classes'] ) . '" ' : '';

        //style
        isset($form['style'])        ? $header .= 'style="' . implode('', $form['style']) . '" ' : '';

        //extra Params
        isset($form['extraParams'])  ? $header .= implode( ' ', $form['extraParams'] ) : '';
        
        $header .= '>' . _N;

        //Erzeugen eines hidden-Inputs dass das abschicken des Formulars signalisiert
        $header .= $this ->loadElem( $form['name'] . '_sent', 'hidden' )
                                    ->setValue('sent')
                                    ->put();
        
        //Letztes Element auf Form setzen
        $this->lastElemName = '_form_';

        return $header;

    }
    
     /**
     * Erzeugt Footer des Formulars
     */
    private function createEndTag() {
        
        //Form als Element setzen
        $form = $this->getElement('_form_');
        
        //Erzeugen des Form-EndTags
        $footer = '</form>';

        //Kommentar zum Ende des Formulars
        $footer .= '<!-- END Form ' . $form['name'] . ' -->';
        
        //Letztes Element auf Form setzen
        $this->lastElemName = '_form_';

        return $footer;
    }

   
    
    /**
     * Kurzform einer Elementausgabe
     * 
     * @param type $elemType
     * @param type $nameId
     * @param type $elemValue
     * @param type $elemClass
     * @return type
     */
    public function putElem( $elemType, $nameId, $elemValue = false, $elemClass = false) {
        
        //ElementNamen erstellen
        $elemName = $this->makeName( $nameId );
        
        //Element starten
        $this->loadElem($elemName, $elemType);
                
        
        //ID
        $this->setId( $this->makeId( $nameId ) );        
                
        //CSS Klasse (vorheriges ersetzen falls über Funktion übergeben)
        $elemClass ? $this->setClass( $elemClass ) : '';
        
        //Namen setzen
        $this->setName($elemName);
        
        //Value eintragen
        $elemValue ? $this->setValue($elemValue) : '';     
        
        //Element ausgeben
        return $this->put();        
    }


    /**
     * Ein <input> Element erzeugen
     * 
     * @param type $param
     */
    private function createInput( $elemName ) {

        //Initialisierung
        $element = $this->getElement($elemName);

        //input
        $return = '<input ';

        //Typ
        $return .= 'type="' . $element['type'] . '" ';

        //name
        $return .= 'name="' . $elemName . '" ';

        //id
        isset($element['id'])           ? $return .= 'id="' . $element['id'] . '" ' : '';

        //value
        isset($element['value'])        ? $return .= 'value="' . $element['value'] . '" ' : '';

        //placeholder
        isset($element['placeholder'])  ? $return .= 'placeholder="' . $element['placeholder'] . '" ' : '';

        //css class
        isset($element['classes'])      ? $return .= 'class="' . implode( ' ', $element['classes'] ) . '" ' : '';

        //style
        isset($element['style'])        ? $return .= 'style="' . implode('', $element['style']) . '" ' : '';

        //checked
        isset($element['checked'])      ? $return .= ($element['checked'] ? 'CHECKED ' : '') : '';
        
        //required
        isset($element['required'])     ? $return .= ($element['required'] ? 'required="required" ' : '') : '';
        
        //autofocus
        isset($element['autofocus'])     ? $return .= ($element['autofocus'] ? 'autofocus="autofocus" ' : '') : '';

        //min
        isset($element['min'])          ? $return .= 'min="' . $element['min'] . '" ' : '';

        //max
        isset($element['max'])          ? $return .= 'max="' . $element['max'] . '" ' : '';

        //step
        isset($element['step'])         ? $return .= 'step="' . $element['step'] . '" ' : '';

        //extra Params
        isset($element['extraParams'])  ? $return .= implode( ' ', $element['extraParams'] ) : '';

        //ende
        $return .= '/>';

        return $return;
    }
    
     /**
     * Ein <input> Element erzeugen
     * 
     * @param type $param
     */
    private function createSubmit( $elemName ) {
        
        //Submit Stopper Snippet
        $return = $this->submitStopper ? $this->getSnippet('submitStopper') : '';
        
        //Normales Input erstellen
        $return .= $this->createInput( $elemName );
        
        return $return;
    }
    
    /**
     * Ein <input type=file> Element erzeugen
     * 
     * @param type $param
     */
    private function createFile( $elemName, $simple = false ) {

        //Initialisierung
        $element = $this->getElement($elemName);
        
        //Designtes FileUpload
        if (!$simple) {
            
            $return = '<div class="fxfo_fileInput" forfile="'.$elemName.'" style="position:relative;">'
                    . '<div class="newInput" style="position:absolute;display:none;">'
                    . $this->getSnippet('newFileInput')
                    . '</div>';
        }
        
        //Normales FileUpload
        else {
            
            $return = '<div>';
        }

        //input
        $return .= '<input type="file"';

        //name
        $return .= 'name="' . $elemName . '" ';

        //id
        isset($element['id'])           ? $return .= 'id="' . $element['id'] . '" ' : '';

        //value
        isset($element['value'])        ? $return .= 'value="' . $element['value'] . '" ' : '';

        //placeholder
        isset($element['placeholder'])  ? $return .= 'placeholder="' . $element['placeholder'] . '" ' : '';

        //css class
        isset($element['classes'])      ? $return .= 'class="' . implode( ' ', $element['classes'] ) . '" ' : '';

        //style
        isset($element['style'])        ? $return .= 'style="' . implode('', $element['style']) . '" ' : '';
        
        //required
        isset($element['required'])     ? $return .= ($element['required'] ? 'required="required" ' : '') : '';
        
        //autofocus
        isset($element['autofocus'])    ? $return .= ($element['autofocus'] ? 'autofocus="autofocus" ' : '') : '';

        //multiple
        isset($element['multiple'])     ? $return .= ($element['multiple'] ? 'MULTIPLE ' : '') : '';

        //extra Params
        isset($element['extraParams'])  ? $return .= implode( ' ', $element['extraParams'] ) : '';

        //ende
        $return .= '/></div>';

        return $return;
    }

    /**
     * Ein <select> Element mit <option>'s erzeugen
     * 
     * @param type $param
     * @return string
     */
    private function createSelect( $elemName ) {

        //Initialisierung
        $element = $this->getElement($elemName);

        //select
        $return = '<select ';

        //name
        $return .= 'name="' . $elemName . '" ';

        //id
        isset($element['id'])           ? $return .= 'id="' . $element['id'] . '" ' : '';

        //size
        isset($element['size'])         ? $return .= 'size="' . $element['size'] . '" ' : '';

        //css class
        isset($element['classes'])      ? $return .= 'class="' . implode( ' ', $element['classes'] ) . '" ' : '';

        //style
        isset($element['style'])        ? $return .= 'style="' . implode('', $element['style']) . '" ' : '';
        
        //required
        isset($element['required'])      ? $return .= ($element['required'] ? 'required="required" ' : '') : '';
        
        //autofocus
        isset($element['autofocus'])     ? $return .= ($element['autofocus'] ? 'autofocus="autofocus" ' : '') : '';

        //multiple
        isset($element['multiple'])     ? $return .= ($element['multiple'] ? 'MULTIPLE ' : '') : '';

        //extra Params
        isset($element['extraParams'])  ? $return .= implode( ' ', $element['extraParams'] ) : '';

        //ende
        $return .= '>' . _N;


        //Falls Options als Array angegeben sind werden sie zusammengesetzt
        if ( isset( $element['value'] ) AND is_array( $element['value'] ) ) { 

            foreach ( $element['value'] AS $option_key => $option_value ) {

                //Select setzen
                if (isset($element['selected']) AND $element['selected'] == $option_key) { 
                    
                    $selected = ' selected';
                }
                
                else {
                    
                    $selected = '';
                }

                //Options zusammenbauen
                $return .= '<option value="' . $option_key . '"' . $selected . '>' . $option_value . '</option>' . _N;
            }
        }

        //Falls Options ein String (fertige <option>'s) ist wird dieser benutzt
        else if ( isset( $element['value'] ) ) {

            $return .= $element['value'];
        }

        //Ende des Selects
        $return .= '</select>';

        //Ausgabe des Elements
        return $return;
    }


    /**
     * Ein <textarea> Element erzeugen
     * 
     * @param type $param
     */
    private function createTextarea( $elemName ) {

        //Initialisierung
        $element = $this->getElement($elemName);

        //textarea
        $return = '<textarea ';

        //name
        $return .= 'name="' . $elemName . '" ';

        //id
        isset($element['id'])           ? $return .= 'id="' . $element['id'] . '" ' : '';

        //placeholder
        isset($element['placeholder'])  ? $return .= 'placeholder="' . $element['placeholder'] . '" ' : '';

        //css class
        isset($element['classes'])      ? $return .= 'class="' . implode( ' ', $element['classes'] ) . '" ' : '';

        //style
        isset($element['style'])        ? $return .= 'style="' . implode('', $element['style']) . '" ' : '';
        
        //required
        isset($element['required'])      ? $return .= ($element['required'] ? 'required="required" ' : '') : '';
        
        //autofocus
        isset($element['autofocus'])     ? $return .= ($element['autofocus'] ? 'autofocus="autofocus" ' : '') : '';

        //extra Params
        isset($element['extraParams'])  ? $return .= implode( ' ', $element['extraParams'] ) : '';

        //
        $return .= '/>';

        //value
        isset($element['value'])        ? $return .= $element['value'] : '';

        //ende
        $return .= '</textarea>';

        return $return;
    }


    /**
     * Schreibt das Element als HTML
     * 
     * @param type $elemName
     */
    public function put( $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        
        //Form-Tag Start
        if ( $elemName == '_formStart_' ) { 

            return $this->createStartTag();
        } 
        
        //Form-Tag Ende
        else if ( $elemName == '_formEnd_' ) {

            return $this->createEndTag();
        } 
        
        //JS Code anzeigen
        else if ( $elemName == '_jsCode_' ) {

            return $this->displayJsCode();
        } 
        
        //Für die anderen Elemente wird der Typ abgefragt
        else {

            $elemType = $this->getElement($elemName, 'type');
        }
        
        
        //Falls Element fehlerhaft
        if ($this->elemHasFailed( $elemName )) {
            
            $this->addClass('fxfo_error');
        }
        
        //Falls Element korrekt und Daten schon abgeschickt
        //TODO: Ausnahme mit submit etc. mal überdenken
        else if ($this->isSent() AND $this->getElement($elemName, 'type') != 'submit') {
            
            $this->addClass('fxfo_noError');
        }

        
        //Select
        if ( $elemType == 'select' ) {

            return $this->createSelect( $elemName );
        }

        //Textarea
        else if ( $elemType == 'textarea' ) {

            return $this->createTextarea( $elemName );
        }

        //File
        else if ( $elemType == 'file' ) {

            return $this->createFile( $elemName );
        }

        //File
        else if ( $elemType == 'simpleFile' ) {

            return $this->createFile( $elemName, true );
        }

        //Submit
        else if ( $elemType == 'submit' ) {

            return $this->createSubmit( $elemName );
        }        
        
        //Input mit allen übrigen Möglichkeiten
        else {

            return $this->createInput( $elemName );
        }
    }

    /**
     * Ein Fluent Interface speichern
     * 
     * @param type $chainId
     * @return type
     */
    public function save( $chainId = false ) {
        
        //Übergebe ID oder eigene erzeugen die returned wird
        $chainId ? : $chainId = $this->randhash();
        
        $this->chains[$chainId]['lastElemName'] = $this->lastElemName;
        $this->chains[$chainId]['rulesLast'] = $this->rulesLast;
        $this->chains[$chainId]['lastDropzone'] = $this->lastDropzone;
        
        return $chainId;
    }

    /**
     * Ein Fluent Interface laden
     * 
     * @param type $chainId
     * @return type
     */
    public function load( $chainId ) {
                
        $this->lastElemName = $this->chains[$chainId]['lastElemName'];
        $this->rulesLast = $this->chains[$chainId]['rulesLast'];
        
        return $this;
    }
    
    /**
     * Setzt das lastElem neu so dass das fluent Interface darauf angwand werden kann
     * 
     * @param type $elemType
     * @param type $elemName
     * @return \directForm
     */
    public function loadElem( $elemName = 'ALL', $elemType = 'ALL') {
        
        //Defaultdaten für alle Elemente speichern
        if ($elemName == 'ALL') {
            
            //Chain starten
            $elemType == 'ALL' ? $this->lastElemName = 'ALL' : $this->lastElemName = 'ALL[' . $elemType . ']';
        }
        
        //Falls normales Element dieses initialisieren und schon gespeicherte Daten übernehmen
        else {
            
            //Falls mehrere ElemNames als Array übergeben
            if (is_array($elemName)) {
    
                //Alle Elemente durchlaufen
                foreach ($elemName as $value) {
                
                    $this->loadElem($value, $elemType);
                }
                
                return $this;
            }
            
            //Chain starten
            $this->lastElemName = $elemName;
            
            //Quellen für Daten
            $cascade = array('ALL', 'ALL[' . $elemType . ']', $elemName);
                        
            //Element initialisieren
            if (!$this->elemIsset($elemName)) {
             
                $this->initElement($elemName);
            }
                        
            //Temporäres Element
            $tempElem = array();
            
            //Alle Werte des TypeDefaults durchgehen
            foreach ($cascade as $sourceName) {                

                //Falls von dieser Quelle keine Daten existieren
                if (!$this->elemIsset($sourceName)) { 
             
                    continue;
                }
                
                //Daten holen
                $source = $this->getElement($sourceName);
                
                //Daten durchgehen
                foreach ($source as $key => $value) {

                    //Merge-Parameter
                    if (isset($tempElem[$key]) AND in_array($key, array('style', 'param', 'class', 'option'))) {

                        //Daten mergen
                        $tempElem[$key] = $tempElem[$key] + $value;

                        continue;
                    }                  

                    //Wert speichern
                    $tempElem[$key] = $value;
                }
            }
            
            //Temporäre Daten fürs Element speichern
            $this->elements[$this->getElemId($elemName)] = $tempElem;
            
            //Defaultdaten als Value setzen
            if (isset($this->defaultData[$elemName])) {

                $this->setValue($this->defaultData[$elemName]);
            }

            //Status setzen falls nicht schon über ALL oder ALL[type]
            $this->getElement($elemName, 'status') === NULL ? $this->setElement($elemName, 'status', 'unchecked') : '';
            
            //Typ setzen
            $this->setType($elemType); 
            
            //4. Value aus den Formdaten holen falls vorhanden
            isset($this->data[$elemName]) ? $this->setValue($this->data[$elemName]) : '';
        }
        
        return $this;
    }    
    
    /**
     * CSS Klassen hinzufügen
     * 
     * @param type $classes     String, wahlweise mehrere durch Leerzeichen getrennte Klassen
     * @param type $elemName    Angabe des Elements falls extern aufgerufen
     * @return \Form
     */
    public function addClass( $classes, $elemName = false ) { 

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;
        
        //HashID des ElemName
        $elemId = $this->getElemId($elemName);

        //Dummy erstellen falls bisher noch keinn Klassen angegeben
        if ( !isset( $this->elements[$elemId]['classes'] ) ) {

            $this->elements[$elemId]['classes'] = array( );
        }

        //Der String wird in ein Array mit CSS Klassen gewandelt
        !is_array($classes) ? $classes = explode( ' ', $classes ) : '';

        //CSS-Klasse hinzufügen
        $this->elements[$elemId]['classes'] = array_merge( $this->elements[$elemId]['classes'], $classes );

        return $this;
    }    

    /**
     * Bisher gemachte Klassenangaben werden mit dieser überschrieben 
     * 
     * @param type $classes
     * @param type $elemName
     * @return \Form
     */
    public function setClass( $classes, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        //Der String wird in ein Array mit CSS Klassen gewandelt
        !is_array($classes) ? $classes = explode( ' ', $classes ) : ''; 

        //CSS-Klasse wird gespeichert
        $this->setElement($elemName, 'classes', $classes);

        return $this;
    }
    
    /**
     * CSS-Klasse aus Element entfernen
     * 
     * @param type $class
     * @param type $elemName
     * @return \Form
     */
    public function removeClass( $class, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        //Css-Class aus dem Array entfernen
        $classes = $this->getElement($elemName, 'classes');
        unset( $classes[array_keys( $classes, $class, true )] );
        $this->setElement( $elemName, 'classes', $classes);

        return $this;
    }

    /**
     * Id schreiben
     * 
     * @param type $id
     * @param type $elemName
     * @return \Form
     */
    public function setId( $id, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'id', $id);

        return $this;
    }
    

    /**
     * Namen schreiben
     * TODO: eigentlich nur für form, problemtaisch sonst da über namen referiert wird
     * 
     * @param type $name
     * @param type $elemName
     * @return \Form
     */
    private function setName( $name, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'name', $name);

        return $this;
    }
    
    
   /**
    * action für das Formular setzen
    * 
    * @param type $action
    * @return \Form
    */
    public function setAction( $action ) {

        $this->setElement( '_form_', 'action', $action);

        return $this;
    }
    
    
   /**
    * setzt den enctype für file upload
    * 
    * @param type $action
    * @return \Form
    */
    public function prepareForFiles() {

        $this->setElement( '_form_', 'filePrepared', true);

        return $this;
    }
    
    
   /**
    * method für das Formular setzen
    * 
    * @param type $method
    * @return \Form
    */
    public function setMethod( $method ) {

        $this->setElement( '_form_', 'method', $method);

        return $this;
    }


    /**
     * Reqired-Attribut setzen
     * 
     * @param type $elemName
     * @return \Form
     */
    public function setNovalidate( ) {

        $this->setElement( '_form_', 'novalidate', true);

        return $this;
    }
    

    /**
     * Style direkt im Element setzen
     * 
     * @param type $style       Inhalt des Style Attributs als String
     * @param type $elemName
     * @return \Form
     */
    public function setStyle( $style, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'style', array($style));
        
        return $this;
    }

    /**
     * Style direkt im Element setzen
     * 
     * @param type $style       Inhalt des Style Attributs als String
     * @param type $elemName
     * @return \Form
     */
    public function addStyle( $style, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;
        
        //HashID des ElemName
        $elemId = $this->getElemId($elemName);

        //Initialisieren falls bisher noch kein Style angegeben
        if ( !isset( $this->elements[$elemId]['style'] ) ) {

            $this->elements[$elemId]['style'] = array( );
        }

        $this->elements[$elemId]['style'][] = $style;
        
        return $this;
    }

    /**
     * Min Attribut setzen (für range/number)
     * 
     * @param type $size
     * @param type $elemName
     * @return \Form
     */
    public function setMin( $min, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'min', $min);

        return $this;
    }

    /**
     * Max Attribut setzen (für range/number)
     * 
     * @param type $size
     * @param type $elemName
     * @return \Form
     */
    public function setMax( $max, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'max', $max);
        
        return $this;
    }

    /**
     * Step Attribut setzen (für range/number)
     * 
     * @param type $size
     * @param type $elemName
     * @return \Form
     */
    public function setStep( $step, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'step', $step);

        return $this;
    }

    /**
     * Size Attribut setzen (für multiples Select)
     * 
     * @param type $size
     * @param type $elemName
     * @return \Form
     */
    public function setSize( $size, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'size', $size);

        return $this;
    }


    /**
     * Typ des Elements setzen (normalerweise direkt in loadElem)
     * 
     * @param type $type
     * @param type $elemName
     * @return \Form
     */
    public function setType( $type, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'type', $type);

        return $this;
    }


    /**
     * Value des Elements setzen
     * 
     * @param type $value
     * @param type $elemName
     * @return \Form
     */
    public function setValue( $value, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;
        
        //HashID des ElemName
        $elemId = $this->getElemId($elemName);
        
        //Falls POSTdaten vorhanden werden diese für das Value verwendet
        if (is_array($this->data) AND count($this->data) > 0 ) {
            
            //Checkbox
            if (isset($this->elements[$elemId]['type']) AND $this->elements[$elemId]['type'] == 'checkbox') {
                
                //Falls Daten für diese Checkbox empfangen ist sie aktiviert
                isset($this->data[$elemId]) ? $this->elements[$elemId]['checked'] = true : '';
            }
            
            //Select
            else if (isset($this->elements[$elemId]['type']) AND $this->elements[$elemId]['type'] == 'select') {
                                
                //Empfangene Value wird ausgewählt
                $this->elements[$elemId]['selected'] = $this->data[$elemName];
            }
            
            //Textfeld etc.
            else {
                
                //Value muss mit Daten überschrieben werden
                $value = $this->data[$elemName];
            }
        }

        //Value einsetzen
        $this->elements[$elemId]['value'] = $value;

        return $this;
    }

    
    /**
     * Placeholder setzen
     * 
     * @param type $elemName
     * @return \Form
     */
    public function setPlaceholder( $placeholder, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'placeholder', $placeholder);

        return $this;
    }

    /**
     * Multiple Parameter für Select setzen
     * 
     * @param type $elemName
     * @return \Form
     */
    public function setMultiple( $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'multiple', true);

        return $this;
    }


    /**
     * Checked-Attribut für Checkbox setzen
     * 
     * @param type $elemName
     * @return \Form
     */
    public function setChecked( $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'checked', true);

        return $this;
    }


    /**
     * Autofocus-Attribut setzen
     * 
     * @param type $elemName
     * @return \Form
     */
    public function setAutofocus( $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'autofocus', true);
        
        $this->autofocus = $elemName;

        return $this;
    }


    /**
     * Reqired-Attribut setzen
     * 
     * @param type $elemName
     * @return \Form
     */
    public function setRequired( $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'required', true);

        return $this;
    }


    /**
     * Zusätzliche Parameter zum Element hinzufügen
     * 
     * @param string $param             Parameter als String
     * @param string $elemName = false  Nur anzugeben falls nicht im Fluent Interface benutzt
     * @return \DirectForm
     */
    public function addParam( $param, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;
        
        //HashID des ElemName
        $elemId = $this->getElemId($elemName);

        //Dummy erstellen falls bisher noch keine Extras
        if ( !isset( $this->elements[$elemId]['extraParams'] ) ) {

            $this->elements[$elemId]['extraParams'] = array( );
        }

        //Parameter zu Array hinzufügen
        $this->elements[$elemId]['extraParams'][] = $param;

        return $this;
    } 

    /**
     * Bisher gemachte Parameter werden mit diesem überschrieben 
     * 
     * @param string $param             Parameter als String
     * @param string $elemName = false  Nur anzugeben falls nicht im Fluent Interface benutzt
     * @return \DirectForm
     */
    public function setParam( $param, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'param', array($param));

        return $this;
    }

    //TODO: wahrscheinlich nicht notwendig da das besser über value laufen kann
    /**
     * Selected Attribut im Options eines Select setzen
     * Funktioniert nicht wenn Options als String übergeben sind
     * 
     * @param type $selected    key des Option-Arrays
     * @param type $elemName
     * @return \Form
     */
//    public function setSelected( $selected, $elemName = false ) {
//
//        //Letztes Element oder übergebenes
//        $elemName ? : $elemName = $this->lastElemName;
//
//        //Select-Option speichern
//        $this->elements[$elemName]['selected'] = $selected;
//
//        return $this;
//    }


    /**
     * Options zu einem Select hinzufügen
     * 
     * @param array $options    Array mit Value=>Label
     * @param type $elemName
     * @return \Form
     */
    public function addOption( array $option, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;
        
        //HashID des ElemName
        $elemId = $this->getElemId($elemName);

        //Dummy erstellen falls bisher noch keine Options
        if ( !isset( $this->elements[$elemId]['option'] ) ) {

            $this->elements[$elemId]['option'] = array( );
        }

        //Option-Array erweitern
        array_merge( $this->elements[$elemId]['option'], $option );

        return $this;
    }


    /**
     * Option aus einem Select entfernen
     * Funktioniert nicht wenn Options als String übergeben sind
     * 
     * @param type $option    key des Option-Arrays
     * @param type $elemName
     * @return \Form
     */
    public function removeOption( $option, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        //Option aus dem Array entfernen
        $options = $this->getElement($elemName, 'options');
        unset( $options[array_keys( $options, $option, true )] );
        $this->setElement( $elemName, 'options', $options);

        return $this;
    }


    /**
     * Options als String übergeben (überschreibt bisherige Options)
     * 
     * @param type $options
     * @param type $elemName
     * @return \Form
     */
    public function setOption( $option, $elemName = false ) {

        //Letztes Element oder übergebenes
        $elemName ? : $elemName = $this->lastElemName;

        $this->setElement( $elemName, 'option', $option);

        return $this;
    }


    /**
     * Erzeugt ein Umfeldpattern für putError(): 
     * Variablen: {id} {name} {value} {errorMsg}
     * 
     * @param string $pattern     -> z.B.: <b>{errorMsg}</b>
     * @param string $delimiter   -> Default ist <br>
     */
    public function setHtmlError( $pattern, $seperator = false ) {

        $this->snip_error = $pattern;

        if ( $seperator ) {

            $this->snip_errorSeperator = $seperator;
        }
    }


    /**
     * Get ID from Peter#56
     * 
     * @param type $nameId
     * @return type
     */
    private function makeId( $nameId ) {

        //ID wird ausgefiltert
        return substr( strstr( $nameId, '#' ), 1 );
    }


    /**
     * Get Name from Peter#56
     * 
     * @param type $nameId
     * @return type
     */
    private function makeName( $nameId ) {

        $return = strstr( $nameId, '#', TRUE );

        //Name wird ausgefiltert
        return $return != '' ? $return : $nameId;
    }
    
    /**
     * display() auch über String Funktion aufrufbar
     * 
     * @return type
     */
    public function __toString() {
        
        return $this->put();
    }
   
    //TODO: Die verschiedenen Schleifen bei ALL und auch sonst sind ziemlich uneffektiv
    /**
     * JS Scripts ausgeben
     * 
     * @return string
     */
    public function putLiveRule( $elemName = 'ALL' ) {
        
        //Alle Fehler ausgeben
        if ($elemName == 'ALL') {
                        
            $elemName = array();
            
            //Alle Regeln durchlaufen
            foreach ($this->rules AS $ruleId => $rule) {
                
                //Nur normale Clientseitige Regeln
                if ($rule['elemName'] == 'ALL') { 
                    
                    continue;
                }
                    
                //ElemName speichern in Array speichern (falls noch nicht passiert)
                $elemName[] = $rule['elemName'];
            }
            
            //Doppelte rauswerfen
            $elemName = array_unique($elemName);
        }      
                
        //Falls $elemName ein Array ist dann rekursiv aufrufen
        if ($recursive = $this->recursiver($elemName, __METHOD__)) {
            
            return $recursive;
        }    
                    
        
        //Rule-Objekt initialisieren
        $return = _N . '//Regelbindungen ' . $elemName . _N;
        
        $return .= 'rules["' . $elemName . '"] = new Object();' . _N;
        
        //Alle Regeln durchgehen
        foreach ($this->rules AS $ruleId => $rule) {
            
            //Nur Regeln für dieses Element
            if ($rule['elemName'] != $elemName) {
                
                continue;
            }            
            
                        
            //Rule Objekt starten
            $return .= 'rules["' . $elemName . '"]["' . $ruleId . '"] = new Object();' . _N;   
            
            //Pattern durchlaufen
            foreach ($rule['pattern'] as $patternKey => $pattern) {
                
                //Custom Pattern abfangen
                if ($pattern['method'] == 'custom') {
                    
                    //Funktion starten
                    $return .= 'fxfo_patternmethods["fxfo_custom_' . $patternKey . '"] =  function(element, params) {' . _N . _N;
                    
                    //Custom-Funktion afufrufen und zurückgeben
                    $return .= _W . 'return ' . $pattern['params']['0'] . '(element, params);' . _N;
                    
                    //Funktion beenden
                    $return .= '}' . _N; 
                    
                    //Methodennamen auf die Funktion umleiten
                    $pattern['method'] = 'custom_' . $patternKey . '';
                    
                    //Parameter ersetzen da sonst Probleme durch Zeichensatz entsteht
                    #$pattern['params']['0'] = $pattern['method']; conditions nicht mehr erlaubt, nur funktionsnamen
                }
                
                //Neues Patternobjekt starten
                $return .= 'rules["' . $elemName . '"]["' . $ruleId . '"]["' . $patternKey . '"] = new Object();' . _N;
                
                //Methodennamen speichern
                $return .= 'rules["' . $elemName . '"]["' . $ruleId . '"]["' . $patternKey . '"]["method"] = "' . $pattern['method'] . '";' . _N;
                
                //Invers festlegen
                $invers = $pattern['invers'] ? 'true' : 'false';
                
                //Invers speichern
                $return .= 'rules["' . $elemName . '"]["' . $ruleId . '"]["' . $patternKey . '"]["invers"] = ' . $invers . ';' . _N;
                                
                //Parameter zusammenfügen
                $params = array();
                foreach ($pattern['params'] as $param) {
                    
                    $params[] = '"'.$param.'"';
                }
                
                //Parameter
                $return .= 'rules["' . $elemName . '"]["' . $ruleId . '"]["' . $patternKey . '"]["params"] = new Array(' . implode(',', $params) . ');' . _N;
            }          
            
            
            //Alle JS Regeln sammeln
            #$this->allLiveRules[$rule['elemName']] = $condition;
        }
                       
        //Funktionen für dieses Element abschließen
        #$return .= "});" . _N . _N;
        
        return $return;
    }
    
    /**
     * Setzt den JS Code für den SUbmit Stopper
     * 
     * @return string
     */
    public function putSubmitStopperJs() {
        
        $return = '//Submitstopper setzen' . _N;
        
        //
        if ($this->submitStopper) {
            
            $return .= 'fxfo_submitStop = true;' . _N;
        }
        
        //
        else {
        
            $return .= 'fxfo-submitStop = false;' . _N;
        }
        
        return $return;
    }

    /**
     * JS Code vorladen
     * 
     * @return \directForm
     */
    public function loadJsCode() {
        
        $this->lastElemName = '_jsCode_';
             
        //Funktionen zum Darstellen der Fehlermeldungen
        $this->html_jscode = file_get_contents(__DIR__ . '/fxfo/validate.js');
                
        //JS Regeln ausgeben
        $this->html_jscode .= $this->putLiveRule('ALL');
        
        //Dropzones ausgeben
        $this->html_jscode .= $this->putDropzoneJs('ALL');
        
        //FileJs ausgeben
        $this->html_jscode .= $this->putFileJs();
        
        //Autofocus Fallback
        $this->html_jscode .= $this->putAutofocusJs();
        
        //Submit Stopper
        #$this->html_jscode .= $this->putSubmitStopperJs();
        
        return $this;
    }
    
    /**
     * <script> - JQ-Corpus wird dem JS-Code hinzugefügt
     * 
     * @return \directForm
     */
    public function setCorpus() {
        
        $this->html_jscode = '<script language="javascript">$( document ).ready(function() {' . _N . $this->html_jscode . _N . '})</script>';
        
        return $this;
    }
    
    /**
     * <script> - JQ-Corpus wird dem JS-Code hinzugefügt
     * 
     * @return \directForm
     */
//    public function addDropzone( $dpzId = 'ALL') {
//        
//        $this->html_jscode .= '//Dropzone ' . $dpzId;
//        
//        $this->html_jscode .= '$("div.dropzone_' . $dpzId . '").dropzone({ url : "' . $this->dropzones[$dpzId] . '"})';
//        
//        return $this;
//    }
    
    /**
     * Geladenen JS Code ausgeben
     *
     * @return type
     */
    private function displayJsCode( ) {
        
        return $this->html_jscode;
    }
    
    /**
     * JS Code laden und ausgeben
     * 
     * @return string
     */
    public function putJsCode( ) {
        
        $this->loadJsCode();
             
        return $this->put();
    }
    
    /**
     * 
     * @param type $method
     * @param type $params
     * @return type
     */
    public function __call($method, $params) {
        
        //Element wird nur geladen
        if (in_array($method, array('checkbox','text','select','textarea','radio','password', 'file', 'email', 'url', 'color', 'number', 'range', 'tel', 'search'))) {
            
            return $this->callElement($method, $params);
        } 
        
        //Element wird geladen und gesetzt
        else if (in_array($method, array('putCheckbox','putText','putSelect','putTextarea','putRadio','putPassword', 'putFile', 'putEmail', 'putUrl', 'putColor', 'putNumber', 'putRange', 'putTel', 'putSearch'))) {
            
            return $this->callElementAndPut($method, $params);
        } 
        
        //Ansonsten falscher Aufruf
        else {
                        
            $this->error("call for unknown method '".$method."'", 'ERROR', 1);
        }
    }
    
    /**
     * Kurzform für loadElem über __call
     * 
     * @param type $method
     * @param type $nameId
     * @return type
     */
    private function callElement($method, $nameId) {
        
        return $this->loadElem($nameId[0], $method);        
    }    
    
    
    /**
     * Kurzform für putElem über __call
     * 
     * @param type $method
     * @param type $params
     * @return type
     */
    private function callElementAndPut($method, $params) {
        
        !isset($params[1]) ? $params[1] = false : '';
        !isset($params[2]) ? $params[2] = false : '';
        
        $method = strtolower(substr($method, 3));
        
        return $this->putElem($method, $params[0], $params[1], $params[2]);        
    }


    /**
     * Getter für Rules nach Elementen
     * 
     * @param type $elemName
     * @return array 2-dimensional bei einzelnem element, sonst 3-dimensional
     */
    public function getRulesOfElement( $elemName = 'ALL' ) {
         
        //Alle Rules durchlaufen
        foreach ( $this->rules AS $ruleId => $rule ) {
           
            //Falls Element nicht gefragt ist
            if ($elemName != $rule['elemName'] AND $elemName != 'ALL') {
                
                continue;
            }           
            
            //Regeldaten werden für return gespeichert
            $return[$rule['elemName']][$ruleId] = $rule;
            
            //TODO: vieleicht kann das array unten auch anders aufgelöst werden
            //Wenn nur nach einem Element gefragt wird ist dieses das letzte
            $lastElem = $rule['elemName'];
        }
        
        //Falls nur ein Element gefragt ist wird die Ausgabe vereinfacht
        if ($elemName != 'ALL') {
            
            //TODO: Fehlerabfrage falls keine Rules vorhanden schöner machen
            $return = (isset($lastElem) AND isset($return[$lastElem])) ? $return[$lastElem] : false;
        }
                      
        return $return;
    }
     
    /**
     * Getter für Rules nach Hashs
     * 
     * @param type $ruleId
     * @return type
     */
    public function getRule($ruleId = 'ALL', $index = 'ALL') {
               
        $return = ($ruleId == 'ALL') ? $this->rules : $this->rules[$ruleId];
        
        //Ganze Regel ausgeben
        if ($index == 'ALL') {
            
            return $return;
        }
        
        //einen Index der Regel ausgeben
        else if (isset($return[$index])) {
            
            return $return[$index];
        }
        
        //Index nicht gefunden
        return false;
    }
    
    public function getError() {
    
        //Fehler müssen ersteinmal gecheckt werden, oder halt einfach checken statt error geben?
        if (!$this->rulesAlreadyChecked) {
    
            $this->error('cant get errors before called validate()', 'WARNING', true );
            
            return NULL;
        }
        
        $rules = $this->getRule();
               
        $errors = array();
        
        foreach ($rules as $ruleId => $rule) {
            
            $this->ruleHasFailed($ruleId) ? $errors[] = $rule : '';
        }
        
        return $errors;
    }
     
    /**
     * Setter für Rules nach Hashs
     * 
     * @param type $ruleId
     * @return type
     */
    public function setRule($ruleId, $index, $value) {
               
        $this->rules[$ruleId][$index] = $value;
    }
    
    /**
     * Getter für Elements
     * 
     * @param type $elemName
     * @param type $index
     * @return type
     */
    public function getElement($elemName = FALSE, $index = 'ALL') {
        
        //Alle Elemente auswerfen bei ALL
        //TODO: index nicht ignorieren, extra abfrage
        if ($elemName === FALSE) {
            
            return $this->elements;
        }
        
        //Ansonsten prüfen ob Element vorhanden
        else if ($this->elemIsset($elemName)) {
        
            //Elementdaten laden
            $element = $this->elements[$this->getElemId($elemName)];            
        }        
        
        //Falls keine Elementdaten vorhanden
        else {
            
            return NULL;
        }
        
        
        //Bei ALL das gesamte Element als Array ausgeben
        if ($index == 'ALL') {
            
            return $element;
        }
        
        //Ansonsten prüfen ob Element vorhanden
        else if (isset($element[$index])) {
        
            //Elementindex ausgeben
            return $element[$index];
        }  
        
        //Falls der Index nicht gefunden wurde
        else {
            
            return NULL;
        }
    }    
    
    /**
     * Setter für Elements (manueller Eingriff)
     * 
     * @param type $elemName
     * @param type $index
     * @param type $value
     */
    public function setElement($elemName, $index, $value) {
               
        //Alle Fehler ausgeben
        if ($elemName == 'ALL') {
            
            //Alle Elemente mit Regeln werden in das Array geschrieben
            $elemName = array_keys($this->elemNameId);
            
            //ALL löschen aus den Elementen da sonst endlossschleife wegen gleicher benennung (wenn filterElem('ALL'))
            //TODO: Erstmal-Lösung, doof 
            if (array_search('ALL', $elemName) !== false) { 
                
                unset($elemName[array_search('ALL', $elemName)]);
            }
        }
        
        //Falls Array als $elemName alle durchgehen
        if (is_array($elemName)) { 
            
            foreach ($elemNameArray = $elemName AS $elemName) { 
                
                $this->setElement($elemName, $index, $value);
            }
            
            return;
        } 
        
        //HashID von $elemName
        $elemId = $this->getElemId($elemName);
        
        //Neues Value für einzelnes Element
        $this->elements[$elemId][$index] = $value;
    }
    
    
    /**
     * Daten abrufen
     * 
     * @param type $elemName
     * @return type
     */
    public function getData($elemName = 'ALL') {
        
        if ($elemName == 'ALL')  {
            
            $return = $this->data;
        }
        
        else {

            $return = isset($this->data[$elemName]) ? $this->data[$elemName] : false;
        }
        
        return $return;
    }
    
    
    
    /**
     * Ist $id ein Array wird $method mit allen Values des Arrays aufgerufen und der Return aneinandergereiht
     * 
     * @param type $id
     * @param type $method
     * @return type
     */
    private function recursiver($id, $method) {
        
        //Falls kein Array raus
        if (!is_array($id)) {
            
            return false;
        }        
        
        //Falls es sich um die Methode einer Klasse handelt
        if (strpos($method, '::')) {
            
            //Auseinandernehmen
            $method = explode('::', $method);
            
            //Falls es die gleiche Klasse ist als Objekt aufrufen
            if ($method[0] == __CLASS__) {
                
                $method[0] = $this;
            }
        }
            
        $return = '';

        //Alle IDs durchlaufen
        foreach ($dummy = $id AS $id) { 
            
            //Falls eine Klassenmethode übergeben wurde
            if (is_array($method)) {
                
                $methodReturn =  $method[0]->$method[1]($id);
            }
            
            //Falls eine einfache Funktion übergeben wurde
            else {

                $methodReturn =  $method($id);
            }

            $return .= $methodReturn;
        }

        return $return;
    }
    
    /**
     * Falls ALL übergeben wird, wird ein Array mit allen Keys aus $keyArray zurückgegeben
     * 
     * @param type $all
     * @param type $keyArray
     * @return type
     */
    private function changeAllToArray($all, $keyArray) {
        
        //Nur wenn ALL gerufen wird
        if ($all != 'ALL') {
            
            return $all;
        }        
              
        //Array initialisieren
        $return = array();

        //Alle Regeln durchlaufen
        foreach ($keyArray AS $key => $value) {

            //All rauslassen
            if ($key == 'ALL') { 

                continue;
            }

            //Array mit Keys bauen
            $return[] = $key; 
        }
        
        return $return;
    }
    
    /**
     * Random Hash
     * 
     * @param type $length
     * @param type $chars
     * @return string
     */
    private function randhash( $length = 5, $chars = '' ) {

        $length = empty( $length ) ? 11 : $length;
        $length = $length > 64 ? 64 : $length;

        if ( !is_array( $chars ) || (is_array( $chars ) && empty( $chars )) ) {
            for ( $i = 65; $i <= 90; $i++ ) {
                $chars[] = chr( $i );
            }
            for ( $i = 97; $i <= 122; $i++ ) {
                $chars[] = chr( $i );
            }
            $chars[] = '_';
        }

        $c = count( $chars );
        $uid = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $uid .= $chars[rand( 0, $c - 1 )];
        }
        return $uid;
    }
    
    private function cutSeperator( $string, $seperator ) {
        
        if ( strlen( $seperator ) > 0 ) {

            return substr( $string, 0, -strlen( $seperator ) );
        } 
        
        else {
            
            return $string;
        }
    }
    
    /**
     * 
     * @param type $bool
     * @return string
     */
    private function changeBoolToString($bool) {
        
        //Bool TRUE abfangen
        if ($bool === true) {
            
            return 'true';
        }
        
        //Bool FALSE abfangen
        if ($bool === false) {
            
            return 'false';
        }
        
        //Nicht-Bool zurückgeben
        return $bool;
    }
}

?>