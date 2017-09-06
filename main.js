$(document).ready(function(){
	$('.mySelectBoxClass').customSelect();
        
        var pageHeight = $( document ).height();
        //var pageWidth = $(window).width();
        $('#divBlack').css({"height": pageHeight});
        
});

$(document).ready(function() {
	$("#datepicker").datepicker();
});

$(".menu").click(function(){ 
	$(".navbar").css("right", "0px");
	$("#black").fadeIn( "fast", function() {
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
$("#range").slider({});

$('.range').draggable({});

$('.table-con-link tbody tr').click( function() {
    window.location = $(this).find('a').attr('href');
}).hover( function() {
    $(this).toggleClass('hover');
});

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

$('.anio2013').addClass("disable");
$('.anio2012').addClass("disable");
$('.anio2011').addClass("disable");


$('#collapse2014').on('shown.bs.collapse', function () {
   $('.anio2014').removeClass("disable");
});
$('#collapse2014').on('hidden.bs.collapse', function () {
   $('.anio2014').addClass("disable");
});

$('#collapse2013').on('shown.bs.collapse', function () {
   $('.anio2013').removeClass("disable");
});
$('#collapse2013').on('hidden.bs.collapse', function () {
   $('.anio2013').addClass("disable");
});

$('#collapse2012').on('shown.bs.collapse', function () {
   $('.anio2012').removeClass("disable");
});
$('#collapse2012').on('hidden.bs.collapse', function () {
   $('.anio2012').addClass("disable");
});

$('#collapse2011').on('shown.bs.collapse', function () {
   $('.anio2011').removeClass("disable");
});
$('#collapse2011').on('hidden.bs.collapse', function () {
   $('.anio2011').addClass("disable");
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