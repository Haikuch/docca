<form method="POST" action="<?= DIR; ?>docs/save" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $document->getId(); ?>">
    
    <div id="docform" class="table single">
        
        <div class="row">
            <div class="cell"><?php echo Language::show('form_title', 'Document'); ?></div>
            <div class="cell"><input type="text" name="name" value="<?= $document->getName(); ?>" placeholder="<?php echo Language::show('form_title_placeholder', 'Document'); ?>"></div>
        </div>
        
        <div class="row">
            <div class="cell"><?php echo Language::show('form_files', 'Document'); ?></div>
            <div class="cell">
                <input type="hidden" name="fileIdsToDelete[]" value="">
                <?php

                    foreach ($document->getFiles() as $file) {

                        echo '<div class="files">';
                            echo '<span class="file-delete" title="' . Language::show('form_file-delete', 'Document') . '">✔</span>';
                            echo '<span class="file-activate" title="' . Language::show('form_file-activate', 'Document') . '">x</span>'; 
                            echo '<a href="' . DIR . 'upload/' . $file->getFileLinkName() . '">' . $file->getName() . '</a>'; 
                            echo '<input type="hidden" name="fileIdsToDelete[]" value="' . $file->getId() . '" disabled>';
                            
                        echo '</div>';
                    }

                ?> 
                <input type="file" name="newfiles[]" multiple>
            </div>
        </div>
        
        <div class="row">
            <div class="cell"><?php echo Language::show('form_sourcetime', 'Document'); ?></div>
            <div class="cell"><input type="text" name="sourceTime" value="<?= date('d.m.Y', $document->getSourceTime()->getTimestamp()); ?>" placeholder="05.11.2000"></div>
        </div>
        
        <div class="row">
            <div class="cell"><?php echo Language::show('form_comment', 'Document'); ?></div>
            <div class="cell"><textarea name="comment" placeholder="<?php echo Language::show('form_comment_placeholder', 'Document'); ?>"><?= $document->getComment(); ?></textarea></div>
        </div>
        
        <div class="row">
            <div class="cell"><?php echo Language::show('form_tags', 'Document'); ?></div>
            <div class="cell">
                <textarea name="tags" placeholder="<?php echo Language::show('form_tags_placeholder', 'Document'); ?>"><?php

                    $i=0;
                    foreach ($document->getTags() as $tag) { $i++;

                        echo $tag->getName();
                        echo count($document->getTags()) > $i ? ', ' : '';
                    }

                ?></textarea>
                
            </div>
        </div>
        
        <div class="row attributes">
            <div class="cell"><?php echo Language::show('form_attributes', 'Document'); ?></div>
            <div class="cell table">
                <?php

                    foreach (App\Models\AttributeRepo::getActive() as $attribute) {

                        echo '<div class="attribute row">';
                        
                            echo '<div class="name cell">';
                                echo $attribute['name']; 
                            echo '</div>';
                            
                            echo '<div class="values cell">';
                                echo '<select name="attributes[' . $attribute['id'] . ']">';
                                    echo '<option value="0">nicht gewählt</option>'; 

                                    foreach ($attribute['values'] as $value) {
                                        
                                        $selected = $document->hasAttributeValue($attribute['id'], $value['id']) ? 'SELECTED' : '';
                                        
                                        echo '<option value="' . $value['id'] . '" ' . $selected . '>' . $value['name'] . '</option>';
                                    }

                                echo '</select>';                                
                            echo '</div>';
                        
                        echo '</div>';
                    }

                ?>            
            </div>
        </div>
        
        <div class="row">
            <div class="cell"></div>
            <div class="cell">
                <input type="submit" value="<?php echo Language::show('form_save', 'Document'); ?>">
                <?= $document->getId() ? '<input type="submit" onclick="return confirm(\'' . Language::show('index_remove_confirm', 'Document') . '\');" name="remove" value="' . Language::show('form_delete', 'Document') . '">' : ''; ?>
            </div>
        </div>
        
    </div>
    
</form>