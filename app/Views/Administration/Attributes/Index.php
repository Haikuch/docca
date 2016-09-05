
<h4>Attribute bearbeiten</h4>

<div id="attributes-index">
    <form method="POST" action="<?= DIR; ?>admin/attributes/save" enctype="multipart/form-data">
        
    
    <?php

        foreach ($attributeList as $attribute) {

            $values = [];
            foreach ($attribute['values'] as $value) {

                $values[] = $value['name'];
            }
            asort($values);

            echo '<input type="hidden" name="id[]" value="'.$attribute['id'].'">';
            echo '<input class="name" type="text" name="name[]" value="'.$attribute['name'].'"> ';    
            echo '<input class="values" type="text" name="values[]" value="' . implode(', ', $values) . '">';  
            echo '<br>';
        }
    ?>
    
    <input class="name" type="text" name="name[]" placeholder="Name des Attributes">
    <input class="values" type="text" name="values[]" placeholder="Mögliche Werte des Attributes"><br>
    <br>
    <input type="submit" value="Speichern">
    </form>
</div>