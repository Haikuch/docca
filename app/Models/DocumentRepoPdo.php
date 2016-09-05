<?php
namespace App\Models;

use Core\Model;
use App\Models\TagRepo;
use App\Models\FileRepo;
use Helpers\Arr;
use Helpers\Db;

class DocumentRepoPdo extends Model {
   
    public static function getActive() {
        
        $stmt = Db::get()->prepare( "SELECT 
                                            d.id AS id,
                                            d.name AS name,
                                            d.comment AS comment,
                                            d.source_time AS sourceTime,
                                            
                                            f.id AS fileId,
                                            f.name AS fileName,
                                            
                                            t.id AS tagId,
                                            t.name  AS tagName

                                        FROM 
                                            documents d

                                        #Taglinks
                                        LEFT JOIN
                                            document_tags dt
                                        ON 
                                            dt.doc_id = d.id

                                        #Tags
                                        LEFT JOIN
                                            tags t
                                        ON
                                            dt.tag_id = t.id

                                        #Filelinks
                                        LEFT JOIN
                                            document_files df
                                        ON
                                            df.doc_id = d.id

                                        #Files
                                        LEFT JOIN
                                            files f
                                        ON
                                            df.file_id = f.id

                                        WHERE 
                                            d.active = 1
                                            
                                        ORDER BY
                                            d.name ASC");
                                
        $stmt->execute();
        $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $documentDatas = self::reformat($records); 
        
        $documentList = [];
        foreach ($documentDatas as $documentData) {
            
            $documentList[] = DocumentFactory::make($documentData);
        }
        
        return $documentList;
    }
    
    public static function getById($docId) { #err($docId);
        
        $docIds = Arr::make($docId); #err($docIds);
        
        if (empty($docIds)) {
            
            return [];
        }
        
        $stmt = Db::get()->prepare( "SELECT 
                                            d.id AS id,
                                            d.name AS name,
                                            d.comment AS comment,
                                            d.source_time AS sourceTime,
                                            d.uploader AS uploader,
                                            
                                            f.id    AS fileId,
                                            f.name  AS fileName,
                                            f.pagenumbers AS filePagenumbers,
                                            
                                            t.id    AS tagId,
                                            t.name  AS tagName,
                                            
                                            a.id    AS attributeId,
                                            a.name  AS attributeName,
                                            v.id    AS valueId,
                                            v.name  AS valueName

                                        FROM 
                                            documents d


                                        LEFT JOIN
                                            document_tags dt
                                        ON 
                                            dt.doc_id = d.id

                                        LEFT JOIN
                                            tags t
                                        ON
                                            dt.tag_id = t.id


                                        LEFT JOIN
                                            document_files df
                                        ON
                                            df.doc_id = d.id

                                        LEFT JOIN
                                            files f
                                        ON
                                            df.file_id = f.id


                                        LEFT JOIN
                                            document_attributes da
                                        ON
                                            da.doc_id = d.id

                                        LEFT JOIN
                                            attributes a
                                        ON
                                            da.attribute_id = a.id

                                        LEFT JOIN
                                            attribute_values v
                                        ON
                                            da.value_id = v.id


                                        WHERE 
                                            d.id IN (" . Db::placeholders($docIds) . ")");
                                
        $stmt->execute($docIds); 
        $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $documentDatas = self::reformat($records); 
        
        $documentList = [];
        foreach ($documentDatas as $documentData) {
            
            $documentList[] = DocumentFactory::make($documentData);
        }
        
        return $documentList;
    }
    
    //
    public static function getFiltered(FilterList $filterList) {
        
        $filters = $filterList->getAllOrEmpty();
        
        $where = '';
        $join = '';
        $params = [];
        
        //documentName
        if (isset($filters['documentname'])) {
            
            $where .= "AND MATCH (d.name) AGAINST (:document_name IN BOOLEAN MODE) ";
            $params[':document_name'] = $filters['documentname']->getValue();
        }
        
        //comment
        if (isset($filters['documentcomment'])) {
            
            $where .= "AND MATCH (d.comment) AGAINST (:comment IN BOOLEAN MODE) ";
            $params[':comment'] = $filters['documentcomment']->getValue();
        }
        
        //sourcetime
        if (isset($filters['sourcetime'])) {
            
            $time = $filters['sourcetime']->getValue('from');
            
            $where .= "AND d.source_time BETWEEN :sourcetime_from AND :sourcetime_to ";
            $params[':sourcetime_from'] = $time['from'];
            $params[':sourcetime_to'] = $time['to'];
        }
        
        //hasTags
        if (isset($filters['hastags'])) {
            
            $tagIds = TagRepo::getIdsByTagList($filters['hastags']->getTagList());
            
            $where .= "AND dt.tag_id IN (" . implode(',', $tagIds) . ") ";
            $joinTagLinks = true;
        }
        
        //reqTags
        if (isset($filters['reqtags'])) {
            
            $tagIds = TagRepo::getIdsByTagList($filters['reqtags']->getTagList());
            
            $where .= "AND dt.tag_id IN (" . implode(',', $tagIds) . ") group by id   having count(*) = " . count($tagIds) . " ";
            $joinTagLinks = true;
        }
        
        //hasAttributeValue
        if (isset($filters['hasattributevalue'])) { #todo: klappt nicht bei zwei attributen weil nur jeder record einzeln ausgewertet wird
            
            $attributes = $filters['hasattributevalue']->getAttributeList(); #err($attributes);
            
            $join .= "  #Attributes
                        LEFT JOIN
                            document_attributes da
                        ON 
                            da.doc_id = d.id ";
            
            foreach ($attributes as $attribute) {
                
                $where .= "AND da.attribute_id = " . (int) $attribute->getId() . " AND da.value_id = " . (int) $attribute->getValueId() . " ";
            }            
        }
        
        //Taglinks
        if (isset($joinTagLinks)) {
            
            $join .= "  #Taglinks
                        LEFT JOIN
                            document_tags dt
                        ON 
                            dt.doc_id = d.id";
        }
        
        //TODO: irgendwie anders machen
        if ($where == '') {
            
            return [];
        }
        
        $stmt_foundDocs = Db::get()->prepare( $sql = "SELECT 
                                            d.id AS id

                                        FROM 
                                            documents d

                                        " . $join . "

                                        WHERE 
                                            d.active = 1
                                            " . $where . ""); #err($sql);  err($params);
                                
        $stmt_foundDocs->execute($params);
        $foundDocs = $stmt_foundDocs->fetchAll(\PDO::FETCH_ASSOC); #err($foundDocs);
        
        $foundIds = [];
        foreach ($foundDocs as $foundDoc) {
            
            $foundIds[] = $foundDoc['id'];
        }
        
        $documentList = self::getById($foundIds); #err($documentList);
        
        return $documentList;
    }
    
    /**
     * reformats sql-record-array to document-data-array
     * 
     * @param array $records
     * @return array $documentDatas
     */
    private function reformat($records) {
        
        //
        if (empty($records)) {
            
            return [];
        }
        
        $documentDatas = [];
        foreach ($records as $record) {
            
            //start new document
            if (!isset($documentDatas[$record['id']])) {
                
                //reindex arrays for last document
                isset($lastDocumentId) AND $documentDatas[$lastDocumentId]['tags'] = array_values($documentDatas[$lastDocumentId]['tags']);
                isset($lastDocumentId) AND $documentDatas[$lastDocumentId]['files'] = array_values($documentDatas[$lastDocumentId]['files']);
                isset($lastDocumentId) AND $documentDatas[$lastDocumentId]['attributes'] = array_values($documentDatas[$lastDocumentId]['attributes']);
                
                $documentDatas[$record['id']] = $record;
                
                //unset unneccessary data
                #todo: because there is too much data in a record that is not documentdata itself, 
                #otherwise we would have to pick the documentdata, but then we would have to add them here everytime we set new datafield for document
                unset($documentDatas[$record['id']]['tagId']);
                unset($documentDatas[$record['id']]['tagName']);
                unset($documentDatas[$record['id']]['fileId']);
                unset($documentDatas[$record['id']]['fileName']);
                unset($documentDatas[$record['id']]['attributeId']);
                unset($documentDatas[$record['id']]['attributeName']);
                unset($documentDatas[$record['id']]['valueId']);
                unset($documentDatas[$record['id']]['valueName']);
                
                $documentDatas[$record['id']]['tags'] = [];
                $documentDatas[$record['id']]['files'] = [];
                $documentDatas[$record['id']]['attributes'] = [];
            }
            
            //set tags
            !empty($record['tagId']) AND $documentDatas[$record['id']]['tags'][$record['tagId']]['id'] = $record['tagId'];
            !empty($record['tagId']) AND $documentDatas[$record['id']]['tags'][$record['tagId']]['name'] = $record['tagName'];
            
            //set files
            !empty($record['fileId']) AND $documentDatas[$record['id']]['files'][$record['fileId']]['id'] = $record['fileId'];
            !empty($record['fileId']) AND $documentDatas[$record['id']]['files'][$record['fileId']]['name'] = $record['fileName'];
            !empty($record['fileId']) AND $documentDatas[$record['id']]['files'][$record['fileId']]['pagenumbers'] = $record['filePagenumbers'];
            
            //set attributes
            if (!empty($record['attributeId'])) {
                
                $documentDatas[$record['id']]['attributes'][$record['attributeId']]['id'] = $record['attributeId'];
                $documentDatas[$record['id']]['attributes'][$record['attributeId']]['name'] = $record['attributeName'];
                $documentDatas[$record['id']]['attributes'][$record['attributeId']]['valueId'] = $record['valueId'];
                $documentDatas[$record['id']]['attributes'][$record['attributeId']]['valueName'] = $record['valueName'];
            }
                
            $lastDocumentId = $record['id'];
        }
                
        //reindex arrays
        $documentDatas[$lastDocumentId]['tags'] = array_values($documentDatas[$lastDocumentId]['tags']);
        $documentDatas[$lastDocumentId]['files'] = array_values($documentDatas[$lastDocumentId]['files']);
        
        return array_values($documentDatas);
    }
    

    public static function remove(int $docId) {
        
        Db::get()->delete('documents', ['id' => $docId]);
        
        self::deleteTagLinksByDocId($docId);
        self::deleteFileLinksByDocId($docId);
    }
   
    public static function save(Document $document) {
        
        //TODO: empty object is currently bad, should be NULL here
        if ( !$document->getId() ) {
            
            return self::insert($document);
        }
        
        return self::update($document);
    }

    public static function update(Document $document) {   
        
        //update document
        $where = ['id' => $document->getId()];        
        Db::get()->update('documents', $document->getDataArray(), $where);

        //tag treatment
        self::deleteTagLinksByDocId($document->getId());
        self::insertTags($document);
        self::insertFiles($document);
        self::deleteAttributeLinksByDocId($document->getId());
        self::insertAttributeLinks($document);
        
        return $document->getId();
    }

    public static function insert(Document $document) {   
        
        $docId = Db::get()->insert('documents', $document->getDataArray());
        $document->setId($docId);
        
        self::insertTags($document);
        self::insertFiles($document);
        self::insertAttributeLinks($document);
        
        return $document->getId();
    }
    
    /**
     * saves the enclosed tags to the database
     * 
     * @param \App\Models\Document $document
     * @return type
     */
    private function insertTags(Document $document) {
        
        $tagIds = TagRepo::save($document->getTags());   
        #todo: save should return taglist not tagids
        self::insertTagLinks($document->getId(), $tagIds);
        
        return $tagIds;
    }
    
    /**
     * saves the enclosed files to the database
     * 
     * @param \App\Models\Document $document
     * @return type
     */
    private function insertFiles(Document $document) {
        
        $files = FileRepo::save($document->getFiles()); 
        self::insertFileLinks($document->getId(), $files);
        
        return $files;
    }
    
    private function deleteAttributeLinksByDocId($docId) {
        
        Db::get()->delete('document_attributes', ['doc_id' => $docId]);
    }
    
    private function deleteTagLinksByDocId($docId) {
        
        Db::get()->delete('document_tags', ['doc_id' => $docId]);
    }
    
    private function deleteFileLinksByDocId($docId) {
        
        //TODO: delete files as well
        Db::get()->delete('document_tags', ['doc_id' => $docId]);
    }
    
    private function insertTagLinks($docId, $tagIds) {
        
        //invalid $docId
        if ($docId < 1) {
            
            throw new \Exception('Unexpected DocumentId format: ' . $docId);
        } 
        
        //invalid $tagIds
        if (!is_array($tagIds)) {
            
            throw new \Exception('Unexpected TagIds format');
        } 
        
        $stmt_link = Db::get()->prepare("INSERT INTO document_tags SET doc_id = :doc_id, tag_id = :tag_id");
        
        foreach ($tagIds as $tagId) {
            
            $stmt_link->execute(['doc_id' => $docId, 'tag_id' => $tagId]);
        }
    }
    
    private function insertAttributeLinks($document) { #err($attributes); die();
                
        $stmt_link = Db::get()->prepare("INSERT INTO document_attributes SET doc_id = :doc_id, attribute_id = :attribute_id, value_id = :value_id");
        
        foreach ($document->getAttributes() as $attribute) {
            
            $stmt_link->execute(['doc_id' => $document->getId(), 'attribute_id' => $attribute->getId(), 'value_id' => $attribute->getValueId()]);
        }
    }
    
    private function insertFileLinks($docId, $files) {
        
        //invalid $docId
        if ($docId < 1) {
            
            throw new \Exception('Unexpected DocumentId format: ' . $docId);
        } 
        
        //invalid $fileIds
        if (!is_array($files)) {
            
            throw new \Exception('Unexpected Files format');
        } 
        
        $stmt_link = Db::get()->prepare("INSERT INTO document_files SET doc_id = :doc_id, file_id = :file_id");
        
        foreach ($files as $file) {
            
            $stmt_link->execute(['doc_id' => $docId, 'file_id' => $file->getId()]);
        }
    }
    
    //
    public static function deleteFileLinksByFileId($fileIds) {
        
        foreach ($fileIds as $fileId) {
            
            if (!$fileId) {
                
                continue;
            }
            
            Db::get()->delete('document_files', ['file_id' => $fileId]);
        }
    }
}
