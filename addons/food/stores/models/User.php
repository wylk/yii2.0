<?php

namespace app\addons\food\stores\models;
use yii\db\ActiveRecord;
use Yii;
class User extends ActiveRecord
{
	public $username;
	public $password;
	public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password','email'], 'required','message'=>'不能为空'],
            //[['username'], 'unique','message'=>'{attribute}已经被占用了'],
            ['password', 'validatePass'],
        ];
    }

    public function validatePass()
    {
        if (!$this->hasErrors()) {
            $data = self::find()->where('username = :username and password = :password', [":username" => $this->username, ":password" => md5($this->password)])->one();
            if (is_null($data)) {
                $this->addError("adminpass", "用户名或者密码错误");
            }
        }

    }

    public function login($data)
    {
        //$this->scenario = "login";
        if ($this->load($data) && $this->validate()) {
            //做点有意义的事
            //$lifetime = $this->rememberMe ? 24*3600 : 0;
            $lifetime = 3600*24;
            $session = Yii::$app->session;
            session_set_cookie_params($lifetime);
            $session['admin'] = [
                'username' => $this->username,
                'isLogin' => 1,
            ];
           // ip2long(Yii::$app->request->userIP)
            //$userIP = Yii::$app->request->userIP;
            $userIP ='180.96.11.189';
            $data = $this->post($userIP);
            $this->updateAll(['logintime' => time(), 'loginip' => $ip], 'username = :username', [':username' => $this->username]);
            return (bool)$session['admin']['isLogin'];
        }
        return false;
    }

        /*
    *根据新浪IP查询接口获取IP所在地
    */
    function getIPLoc_sina($queryIP){
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP;
        $ch = curl_init($url);
        //curl_setopt($ch,CURLOPT_ENCODING ,'utf8');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        $location = curl_exec($ch);
        $location = json_decode($location);
        curl_close($ch);

        $loc = "";
        if($location===FALSE) return "";
        if($location < 0) return "";
        if (empty($location->desc)) {
            $loc = $location->province.'-'.$location->city.'-'.$location->district.'-'.$location->isp;
        }else{
            $loc = $location->desc;
        }
        return $loc;
    }

    public function post($ip,$https=true,$method='get',$data=null)
    {
        $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$ip;
        //1.初始化url
        $ch = curl_init($url);
        //2.设置相关的参数
        //字符串不直接输出,进行一个变量的存储
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //判断是否为https请求
        if($https === true){
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        //判断是否为post请求
        if($method == 'post'){
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //3.发送请求
        $str = curl_exec($ch);
        //4.关闭连接
        curl_close($ch);
        //返回请求到的结果
        $location = json_decode($str);
        return $location->country;
  }

}