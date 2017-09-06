<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "campaign".
 *
 * @property string $CampaingId
 * @property string $Year
 * @property string $IsActive
 *
 * @property Project[] $projects
 */
class Campaign extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'campaign';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Year', 'IsActive'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CampaingId' => Yii::t('app', 'Campaing ID'),
            'Year' => Yii::t('app', 'Year'),
            'IsActive' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['CampaignId' => 'CampaingId']);
    }
}
