$(document).ready(function()   
{ 
	// 动态给index-content宽度
	var documentWidth = $(document).width() - 225 - 40; 
	$(".realTime-content").css({"width": documentWidth+"px"});
	// 当窗口高度发生变化时，动态修改高度
	$(window).resize(function() { 
		var documentWidth = $(document).width() - 225 - 40; 
		$(".realTime-content").css({"width": documentWidth+"px"});
	})
})

// bootstrap提示工具
$(function () { $("[data-toggle='tooltip']").tooltip(); });

function addSeat(cla,name,id,endTime,phy_id) {
	if (endTime != "0") {
		var txt = '<div class="seat-card user-card">'+name+'</div>';
	}else{
		var txt = '<div onclick="userEnd('+id+','+phy_id+',\''+cla+'\',\''+name+'\')" class="seat-card user-card user-notend user'+id+'">'+name+'</div>';
	}
	$("."+cla).append(txt);
}