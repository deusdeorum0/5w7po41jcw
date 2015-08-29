<?php
namespace backend\controllers;

use app\models\Test;
use Yii;
use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\ContentNegotiator;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

/**
 * Test controller
 */
class TestController extends Controller
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
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'results' => ['get'],
                    'login' => ['post'],
                    'task' => ['get'],
                    'answer' => ['post']
                ],
            ],
            [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'only' => ['task', 'answer'],
                //'only' => [''],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['task', 'answer'],
                'rules' => [
                    [
                        'actions' => ['task', 'answer'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]

        ]);
    }

    public function actionLogin()
    {
        $devToken = Yii::$app->request->post('dev_token');
        if (!in_array($devToken, self::$devTokens)) {
            throw new \yii\web\ForbiddenHttpException;
        }

        $model = new Test();
        $model->name = Yii::$app->request->post('name');
        $model->devToken = $devToken;

        if ($model->validate()) {
            // generating token
            $model->generateAuthKey();
            $model->generateTask();
            $model->save();

            Yii::$app->user->login($model);

            return ['token' => Yii::$app->user->identity->getAuthKey()];
        }
        else {
            return $model->errors;
        }
    }

    public function actionTask()
    {

        $devToken = Yii::$app->request->get('dev_token');
        if (!in_array($devToken, self::$devTokens) || Yii::$app->user->identity->devToken != $devToken) {
          throw new \yii\web\ForbiddenHttpException;
        }

        $testModel = Yii::$app->user->identity;
        if (!$testModel->finished) {
          return $testModel->getTask();
        }
        else {
          throw new \yii\web\BadRequestHttpException;
        }

    }


    public function actionAnswer()
    {

      $devToken = Yii::$app->request->post('dev_token');
      if (!in_array($devToken, self::$devTokens) || Yii::$app->user->identity->devToken != $devToken) {
        throw new \yii\web\ForbiddenHttpException;
      }

        $testModel = Yii::$app->user->identity;

        if ($testModel->finished) {
          throw new \yii\web\BadRequestHttpException;
        }

        $json = $testModel->checkAnswer(Yii::$app->request->post('_id'));

        return $json;

    }

    public function actionResults()
    {

        $devToken = Yii::$app->request->get('dev_token');
        if (!in_array($devToken, self::$devTokens)) {
          throw new \yii\web\ForbiddenHttpException;
        }

        $results = Test::find()
            ->select(['_id' => false, 'tasks' => false, 'token' => false, 'devToken' => false])
            ->where(['correct' => ['$gt' => 0]])
            ->andWhere(['devToken' => $devToken])
            ->orderBy(['correct' => SORT_DESC])
            ->limit(10)
            ->all();

        return $results;
    }
}
