<?php


namespace app\user\logic;



use app\user\model\User;
use think\Loader;
use app\user\model\VerificationEmail as EmailDB;
use think\Validate;

class Index extends LogicModel
{

    public static function login(){
        //基本验证
        if (empty($_POST)) return "非法访问！";

        $rule = [
            'email'     =>     'require|email',
            'passwd'    =>     'require',
//            '__token__' =>     'token',
        ];
        $msg = [
            'email.email'           =>  "注意邮箱格式！",
            'email.require'         =>  "注意邮箱输入！",
            'passwd.require'        =>  "注意密码输入！",
//            '__token__'    =>  "页面过期，请刷新后在提交！",
        ];
        $validate = Validate::make($rule,$msg);
        $result = $validate->check($_POST);
        if (!$result) return $validate->getError();

        //验证
        $User = new User();
        $data['email'] = $_POST['email'];
        $userData = $User->get($data);
        if (empty($userData)) return "用户不存在，请注册！";
        $submitPasswd = user_hash($_POST['passwd'],$userData['salt']);
        if ($submitPasswd == $userData['passwd']){
            //缓存
            return ['code'=>'0','msg'=>'登陆成功'];
        }else{
            return "密码错误,请重新输入！";
        }
    }

    /**
     * @title 注册
     * @return array|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function register(){
        $User = new User();
        $validate = Loader::validate('User');
        if(!$validate->check($_POST)){
            return $validate->getError();
        }
        //判断昵称是否重复
        $data['nickname'] = $_POST['nickname'];
        $isNickname = $User->get($data);
        if ($isNickname){
            return "Nickname 已被使用请更换！";
        }

        //邮件验证码
        $EmailDB = new EmailDB();
        $codeData =  $EmailDB->where(['email'=>$_POST['email'],'code_type'=>0])->order('add_time','desc')->find();
        if (!empty($codeData)){
            if (time()>$codeData['add_time']+12*60*60) return "验证码错误 请重新输入（001）！";
            //strcasecmp() 相同false
            //strtoupper() 转大写
            if (strcasecmp($_POST['email_code'],$codeData['code_num'])) return "验证码错误 请重新输入（002）！";
            //验证成功  修改状态
            $EmailDB->isUpdate(true)->save(['id'=>$codeData['id'],'code_type'=>1]);
        }else{
            return "验证码错误 请重新输入（003）！";
        }

        //密码构造
        $data['salt'] = get_salt();
        $data['passwd'] = user_hash($_POST['passwd'],$data['salt']);
        //准备入库
        $data['email'] = $_POST['email'];
        $data['chat_type'] = $_POST['user_type'];
        $data['add_time'] = time();

        $User->data($data)->save();
        if(!$User->id) return "网络错误，请稍后再试！";
        return ['code'=>0,'msg'=>'注册成功，请前往登陆！'];

    }


}