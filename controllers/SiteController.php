<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

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
            $vmlist = null;
            if (Yii::$app->user->identity->vmlist != "" && !is_null(Yii::$app->user->identity->vmlist)) {
                $vmlist = explode(",", Yii::$app->user->identity->vmlist);
            }

            if ($vm != null && $action != null && $vm != "" && $action != "" && isset($vm) && isset($action)) {
                if (in_array($vm, $vmlist)) {
                    $output = '';
                    $status = trim(shell_exec('sudo ' . Yii::$app->params['scriptDir'] . Yii::$app->params['scriptStatus'] . ' ' . $vm));
                    $startCmd = 'echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.last 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now';
                    $stopCmd = 'echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.last 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now';
                    $restartCmd = 'echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStop'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.last 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err && echo sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptStart'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.last 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now';
                    $fsckCmd = 'echo "sudo ' . Yii::$app->params['scriptDir'] . '/' . Yii::$app->params['scriptFsck'] . ' ' . $vm . ' 1> ' . Yii::$app->params['logDir'] . '/' . $vm . '.last 2> ' . Yii::$app->params['logDir'] . '/' . $vm . '.err" | sudo at now';
                    switch ($action) {
                        case 'start':
                            if ($status != '1') {
                                if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
//                                    \Yii::trace($startCmd);
                                    shell_exec($startCmd);
                                    \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Start scheduled');
                                } else {
                                    \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Start already scheduled');
                                }
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : VM already start');
                            }
                            break;
                        case 'stop':
                            if ($status == '1') {
                                if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
//                                    \Yii::trace($stopCmd);
                                    shell_exec($stopCmd);
                                    \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Stop scheduled');
                                } else {
                                    \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Stop already scheduled');
                                }
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : VM already stop');
                            }
                            break;
                        case 'restart':
                            if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
//                                \Yii::trace($restartCmd);
                                shell_exec($restartCmd);
                                \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Reboot scheduled');
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Reboot already scheduled');
                            }
                            break;
                        case 'fsck':
                            if (!file_exists(Yii::$app->params['actionDir'] . $vm . '-' . Yii::$app->params['hosterName'] . '-' . $action)) {
//                                \Yii::trace($fsckCmd);
                                shell_exec($fsckCmd);
                                \Yii::$app->getSession()->setFlash('success', ucfirst($vm) . ' : Check disk scheduled');
                            } else {
                                \Yii::$app->getSession()->setFlash('error', ucfirst($vm) . ' : Check disk already scheduled');
                            }
                            break;
                        default:
                            \Yii::$app->getSession()->setFlash('error', 'Forbidden !');
                    }
                    return $this->redirect(['index']);
                } else {
                    \Yii::$app->getSession()->setFlash('error', 'This is not your VM !');
                }
            }
            return $this->render('index', [
                        'vmlist' => $vmlist,
                        'action' => $action,
            ]);
        }

//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        }
        return $this->redirect(['/login']);
    }

//    public function actionLogin() {
//        if (!\Yii::$app->user->isGuest) {
//            return $this->render('index');
//        }
//
//        $model = new LoginForm();
//        if ($model->load(Yii::$app->request->post()) && $model->login()) {
//            return $this->goBack();
//        }
//        return $this->render('login', [
//                    'model' => $model,
//        ]);
//    }
//
//    public function actionLogout() {
//        Yii::$app->user->logout();
//
//        return $this->goHome();
//    }

    public function actionEthgraph($vm = null) {
        if (!\Yii::$app->user->isGuest && $vm != null) {
            return $this->renderPartial('_ethgraph', [
                        'vm' => $vm,
            ]);
        }
    }

}
