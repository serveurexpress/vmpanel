<?php
/* @var $this yii\web\View */

$this->title = 'VMpanel';
?>
<div class="site-index">
    <div class="body-content">
        <?php
        foreach ($vmlist as &$vm) {
            echo $vm;
        }
        ?>
    </div>
</div>
