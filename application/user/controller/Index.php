<?php

namespace app\user\controller;


use app\user\BaseController\Rece;
use app\user\logic\VerificationEmail;
use think\Request;
use app\user\logic\Index as IndexLogic;

class Index extends Rece
{
    /**
     * 首页
     */
    public function index()
    {
        if (Request::instance()->isPost()){
            $res = IndexLogic::login();
            if (is_array($res)){
                $this->success('成功','admin/index');
            }else{
//                return json(['code'=>1,'msg'=>$res]);
                $this->error($res);
            }
        }
        return $this->fetch();
    }

    /**
     * 注册
     */
    public function register(){
        if (Request::instance()->isPost()){
            $res = IndexLogic::register();
            if (is_array($res)){
                return json($res);
            }else{
                return json(['code'=>1,'msg'=>$res]);
            }
        }
        return $this->fetch();
    }

    /**
     * 密码找回
     */
    public function findPasswd(){

    }

    /**
     * 邮件验证发送
     */
    public function sendEmail(){
        if ($_GET['email']){
            $res = VerificationEmail::registerSend();
            if (is_array($res)){
                return json($res);
            }else{
                return json(['code'=>1,'msg'=>$res]);
            }
        }
        return json(['code'=>1,'msg'=>"注意邮箱格式！"]);
    }

}