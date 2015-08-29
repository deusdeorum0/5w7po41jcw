<?php
/**
 * Created by PhpStorm.
 * User: deusdeorum
 * Date: 10.05.15
 * Time: 22:28
 */

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

class AngularAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $js = [
        'angular/angular.js',
        'angular-route/angular-route.js',
        'angular-strap/dist/angular-strap.js',
        'angular-loading-bar/build/loading-bar.js',
        'angular-animate/angular-animate.js'
    ];
    public $css = [
        'angular-loading-bar/build/loading-bar.css',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}