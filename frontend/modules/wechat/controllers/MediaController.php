<?php
namespace modules\wechat\controllers;

use yii;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use modules\wechat\models\Media;
use modules\wechat\models\Message;
use modules\wechat\models\MediaForm;
use modules\wechat\models\MediaSearch;
use modules\wechat\models\MediaNewsForm;
use yii\data\Pagination;

class MediaController extends AdminController
{
    /**
     * 选择文件(ajax触发)
     * @return string
     */
    public function actionPick()
    {

        if(Yii::$app->getRequest()->getIsGet()){
            $data = Media::find()->andWhere(['wid' => $this->getWechat()->id,'type'=>Yii::$app->request->queryParams['type']]);
            $pages = new Pagination(['totalCount' =>$data->count(), 'pageSize' => '10']);
            $model =$data->orderBy('id desc')->offset($pages->offset)->limit($pages->limit)->all();
            return $this->render('pick',[
                'model' =>$model,
                'pages' =>$pages,
            ]);
        }else{
            $post=Yii::$app->request->post();
            preg_match_all('/(\?|&)(.+?)=([^&?]*)/i', $post['page'],$pageArr);
            $totalCount=$pageArr[3][2];//总页数
            $thisPage=$pageArr[3][1];//当前页数
            $thistype=$pageArr[3][0];//类型

            //组装ajax分页链接参数,因为正则的原因所以顺序不能改变
            $_GET['type']=$thistype;
            $_GET['page']=$thisPage;
            $_GET['per-page']=$totalCount;

            $data = Media::find()->orderBy('id desc')->andWhere(['wid' => $this->getWechat()->id,'type'=>$thistype]);
            $pages = new Pagination(['totalCount' =>$data->count(), 'pageSize' => '10']);
            $model =$data->orderBy('id')->offset($pages->offset)->limit($pages->limit)->all();
            return $this->renderAjax('pick',[
                'model' =>$model,
                'pages' =>$pages,
            ]);
        }

    }



    /**
     * 上传图片接口
     * @throws NotFoundHttpException
     */
    public function actionUpload()
    {
        $model = new MediaForm($this->getWechat());
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->upload()) {
                return $this->message('上传成功', 'success');
            }
        }
//        $files = static::getFiles();
//        if (!empty($files)) {
//            if (($mediaType = Yii::$app->request->post('mediaType')) === null) {
//                return $this->message('错误的媒体素材类型!', 'error');
//            }
//            foreach ($files as $name) {
//                $_model = clone $model;
//                $_model->setAttributes([
//                    'type' => $mediaType,
//                    'material' => ''
//                ]);
//                $uploadedFile = UploadedFile::getInstanceByName($name);
//                $result = $this->getWechat()->getSdk()->uploadMedia($uploadedFile->tempName, $mediaType);
//            }
//        }
        return $this->render('upload', [
            'model' => $model
        ]);
    }

    /**
     * Lists all Media models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['wid' => $this->getWechat()->id]);

        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['viewUrl']='view';//更新按钮链接
        Yii::$app->params['editUrl']='update';//更新按钮链接
        Yii::$app->params['delUrl']='delete';//删除按钮链接
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Media model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$mediaid)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Media model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $media = new MediaForm($this->getWechat());
        $news = new MediaNewsForm();
        $request = Yii::$app->request;
        if ($request->getIsPost()) {
            $post = $request->post();
            switch ($request->post('mediaType')) {
                case Media::TYPE_MEDIA://媒体素材
                    $media->load($post);
                    $media->file = UploadedFile::getInstance($media, 'file');
                    
                    if ($media->save()) {
                        return $this->message('操作成功!', 'success');
                    }
                    break;
                case Media::TYPE_NEWS://图文素材
                    $news->load($post);
                    if ($news->save()) {

                    }
                    break;
                default:
                    throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
        return $this->render('create', [
            'media' => $media,
            'news' => $news
        ]);
    }

    /**
     * Updates an existing Media model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $news = new MediaNewsForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'media' => $model,
                'news' => $news
            ]);
        }
    }

    /**
     * Deletes an existing Media model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$mediaid)
    {
        if($mediaid){
            $this->wechat->getSdk()->deleteMaterial($mediaid);//删除永久素材
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Media model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Media the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}