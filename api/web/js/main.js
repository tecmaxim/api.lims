$(document).ready(function(){
	$('.mySelectBoxClass').customSelect();
       
   $(".dashboard_collapse").click(function(){ 
      $("#" + $(this).attr('data-id')).toggle();
    });
});

function example()
{
    alert('prueba'); return false;
}




// $(document).ready(function() {
//	$("#datepicker").datepicker();
//});

$(".menu").click(function(){
	$(".navbar").css("right", "0px");
      
        var pageHeight = $( document ).height();
	var pageWidth = $(window).width();
     
        $('#black').css({"height": pageHeight}).fadeIn( "fast", function() {
            
		$(this).show();
	});
});

$(document).on('click touchstart', '.black', function() {
	$(".navbar").css("right", "-240px");
	$(this).hide();
});

$("html").bind("click touchstart", function(){
	$(".notifications-panel").hide();
});
$(".notifications").click(function(){ 
	$(".notifications-panel").toggle();
});
$('.navbar').click(function(event){
    event.stopPropagation();
});
$('.menu').click(function(event){
    event.stopPropagation();
});
$('.notifications').click(function(event){
    event.stopPropagation();
});


$('.close').click(function () {
    $(this).parent().fadeOut();
});

// With JQuery
//$("#range").slider({});

//$('.range').draggable({});

$('.table-con-link tbody tr td:not(.no-link)').each( function(i, e) 
{
    $(e).attr('data-toggle', 'modal');
    $(e).attr('data-target', '#modal');
    $(e).attr('data-url', $(this).parent().find('a').attr('href'));
   //console.log($(e));
});
$('.table-con-link tbody tr ').click( function() {
    // window.location = $(this).find('a').attr('href');
    
    // console.log($(this).find('a').attr('href'));
    // $('#modal').data('bs.modal', null);
    // $('#modal').removeData('bs.modal');
    // $('#modal').modal(
    // {
    //   remote: $(this).find('a').attr('href'),
    //   toggle: 'modal',
    //   show: true
    // });
}).hover( function() {
   
    $(this).toggleClass('hover');
});
$(document).on('pjax:complete', function(){
    $('.table-con-link tbody tr td:not(.no-link)').each( function(i, e) 
{
    $(e).attr('data-toggle', 'modal');
    $(e).attr('data-target', '#modal');
    $(e).attr('data-url', $(this).parent().find('a').attr('href'));
   //console.log($(e));
});
   $('.table-con-link tbody tr ').hover( function() {
   
    $(this).toggleClass('hover');
});
})

$('#tipo-de-reclamo').on('shown.bs.collapse', function () {
   $(".glyphicon-1").removeClass("collapse-open").addClass("collapse-close");
});
$('#tipo-de-reclamo').on('hidden.bs.collapse', function () {
   $(".glyphicon-1").removeClass("collapse-close").addClass("collapse-open");
});

$('#informacion-basica').on('shown.bs.collapse', function () {
   $(".glyphicon-2").removeClass("collapse-open").addClass("collapse-close");
});
$('#informacion-basica').on('hidden.bs.collapse', function () {
   $(".glyphicon-2").removeClass("collapse-close").addClass("collapse-open");
});

$('#informacion-semilla').on('shown.bs.collapse', function () {
   $(".glyphicon-3").removeClass("collapse-open").addClass("collapse-close");
});
$('#informacion-semilla').on('hidden.bs.collapse', function () {
   $(".glyphicon-3").removeClass("collapse-close").addClass("collapse-open");
});
$('#calidad-de-semillas').on('shown.bs.collapse', function () {
   $(".glyphicon-4").removeClass("collapse-open").addClass("collapse-close");
});

$('#calidad-de-semillas').on('hidden.bs.collapse', function () {
   $(".glyphicon-4").removeClass("collapse-close").addClass("collapse-open");
});

$('#relevamiento-del-reclamo').on('shown.bs.collapse', function () {
   $(".glyphicon-5").removeClass("collapse-open").addClass("collapse-close");
});
$('#relevamiento-del-reclamo').on('hidden.bs.collapse', function () {
   $(".glyphicon-5").removeClass("collapse-close").addClass("collapse-open");
});

$(function() {
    $('.table-reclamos tbody tr td:first-child input').change(function() {
        $(this).closest('tr').toggleClass("highlight", this.checked);
    });
});

$('#cultivo').change(function() {
    if ($(this).val() === "") {
      $('.sl-cultivo label').addClass('needsfilled-label');
      $('.sl-cultivo .customSelect').addClass('needsfilled-select');
    } else {
      $('.sl-cultivo label').removeClass('needsfilled-label');
      $('.sl-cultivo .customSelect').removeClass('needsfilled-select');
    }
});

