<?php

namespace app\modules\lab\grids;

use app\modules\site\bussines\UserBussines;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserGrid represents the model behind the Grid form of `app\models\User`.
 */
class OperadorGrid extends UserBussines
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', ], 'integer'],
            [['username', 'email', 'auth_key', 'created_at', 'updated_at', 'password', 'nombres', 'identificacion', 'foto'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with Grid query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function Grid($params)
    {
        $query = UserBussines::find()->alias('u');
        
        $query->innerJoin('auth_assignment','u.id=auth_assignment.user_id');
        
        $query->where(['item_name'=>'operador']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['ilike', 'username', $this->username])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'auth_key', $this->auth_key])
            ->andFilterWhere(['ilike', 'nombres', $this->nombres])
            ->andFilterWhere(['ilike', 'identificacion', $this->identificacion]);

        return $dataProvider;
    }
}
