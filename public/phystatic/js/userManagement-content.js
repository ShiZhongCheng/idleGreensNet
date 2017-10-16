
$(document).ready(function()   
{ 
	// 动态给index-content宽度
	var documentWidth = $(document).width() - 225 - 40; 
	$(".userManagement-content").css({"width": documentWidth+"px"});
	// 当窗口高度发生变化时，动态修改高度
	$(window).resize(function() { 
		var documentWidth = $(document).width() - 225 - 40; 
		$(".userManagement-content").css({"width": documentWidth+"px"});
	})
})

// bootstrap提示工具
$(function () { $("[data-toggle='tooltip']").tooltip(); });

// 选中用户类型
function chooseRole(role) {
 	$("#role"+role).addClass("btn-primary");
} 