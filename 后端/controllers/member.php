<?php
namespace hsC;
class member{
	public function codeToSession(){
        if(empty($_GET['code'])){exit(jsonCode('error', 'code error'));}
        $url =  "https://api.weixin.qq.com/sns/jscode2session?appid=".HS_APPID.
        "&secret=".HS_SECRET."&js_code=".$_GET['code']."&grant_type=authorization_code";
        $curl = new \hsTool\curl();
        $res = $curl->get($url);
        echo $res;
    }
	public function index(){
		
	}
	public function login(){
		//签名验证
		checkSign();
		//调用模型完成用户登录及注册
		$memberModel = new \hsModel\member();
		$memberModel->login();
	}
}
?>