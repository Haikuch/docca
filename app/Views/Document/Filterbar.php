<div id="filterbar">
    <form action="find" method="POST">
        <?php
            foreach ($filterList->getAllOrEmpty() as $actingFilter) {
        ?>
        <div class="filter">
            
            <select name="filters[]">
                <option value="none"><?= Language::show('selectdefault', 'Filterbar'); ?></option>
                <?php
                    foreach (\App\Models\Filterset::getList() as $filter) {
                        
                        echo '<option value="' . $filter->getName() . '"';
                        echo ($filter->getName() == $actingFilter->getName()) ? 'SELECTED' : '';
                        echo '>';
                            echo Language::show('description_' . $filter->getName(), 'Filterbar');
                        echo '</option>';
                    } 
                ?>
            </select>
            
            <span class="value-fields">
                <span filtername="<?= $actingFilter->getName(); ?>">
                    <?= $actingFilter->getValueFields(); ?>
                </span>
            </span>
            
            <img class="removefilter" src="<?= Url::templatePath().'images/removefilter.png'; ?>"> 
            <img class="addfilter" src="<?= Url::templatePath().'images/addfilter.png'; ?>">            
        </div>
        <?php
            }
        ?>
    <input type="submit" value="Anzeigen">
    </form>
</div>

<div id="filterbar-clones">
    <div class="filter">
        <select name="filters[]">
            <option value="none"><?= Language::show('selectdefault', 'Filterbar'); ?></option>
            <?php
                    foreach (\App\Models\Filterset::getList() as $filter) {
            ?>
                <option value="<?= $filter->getName(); ?>">
                    <?= Language::show('description_' . $filter->getName(), 'Filterbar'); ?>
                </option>
            <?php        
                    } 
            ?>
        </select>
        
        <span class="value-fields"></span>
        
        <img class="removefilter" src="<?= Url::templatePath().'images/removefilter.png'; ?>"> 
        <img class="addfilter" src="<?= Url::templatePath().'images/addfilter.png'; ?>"> 
    </div>
    
    <span class="value-fields">
        <?php
        foreach (\App\Models\Filterset::getList() as $filter) {

            echo '<span filtername="' . $filter->getName() . '">';
                echo $filter->getValueFields();
            echo '</span>';
        } 
        ?>
    </span>
</div>