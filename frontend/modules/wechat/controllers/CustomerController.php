<?php
namespace modules\wechat\controllers;

use yii;
use yii\web\NotFoundHttpException;
use modules\wechat\models\Customer;
use modules\wechat\models\CustomerSearch;;

class CustomerController extends AdminController
{
    /**
     * Lists all Customer models.
     * @return mixed
     */
    public function actionIndex()
    {
        $customerModel = new CustomerSearch();
        $dataProvider = $customerModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);

        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['inviteUrl']='invite';//绑定微信按钮链接
        Yii::$app->params['editUrl']='update';//更新按钮链接
        Yii::$app->params['delUrl']='delete';//删除按钮链接

        return $this->render('index', [
            'customerModel' => $customerModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 更新客服分组
     * @return mixed
     */
    public function actionCheckCustomer()
    {
        $customer = Yii::createObject(Customer::className());
        $customService=$this->wechat->getSdk()->getCustomService();
        $customList = $customService->getAccountList();//获取所有客服分组
        //print_r($customList);exit;

        if (is_array($customList)) {
            $customer->deleteCustomer($this->getWechat());
            foreach($customList as $v){
                $customer->updateCustomer($this->getWechat(),$v);
            }
        }

        echo json_encode(['status'=>1,'info'=>'同步成功']);

    }

    /**
     * Creates a new Customer model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Customer();
        if ($model->load(Yii::$app->request->post())) {//验证post数据
            $customService=$this->wechat->getSdk()->getCustomService();
            $accountArr=['kf_account'=>$model->kf_account,'nickname'=>$model->kf_nick];
            $account=$customService->addAccount($accountArr);

            $model->wid=$this->getWechat()->id;
            if($account){
                if($model->save()){
                    return $this->flash('添加客服成功', 'success');
                }else{
                    return $this->flash('添加客服失败,'.$this->arrToStr($model->errors), 'error');
                }
            }else{
                return $this->flash('添加客服失败', 'error');
            }
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Customer model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->getIsPost()) {
            $customService=$this->wechat->getSdk()->getCustomService();
            $model->load(Yii::$app->request->post());

            $accountArr=['kf_account'=>$model->kf_account,'nickname'=>$model->kf_nick];

            $pos=strpos($model->kf_headimgurl,'http');//判断头像是否来自微信官方
            if($pos === false){
                $account=$customService->updateAccount($accountArr);
                $accountAvatar=$customService->setAccountAvatar($model->kf_account, $model->kf_headimgurl);
                if($account && $accountAvatar){
                    if($model->save()){
                        return $this->flash('修改客服成功', 'success');
                    }else{
                        return $this->flash('修改客服失败,'.$this->arrToStr($customer->errors), 'error');
                    }
                }else{
                    return $this->flash('修改客服失败', 'error');
                }
            }else{
                $account=$customService->updateAccount($accountArr);
                if($account){
                    if($model->save()){
                        return $this->flash('修改客服成功', 'success');
                    }else{
                        return $this->flash('修改客服失败,'.$this->arrToStr($model->errors), 'error');
                    }
                }else{
                    return $this->flash('修改客服失败', 'error');
                }
            }
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    public function actionInvite($id){

        $model = $this->findModel($id);

        if($model->load(Yii::$app->request->post())){
            if($model->validate()){

                $customService=$this->wechat->getSdk()->getCustomService();
                $accountArr=['kf_account'=>$model->kf_account,'invite_wx'=>$model->kf_wx];
                $status=$customService->inviteWorker($accountArr);
                if($status){
                    $model->invite_wx=$model->kf_wx;
                    unset($model->kf_wx);
                    if($model->save()){
                        $this->actionCheckCustomer();
                        return $this->flash('申请成功，请在微信客户端确认', 'success', ['index']);
                    }
                }else{
                    return $this->flash('绑定失败', 'error', ['index']);
                }

            }else{
                return $this->flash($this->arrToStr($model->errors), 'error', ['index']);
            }
        }else{
            return $this->render('invite', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing Customer model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $customService=$this->wechat->getSdk()->getCustomService();
        $status=$customService->deleteAccount($model->kf_account);
        if($status && $model->delete()){
            return $this->flash('删除成功', 'success', ['index']);
        }else{
            return $this->flash('删除失败', 'error', ['index']);
        }
    }

    /**
     * Finds the Customer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Customer::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}