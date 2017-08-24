<?php
namespace app\web;
use app\web\modules\models\Log;
use Yii;

class Helper {

    /*历史访客数*/
    public static function getHistoryVisNum(){
        $res = Log::find()->count();
        return $res;
    }

    /*最近一个月访问量*/
    public static function getMonthHistoryVisNum(){
        $LastMonth = strtotime("-1 month");
        $res = Log::find()->where(['>','create_time',$LastMonth])->count();
        return $res;
    }

}