<div id="docview">
    
    <div id="haze"></div>
    
    <div id="file-options">
        <div class="download">
            <span class="title">Datei herunterladen</span>
            <?php

                foreach ($document->getFiles() as $file) { 
            
                    echo '<div>';
                        echo '<a href="' . DIR . 'upload/' . $file->getFileLinkName() . '" file-id="' . $file->getId() . '">';
                            echo $file->getName();
                        echo '</a>';
                    echo '</div>';

                } 
            ?>
        </div>
    </div>

<form method="POST" action="save" enctype="multipart/form-data">
        
    <div class="table single data">
        
        <div class="caption">
            <?= $document->getName(); ?>
        </div>
        
        <div class="row files">
            <div class="cell"><?= Language::show('form_files', 'Document'); ?></div>
            <div class="cell">
                <?php

                    foreach ($document->getFiles() as $file) {

                        echo '<div class="file" file-id="' . $file->getId() . '">';

                        //start thumb per page
                        //todo: all thumbs from all files should be merged together
                        for ($i=0; $file->getPageNumbers() > $i; $i++) {
                            
                            $width = $file->getPageNumbers() > 5 ? (100 / $file->getPageNumbers() - 2) : 'inherit';
                        
                ?>
                            <a href="#show-preview" class="thumb" style="width: <?= $width; ?>%; z-index: <?= $file->getPageNumbers()-$i; ?>">

                                <img src="<?= THUMBPATH . $file->getThumbLinkName($i); ?>.jpg">

                            </a>

                            <img class="preview" src="<?= PREVIEWPATH . $file->getPreviewLinkName($i); ?>.jpg">
                <?php
                            
                        }

                        echo '</div>';
                
                    }
                    
                    if (count($document->getFiles()) < 1) {
                        
                        echo Language::show('form_nofiles', 'Document');
                    }

                ?>
            </div>
        </div>
        
        <div class="row sourcetime">
            <div class="cell"><?= Language::show('form_sourcetime', 'Document'); ?></div>
            <div class="cell"><?= date('d.m.Y', $document->getSourceTime()->getTimestamp()); ?></div>
        </div>
        
        <div class="row comment">
            <div class="cell"><?= Language::show('form_comment', 'Document'); ?></div>
            <div class="cell"><i><?= $document->getComment(); ?></i></div>
        </div>
        
        <div class="row tags">
            <div class="cell"><?= Language::show('form_tags', 'Document'); ?></div>
            <div class="cell">
                <?php

                    $i=0;
                    foreach ($document->getTags() as $tag) { $i++;

                        echo '<a href="' . DIR . 'docs/find?filter[hastags]=' . $tag->getName() . '">' . $tag->getName() . '</a>';
                        echo count($document->getTags()) > $i ? ' | ' : '';
                    }

                ?> 
            </div>
        </div>
        
        <div class="row attributes">
            <div class="cell"><?= Language::show('form_attributes', 'Document'); ?></div>
            <div class="cell table">
                <?php

                    $i=0;
                    foreach ($document->getAttributes() as $attribute) {

                        echo '<div class="row">';
                            echo '<span class="cell attribute-name">' . $attribute->getName() . '</span>';
                            echo '<a class="cell attribute-value" href="' . DIR . 'docs/find?filter[hasattributevalue]=' . $attribute->getName() . ':' . $attribute->getValueName() . '">' . $attribute->getValueName() . '</a>';
                        echo '</div>';
                    }

                ?> 
            </div>
        </div>
        
        <div class="row uploader">
            <div class="cell"><?= Language::show('form_uploader', 'Document'); ?></div>
            <div class="cell"><?= $document->getUploader(); ?></div>
        </div>
        
    </div>
    
    <div class="options">
        
        <a href="<?= DIR . 'docs/edit/' . $document->getId(); ?>" class="edit">
            <img src="<?= Url::templatePath().'images/editing10.png'; ?>">
        </a>
        <a href="<?= DIR . 'docs/remove/' . $document->getId(); ?>" onclick="return confirm('<?php echo Language::show('index_remove_confirm', 'Document'); ?>');" class="delete">
            <img src="<?= Url::templatePath().'images/delete10.png'; ?>">
        </a>
        
    </div>
    
</form>
    
</div>