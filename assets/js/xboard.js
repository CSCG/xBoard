$(function(){
	$('.unixtime').each(function(){
		var timestamp = $(this).attr("data-timestamp");
		var date = new Date(timestamp*1000);
		$(this).html(date.toLocaleTimeString() + " " + date.toLocaleDateString());
	});
});