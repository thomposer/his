<?php
namespace app\commands;

use app\modules\spot\models\CureList;
use app\modules\spot\models\Spot;
use Yii;
use yii\console\Controller;

/**
 * @author abelhe
 */
class SpotController extends Controller
{

    public function actionIndex()
    {
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from(Spot::tableName())
            ->where(['parent_spot' => '0'])
            ->all();

        $columns = ['name', 'price', 'discount', 'status', 'type','remark','tag_id', 'create_time', 'update_time', 'spot_id'];
        $values = [];
        $time = time();
        foreach ($rows as $row) {
            $values[] = ['青霉素皮试', '0', 1, 1, 1,'',0, $time, $time, $row['id']];
        }
        $connection= Yii::$app->db;
        $connection->createCommand()->batchInsert(CureList::tableName(), $columns, $values)->execute();
    }
}
