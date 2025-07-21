<?php

namespace app\modules\lab\grids;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Laboratorista;

/**
 * LaboratoristaGrid represents the model behind the Grid form of `app\models\Laboratorista`.
 */
class LaboratoristaGrid extends Laboratorista
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'dbremove'], 'integer'],
            [['nombres', 'cargo', 'registro_msp', 'registro_senescyt'], 'safe'],
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
        $query = Laboratorista::find();
        $query->where(['dbremove' => false ]);

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
            'dbremove' => $this->dbremove,
        ]);

        $query->andFilterWhere(['like', 'nombres', $this->nombres])
            ->andFilterWhere(['like', 'cargo', $this->cargo])
            ->andFilterWhere(['like', 'registro_msp', $this->registro_msp])
            ->andFilterWhere(['like', 'registro_senescyt', $this->registro_senescyt]);

        return $dataProvider;
    }
}
