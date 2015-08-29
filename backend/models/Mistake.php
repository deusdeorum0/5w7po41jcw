<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;


/**
 * Mistake model
 *
 * @property string $_id
 * @property string $word
 * @property string $option
 * @property integer $count
 * @property string $devToken
 *
 */


class Mistake extends ActiveRecord
{

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'mistakes';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'word', 'option', 'count', 'devToken'];
    }


    public function upsert($wordID, $optionID, $reverse, $devToken)
    {
        $wordModelTask = Word::find()
            ->where(['_id' => $wordID])
            ->one();
        $wordModelOption = Word::find()
            ->where(['_id' => $optionID])
            ->one();

        if (($wordModelTask) && ($wordModelOption)) {
            if ($reverse) {
                $word = $wordModelTask->word;
                $option = $wordModelOption->translation;
            } else {
                $word = $wordModelTask->translation;
                $option = $wordModelOption->word;
            }
            $mistake = Mistake::find()
                ->where(['word' => $word])
                ->where(['option' => $option])
                ->where(['devToken' => $devToken])
                ->one();
            if ($mistake) {
                $mistake->count = $mistake->count + 1;
                $mistake->save();
            } else {
                $this->word = $word;
                $this->option = $option;
                $this->devToken = $devToken;
                $this->count = 1;
                $this->save();
            }
            return 1;
        }
        else {
            return 0;
        }
    }


}
