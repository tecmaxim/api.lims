<div id="container-header">
    <div class="nav nav-pills">
    <div class="steps selected" id="step1"><span class="glyphicon glyphicon-cog"></span> Project Definition</div>
    <!-- <div class="steps" id="step2">Markers By Trait</div> -->
    <div class="steps" id="step4"><span class="glyphicon glyphicon-list-alt"></span> Grid Definition</div>
    <?php if(Yii::$app->user->getIdentity()->itemName != 'breeder'): ?>
    <div class="steps" id="step2"><span class="glyphicon glyphicon-check"></span> Marker Selection</div>
    <?php endif; ?>
    <div class="steps" id="step5"><span class="glyphicon glyphicon-blackboard"></span> Project Preview</div>
    <div class="steps" id="sent"><span class="glyphicon glyphicon-send"></span> Shipment</div>
    <div class="steps" id="step6"><span class="glyphicon glyphicon-calendar"></span> Sample Dispatch</div>
    <div class="steps" id="step7"><span class="glyphicon glyphicon-calendar"></span> Sample Reception</div>
    </div>
</div>
<!--<div id="container-header">
    <ul class="nav nav-pills">
         <li class="active"><a href="#"><span class="glyphicon glyphicon-home"></span> Project Definition </a></li>
         <li><a href="#"><span class="glyphicon glyphicon-check"></span> Markers Selection</a></li>
         <li><a href="#"><span class="glyphicon glyphicon-list-alt"></span> Grid Definition</a></li>
         <li><a href="#"><span class="glyphicon glyphicon-blackboard"></span> Project Preview</a></li>
         <li><a href="#"><span class="glyphicon glyphicon-calendar"></span> Sample Dispatch</a></li>
         <li><a href="#"><span class="glyphicon glyphicon-calendar"></span> Sample Reception</a></li>
    </ul>
</div> -->