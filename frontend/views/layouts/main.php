<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" ng-app="app">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>SkyEng.Dictionary</title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>

    <div class="container">
        <div class="header clearfix">
            <nav bs-navbar>
                <ul class="nav nav-pills pull-right">
                    <li role="presentation" data-match-route="/$"><a href="/#/">Тестирование</a></li>
                    <li role="presentation" data-match-route="/best"><a href="/#/best">Лучшие результаты</a></li>
                    <li role="presentation" data-match-route="/mistakes"><a href="/#/mistakes">Популярные ошибки</a></li>
                </ul>
            </nav>
            <h3 class="text-muted">SkyEng.Словарь</h3>
        </div>

        <div ng-view></div>

    </div> <!-- /container -->

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
