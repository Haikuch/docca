<?php
namespace App\Models;

class FilterSourcetime extends FilterAbstract {
    
    protected $name = 'sourcetime';
    protected $value;
    
    public function __construct($value) {
        
        parent::__construct($value);
    }
    
    //
    public function setValue($value) { 
        
        if (empty($value)) {
            
            return;
        }
        
        $values = explode('-', $value);
        $from = explode('.', $values[0]);
        $to = explode('.', $values[1]);
        
        $times['from'] = mktime(0,0,0, $from[1], $from[0], $from[2]);
        $times['to'] = mktime(0,0,0, $to[1], $to[0], $to[2]);
        
        $this->value = ['from' => $times['from'], 'to' => $times['to']];
    }
    
    //
    public function getValueFields() {
        
        $value = $this->getValue();
        
        //default time
        if (empty($value)) {
            
            $value['from'] = time() - 60*60*24*365;
            $value['to'] = time();
        }
        
        $return = 'von ';
        $return .= '<input type="date" filtername="sourcetime" name="sourcetime[from]" placeholder="15.02.2015" value="' . date('d.m.Y', $value['from']) . '">';
        $return .= ' bis ';
        $return .= '<input type="date" filtername="sourcetime" name="sourcetime[to]" placeholder="11.06.2016" value="' . date('d.m.Y', $value['to']) . '">';
        
        return $return;
    }
}
