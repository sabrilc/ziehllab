<?php

namespace app\modules\lab\grids;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserGrid represents the model behind the Grid form of `app\models\User`.
 */
class AdminGrid extends User
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
        $query = User::find();
        
        $query->innerJoin('auth_assignment','user.id=auth_assignment.user_id');
        
        $query->where(['item_name'=>'administrador']);

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

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'nombres', $this->nombres])           
            ->andFilterWhere(['like', 'identificacion', $this->identificacion]);

        return $dataProvider;
    }
}
