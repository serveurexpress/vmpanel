<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($vm = null, $action = null) {
        if (!\Yii::$app->user->isGuest) {
            $vmlist = Yii::$app->user->identity->vmList;

            if ($vm != null && $action != null && $vm != "" && $action != "" && isset($vm) && isset($action)) {
                if (in_array($vm, $vmlist)) {
                    $output = '';
                    switch ($action) {
                        case 'start':
                            $output = shell_exec('sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm);
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : <br />' . nl2br($output));
                            break;
                        case 'stop':
                            $output = shell_exec('sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm);
                            \Yii::trace('sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm);
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : <br />' . nl2br($output));
                            break;
                        case 'restart':
                            $output = shell_exec('sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm);
                            $output .= shell_exec('sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm);
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : <br />' . nl2br($output));
                            break;
                        case 'fsck':
                            $output = shell_exec('sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptFsck'] . ' ' . $vm);
                            \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : <br />' . nl2br($output));
                            break;
                        default:
                            \Yii::$app->getSession()->setFlash('error', 'Action interdite !');
                    }
                } else {
                    \Yii::$app->getSession()->setFlash('error', 'Cette VM n\'est pas à vous !');
                }
                return $this->redirect(['index'], [
                            'vmlist' => $vmlist,
                ]);
            }
            return $this->render('index', [
                        'vmlist' => $vmlist,
            ]);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->render('index');
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
                    'model' => $model,
        ]);
    }

    public function actionLogout() {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
