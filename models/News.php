<?php

namespace app\models;

use app\helper\ImageHandler;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $title
 * @property string $short_text
 * @property string $full_text
 * @property string $image_path
 * @property integer $status
 * @property integer $created_at
 *
 * @property User $user
 */
class News extends ActiveRecord
{
    /**
     * @var $image
     */
    public $image;

    /**
     * @var $status_array
     */
    public $status_array = [
        1 => 'deactivate',
        2 => 'active',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => time(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title','short_text', 'full_text', 'status'], 'required'],
            [['image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'on' => 'create'],
            [['user_id', 'status'], 'integer'],
            [['short_text', 'full_text'], 'string'],
            [['title', 'image_path'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'short_text' => 'Short Text',
            'full_text' => 'Full Text',
            'image_path' => 'Image',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Delete images news files
     *
     */
    public function cleanNewsImageDir($dir) {
        //get files
        $files = glob($dir."*");
        if (count($files) > 0) {
            foreach ($files as $file) {
                //if file exist
                if (file_exists($file)) {
                    //delete file
                    unlink($file);
                }
            }
        }
    }

    /**
     * Delete image from news
     * @param $path
     * */
    public function unlinkNewsImage()
    {
        $image_news = $this->getNewsImagePath()."/".$this->image_path;
        //get image path file from root directory
        $file = substr(ImageHandler::getPath($image_news), 0, -1);
        if(is_file($file )) {
            unlink($file );
        }

    }

    /**
     * get news image path folder
     * */
    public function getNewsImagePath()
    {
        return 'news/'.$this->user_id;
    }

    /**
     * upload image
     *
     * @return bool
     */
    public function upload()
    {
        //set model data
        $this->image = UploadedFile::getInstance($this, 'image');

        //delete file when upload isset and go upload new
        if(!is_null($this->image)) {
            $this->unlinkNewsImage();
        } else {
            //nothing upload
            return true;
        }
        //new uploaded image
        $this->image_path = time().'_news' . '.' . $this->image->extension;

        //create dir for news image
        if (!file_exists(Yii::getAlias('@app/web/images/news/'.$this->user_id.'/'))) {
            mkdir(Yii::getAlias('@app/web/images/news/'.$this->user_id.'/'), 0777, true);
        }
        if ($this->validate()) {
            $path = $this->getNewsImagePath();
            //save image
            $this->image->saveAs(ImageHandler::getPath($path).$this->image_path);
            //image resize
            ImageHandler::rateablyResize(ImageHandler::getPath($path).$this->image_path);

            $this->image = null;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Get per-page items
     *
     * @return array|mixed|string
     */
    public static function getPerPage()
    {
        return Yii::$app->request->get('per-page') ?? '5';
    }

}
