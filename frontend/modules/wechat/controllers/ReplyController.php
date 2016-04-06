<?php

namespace modules\wechat\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use modules\wechat\models\ReplyRule;
use modules\wechat\models\ReplyRuleSearch;
use modules\wechat\models\ReplyRuleKeyword;
use yii\web\UploadedFile;

/**
 * 模块回复规则控制
 * @package modules\wechat\controllers
 */
class ReplyController extends AdminController
{
    /**
     * 扩展模块回复列表
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReplyRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([
            'wid' => $this->getWechat()->id, // 公众号过滤
            'module' => 'text',
        ]);
        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['editUrl']='update';//更新按钮链接
        Yii::$app->params['delUrl']='delete';//删除按钮链接
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => '文字回复规则',//当前标题
            'addTitle' => '添加文字回复规则',//添加按钮标题
            'addUrl' => 'create',//添加按钮链接
        ]);
    }

    /**
     * 添加文字回复
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReplyRule();
        $ruleKeyword = new ReplyRuleKeyword();

        $ruleKeywords = [];
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'text_instert');

            if ($info['status']=='success') {
                return $this->flash('添加成功!', $info['status'], ['update', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }

        }
        Yii::$app->params['type']='';
        return $this->render('create', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '文字回复',//当前位置
            'title' => '添加文字回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'index',//面包屑跳转的页面
        ]);
    }

    /**
     * 修改文字回复
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $ruleKeyword = new ReplyRuleKeyword();

        $ruleKeywords = $model->keywords;
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'text_instert');
            if ($info['status']=='success') {
                return $this->flash('修改成功!', $info['status'], ['update', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }
        Yii::$app->params['type']='';
        return $this->render('update', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '文字回复',//当前位置
            'title' => '修改文字回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'index',//面包屑跳转的页面
        ]);
    }

    /**
     * 删除文字回复
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if(ReplyRule::deleteAll(['id'=>$id]) && ReplyRuleKeyword::deleteAll(['rid'=>$id])){
            return $this->redirect(['index']);
        }
    }

    /**
     * 图文回复列表
     * @param integer $id
     * @return mixed
     */
    public function actionNews()
    {
        $searchModel = new ReplyRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([
            'wid' => $this->getWechat()->id, // 公众号过滤
            'module' => 'news',
        ]);
        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['editUrl']='update-news';//更新按钮链接
        Yii::$app->params['delUrl']='delete-news';//删除按钮链接
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => '图文回复规则',//当前标题
            'addTitle' => '添加图文回复规则',//添加按钮标题
            'addUrl' => 'create-news',//添加按钮链接
        ]);
    }

    /**
     * 添加图文回复
     * @return mixed
     */
    public function actionCreateNews()
    {
        $model = new ReplyRule();
        $ruleKeyword = new ReplyRuleKeyword();
        $ruleKeyword->setScenario('news');
        $ruleKeywords = [];
        if ($model->load(Yii::$app->request->post())) {//验证post数据
            $model->wid = $this->getWechat()->id;

            $files = UploadedFile::getInstance($ruleKeyword,'thumbs');

            if($files){
                //图片上传
                $saveFile='uploads/thumbs/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $thumbs=$this->uploadTo($files,$saveFile);

                if($thumbs['status']==0){
                    return $this->flash($thumbs['info'], 'error', ['update-news', 'id' => $model->id]);
                }else{
                    $ruleKeyword->thumbs=$thumbs['info'];
                }
            }

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'news_instert');
            if ($info['status']=='success') {
                return $this->flash('添加成功!', $info['status'], ['update-news', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }
        Yii::$app->params['type']='News';
        return $this->render('create', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '图文回复',//当前位置
            'title' => '添加图文回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'news',//面包屑跳转的页面
        ]);
    }

    /**
     * 修改图文回复
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateNews($id)
    {
        $model = $this->findModel($id);

        $ruleKeyword = new ReplyRuleKeyword();
        $ruleKeywords = $model->keywords;
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $files = UploadedFile::getInstance($ruleKeyword,'thumbs');

            if($files){
                //图片上传
                $saveFile='uploads/thumbs/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $thumbs=$this->uploadTo($files,$saveFile);

                if($thumbs['status']==0){
                    return $this->flash($thumbs['info'], 'error', ['update-news', 'id' => $model->id]);
                }else{
                    $ruleKeyword->thumbs=$thumbs['info'];
                }
            }

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'news_instert');

            if ($info['status']=='success') {
                return $this->flash('修改成功!', $info['status'], ['update-news', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }
        Yii::$app->params['type']='News';
        return $this->render('update', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '图文回复',//当前位置
            'title' => '修改图文回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'news',//面包屑跳转的页面
        ]);
    }

    /**
     * 删除图文回复
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteNews($id)
    {
        $replyRuleKeyword = ReplyRuleKeyword::findOne(['rid'=>$id]);
        @unlink($replyRuleKeyword->thumbs);

        if(ReplyRule::deleteAll(['id'=>$id]) && $replyRuleKeyword->delete()){
            return $this->redirect(['news']);
        }
    }

    /**
     * 音乐回复列表
     * @param integer $id
     * @return mixed
     */
    public function actionMusic()
    {
        $searchModel = new ReplyRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([
            'wid' => $this->getWechat()->id, // 公众号过滤
            'module' => 'music',
        ]);
        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['editUrl']='update-music';//更新按钮链接
        Yii::$app->params['delUrl']='delete-music';//删除按钮链接
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => '音乐回复规则',//当前标题
            'addTitle' => '添加音乐回复规则',//添加按钮标题
            'addUrl' => 'create-music',//添加按钮链接
        ]);
    }

    /**
     * 添加音乐回复
     * @return mixed
     */
    public function actionCreateMusic()
    {
        $model = new ReplyRule();
        $ruleKeyword = new ReplyRuleKeyword();

        $ruleKeywords = [];
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $musicFiles = UploadedFile::getInstance($ruleKeyword,'music');
            if($musicFiles){
                //音乐上传
                $saveFile='uploads/music/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $music=$this->uploadTo($musicFiles,$saveFile);

                if($music['status']==0){
                    return $this->flash($music['info'], 'error', ['update-music', 'id' => $model->id]);
                }else{
                    $ruleKeyword->music=$music['info'];
                }
            }

            $HQmusicFiles = UploadedFile::getInstance($ruleKeyword,'HQMusic');
            if($HQmusicFiles){
                //高质量音乐上传
                $saveFile='uploads/HQmusic/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $HQMusic=$this->uploadTo($HQmusicFiles,$saveFile);

                if($HQMusic['status']==0){
                    return $this->flash($HQMusic['info'], 'error', ['update-music', 'id' => $model->id]);
                }else{
                    $ruleKeyword->HQMusic=$HQMusic['info'];
                }
            }

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'music_instert');
            if ($info['status']=='success') {
                return $this->flash('添加成功!', $info['status'], ['update-music', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }

        Yii::$app->params['type']='Music';
        return $this->render('create', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '音乐回复',//当前位置
            'title' => '添加音乐回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'music',//面包屑跳转的页面
        ]);
    }

    /**
     * 修改音乐回复
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateMusic($id)
    {
        $model = $this->findModel($id);

        $ruleKeyword = new ReplyRuleKeyword();
        $ruleKeywords = $model->keywords;
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $musicFiles = UploadedFile::getInstance($ruleKeyword,'music');
            if($musicFiles){
                //音乐上传
                $saveFile='uploads/music/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $music=$this->uploadTo($musicFiles,$saveFile);

                if($music['status']==0){
                    return $this->flash($music['info'], 'error', ['update-music', 'id' => $model->id]);
                }else{
                    $ruleKeyword->music=$music['info'];
                }
            }

            $HQmusicFiles = UploadedFile::getInstance($ruleKeyword,'HQMusic');
            if($HQmusicFiles){
                //高质量音乐上传
                $saveFile='uploads/HQmusic/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $HQMusic=$this->uploadTo($HQmusicFiles,$saveFile);

                if($HQMusic['status']==0){
                    return $this->flash($HQMusic['info'], 'error', ['update-music', 'id' => $model->id]);
                }else{
                    $ruleKeyword->HQMusic=$HQMusic['info'];
                }
            }
            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'music_instert');
            if ($info['status']=='success') {
                return $this->flash('修改成功!', $info['status'], ['update-music', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }

        Yii::$app->params['type']='Music';

        return $this->render('update', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '音乐回复',//当前位置
            'title' => '修改音乐回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'music',//面包屑跳转的页面
        ]);
    }

    /**
     * 删除音乐回复
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteMusic($id)
    {
        if(Yii::$app->getRequest()->getIsAjax()){
            $type=$_GET['type'];
            $ruleKeyword = ReplyRuleKeyword::findOne($id);
            $ruleKeyword->setScenario('music_delete');//删除场景
            $musicUrl=$ruleKeyword->$type;

            $ruleKeyword->$type = '';
            if($ruleKeyword->save()){
                @unlink($musicUrl);
                $result=['status'=>1,'info'=>'删除成功'];
                return  json_encode($result);
            }else{
                $result=['status'=>0,'info'=>'删除失败'];
                return  json_encode($result);
            }
        }else{
            $rule = ReplyRule::findOne(['id'=>$id]);
            $replyRuleKeyword = ReplyRuleKeyword::findOne(['rid'=>$id]);
            $musicUrl=$replyRuleKeyword->music;
            $HQMusicUrl=$replyRuleKeyword->HQMusic;

            if($rule->delete() && $replyRuleKeyword->delete()){
                @unlink($musicUrl);
                @unlink($HQMusicUrl);
                return $this->redirect(['music']);
            }
        }

    }

    /**
     * 图片回复列表
     * @param integer $id
     * @return mixed
     */
    public function actionImages()
    {
        $searchModel = new ReplyRuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere([
            'wid' => $this->getWechat()->id, // 公众号过滤
            'module' => 'images',
        ]);
        $dataProvider->query->orderBy('id desc');

        Yii::$app->params['editUrl']='update-images';//更新按钮链接
        Yii::$app->params['delUrl']='delete-images';//删除按钮链接
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'title' => '图片回复规则',//当前标题
            'addTitle' => '添加图片回复规则',//添加按钮标题
            'addUrl' => 'create-images',//添加按钮链接
        ]);
    }

    /**
     * 添加图片回复
     * @return mixed
     */
    public function actionCreateImages()
    {
        $model = new ReplyRule();
        $ruleKeyword = new ReplyRuleKeyword();
        $ruleKeyword->setScenario('Images');
        $ruleKeywords = [];
        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $files = UploadedFile::getInstance($ruleKeyword,'images');

            if($files){
                //图片上传
                $saveFile='uploads/images/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $images=$this->uploadTo($files,$saveFile);

                if($images['status']==0){
                    return $this->flash($images['info'], 'error', ['update-images', 'id' => $model->id]);
                }else{
                    $ruleKeyword->images=$images['info'];
                }
            }

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'images_instert');
            if ($info['status']=='success') {
                return $this->flash('添加成功!', $info['status'], ['update-images', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }
        Yii::$app->params['type']='Images';
        return $this->render('create', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '图片回复',//当前位置
            'title' => '添加图片回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'images',//面包屑跳转的页面
        ]);
    }

    /**
     * 修改图片回复
     * @param integer $id
     * @return mixed
     */
    public function actionUpdateImages($id)
    {
        $model = $this->findModel($id);

        $ruleKeyword = new ReplyRuleKeyword();
        $ruleKeywords = $model->keywords;

        if ($model->load(Yii::$app->request->post())) {
            $model->wid = $this->getWechat()->id;

            $files = UploadedFile::getInstance($ruleKeyword,'images');

            if($files){
                //图片上传
                $saveFile='uploads/images/'.$model->wid.'/'.date('Ymd',time());//保存路径
                $images=$this->uploadTo($files,$saveFile);

                if($images['status']==0){
                    return $this->flash($images['info'], 'error', ['update-images', 'id' => $model->id]);
                }else{
                    $ruleKeyword->images=$images['info'];
                }
            }

            $info=$this->save($model, $ruleKeyword, $ruleKeywords,'images_instert');
            if ($info['status']=='success') {
                return $this->flash('修改成功!', $info['status'], ['update-images', 'id' => $model->id]);
            }else{
                return $this->flash($info['info'], $info['status']);
            }
        }
        Yii::$app->params['type']='Images';
        return $this->render('update', [
            'model' => $model,
            'ruleKeyword' => $ruleKeyword,
            'ruleKeywords' => $ruleKeywords,
            'label' => '图片回复',//当前位置
            'title' => '修改图片回复规则',//当前标题
            'formDisplay' => '_form',//跳转的视图页
            'url' => 'images',//面包屑跳转的页面
        ]);
    }

    /**
     * 删除图片回复
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteImages($id)
    {
        $rule = ReplyRule::findOne(['id'=>$id]);
        $replyRuleKeyword = ReplyRuleKeyword::findOne(['rid'=>$id]);
        $imagesUrl=$replyRuleKeyword->images;

        if($rule->delete() && $replyRuleKeyword->delete()){
            @unlink($imagesUrl);
            return $this->redirect(['images']);
        }
    }

    /**
     * 保存内容
     * @param $rule
     * @param $keyword
     * @param array $keywords
     * @return bool
     */
    protected function save($rule, $keyword, $keywords = [],$scene='')
    {
        if (!$rule->save()) {
            return false;
        }
        //ArrayHelper::index()重建数组索引
        $_keywords = ArrayHelper::index($keywords, 'id');
        $keywords = [];
        $valid = true;

        $data = Yii::$app->request->post($keyword->formName());

        //图片
        if(!empty($keyword->thumbs)){
            $data['thumbs']=$keyword->thumbs;
        }
        //图片
        if(!empty($keyword->images)){
            $data['images']=$keyword->images;
        }
        //高质量音乐
        if(!empty($keyword->HQMusic)){
            $data['HQMusic']=$keyword->HQMusic;
        }
        //音乐
        if(!empty($keyword->music)){
            $data['music']=$keyword->music;
        }

        $data['start_at']=strtotime($data['start_at']);
        $data['end_at']=strtotime($data['end_at']);
        if($data['end_at']<$data['start_at']){
            return $this->flash('结束时间不能少于开始日期!', 'error', ['update', 'id' => $rule->id]);
        }

        if (!empty($data['id']) && $_keywords[$data['id']]) {
            $_keyword = $_keywords[$data['id']];
            unset($_keywords[$data['id']]);
        } else {
            $_keyword = clone $keyword;
        }

        unset($data['id']);
        $keywords[] = $_keyword;

        //启用场景
        if($scene){
            $_keyword->setScenario($scene);
        }

        $_keyword->setAttributes(array_merge($data, [
            'rid' => $rule->id
        ]));
        $valid = $valid && $_keyword->save();

        //验证错误则删除
       if($_keyword->hasErrors()){
           ReplyRule::deleteAll(['id' => $rule->id]);
       }

        !empty($_keywords) && ReplyRuleKeyword::deleteAll(['id' => array_keys($_keywords)]); // 无更新的则删除

        if($valid){
            return ['status'=>'success','info'=>$valid];
        }else{
            return ['status'=>'error','info'=>$this->arrToStr($_keyword->errors)];
        }

    }

    /**
     * Finds the ReplyRule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReplyRule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReplyRule::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


}
