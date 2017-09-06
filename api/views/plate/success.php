<?php if(isset($response)): ?>
        
    <?php if($response == 'ok'): ?>    
        <div class="alert alert-success">The has been changed succesfully.</div>
    <?php else:?>
        <div class="alert alert-success">Warning. The plate is linked with 2 or more projects! </div>
    <?php endif;?>
<?php  else: ?>
    <div class="alert alert-success">The Plate was Ordered Again successfully.</div>
<?php  endif; ?>