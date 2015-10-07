<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use kartik\icons\Icon;
use kartik\widgets\AlertBlock;
use app\components\se;

$this->title = 'VMpanel';
?>
<div class="site-index">
    <div class="body-content">
        <?php
        $result = "";
        $nb_vm = count($vmlist);
        if (!is_null($vmlist)) {
            foreach ($vmlist as &$vm) {
                $startStatus = '';
                $stopStatus = '';
                $log = se::getLog($vm);
                $err = se::getErr($vm);
                $imgEth = '';

                $status = se::getStatus($vm);
                if ($status == '1') {
                    $status = '<i class="fa fa-power-off poweron"></i>';
                    $startStatus = ' disabled';
                } else {
                    $status = '<i class="fa fa-power-off poweroff"></i>';
                    $stopStatus = ' disabled';
                }

                $buttons = '<a href="/index.php?vm=' . $vm . '&action=start" class="btn btn-default' . $startStatus . '" title="Start">' . Icon::show('play') . '</a>'
                        . '<a href="/index.php?vm=' . $vm . '&action=stop" class="btn btn-default' . $stopStatus . '" title="Stop">' . Icon::show('stop') . '</a>'
                        . '<a href="/index.php?vm=' . $vm . '&action=fsck" class="btn btn-default' . $startStatus . '" title="Check disk">' . Icon::show('hdd-o') . '</a>';
                // Vérification si une action est en cours
                $nbAction = se::getAction($vm);
                // Une action est en cours
                if (count($nbAction) > 0) {
                    $buttonsMenu = '<div class="row hidden">' . $buttons . '</div>' .
                            ' <div class="progress">
                            <div id="progress-' . $vm . '" class="progress-bar progress-bar-striped active" role="progressbar"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width:100%">
                            </div>
                          </div>';
                    $actionMenu = '<div id="logs' . $vm . '" class="panel-footer"><div class="row"><div class="col-md-12"><label class="control-label">Action in progress</label>' . Html::textarea('ActionResult' . $vm, $log, ['id' => 'ActionResult' . $vm, 'class' => 'form-control', 'rows' => '6']) . '</div></div></div>';
                    $buttonsGraphLog = '<button id = "btnGraph' . $vm . '" type="button" class="btn btn-info hidden" data-toggle="collapse" data-target="#graph' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('area-chart') . '
                            </button>
                            <button id = "btnLogs' . $vm . '" type="button" class="btn btn-default hidden" data-toggle="collapse" data-target="#logs' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('files-o') . '
                            </button>';
                    $this->registerJs('$(document).ready(function(){
                        var textareaLog = document.getElementById("ActionResult' . $vm . '");
                        textareaLog.scrollTop = textareaLog.scrollHeight;
                        intervalID' . $vm . ' = setInterval(function(){
                            if ($("#progress-' . $vm . '").length > 0) {
                                $.pjax.reload({container:"#pjax-' . $vm . '"});
                            } else {
                                clearInterval(intervalID' . $vm . ');
                            }
                        },' . Yii::$app->params['refreshDelay'] . ');
              });', View::POS_END);
                } else {
                    if (se::isLive($vm)) {
                        $hosterName = se::getLive($vm);
                        $buttonsMenu = '<div class="row alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Already running on another hoster : ' . ucfirst($hosterName) . '</div>';
                    } elseif (se::isNet($vm)) {
                        $hosterName = se::getNet($vm);
                        $buttonsMenu = '<div class="row alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Network already running on another hoster : ' . ucfirst($hosterName) . '</div>';
                    } elseif (se::isMount($vm)) {
                        $hosterName = se::getMount($vm);
                        $buttonsMenu = '<div class="row alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Disk already mount on another hoster : ' . ucfirst($hosterName) . '</div>';
                    } else {
                        if ((se::getLive($vm) == Yii::$app->params['hosterName']) && (se::getStatus($vm) != '1')) {
                            $buttonsMenu = '<div class="row">' . $buttons . '</div><div class="row alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error : VM fail to start</div>';
                        } else {
                            $buttonsMenu = '<div class="row">' . $buttons . '</div>';
                        }
                    }
                    $actionMenu = '<div id="logs' . $vm . '" class="panel-footer collapse"><div class="row"><div class="col-md-12"><label class="control-label">Last actions</label>' . Html::textarea('ActionResult' . $vm, $log, ['id' => 'ActionResult' . $vm, 'class' => 'form-control', 'rows' => '6']) . '</div></div>'
                            . '<div class="row"><div class="col-md-12"><label class="control-label">Last errors</label>' . Html::textarea('ActionResultErr' . $vm, $err, ['id' => 'ActionResultErr' . $vm, 'class' => 'form-control', 'rows' => '6']) . '</div></div></div>';
                    $buttonsGraphLog = '<button id = "btnGraph' . $vm . '" type="button" class="btn btn-info" data-toggle="collapse" data-target="#graph' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('area-chart') . '
                            </button>
                            <button id = "btnLogs' . $vm . '" type="button" class="btn btn-default" data-toggle="collapse" data-target="#logs' . $vm . '">
                                <span class="glyphicon glyphicon-collapse-down"></span> ' . Icon::show('files-o') . '
                            </button>';
                }
                $imgEthDaily = 'http://' . Yii::$app->params['hosterName'] . '.x1.fr' . Yii::$app->params['rrdDir'] . 'tap' . substr($vm, 1) . '-daily.png';
                if (se::checkRemoteFile($imgEthDaily)) {
                    $imgEth = '<div class="row">
                        <div class="col-md-12 col-lg-12"><a href="#" id="imgEth' . $vm . '"><img class="img-responsive" src="' . $imgEthDaily . '"></a></div>
                    </div>';
                }
                $result = '
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                                <div class="col-md-12 text-center">
                                    <h4>' . ucfirst($vm) . '</h4> 
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">' . $buttonsGraphLog . '</div>
                            <div class="col-md-4 text-center">' . $buttonsMenu . '</div>
                            <div class="col-md-4 text-right">' . $status . ' </div>
                        </div>
                    </div>
                    <div id="graph' . $vm . '" class="panel-body collapse">
                    ' . $imgEth . '
                    </div>
                    ' . $actionMenu . '
                </div>';
                Pjax::begin(['id' => 'pjax-' . $vm]);
                echo AlertBlock::widget([
                    'type' => AlertBlock::TYPE_ALERT,
                    'useSessionFlash' => true,
                    'delay' => false
                ]);
                echo $result;
                Pjax::end(['id' => 'pjax-' . $vm]);
                Modal::begin([
                    'id' => 'modalGraphEth' . $vm,
                    'size' => Modal::SIZE_LARGE,
                    'header' => '<h3>Bandwidth ' . ucfirst($vm) . '</h3>',
                    'toggleButton' => false,
                ]);
                echo '<div id="modalGraphEthContent' . $vm . '"></div>';
                Modal::end();
                $this->registerJs('$(document).ready(function(){
                $("#graph' . $vm . '").on("hide.bs.collapse", function(){
                    $("#btnGraph' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-down"></span>  ' . Icon::show('area-chart') . '\');
                });
                $("#graph' . $vm . '").on("show.bs.collapse", function(){
                    $("#btnGraph' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-up"></span>  ' . Icon::show('area-chart') . '\');
                });
                
                $("#logs' . $vm . '").on("hide.bs.collapse", function(){
                    $("#btnLogs' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-down"></span>  ' . Icon::show('files-o') . '\');
                });
                $("#logs' . $vm . '").on("show.bs.collapse", function(){
                    $("#btnLogs' . $vm . '").html(\'<span class="glyphicon glyphicon-collapse-up"></span>  ' . Icon::show('files-o') . '\');
                });
                $( "#imgEth' . $vm . '" ).click(function() {
                    $("#modalGraphEth' . $vm . '").modal("show")
                    .find("#modalGraphEthContent' . $vm . '")
                    .load("/site/ethgraph?vm=' . $vm . '");
                });
                var psconsole = $("#ActionResult' . $vm . '");
                if(psconsole.length) {
                    psconsole.scrollTop(psconsole[0].scrollHeight - psconsole.height());
                }
              });', View::POS_END);
            }
        } else {
            echo "<h3>Sorry, you don't have any VM yet.</h3>";
        }
        ?>
    </div>
</div>
