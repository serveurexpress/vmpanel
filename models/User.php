<?php

namespace app\models;

class User extends \dektrium\user\models\User
{
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        // add field to scenarios
        $scenarios['create'][]   = 'vmlist';
        $scenarios['update'][]   = 'vmlist';
        $scenarios['register'][] = 'vmlist';
        return $scenarios;
    }

    public function rules()
    {
        $rules = parent::rules();
        // add some rules
        $rules['vmlistRequired'] = ['vmlist', 'required'];
        $rules['vmlistLength']   = ['vmlist', 'string', 'max' => 255];

        return $rules;
    }
}