$('#problema').change(function() {
    if ($(this).val() === "") {
      $('.sl-problema label').addClass('needsfilled-label');
      $('.sl-problema .customSelect').addClass('needsfilled-select');
    } else {
      $('.sl-problema label').removeClass('needsfilled-label');
      $('.sl-problema .customSelect').removeClass('needsfilled-select');
    }
});

$('#detalle').change(function() {
    if ($(this).val() === "") {
      $('.sl-detalle label').addClass('needsfilled-label');
      $('.sl-detalle .customSelect').addClass('needsfilled-select');
    } else {
      $('.sl-detalle label').removeClass('needsfilled-label');
      $('.sl-detalle .customSelect').removeClass('needsfilled-select');
    }
});

$(document).ready(function(){  
    $("#form-nuevo-reclamo").submit(function () {  
        if($("#cultivo").val() === "") {  
            $('.sl-cultivo label').addClass('needsfilled-label');
            $('.sl-cultivo .customSelect').addClass('needsfilled-select');
            $('.mensaje-error-validacion').show();
        } 
        if($("#problema").val() === "") {  
            $('.sl-problema label').addClass('needsfilled-label');
            $('.sl-problema .customSelect').addClass('needsfilled-select');
            $('.mensaje-error-validacion').show();  
        } 
        if($("#detalle").val() === "") {  
            $('.sl-detalle label').addClass('needsfilled-label');
            $('.sl-detalle .customSelect').addClass('needsfilled-select');
            $('.mensaje-error-validacion').show();
        }
        return false;  
    });  
});  

/**
 * MODAL FUNCTIONS
 * ===============
 */

$('body').on('show.bs.modal','#modal', function (event) {
    var link = $(event.relatedTarget); // link that triggered the modal
    var contentUrl = link.data('url'); 
    var title = link.data('title');// Extract info from data-* attributes
    var modal = $(this);
    
    var divLoading = $('<div/>').attr('id', 'modalLoading');
    var pathname = window.location.pathname;
    if (pathname === "/advanta.gdbms/frontend/web/")
    {
        var imgLoading = $('<img/>').attr('src', 'images/loading.gif').attr('width', 30);
    }
    else
    {
        var imgLoading = $('<img/>').attr('src', '../images/loading.gif').attr('width', 30);
    }
    
    divLoading.append(imgLoading);
    modal.find('.modal-body').html(divLoading);

    if(typeof title !== 'undefined')
        modal.find('.modal-title').text(title);
    else
        modal.find('.modal-title').hide();
    
    modal.find('.modal-body').load(contentUrl);
    
    // This could be change at future
    modal.find('.modal-footer').hide();
});


// Reset modal
$('#modalItem').on('show.bs.modal', function(e){
    var alertSuccess = $('#alertSuccess');
    var alertFail = $('#alertFail');
    var form = $('#itemForm');
    
    alertSuccess.hide();
    alertFail.hide();
    form.show();
    form[0].reset();
});

$(document).on('click','[data-reload="#modal"]', function (event) {
    var link = $(this); // link that triggered the modal
    var contentUrl = link.data('url'); 
    var title = link.data('title');// Extract info from data-* attributes
    var modal = $(link.data('reload'));
    
    var divLoading = $('<div/>').attr('id', 'modalLoading');
    var imgLoading = $('<img/>').attr('src', '../images/loading.gif').attr('width', 30);
    divLoading.append(imgLoading);
    modal.find('.modal-body').html(divLoading);

    if(typeof title !== 'undefined')
        modal.find('.modal-title').text(title);
    else
        modal.find('.modal-title').hide();

    modal.find('.modal-body').load(contentUrl);

    // This could be change at future
    modal.find('.modal-footer').hide();
});

$(document).on('hidden.bs.modal', '#modal', function () {
    //data: return data from server
    if($('#itemList').length == 1) 
        $.pjax.reload({container:'#itemList'});  //Reload
});

$(document).on('submit','#itemForm', function(event) {
    var postData = $(this).serializeArray();
    var alertFail = $('#alertFail');
    var form = $('#itemForm');
    var formURL = form.attr('action');
    var modal = $('#modal');
    //alert('estoy entrando aca');
    postData[postData.length] = {'name': 'submit', 'value':'1'};
    //alertSuccess.hide();
    //alertFail.hide();

    $.ajax(
    {
        url : formURL,
        type: 'POST',
        data : postData,
        success:function(data, textStatus, jqXHR) 
        {
            modal.find('.modal-body').html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) 
        {
            //if fails      
            form.replaceWith(alertFail.html());
        }
    });

    event.preventDefault();
    event.stopPropagation();
});