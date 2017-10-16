$(document).ready(function()   
{ 
	// 动态给index-content宽度
	var documentWidth = $(document).width() - 225 - 40; 
	$(".index-content").css({"width": documentWidth+"px"});
	// 当窗口高度发生变化时，动态修改高度
	$(window).resize(function() { 
		var documentWidth = $(document).width() - 225 - 40; 
		$(".index-content").css({"width": documentWidth+"px"});
	})

	// 左侧功能区首页选中
	$(".left .nav-pills li:eq(0)").addClass("actived");

	// 轮播图（5秒切换）
    $('#myCarousel').carousel({
	    interval: 5000
	})
})

// 用户统计
function UserStatistics(student,administrators,teacher) {
    var userStatistics = echarts.init(document.getElementById('userStatistics'));
    // 指定图表的配置项和数据
	userStatisticsOption = {
		title: {
	        left: 'left',
	        text: '用户人数统计',
	    },
	    tooltip: {
	        trigger: 'item',
	        formatter: "{a} <br/>{b}: {c} ({d}%)"
	    },
	    legend: {
	        orient: 'vertical',
	        x: 'right',
	        data:['学生','教师','管理员']
	    },
	    series: [
	        {
	            name:'具体人数',
	            type:'pie',
	            radius: ['40%', '55%'],
	            label: {
	                normal: {
	                    formatter: '{a|{a}}{abg|}\n{hr|}\n  {b|{b}：}{c}  {per|{d}%}  ',
	                    backgroundColor: '#eee',
	                    borderColor: '#aaa',
	                    borderWidth: 1,
	                    borderRadius: 4,
	                    // shadowBlur:3,
	                    // shadowOffsetX: 2,
	                    // shadowOffsetY: 2,
	                    // shadowColor: '#999',
	                    // padding: [0, 7],
	                    rich: {
	                        a: {
	                            color: '#999',
	                            lineHeight: 22,
	                            align: 'center'
	                        },
	                        // abg: {
	                        //     backgroundColor: '#333',
	                        //     width: '100%',
	                        //     align: 'right',
	                        //     height: 22,
	                        //     borderRadius: [4, 4, 0, 0]
	                        // },
	                        hr: {
	                            borderColor: '#aaa',
	                            width: '100%',
	                            borderWidth: 0.5,
	                            height: 0
	                        },
	                        b: {
	                            fontSize: 16,
	                            lineHeight: 33
	                        },
	                        per: {
	                            color: '#eee',
	                            backgroundColor: '#334455',
	                            padding: [2, 4],
	                            borderRadius: 2
	                        }
	                    }
	                }
	            },
	            data:[	      
	                {value:student, name:'学生'},
	                {value:administrators, name:'教师'},
	                {value:teacher, name:'管理员'},
	            ]
	        }
	    ]
	};
    userStatistics.setOption(userStatisticsOption);
}

// 实验和报修统计
function ExperimentStatistics(finshEx,nowEx,foundEx,haveVideoEx,allRep,unDealRep) {
	var experimentStatistics = echarts.init(document.getElementById('experimentStatistics'));
	experimentStatisticsOption = {
		title: {
	        left: 'left',
	        text: '实验和报修统计',
	    },
	    tooltip : {
	        trigger: 'axis',
	        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
	            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
	        }
	    },
	    legend: {
	    	orient: 'vertical',
	    	x: 'right',
	        data:['已完成实验','正在进行的实验','现开设实验','已上传视频实验','报修总数','未处理报修']
	    },
	    grid: {
	        left: '3%',
	        right: '4%',
	        bottom: '3%',
	        containLabel: true
	    },
	    xAxis : [
	        {
	            type : 'category',
	            data : ['截止目前数据']
	        }
	    ],
	    yAxis : [
	        {
	            type : 'value'
	        }
	    ],
	    series : [
	        {
	            name:'已完成实验',
	            type:'bar',
	            data:[finshEx]
	        },
	        {
	            name:'正在进行的实验',
	            type:'bar',
	            data:[nowEx]
	        },
	        {
	            name:'现开设实验',
	            type:'bar',
	            
	            data:[foundEx]
	        },
	        {
	            name:'已上传视频实验',
	            
	            type:'bar',
	            data:[haveVideoEx]
	        },
	        {
	            name:'报修总数',
	            
	            type:'bar',
	            data:[allRep]
	        },
	        {
	            name:'未处理报修',
	            
	            type:'bar',
	            data:[unDealRep]
	        }
	    ]
	};

	experimentStatistics.setOption(experimentStatisticsOption);
}