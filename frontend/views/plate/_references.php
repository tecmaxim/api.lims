<div id="references">
     <div class="row">
        <h4 id="action_references"><span class="glyphicon glyphicon-plus"></span> References</h4>
     </div>
    <div id="detail_references" class="row hiddem">
        
        <div class="col-xs-12 col-sm-6 col-md-1 col-lg-1 ">
            <div class="sample_ok"></div> <div> Samples Successfully Controlled </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-1 col-lg-1 ">
            <div class="rojo"></div> Dead Samples
        </div>
        <div class="col-xs-12 col-sm-6 col-md-1 col-lg-1 ">
            <div class="bad_adn"></div> DNA Bad Extraction
        </div>
        <div class="col-xs-12 col-sm-6 col-md-1 col-lg-1 ">
            <div class="green_success"></div> DNA Successful Extraction
        </div>
        <div class="col-xs-12 col-sm-6 col-md-1 col-lg-1 ">
            <div class="position"></div> Default - No actions
        </div>
        
        <div class="col-xs-12 col-sm-6 col-md-1 col-lg-1 ">
            <div class="emptyness"></div> Empty Well
        </div>
        
    </div>
</div>

<script>

$(function(){
    $('#action_references').click(function(){ 
                                if($("#detail_references").is(":visible"))
                                {
                                    $("#detail_references").slideUp('500');
                                }else
                                {
                                    $("#detail_references").slideDown('500');
                                };
                            });
});
</script>