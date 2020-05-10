<?php


namespace app\user\logic;


use app\user\model\User;
use app\user\model\VerificationEmail as EmailDB;

class VerificationEmail extends LogicModel
{
    private $email;
    protected $num=4;
    protected $len=0;

    public function __construct()
    {
        //验证码请求控制
        $this->email = $_GET['email'];

        $EmailDB = new EmailDB();
        $len = $EmailDB->where('email',$_GET['email'])
            ->whereTime('add_time','between',[mktime(0, 0, 0, date('m'), date('d'), date('Y')),mktime(23, 59, 59, date('m'), date('d'), date('Y'))])->count();
       if ($len){
           $this->len = $len;
       }
    }

    public static function registerSend(){
        $obj = new self();
        if ($obj->len>=$obj->num){
            return "您今日次数已使用完,请注意邮件是否回收到垃圾箱！";
        }
        //判断邮箱是否已经注册
        $User = new User();
        $res = $User->get(['email'=>$obj->email]);
        if ($res) return 'Email 已注册请，请前往找回密码！';
        //验证码生成
        $code = $obj->emailCode();
        $title = "注册验证";
        $address = $obj->email;
        $message = '<div style="font-size: 16px;">您的验证码为<strong style="color: #00FF00">'.$code.'</strong>,请在十二小时内完成验证！</div>';
//        return ['code'=>5,'msg'=>"调试！"];
        if (SendMail($address,$title,$message)){
            //数据入库
            $EmailDB = new EmailDB();
            $EmailDB->data([
                'email'     =>  $obj->email,
                'code_num'  =>  $code,
                'add_time'  =>  time(),
            ])->save();
            if (!$EmailDB->id) return "网络错误，请稍后再试！";

            return ['code'=>0,'msg'=>"验证码发送成功！"];
        }else{
            return "验证码发送失败，请刷新页面重新获取！";
        }
    }

    public function emailCode($length=5){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        for($i = 0; $i < $length; $i++)
        {
            // 两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
//            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return strtoupper($str);
    }

}