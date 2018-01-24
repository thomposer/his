<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;
use yii;
use yii\console\Controller;
use PHPExcel;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_IOFactory;
use app\modules\make_appointment\models\Appointment;
use app\modules\make_appointment\models\search\AppointmentSearch;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use app\modules\patient\models\PatientRecord;
use app\modules\patient\models\Patient;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SendemailController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex(){
        if(empty($emailFile)){
            $emailFile = 'sendEmail';
        }
        $dataProvider = $this->getAppointmentInfo();
        $mail = Yii::$app->mailer->compose($emailFile, ['data' => $dataProvider]);
        $fileName = $this->exportAppointmentList();
        $mail->setTo(["cf.info@ump.com.hk"]);
        $attachmentPath = "/data/web/easyhin/appointment_data/".$fileName;
        $mail->attach($attachmentPath);
        $mail->setSubject("umpeasyhin预约信息汇总");
        $mail->send();
    }

    public function exportAppointmentList(){
        $objectPHPExcel = new PHPExcel();
        $objectPHPExcel->setActiveSheetIndex(0);
        $objectPHPExcel->setActiveSheetIndex(0);
        $page_size = 52;
        $dataProvider = $this->getAppointmentInfo();
        $count = count($dataProvider);
        $page_count = (int)($count/$page_size) +1;
        $current_page = 0;
        $n = 0;
        $type = PatientRecord::$getType;
        foreach ( $dataProvider as $key => $appoinment )
        {
            if ( $n % $page_size === 0 )
            {
                $current_page = $current_page +1;

                //报表头的输出
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->getFont()->setSize(24);
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B2','日期：'.date("Y年m月j日",strtotime("+1 day")));
                $objectPHPExcel->setActiveSheetIndex(0)->getStyle('G2')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                //设置居中
                $objectPHPExcel->getActiveSheet()->getStyle('B3:H3')
                    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            //明细的输出
            $objectPHPExcel->getActiveSheet()->setCellValue('B'.($n+4) ,$key+1);
            $objectPHPExcel->getActiveSheet()->setCellValue('C'.($n+4) ,$appoinment['username']);
            $objectPHPExcel->getActiveSheet()->setCellValue('D'.($n+4) ,$appoinment['iphone']);
            $objectPHPExcel->getActiveSheet()->setCellValue('E'.($n+4) ,$type[$appoinment['type']]);
            $objectPHPExcel->getActiveSheet()->setCellValue('F'.($n+4) ,$appoinment['departmentName']);
            $objectPHPExcel->getActiveSheet()->setCellValue('G'.($n+4) ,$appoinment['doctorName'] );
            $objectPHPExcel->getActiveSheet()->setCellValue('H'.($n+4) ,date("Y-m-d H:i:s",$appoinment['time']));
            //设置边框
            $currentRowNum = $n+4;
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':H'.$currentRowNum )
                ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':H'.$currentRowNum )
                ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':H'.$currentRowNum )
                ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':H'.$currentRowNum )
                ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':H'.$currentRowNum )
                ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objectPHPExcel->getActiveSheet()->getStyle('B'.($n+4).':H'.$currentRowNum )
                ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $n = $n +1;
        }

        //设置分页显示

        $objectPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
        $objectPHPExcel->getActiveSheet()->getPageSetup()->setVerticalCentered(false);

        //报表头的输出
        $objectPHPExcel->getActiveSheet()->mergeCells('B1:G1');
        $objectPHPExcel->getActiveSheet()->setCellValue('B1','预约信息表');
        $objectPHPExcel->setActiveSheetIndex(0)->getStyle('B1')->getFont()->setSize(20);

        //表格头的输出
        $objectPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('B3','序号');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(6.5);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('C3','患者姓名');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('D3','患者手机');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H3','手机号');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('E3','预约类型');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('F3','预约科室');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('G3','预约医生');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objectPHPExcel->setActiveSheetIndex(0)->setCellValue('H3','预约时间');
        $objectPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);


        //设置居中
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3')
            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //设置边框
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3' )
            ->getBorders()->getVertical()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

        //设置颜色
        $objectPHPExcel->getActiveSheet()->getStyle('B3:H3')->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('FF66CCCC');
        ob_end_clean();
        ob_start();
        $objWriter= PHPExcel_IOFactory::createWriter($objectPHPExcel,'Excel2007');
        $date = date("Y-m-d",strtotime("+1 day"));
        $outputFileName = $date.".xls";
        $objWriter->save("/data/web/easyhin/appointment_data/".$outputFileName);
        return $outputFileName;
    }

    public function getAppointmentInfo(){
        $query = new Query();
        $query->from(['a' => Appointment::tableName()]);
        $query->select(['a.id','us.username as doctorName','b.iphone','a.time','d.type','b.username','a.record_id','b.sex','b.birthday','c.name as departmentName','d.status']);
        $query->leftJoin(['d' => PatientRecord::tableName()],'{{a}}.record_id = {{d}}.id');
        $query->leftJoin(['us' => User::tableName()],'{{a}}.doctor_id = {{us}}.id');
        $query->leftJoin(['b' => Patient::tableName()],'{{a}}.patient_id = {{b}}.id');
        $query->leftJoin(['c' => SecondDepartment::tableName()],'{{a}}.second_department_id = {{c}}.id');
        $query->where('b.spot_id = :spotId',[':spotId' => '10']);
        $query->andWhere(['d.status'=>'1']);
        $query->andWhere(['>','a.time',strtotime(date("Y-m-d"))+86400]);
        $query->andWhere(['<','a.time',strtotime('+1 day')+86400]);
        $dataProvider = $query->all();
        return $dataProvider;
    }
}
