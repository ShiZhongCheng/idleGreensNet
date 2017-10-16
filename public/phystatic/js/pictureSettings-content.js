$(document).ready(function()   
{ 
	// 动态给index-content宽度
	var documentWidth = $(document).width() - 225 - 40; 
	$(".pictureSettings-content").css({"width": documentWidth+"px"});
	// 当窗口高度发生变化时，动态修改高度
	$(window).resize(function() { 
		var documentWidth = $(document).width() - 225 - 40; 
		$(".pictureSettings-content").css({"width": documentWidth+"px"});
	})
})

// bootstrap提示工具
$(function () { $("[data-toggle='tooltip']").tooltip(); });

// 模态框显示，（模态标题,参数,模态id,类型）
function modalShow(modalTitle,parameter,modalID,type) {
	switch(type)
	{
		// 修改说明
		case 'configure-explain':
			// 给模态body加内容
			var content =  '<div class="modal-body">'+
								'<div>'+
									'<textarea class="form-control" rows="5" placeholder="请输入新说明"></textarea>'+
							    '</div>'+
							'</div>'+
							'<div class="modal-footer">'+
								'<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>'+
								'<button type="button" class="btn btn-primary" onclick="confirmChange(this,'+parameter+',\''+type+'\',\''+modalID+'\')">确认更改</button>'+
							'</div>';
			$("#"+modalID+" .modal-content").append( content );
			break;

		// 更改图片
		case 'configure-img':
			// 给模态body加内容
			var content  =  '<div class="modal-body">'+
								'<div id="zyupload" class="zyupload"></div>'+
							'</div>'+
							'<div class="modal-footer">'+
								'<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>'+
								'<button type="button" class="btn btn-primary" onclick="confirmChange(this,'+parameter+',\''+type+'\',\''+modalID+'\')">确认更改</button>'+
							'</div>';
			$("#"+modalID+" .modal-content").append( content );
			$("#"+modalID+" .modal-content").width(780);

			// 图片上传初始换
			newZyUpload("zyupload");
			break;
	}

	// 修改模态标题
	$("#"+modalID+" .modal-title").text(modalTitle);
	// 模态显示出来
	$('#'+modalID).modal('show');
}
// 模态关闭时执行
$(function () { 
	$('#myModal').on('hide.bs.modal', function () {
		// 刷除内容
		$(this).find(".modal-body").remove();
		$(this).find(".modal-footer").remove();
	})
});
// 确认修改函数，(参数,类型)
function confirmChange(obj,parameter,type,modalID) {
	switch(type)
	{
		case 'configure-explain':
			var newValue = $(obj).parent().parent().find("textarea").val();
			if (newValue == "") {
				addAlert("提醒！","内容为空，请填写内容","warning");
				return;
			}
			// 进行后台修改
			ajaxHttp(newValue,parameter,type);
			break;

	    case 'configure-img':
		    var newValue = $(obj).parent().parent().find("#zyupload").val();
		    if (newValue == "") {
				addAlert("提醒！","请先上传图片","warning");
				return;
			}
			// 进行后台修改
			ajaxHttp(newValue,parameter,type);
	        break;
	}
	// 模态隐藏
	$('#'+modalID).modal('hide');
}