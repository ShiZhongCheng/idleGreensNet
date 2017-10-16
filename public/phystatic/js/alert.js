// 添加弹框函数，(success,info,warning,danger)
function addAlert(title,content,type) {
	// 所要添加弹框的字符串内容
	var cla = "cla"+getNowFormatDate();
	var appendContent = '<div class="alert alert-'+type+' '+cla+'">'+
							'<a href="#" class="close" data-dismiss="alert">&times;</a>'+
							'<strong>'+title+'</strong>'+content+
						'</div>';
    // 判断当前有多少个弹框
    var index = $('.alert-content').children('.alert').length;
    // 如果个数大于等于3，让第一个弹框消失然后在添加弹框，弹框显示总数为3
	if (index >= 3) {
		$('.alert-content .alert:first').fadeOut("slow");
		$('.alert-content .alert:first').remove();
	}
	// 添加弹框
	$(".alert-content").append(appendContent);
	// 30秒后让此弹框消失
	setTimeout("closeAlert('"+cla+"')",6000);
}
// 弹框消失函数
function closeAlert(cla) {
	// 取得最后一个弹框,然后让其消失
	$("."+cla).fadeOut("slow");
	$("."+cla).remove();
}
// 获取当前时间函数
function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "";
    var seperator2 = "";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
            + seperator1 + date.getHours() + seperator2 + date.getMinutes()
            + seperator2 + date.getSeconds();
    return currentdate;
}
// bootstrap 弹框点击关闭
$(function(){
	$(".close").click(function(){
		$(this).alert();
	});
});  

// 移除加载
function loadingRemove() {
	$(".loading").remove();
}