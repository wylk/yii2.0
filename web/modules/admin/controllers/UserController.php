<?php
namespace app\web\modules\admin\controllers;
use Yii;
use yii\web\Controller;
use app\web\modules\models\Log;
//use backend\models\PasswordForm;
use yii\data\Pagination;
use app\web\modules\models\AuthItem;
// use app\web\modules\models\PasswordForm;
use yii\base\NotSupportedException;
use app\web\modules\models\User;
// namespace backend\controllers;
// use backend\models\AuthItem;
// use backend\models\PasswordForm;
// use backend\models\User;


class UserController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 用户列表
     */
    public function actionList()
    {
        Yii::$app->user->identity->username;
        $data = User::find();
        $pages = new Pagination(['totalCount' =>$data->count(), 'pageSize' => '15']);

        $user = $data->joinWith('usergroup')->offset($pages->offset)->limit($pages->limit)->all();
        // var_dump($user);
        // die;
        $this->layout='layout1';
        return $this->render('list',[
            'user'=>$user,
            'pages' => $pages
        ]);
    }

    /**
     * 新增用户
     */
    public function actionCreate()
    {
        $model = new User();
        $model1 = new AuthItem();

        $auth = Yii::$app->authManager;
        $item = $auth->getRoles();
        $itemArr =array();
        foreach($item as $v){
            $itemArr[] .= $v->name;
        }
        foreach($itemArr as $key=>$value)
        {
            $item_one[$value]=$value;
        }

        if ($model->load(Yii::$app->request->post())) {
            /* 表单验证 */
            $post = Yii::$app->request->post();
            // var_dump($post);
            // die;

            $data = $post['User'];
            $data['created_at']     = time();
            // $data['username']=$post['username'];
            // $data['email']=$post['email'];
            $model->username= $data['username'];
            $model->email= $data['email'];
            /* 表单数据加载和验证，具体验证规则在模型rule中配置 */
            /* 密码单独验证，否则setPassword后密码肯定符合rule */
            if (empty($data['auth_key']) || strlen($data['auth_key']) < 6) {
                $this->error('密码为空或小于6字符');
            }
            $model->setAttributes($data);
            $model->generateAuthKey();
            $model->setPassword($data['auth_key']);
            /* 保存用户数据到数据库 */
            if ($model->save()) {
                //获取插入后id
                $user_id = $model->attributes['id'];
                $role = $auth->createRole($post['AuthItem']['name']);     //创建角色对象
                $auth->assign($role, $user_id);                           //添加对应关系
                return $this->redirect(['list']);
            }else{
                $this->error('操作错误');
            }
        } else {
            $this->layout="layout1";
            return $this->render('create', [
                'model' => $model,
                'model1' => $model1,
                'item' => $item_one
            ]);
        }
    }

    /**
     * 更新用户
     */
    public function actionUpdate(){
        $id = Yii::$app->request->get('id');
        $model = User::find()->joinWith('usergroup')->where(['id'=>$id])->one();
        $auth = Yii::$app->authManager;
        $item = $auth->getRoles();
        $itemArr =array();
        foreach($item as $v){
            $itemArr[] .= $v->name;
        }
        foreach($itemArr as $key=>$value)
        {
            $item_one[$value]=$value;
        }
        $model1 = $this->findModel($id);
        if ($model1->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            // var_dump($post);
            // die;
            //更新密码
            if(!empty($post['User']['auth_key_new'])){
                $model1->setPassword($post['User']['auth_key_new']);
                $model1->generateAuthKey();
            }else{
                $model1->auth_key = $post['User']['auth_key'];
            }
            $model1->email=$post['User']['email'];
            $return=$model1->save($post);
            // var_dump($return);
            // die;
            if(!empty($post['AuthAssignment']['item_name'])){
                //分配角色
                $role = $auth->createRole($post['AuthAssignment']['item_name']);    //创建角色对象
                $user_id = $id;
                $auth->revokeAll($user_id);
                $auth->assign($role, $user_id);       //分配角色与用户对应关系
            }

            return $this->redirect(['user/update', 'id' => $model1->id]);
        }
        $this->layout="layout1";
        return $this->render('update',[
            'model' => $model,
            'item' => $item_one
        ]);
    }

    /**
     * 删除用户
     */
    public function actionDelete($id)
    {
        $return=$this->findModel($id)->delete();
        if($return)
        {
            return $this->redirect(['user/list']);
        }else
        {
            return false;
        }
        // $this->success('删除成功！','list');
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            // $this->error('删除失败！');
            return false;
        }
    }

}
