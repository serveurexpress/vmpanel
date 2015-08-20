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
                    $status = trim(shell_exec('sudo ' . Yii::$app->params['scriptDir'] . Yii::$app->params['scriptStatus'] . ' ' . $vm));
                    switch ($action) {
                        case 'start':
                            if ($status != '1') {
                                if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
                                    shell_exec('echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.log 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now');
                                    \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Démarrage de la VM en cours');
                                } else {
                                    \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Démarrage de la VM déja en cours');
                                }
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : VM déja démarrée');
                            }
                            break;
                        case 'stop':
                            if ($status == '1') {
                                if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
                                    shell_exec('echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.log 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now');
                                    \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Arret de la VM en cours');
                                } else {
                                    \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Arret de la VM déja en cours');
                                }
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : VM déja arrêtée');
                            }
                            break;
                        case 'restart':
                            if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
                                shell_exec('echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.log 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err && echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.log 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now');
                                \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Redémarrage de la VM en cours');
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Redémarrage de la VM déja en cours');
                            }
                            break;
                        case 'fsck':
                            if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
                                shell_exec('echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptFsck'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.log 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now');
                                \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Fsck de la VM en cours');
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Fsck de la VM déja en cours');
                            }
                            break;
                        default:
                            \Yii::$app->getSession()->setFlash('error', 'Action interdite !');
                    }
                } else {
                    \Yii::$app->getSession()->setFlash('error', 'Cette VM n\'est pas à vous !');
                }
            }
            return $this->render('index', [
                        'vmlist' => $vmlist,
                        'action' => $action,
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
