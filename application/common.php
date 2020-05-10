<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function fail_return($content=""){
    $content = $content?$content:"非法访问";
    return json(['code'=>1,'msg'=>$content]);
}

function success_return($content=""){
    $content = $content?$content:"非法访问";
    return json(['code'=>0,'msg'=>$content]);
}

/**
 * 密码加密方式[pwd+salt+authkey]
 * @param $passwordinput
 * @param $salt
 * @return string
 */
function user_hash($passwordinput,$salt){
    $passwordinput = "{$passwordinput}-{$salt}-".config('setting.authkey')."";
    return sha1($passwordinput);
}

function get_salt(){
    return rand(100000,999999);
}

/**
 * @param $address
 * @param $title
 * @param $message
 * @return mixed
 */
function SendMail($address,$title,$message){
    vendor ('phpmailer.phpmailer.src.PHPMailer');
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet='UTF-8';
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);
    // 设置邮件正文
    $mail->Body=$message;
    //设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
    $mail->From=config('mailer.smtp_email');
    //设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
    $mail->FromName=config('mailer.smtp_user');
    // 设置邮件标题
    $mail->Subject=$title;
    // 设置SMTP服务器。
    $mail->Host=config('mailer.smtp_host');
    // 设置为"需要验证"
    $mail->SMTPAuth=true;
    //smtp登录的账号 这里填入字符串格式的qq号即可
    $mail->Username=config('mailer.smtp_email');
    //smtp登录的密码 使用生成的授权码 你的最新的授权码
    $mail->Password=config('mailer.smtp_key');
    //开启html解析
    $mail->IsHTML(true);

    //端口&协议ssl   生产环境需要开启
//    $mail->SMTPSecure = 'ssl';
//    $mail->Port = config('mailer.smtp_port');

    // 发送邮件。    成功返回true或false
    return($mail->Send());
}

