<table class="phperror" style="background: yellowgreen;border: 1px solid orange;padding-top:0px;">
    
    
    <?php 
    
        for ($i=0;$i<=$deep;$i++) {
            
    ?>
    <tr>
        <td>
            <span class="file" style="font-size:8pt; font-style: italic;color: #555555;"><?= $file[$i]; ?></span> 
            <span class="line" style="font-size:8pt; font-style: italic;color: #555555;">[<?= $function[$i]; ?>]</span>
            <span class="line" style="font-size:8pt; font-style: italic;color: #555555;"><?= $line[$i]; ?></span>
        </td>
    </tr>
    <?php } ?>
    
    <tr>
        <td class="">
            <pre class="message2">|<?php (is_object($var)  OR is_array($var)) ? var_dump( $var) : print_r(htmlspecialchars($var)); ?>|</pre>
        </td>
    </tr>
     
    
</table>
