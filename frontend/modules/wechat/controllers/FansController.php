<?php

namespace modules\wechat\controllers;

use yii;
use yii\web\NotFoundHttpException;
use modules\wechat\models\Fans;
use modules\wechat\models\MpUser;
use modules\wechat\models\Message;
use modules\wechat\models\FansSearch;
use modules\wechat\models\MessageHistory;
use modules\wechat\models\MessageHistorySearch;
use components\MpWechat;
use yii\web\UploadedFile;

/**
 * 模块回复规则控制
 * @package modules\wechat\controllers
 */
class FansController extends AdminController
{
    /**
     * 粉丝列表
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FansSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, true);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);

        $dataProvider->query->orderBy('id desc');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 更新粉丝列表
     * @return mixed
     */
    public function actionCheckFans()
    {
        $fansModel = new Fans();
        $mpWechat = Yii::createObject(MpWechat::className(), [$this->getWechat()]);
        $userList = $mpWechat->getUserList('');//获取所有关注的粉丝列表

        if($userList['count']>10000){
            $userList_ = $mpWechat->getUserList($userList['next_openid']);//粉丝数量大于10000时
            $userList=array_merge($userList,$userList_);
        }

        if (is_array($userList) && !empty($userList['data']['openid'])) {
            $user_list=[];

            foreach($userList['data']['openid'] as $k=>$v){
                $user_list[$k] = $mpWechat->getUserInfo($v);
            }

            foreach($user_list as $v){
                $fansModel->deleteUser($this->getWechat(),$v);
                $fansModel->updateUser($this->getWechat(),$v);
            }

            echo json_encode(['status'=>1,'info'=>'同步成功']);
        }else{
            echo json_encode(['status'=>0,'info'=>'当前没有关注粉丝']);
        }

    }

    /**
     * 用户查看
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMessage($id)
    {
        $model = $this->findModel($id);
        $message = new Message($this->getWechat());

        if ($message->load(Yii::$app->request->post())) {
            $message->toUser = $model->open_id;
            if ($message->send()) {
                $data=['wid'=>$this->getWechat()->id,'from'=>$this->getWechat()->original,'to'=>$message->toUser,'message'=>$message->content,'type'=>$message->msgType,'created_at'=>time()];
                $historyModel = new MessageHistory();
                $historyModel->add($data);
                $this->flash('消息发送成功!', 'success');
            }
        }
        $message->toUser = $model->open_id;

        $searchModel = new MessageHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->sort = [
            'defaultOrder' => ['created_at' => SORT_DESC]
        ];

        $dataProvider->query
            ->wechat($this->getWechat()->id)
            ->wechatFans($this->getWechat()->original,$model->open_id);

        return $this->render('message', [
            'model' => $model,
            'message' => $message,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 上传素材
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionUpload($id)
    {
        $model = $this->findModel($id);
        if (isset($_FILES[$model->formName()]['name'])) {
            foreach ($_POST[$model->formName()] as $attribute => $value) {
                $uploadedFile = UploadedFile::getInstance($model, $attribute);
                if ($uploadedFile === null) {
                    continue;
                } elseif ($uploadedFile->error == UPLOAD_ERR_OK) {
//                    $this->getWechat()->getSdk()->uploadMedia($this->tempName, );
                }
            }
        }
        return $this->message('上传失败', 'error');
    }

    /**
     * Updates an existing Fans model.
     * If update is successful, the browser will be redirected to the 'update' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $mpusermodel = $this->findMpuserModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'mpusermodel' => $mpusermodel,
            ]);
        }
    }

    /**
     * Finds the Fans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $query = Fans::find()
            ->andWhere([
                'id' => $id,
                'wid' => $this->getWechat()->id
            ]);
        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findMpuserModel($id)
    {
        $query = Mpuser::find()
            ->andWhere([
                'id' => $id
            ]);
        if (($model = $query->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
