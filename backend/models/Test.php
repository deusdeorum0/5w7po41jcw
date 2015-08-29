<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\mongodb\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * Test model
 *
 * @property string $_id
 * @property string $name
 * @property string $token
 * @property integer $correct
 * @property integer $mistakes
 * @property object $current_task
 * @property array $tasks
 * @property boolean $finished
 * @property string $devToken
 *
 */


class Test extends ActiveRecord implements IdentityInterface
{

    public function init() {
        parent::init();
        $this->correct = 0;
        $this->mistakes = 0;
        $this->tasks = array();
        $this->finished = false;
    }

    public function generateTask() {
        $task = array();

        $task['reverse'] = rand(0, 1);
        $task['words'] = array();


        for ( $i = 0; $i<4; $i++ ) {

            $rand = lcg_value();

            $modelWord = Word::find();
            if ($i==0) {
                $modelWord->where(['not in', '_id', $this->tasks]);
            } else {
                $modelWord->where(['not in', '_id', $task['words']]);
            }
            $modelWord->andWhere(['rnd' => ['$gte' => $rand]])
                ->orderBy('rnd');
            $modelWord = $modelWord->one();

            if (!$modelWord) {
                $modelWord = Word::find();
                if ($i==0) {
                    $modelWord->where(['not in', '_id', $this->tasks]);
                } else {
                    $modelWord->where(['not in', '_id', $task['words']]);
                }
                $modelWord->andWhere(['rnd' => ['$lt' => $rand]])
                    ->orderBy('rnd');
                $modelWord = $modelWord->one();
            }

            // No more tasks
            if (!$modelWord)
                return 0;

            $task['words'][] = $modelWord->_id;
        }

        $this->current_task = $task;
        $newTasks = $this->tasks;
        $newTasks[] = $this->current_task['words'][0];
        $this->tasks = $newTasks;

        return 1;
    }

    public function getTask() {
        $task = new Task();

        $wordModel = Word::find()
            ->where(['_id' => $this->current_task['words'][0]])
            ->one();

        $reverse = $this->current_task['reverse'];

        if ($reverse) {
            $task->question = $wordModel->word;
            $task->options[] = ['_id' => $wordModel->_id, 'word' => $wordModel->translation];
        } else {
            $task->question = $wordModel->translation;
            $task->options[] = ['_id' => $wordModel->_id, 'word' => $wordModel->word];
        }

        $wordModel = Word::find()
            ->where(['in', '_id', array_slice($this->current_task['words'], 1)])
            ->all();

        foreach ($wordModel as $opt) {
            if ($reverse) {
                $task->options[] = ['_id' => $opt->_id, 'word' => $opt->translation];
            } else {
                $task->options[] = ['_id' => $opt->_id, 'word' => $opt->word];
            }
        }

        shuffle($task->options);

        return $task;
    }

    public function checkAnswer($answerID) {

        $option = Word::find()->
        where(['_id' => $answerID])
            ->one();

        $correct = false;

        if ($option) {
            // Answer is correct
            if ($this->current_task['words'][0] == $option->_id) {
                $correct = true;
                $this->correct = $this->correct + 1;
                // Answer is incorrect
            } else {
                $this->mistakes = $this->mistakes + 1;

                // Adding mistake
                $mistake = new Mistake();
                $mistake->upsert($this->current_task['words'][0], $option->_id, $this->current_task['reverse'], $this->devToken);
            }

            // Mistakes limit achieved
            if ($this->mistakes >= 3) {
                $this->finished = true;
            } else {
                // No more tasks left
                if (!$this->current_task['words']) {
                    $this->finished = true;
                }
            }

            if ((!$this->finished) && ($correct)) {
                // If no more tasks left
                if ($this->generateTask() == 0) {
                    $this->finished = true;
                }
            }

            $this->save();
        }

        $json = array();
        if ($option) {
            $json['result'] = $correct;
        }
        $json['correct'] = $this->correct;
        $json['mistakes'] = $this->mistakes;
        $json['finished'] = $this->finished;

        return $json;
    }

    public function rules()
    {
        return [
            ['name', 'filter', 'filter' => 'trim'],
            ['name', 'required', 'message' => 'Имя пользователя — обязательное поле.'],
            ['name', 'string', 'min' => 2, 'max' => 32, 'tooShort' => 'Имя пользователя не может быть менее 2 символов.',
             'tooLong' => 'Максимальная длина имени пользователя — 32 символа.'],
            ['name', 'match', 'pattern' => '/^[a-zа-я]\w*$/iu', 'message' => 'Имя пользователя не должно начинаться '.
                'с цифры, и должно состоять только из цифр, подчеркивания и букв.'],
        ];
    }

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'tests';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'name', 'token', 'correct', 'mistakes', 'current_task', 'tasks', 'finished', 'devToken'];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['_id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_id->__toString();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"validateAuthKey" is not implemented.');
    }

    public function generateAuthKey()
    {
        $this->token = Yii::$app->security->generateRandomString();
    }


}
