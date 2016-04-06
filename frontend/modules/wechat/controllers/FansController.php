<?php

namespace modules\wechat\controllers;

use yii;
use yii\web\NotFoundHttpException;
use modules\wechat\models\Fans;
use modules\wechat\models\MpUser;
use modules\wechat\models\Media;
use modules\wechat\models\Message;
use modules\wechat\models\FansGroups;
use modules\wechat\models\FansSearch;
use modules\wechat\models\FansGroupsSearch;
use modules\wechat\models\MessageHistory;
use modules\wechat\models\MessageHistorySearch;
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
        $dataProvider = $searchModel->search($this->request->queryParams, true);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);

        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['changeUrl']='change-group';//移动用户组按钮链接
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 粉丝分组
     * @return mixed
     */
    public function actionFangroup()
    {
        $searchModel = new FansGroupsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams, true);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);

        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['editUrl']='update-fansgroup';//更新按钮链接
        Yii::$app->params['delUrl']='delete-fansgroup';//删除按钮链接
        return $this->render('fangroup', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => '分组列表',//当前标题
            'addTitle' => '添加分组',//添加按钮标题
            'addUrl' => 'create-fansgroup',//添加按钮链接
        ]);
    }

    /**
     * 更新粉丝列表
     * @return mixed
     */
    public function actionCheckFans()
    {
        $fansModel = new Fans();
        $userList = $this->wechat->getSdk()->getUserList('');//获取所有关注的粉丝列表

        if($userList['count']>10000){
            $userList_ = $this->wechat->getSdk()->getUserList($userList['next_openid']);//粉丝数量大于10000时
            $userList=array_merge($userList,$userList_);
        }

        if (is_array($userList) && !empty($userList['data']['openid'])) {
            $user_list=[];

            foreach($userList['data']['openid'] as $k=>$v){
                $user_list[$k] = $this->wechat->getSdk()->getUserInfo($v);
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
     * 更新粉丝分组
     * @return mixed
     */
    public function actionCheckFansgroup()
    {
        $fansGroupsModel = new FansGroups();
        $groupList = $this->wechat->getSdk()->getGroupList();//获取所有粉丝分组
        //print_r($groupList);exit;

        if (is_array($groupList)) {
            $fansGroupsModel->deleteGroup($this->getWechat());
            foreach($groupList as $v){
                $fansGroupsModel->updateGroup($this->getWechat(),$v);
            }
        }

        echo json_encode(['status'=>1,'info'=>'同步成功']);

    }

    /**
     * 发送消息
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionMessage($id)
    {
        $model = $this->findModel($id);

        $message = new Message($this->getWechat());

        $message->toUser = $model->open_id;

        if ($message->load($this->request->post())) {
            if (111) {
            //if ($message->send()) {
                if($message->msgType=='text'){
                    $messages=$message->content;
                }elseif($message->msgType=='music'){
                    $mediaModel = new Media();
                    $media=$mediaModel::findOne([
                        'mediaId' => $message->thumbMediaId,
                    ]);
                    $messages=['file'=>$media->file,'musicUrl'=>$message->musicUrl,'hqMusicUrl'=>$message->hqMusicUrl];
                }else{
                    $mediaModel = new Media();
                    $media=$mediaModel::findOne([
                        'mediaId' => $message->mediaId,
                    ]);
                    $messages=$media->file;
                }
                $data=['wid'=>$this->getWechat()->id,'from'=>$this->getWechat()->original,'to'=>$message->toUser,'message'=>$messages,'type'=>$message->msgType,'created_at'=>time()];
                $historyModel = new MessageHistory();
                $historyModel->add($data);
                $this->flash('消息发送成功!', 'success');
            }
        }


        $searchModel = new MessageHistorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        //$dataProvider->query->orderBy('created_at desc');

        $dataProvider->query
            ->wechat($this->getWechat()->id)
            ->wechatFans($this->getWechat()->original,$model->open_id);
        $message->msgType = 'text';//设置radiolist默认值
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

        if ($model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'mpusermodel' => $mpusermodel,
            ]);
        }
    }

    /**
     * 添加粉丝分组
     * @return mixed
     */
    public function actionCreateFansgroup()
    {
        $model = new FansGroups();
        if($this->request->getIsPost()){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $groupArr=$this->wechat->getSdk()->createGroup($this->request->post());//添加分组

            if(is_array($groupArr) && !empty($groupArr)){
                $model->name=$groupArr['name'];
                $model->groupid=$groupArr['id'];
                $model->wid=$this->getWechat()->id;

                if($model->save()){
                    return ['status'=>'1','info'=>'添加成功'];
                }else{
                    return ['status'=>'0','info'=>$this->arrToStr($model->errors)];
                }
            }else{
                return ['status'=>'0','info'=>'添加失败'];
            }
        }else{
            return $this->render('createFansgroup', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 修改粉丝分组
     * @return mixed
     */
    public function actionUpdateFansgroup($id)
    {
        $model = $this->findGroupModel($id);
        if($this->request->getIsPost()){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $group=$this->wechat->getSdk()->updateGroup($this->request->post());//修改分组

            if($group){
                $model->name=$this->request->post('name');
                $model->groupid=$this->request->post('id');
                $model->wid=$this->getWechat()->id;

                if($model->save()){
                    return ['status'=>'1','info'=>'修改成功'];
                }else{
                    return ['status'=>'0','info'=>$this->arrToStr($model->errors)];
                }
            }else{
                return ['status'=>'0','info'=>'修改失败'];
            }
        }else{
            return $this->render('createFansgroup', [
                'model' => $model,
            ]);

        }
    }

    /**
     * 删除粉丝分组
     * @return mixed
     */
    public function actionDeleteFansgroup($id,$groupid)
    {
        $status=$this->wechat->getSdk()->deletGroup($groupid);//删除分组
        if($status){
            $this->findGroupModel($id)->delete();
            return $this->redirect(['fangroup']);
        }else{
            return $this->flash('删除失败或同步后再删除', 'error', ['fangroup']);
        }

    }

    /**
     * 移动粉丝分组
     * @return mixed
     */
    public function actionChangeGroup($openid)
    {
        $model = MpUser::findOne(['open_id' => $openid]);

        if($model->load($this->request->post())){
            if($model->validate()){
                $data=['openid'=>$openid,'to_groupid'=>$model->group_id];
                $status=$this->wechat->getSdk()->updateUserGroup($data);
                if($status){
                    if($model->save()){
                        return $this->flash('移动成功', 'success', ['index']);
                    }else{
                        return $this->flash('分组状态将在同步粉丝后更新', 'error', ['index']);
                    }
                }else{
                    return $this->flash('移动失败', 'error', ['index']);
                }

            }else{
                return $this->flash($this->arrToStr($model->errors), 'error', ['index']);
            }
        }else{
            $fansgoupmodel = FansGroups::find()->andWhere(['wid' => $this->getWechat()->id])->all();

            return $this->render('changeGroup', [
                'model' => $model,
                'fansgoup' => $fansgoupmodel,
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

    /**
     * Finds the FansGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fans the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findGroupModel($id)
    {
        $query = FansGroups::find()
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
