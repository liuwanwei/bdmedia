<?php

/**
 * This is the model class for table "Video".
 *
 * The followings are the available columns in table 'Video':
 * @property string $id
 * @property string $title
 * @property string $mark
 * @property string $description
 * @property string $url
 * @property string $imgUrl
 * @property integer $playCount
 * @property string $createTime
 * @property string $modifyTime
 * @property string $version
 */
class Video extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Video';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('playCount, version', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('mark', 'length', 'max'=>20),
			array('description', 'length', 'max'=>500),
			array('url, imgUrl', 'length', 'max'=>200),
			array('createTime, modifyTime', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, description, url, playCount, version, createTime, modifyTime', 'safe', 'on'=>'search'),
		);
	}

	public function init()
	{
		parent::init();

		$this->createTime = date('Y-m-d H:i:s');
		$this->modifyTime = $this->createTime;
		$this->version = 0;
		$this->mark = null;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => '标题',
			'mark' => '标记',
			'description' => '描述',
			'url' => '视频url',
			'imgUrl' => '图片url',
			'playCount' => '播放次数',
			'version' => '版本', 
			'createTime' => '创建时间',
			'modifyTime' => '更新时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('mark',$this->mark,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('imgUrl',$this->imgUrl,true);
		$criteria->compare('playCount',$this->playCount);
		$criteria->compare('version',$this->version);
		$criteria->compare('createTime',$this->createTime,true);
		$criteria->compare('modifyTime',$this->modifyTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Video the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/*
	 * 获取按version分组倒序后的version值
	 */
	public function getGroupLastNum()
	{
		$sql = 'select distinct version from Video group by version order by version DESC limit 1';
		$command = Yii::app()->db->createCommand($sql);
		$result = $command->queryAll();
		return $result[0]['version'];
	}
}
