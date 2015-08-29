<?php

namespace app\models;

use Yii;
use yii\mongodb\ActiveRecord;


/**
 * Word model
 *
 * @property string $_id
 * @property string $word
 * @property string $translation
 *
 */


class Word extends ActiveRecord
{

    /**
     * @return string the name of the index associated with this ActiveRecord class.
     */
    public static function collectionName()
    {
        return 'words';
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes()
    {
        return ['_id', 'word', 'translation'];
    }


}
