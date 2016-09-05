//Standard Input ausblenden
$(".fxfo_fileInput input[type=file]")   .css("opacity","0")
                                        .css("-moz-opacity","0")
                                        .css("position","absolute")
                                        .css("z-index","2");

//Fake-Input einblenden
$(".fxfo_fileInput .newInput").css("display","");

//Value des Standardinput übernehmen
$(".fxfo_fileInput input[type=file]").change(function() {
    
    $(".fxfo_fileInput[forfile=" + $(this).attr("name") + "] .filepath").html($(this).val());
});