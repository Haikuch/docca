<?php
namespace App\Models;

use Core\Model;
use Helpers\Arr;
use Helpers\Db;

class TagRepoPdo {
    
    //
    public function getById($tagId) {
        
        $tagIds = array_map('trim', Arr::make($tagId));
        
        foreach ($tagIds as $tagId) {
            
            $stmt = Db::get()->prepare("SELECT 
                                            *
                                        
                                        FROM
                                            tags
                                        
                                        WHERE
                                            id IN (" . Db::placeholders($tagIds) . ") ");
        }
        
        $stmt->execute($tagIds);
        $tagDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $tagList = [];
        foreach ($tagDatas as $tagData) {
            
            $tagList[] = TagFactory::make($tagData);
        }
        
        return $tagList;
    }
    
    //
    public function getByName($tagName) {
        
        $tagNames = array_map('trim', Arr::make($tagName, ','));
        
        foreach ($tagNames as $tagName) {
            
            $stmt = Db::get()->prepare("SELECT 
                                            *
                                        
                                        FROM
                                            tags
                                        
                                        WHERE
                                            name IN (" . Db::placeholders($tagNames) . ") ");
        }
        
        $stmt->execute($tagNames);
        $tagDatas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $tagList = [];
        foreach ($tagDatas as $tagData) {
            
            $tagList[] = TagFactory::make($tagData);
        }
        
        return $tagList;
    }
    
    //
    public function save($tags) {
        
        $stmt = Db::get()->prepare("INSERT INTO tags (name) VALUES (:name) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)");
        
        $tagIds = [];
        foreach ($tags as $tag) {
            
            $stmt->execute([':name' => $tag->getName()]);
            $tagIds[] = Db::get()->lastInsertId();
        }
        
        return $tagIds;
    }
}
