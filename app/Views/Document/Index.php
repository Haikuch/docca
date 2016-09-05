<div id="docslist" class="table">
    
    <div class="row head">
        <div class="cell"><?php echo Language::show('head_title', 'Document'); ?></div>
        <div class="cell"><?php echo Language::show('head_comment', 'Document'); ?></div>  
        <div class="cell"><?php echo Language::show('head_tags', 'Document'); ?></div>    
        <div class="cell"><?php echo Language::show('head_actions', 'Document'); ?></div> 
    </div>
    
    <?php  foreach ($documents AS $document) { ?>
    
        <div class="row">
            <div class="cell name">
                <a href="<?= DIR . 'docs/view/' . $document->getId(); ?>">
                    <?= $document->getNameMarkedBy($marked['documentname'], '<span class="marked">', '</span>'); ?>
                </a>
            </div>
            
            <div class="cell comment">
                <?= $document->getCommentMarkedBy($marked['documentcomment'], '<span class="marked">', '</span>'); ?>
            </div>
            
            <div class="cell tags">
                <?php

                    $i=0;
                    foreach ($document->getTags() as $tag) { $i++;

                    $markClass = (isset($marked['tags']) AND in_array(strtolower($tag->getName()), $marked['tags'])) ? 'marked' : '';
                        echo '<a href="' . DIR . 'docs/find?filter[hastags]=' . $tag->getName() . '" class="' . $markClass . '">' . $tag->getName() . '</a>';
                        echo count($document->getTags()) > $i ? ' | ' : '';
                    }

                ?> 
            </div>
            
            <div class="cell actions">
                <a href="<?= DIR . 'docs/edit/'.$document->getId(); ?>" title="<?php echo Language::show('index_edit', 'Document'); ?>"><img src="<?= Url::templatePath().'images/edit.png'; ?>"></a>
                <a href="<?= DIR . 'docs/remove/'.$document->getId(); ?>" onclick="return confirm('<?php echo Language::show('index_remove_confirm', 'Document'); ?>');" title="<?php echo Language::show('index_delete', 'Document'); ?>"><img src="<?= Url::templatePath().'images/delete2.png'; ?>"></a>
            </div>
        </div>  
 
    <?php } ?>
        
</div>