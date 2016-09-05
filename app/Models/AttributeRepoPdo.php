<?php
namespace App\Models;

use Helpers\Arr;
use Helpers\Db;

class AttributeRepoPdo {
    
    /**
     * returns all existing attributes and its possible values as data array
     * 
     * @return type
     */
    public static function getActive() {
        
        $stmt = Db::get()->prepare("SELECT 
                                            a.id    AS id,
                                            a.name  AS name,
                                            
                                            av.id   AS value_id,
                                            av.name AS value_name
                                        
                                        FROM
                                            attributes a
                                            
                                        LEFT JOIN
                                            attribute_values av
                                        ON
                                            a.id = av.attribute_id
                                            
                                        ORDER BY
                                            a.sort ASC");
        
        $stmt->execute();
        $attributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $attributeList = [];
        foreach ($attributes as $attribute) {
            
            $attributeList[$attribute['id']]['id'] = $attribute['id'];
            $attributeList[$attribute['id']]['name'] = $attribute['name'];
            
            $attributeList[$attribute['id']]['values'][$attribute['value_id']]['id'] = $attribute['value_id'];
            $attributeList[$attribute['id']]['values'][$attribute['value_id']]['name'] = $attribute['value_name'];
        }
        
        return $attributeList;
    }
    
    public static function getNameByAttributeId($attributeId) {
        
        $stmt = Db::get()->prepare("SELECT 
                                            a.name  AS name
                                        
                                        FROM
                                            attributes a
                                            
                                        WHERE
                                            id = :attribute_id");
        
        $stmt->execute([':attribute_id' => $attributeId]);
        $attributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $attributes[0]['name'];
    }
    
    public static function getValueNameByValueId($valueId) {
        
        $stmt = Db::get()->prepare("SELECT 
                                            v.name  AS name
                                        
                                        FROM
                                            attribute_values v
                                            
                                        WHERE
                                            id = :value_id");
        
        $stmt->execute([':value_id' => $valueId]);
        $values = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $values[0]['name'];
    }
    
    //
    public function getValuesByAttributeId($attributeId) {
        
        $stmt = Db::get()->prepare("SELECT 
                                            a.id    AS id,
                                            a.name  AS name,
                                            
                                            av.id   AS value_id,
                                            av.name AS value_name
                                        
                                        FROM
                                            attributes a
                                            
                                        LEFT JOIN
                                            attribute_values av
                                        ON
                                            a.id = av.attribute_id
                                            
                                        WHERE
                                            id = :attribute_id");
        
        $stmt->execute([':attribute_id' => $attributeId]);
        $attributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $i = 0;
        $values = [];
        foreach ($attributes as $attribute) { $i++;
            
            $values[$i]['id'] = $attribute['value_id'];
            $values[$i]['name'] = $attribute['value_name'];
        }
        
        return $values;
    }
    
    /**
     * returns \Attribute Model createt by pairname
     * 
     * @param type $attributeName
     * @param type $valueName
     * @return \Attribute
     */
    public static function getByNamePair($attributeName, $valueName) {
        
        $stmt = Db::get()->prepare("SELECT 
                                            a.id    AS id,
                                            a.name  AS name,
                                            
                                            av.id   AS value_id,
                                            av.name AS value_name
                                        
                                        FROM
                                            attributes a
                                            
                                        LEFT JOIN
                                            attribute_values av
                                        ON
                                            a.id = av.attribute_id
                                            
                                        WHERE 
                                            a.name = :attribute_name
                                            AND av.name = :value_name");
        
        $stmt->execute(['attribute_name' => $attributeName, 'value_name' => $valueName]);
        $attribute = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $data['id'] = $attribute['id'];
        $data['name'] = $attribute['name'];
        $data['valueId'] = $attribute['value_id'];
        $data['valueName'] = $attribute['value_name'];

        return AttributeFactory::make($data);
    }
    
    //
    public function save($data) {

        foreach ($data as $attribute) {
            
            //
            if (!empty($attribute['id'])) {
             
                self::update($attribute);
            }
            
            //
            else {
                
                self::insert($attribute);
            }
        }
    }
    
    //
    private function update($attribute) {
        
        Db::get()->update('attributes', ['name' => $attribute['name']], ['id' => $attribute['id']]);  
        
        $stmt = Db::get()->prepare("INSERT INTO attribute_values (attribute_id, name) VALUES (:attribute_id, :name) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
        
        foreach ($attribute['values'] as $value) {
            
            $stmt->execute([':attribute_id' => $attribute['id'], ':name' => $value]);  
        }
    }
    
    //
    private function insert($attribute) {
        
        $attributeId = Db::get()->insert('attributes', ['name' => $attribute['name']]);  
        
        $stmt = Db::get()->prepare("INSERT INTO attribute_values (attribute_id, name) VALUES (:attribute_id, :name) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
        
        foreach ($attribute['values'] as $value) {
            
            $stmt->execute([':attribute_id' => $attributeId, ':name' => $value]);  
        }
    }
    
    //
    public function remove($data) {
        
        Db::get()->delete('attributes', ['id' => $data['id']]);
        Db::get()->delete('attribute_values', ['attribute_id' => $data['id']]);
    }
}
