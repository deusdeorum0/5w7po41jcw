<?php
namespace backend\controllers;

use app\models\Mistake;
use Yii;
use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;

/**
 * Mistake controller
 */
class MistakeController extends Controller
{

    private static $devTokens = [
      '2752466fafb3f9ac',
      'a74ea80e0ac0f2de',
      '197d806249f2ccb3',
      '03e0bec5986db246',
      'd3145ed54b2593bc',
      'e5a6d8631cd1ae57',
      'd96b4356cf13a896',
      'ee6c790b2b17987c',
      '7fc6accd9abca076',
      'f9d4ae9ea1cc1da7',
      '49a225613f11f0c8',
      '45f45a600524c8a2',
      '00a979c8719f99c4',
      '1b393fd192ec9896',
      'cc7cb35208b54e2f',
      '933abb7a094a8587',
      '9ab1d50bf7ab035e',
      '6fa3d44eb3f8e7e0',
      'f37a3a7bf881df9a',
      '1259c945d53dcdc7',
      '87eefce550cf3a00',
      '564a46e88e52ed08',
      '68e56be80374cb71',
      '7a21ff8d485c23b1',
      'dc2d27cc5378b667',
      '6360e94291569a9c',
      '0a8b66252f193f5e',
      '0e5535c118f7830e',
      '4b618391b82fe121',
      '1d1f8c583a4bc8c3'
    ];

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                ],
            ],
            [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }


    public function actionIndex()
    {
        $devToken = Yii::$app->request->get('dev_token');
        if (!in_array($devToken, self::$devTokens)) {
          throw new \yii\web\ForbiddenHttpException;
        }

        $mistakes = Mistake::find()
            ->select(['_id' => false, 'devToken' => false])
            ->orderBy(['count' => SORT_DESC])
            ->where(['devToken' => $devToken])
            ->limit(10)
            ->all();

        return $mistakes;
    }
}
