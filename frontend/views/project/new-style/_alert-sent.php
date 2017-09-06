<?php if($msj['type'] == 'success'): ?>
    
<div class="alert alert-<?= $msj['type']?>">
    <strong><span class="glyphicon glyphicon glyphicon-ok-sign"></span> <?= $msj['message']?></strong>
</div>
<?php else: ?>

<div class="alert alert-<?= $msj['type']?>">
    <strong><span class="glyphicon glyphicon glyphicon-remove-sign"></span> <?= $msj['message']?></strong>
</div>
<?php endif; ?>
