<?php
namespace app\api\controller;
use think\View;
use think\Request;
use think\Loader;
use think\Cookie;
use think\Db;

class Admin
{
	// 首页
	public function index()
	{
		// 判断是否需要登录
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifNeedLogin();

        // 获取首页所需数据
        $IndexDataModel = Loader::model('IndexData');
        $InDa = $IndexDataModel->getData();
		
		$view = new View();
		$view->engine->layout('layout/layout');
		return $view->fetch('index',[
            'title'           => '杭电物理实验中心',
            'userMesg'        => $userMesg,
            'InDa'            => $InDa,
        ]);
	}
	// 登录
	public function login()
	{
		$date = date("Y/m/d");
		$view = new View();
		return $view->fetch('login',[
            'title'           => '杭电物理实验中心——首页',
            'date'            => $date,
        ]);
	}
	// 图片设置
	public function pictureSettings()
	{
		// 判断是否需要登录和是否拥有权限
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifHavePower();

        // 获取图片设置所需数据
        $PictureSettingsDataModel = Loader::model('PictureSettingsData');
        $InDa = $PictureSettingsDataModel->getData();

        $view = new View();
		$view->engine->layout('layout/layout');
		return $view->fetch('pictureSettings',[
            'title'           => '杭电物理实验中心——图片设置',
            'userMesg'        => $userMesg,
            'InDa'            => $InDa,
        ]);
	}
	// 课前必看
	public function courseSettings()
	{
		// 判断是否需要登录和是否拥有权限
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifHavePower();

        // 获取课前必看所需数据
        $PictureSettingsDataModel = Loader::model('CourseSettingsData');
        $InDa = $PictureSettingsDataModel->getData();

        $view = new View();
		$view->engine->layout('layout/layout');
		return $view->fetch('courseSettings',[
            'title'           => '杭电物理实验中心——课前必看',
            'userMesg'        => $userMesg,
            'InDa'            => $InDa,
        ]);
	}
	// 用户管理
	public function userManagement()
	{
		// 判断是否需要登录
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifHavePower();

        // 获取用户管理所需数据
        $UserManagementData = Loader::model('UserManagementData');
        $InDa = $UserManagementData->getData();

        $view = new View();
		$view->engine->layout('layout/layout');
		return $view->fetch('userManagement',[
            'title'           => '杭电物理实验中心——用户管理',
            'userMesg'        => $userMesg,
            'InDa'            => $InDa,
        ]);
	}
	// 实验记录
	public function experimentalRecord()
	{
		// 判断是否需要登录
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifNeedLogin();

        // 获取首页所需数据
        $IndexDataModel = Loader::model('ExperimentalRecord');
        $InDa = $IndexDataModel->getData();
		
		$view = new View();
		$view->engine->layout('layout/layout');
		return $view->fetch('experimentalRecord',[
            'title'           => '杭电物理实验中心——实验记录',
            'userMesg'        => $userMesg,
            'InDa'            => $InDa,
        ]);
	}
	// 实时页面
	public function realTime()
	{
		// 判断是否需要登录
		$GeneralModel = Loader::model('General');
        $userMesg = $GeneralModel->ifNeedLogin();

        // 获取首页所需数据
        $IndexDataModel = Loader::model('RealTimeData');
        $InDa = $IndexDataModel->getData();
		
		$view = new View();
		$view->engine->layout('layout/layout');
		return $view->fetch('realTime',[
            'title'           => '杭电物理实验中心——实时情况',
            'userMesg'        => $userMesg,
            'InDa'            => $InDa,
        ]);
	}
}