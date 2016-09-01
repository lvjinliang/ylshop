<?php
namespace Org\Util;
class Email {

    /**
     * 添加邮件记录
     * @author 良子
     * @param $to
     * @param $tplid
     * @param $title
     * @param $content
     * @param int $pri
     * @return bool|mixed
     */
    static public function addEmailLog($to, $tplid, $title, $content, $pri = 9) {
        if (empty($to) || empty($tplid) || empty($title) || empty($content)) {
            return false;
        } else {
            $data['to'] = $to;
            $data['tplid'] = $tplid;
            $data['title'] = $title;
            $data['content'] = $content;
            $data['content'] = htmlspecialchars_decode($data['content']);
            $data['pri'] = $pri;
            $data['date_added'] = time();
            $status = M('EmailLog')->add($data);
            if($status) {
                if((int)C('MAIL_SEND_TIME')===1){
                    self::sendMail($data['to'],$data['title'],$data['content'],$status);
                }
            }
            return $status;
        }
    }

    static public function AddRegisterEmailVerify($to,$username, $code) {
        $condition['id'] = 2;
        $data = M('EmailTpl')->where($condition)->find();
        $replace[] = $username;
        $replace[] = C('SHOP_URL').U('home/register/check',array('code'=>$code));
        $data['content'] = str_replace(array('{userName}','{url}'), $replace, $data['content']);
        $status = self::addEmailLog($to,2,$data['title'],$data['content'],9);
        return $status;
    }

    static public function AddRegisterSuccessEmail($to, $username){
        $condition['id'] = 1;
        $data = M('EmailTpl')->where($condition)->find();
        $replace[] = $username;
        $replace[] = date("Y-m-d H:i:s");
        $data['content'] = str_replace(array('{userName}','{date}'), $replace, $data['content']);
        $status = self::addEmailLog($to,1,$data['title'],$data['content'],8);
        return $status;
    }


    static public function sendMail($to, $subject, $content, $emailLogId=0) {
        $data = array();
        try {
            $mail = new \Org\Util\PHPMailer();
            $mail->IsSMTP(); // 使用SMTP方式发送
            $mail->SMTPAuth   = true;
            $mail->CharSet='UTF-8';// 设置邮件的字符编码
            $mail->Host = C('MAIL_HOST'); // 您的企业邮局服务器
            $mail->Port = C('MAIL_PORT'); // 设置端口
            $mail->SMTPSecure = C('MAIL_SECURE');
            $mail->SMTPAuth = true; // 启用SMTP验证功能
            $mail->Username = C('MAIL_USERNAME'); // 邮局用户名(请填写完整的email地址)
            $mail->Password = C('MAIL_PASSWORD'); // 邮局密码

            $mail->From = C('MAIL_USERNAME'); //邮件发送者email地址
            $mail->FromName = C('MAIL_FROMNAME');
            $mail->AddAddress($to, $to); //收件人地址，可以替换成任何想要接收邮件的email信箱,格式是AddAddress("收件人email","收件人姓名")
            $mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
            $mail->Subject = $subject;//"PHPMailer测试邮件"; //邮件标题
            $mail->Body = $content; //邮件内容
            $result = $mail->Send();
            if($result) {
                \Think\Log::write('发送成功','INFO','',C('LOG_PATH').'ylshop.log');
                $data['send_status'] = 1;
            } else {
                \Think\Log::write($mail->ErrorInfo,'INFO','',C('LOG_PATH').'ylshop.log');
                $data['message'] = $mail->ErrorInfo;
                $data['send_status'] = 0;
            }
        } catch  (phpmailerException $e) {
            \Think\Log::write($e->errorMessage(),'INFO','',C('LOG_PATH').'ylshop.log');
            $data['message'] = $e->errorMessage();
            $data['send_status'] = 0;

        }
        if($emailLogId) {
            $data['id'] = $emailLogId;
            $data['sendtime'] = time();
            M('EmailLog')->save($data);
            if($data['send_status'] == 0) {
                M('EmailLog')->where('id='.$emailLogId)->setInc('error_num',1);
            }
        }

    }


}