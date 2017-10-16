$(document).ready(function()   
{   
	// 动态给content-box高度
	var documentHeight = $(document).height() - 52; 
	$(".content-box").css({"min-height": documentHeight+"px"});
	$(".left").css({"min-height": documentHeight+"px"});
	// 当窗口高度发生变化时，动态修改高度
	$(window).resize(function() { 
		var documentHeight = $(document).height() - 52; 
		$(".content-box").css({"min-height": documentHeight+"px"});
		$(".left").css({"min-height": documentHeight+"px"});
	})

	// 左侧图片配置折叠效果
	$(".panel-heading").click(function () {
		// 判断是否存在class in
		var haveIn = $(this).parent().parent().find(".panel-collapse").hasClass("in");
		if (haveIn) {
			// 有in，添加下箭头
			$(this).find("i").removeClass();
			$(this).find("i").addClass("fa fa-chevron-down")
		}else{
			// 没有i你，添加上箭头
			$(this).find("i").removeClass();
			$(this).find("i").addClass("fa fa-chevron-up")
		}
	})
}) 