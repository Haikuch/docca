<table class="phperror">
    
    <tr>
        <td class="">
            <span class="fat mysmall"><?= $level; ?>|</span> 
            <span class="file"><?= $file; ?></span> 
            <span class="fat mysmall"><?= $line; ?></span>
        </td>
    </tr>
    
    <tr>
        <td>
            <span class="message1"><?= isset($message[0]) ? $message[0] : ''; ?></span> |
            <span class="message2"><?= isset($message[1]) ? $message[1] : ''; ?></span> |        
            <span class="message2"><?= isset($message[2]) ? $message[2] : ''; ?></span> |        
            <span class="message2"><?= isset($message[3]) ? $message[3] : ''; ?></span> |        
        </td>
    </tr>
    
     <tr>
        <td>                         
            <pre class="context"><? print_r($context); ?></pre>
        </td>
    </tr>
    
</table>
