<?php
/**
 * User: 良子
 * Date: 16-03-01
 */

namespace Home\Model;

use Think\Model;

class EmailVerifyModel extends Model {

    /**
     * @param $code
     * @return int {1:非法验证码,2:验证码失效,3:该验证码已验证,4:验证成功，5:系统错误}
     */
    public function verifyCode($code) {
        $sql = "SELECT ev.*,a.username FROM __PREFIX__email_verify ev
                INNER JOIN __PREFIX__account a ON ev.account_id=a.id
                WHERE ev.type='register' AND ev.code='{$code}' LIMIT 0,1";
        $verifyData = $this->query($sql);
        $verifyData = $verifyData[0];
        if(empty($verifyData)){
            return 1;
        } else {
            if(time()>$verifyData['date_end']) {
                $this->execute("DELETE FROM __PREFIX__account WHERE id='{$verifyData['account_id']}'");
                $this->execute("DELETE FROM __PREFIX__account_activity WHERE account_id='{$verifyData['account_id']}'");
                return 2;
            } elseif($verifyData['status']==2) {
                return 3;
            } else {
                $sql = "UPDATE  __PREFIX__account
                        SET is_validated=1
                        WHERE id='{$verifyData['account_id']}'";
                if($this->execute($sql)!==false){
                    $this->execute("UPDATE  __PREFIX__account SET is_validated=1 WHERE code='{$code}'");
                    \Org\Util\Email::AddRegisterSuccessEmail($verifyData['email'],$verifyData['username']);
                    return 4;
                } else {
                    return 5;
                }
            }
        }

    }
}


?>