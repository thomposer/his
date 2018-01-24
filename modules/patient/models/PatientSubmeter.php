<?php

namespace app\modules\patient\models;

use Yii;

/**
 * This is the model class for table "{{%patient_submeter}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $patient_id
 * @property integer $nationality 国籍
 * @property integer $languages 第一语言
 * @property integer $faiths 宗教信仰
 * @property string $other_languages 其它第一语言
 * @property string $other_faiths 其它宗教信仰
 * @property integer $parent_education 父母教育程度
 * @property integer $parent_occupation 父母职业
 * @property integer $parent_marriage 父母婚姻状况
 * @property integer $guardian 监护人
 * @property string $other_guardian 其它监护人
 * @property string $pregnancy_week 出生记录-孕周
 * @property decimal $childbirth_heightcm 出生记录-身长(单位：cm)
 * @property decimal $childbirth_weightkg 出生记录-体重(单位：kg)
 * @property decimal $childbirth_head_circumference 出生记录-头围(单位：cm)
 * @property integer $childbirth_way 出生记录-分娩方式(1-顺产，2-剖腹产，3-真空吸引，4-产钳助产)
 * @property string $childbirth_case 出生记录生产情况（1-侧切，2-羊水先破，3-使用催产素，4-使用麻醉药品，5-撕裂）
 * @property decimal $childbirth_time 出生记录-羊水破后产程时间(单位：小时)
 * @property string $childbirth_situation 出生记录-出生时疾病情况
 * @property integer $childbirth_hearing 出生记录-听力筛查（1-通过，2-未通过，3-未做）
 * @property decimal $pregnancy_heightcm 妈妈孕前记录-妈妈身高（单位：cm）
 * @property decimal $pre_pregnancy_weightkg 妈妈孕前记录-孕前体重（单位：kg）
 * @property decimal $pregnancy_max_weightkg 妈妈孕前记录-孕期最大体重（单位：kg）
 * @property decimal $pregnancy_min_weightkg 妈妈孕前记录-孕期最低体重（单位：kg）
 * @property string $pregnancy_situation 妈妈孕期记录-孕期疾患
 * @property string $create_time
 * @property string $update_time
 *
 * @property Patient $patient
 */
class PatientSubmeter extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%patient_submeter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'patient_id', 'nationality', 'languages', 'faiths', 'parent_education', 'parent_occupation', 'parent_marriage', 'guardian', 'childbirth_way', 'childbirth_hearing', 'create_time', 'update_time'], 'integer'],
            [['other_languages', 'other_faiths', 'other_guardian'], 'string', 'max' => 64],
            [['pregnancy_week'], 'string', 'max' => 255],
            [['childbirth_situation', 'pregnancy_situation'], 'string', 'max' => 100],
            [['pregnancy_heightcm', 'childbirth_heightcm'], 'number', 'max' => 250, 'min' => 40],
            [['childbirth_head_circumference'], 'number', 'max' => 60, 'min' => 30],
            [['childbirth_time'], 'number', 'max' => 200, 'min' => 0],
            [['childbirth_weightkg', 'pre_pregnancy_weightkg', 'pregnancy_max_weightkg', 'pregnancy_min_weightkg'], 'number', 'max' => 200, 'min' => 0],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::className(), 'targetAttribute' => ['patient_id' => 'id']],
            [['pregnancy_heightcm'], 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '身高只能精确到小数点后一位'],
            [['childbirth_heightcm'], 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '身长只能精确到小数点后一位'],
            [['childbirth_time'], 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '羊水破后产程时间只能精确到小数点后一位'],
            [['childbirth_weightkg', 'pre_pregnancy_weightkg', 'pregnancy_max_weightkg', 'pregnancy_min_weightkg'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '体重只能精确到小数点后两位'],
            [['childbirth_head_circumference'], 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '头围只能精确到小数点后一位'],
            [['pregnancy_week', 'childbirth_case', 'childbirth_situation', 'pregnancy_situation'], 'default', 'value' => ''],
            [['nationality', 'languages', 'faiths', 'parent_education', 'parent_occupation', 'parent_marriage', 'guardian', 'childbirth_way', 'childbirth_hearing'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'patient_id' => '患者ID',
            'nationality' => '国籍',
            'languages' => '第一语言',
            'faiths' => '宗教信仰',
            'other_languages' => '其它第一语言',
            'other_faiths' => '其它宗教信仰',
            'parent_education' => '父母教育程度',
            'parent_occupation' => '父母职业',
            'parent_marriage' => '父母婚姻状况',
            'guardian' => '法定监护人',
            'other_guardian' => '其它监护人',
            'pregnancy_week' => '孕周',
            'childbirth_heightcm' => '身长（cm）',
            'childbirth_weightkg' => '体重（kg）',
            'childbirth_head_circumference' => '头围（cm）',
            'childbirth_way' => '分娩方式',
            'childbirth_case' => '生产情况',
            'childbirth_time' => '羊水破后产程时间（小时）',
            'childbirth_situation' => '出生时疾病情况',
            'childbirth_hearing' => '听力筛查是否通过',
            'pregnancy_heightcm' => '妈妈身高（cm）',
            'pre_pregnancy_weightkg' => '孕前体重（kg）',
            'pregnancy_max_weightkg' => '孕期最大体重（kg）',
            'pregnancy_min_weightkg' => '孕期最低体重（kg）',
            'pregnancy_situation' => '孕期疾患',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient() {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id']);
    }

    /**
     * @var父母教育程度
     */
    public static $getParentEducation = [
        1 => '小学',
        2 => '初中',
        3 => '高中',
        4 => '大专',
        5 => '本科',
        6 => '硕士',
        7 => '博士'
    ];

    /**
     * @var父母职业
     */
    public static $getParentOccupation = [
        1 => '机关事业单位人员',
        2 => '企业职员',
        3 => '企业管理者',
        4 => '医护人员',
        5 => '个人经营者',
        6 => '自由职业',
        7 => '暂无职业',
        8 => '其他'
    ];

    /**
     * @var父母婚姻状况
     */
    public static $getParentMarriage = [
        1 => '未婚',
        2 => '已婚',
        3 => '离异',
        4 => '分居',
        5 => '丧偶',
        6 => '保密'
    ];

    /**
     * @var法定监护人
     */
    public static $getGuardian = [
        1 => '父母',
        2 => '单亲-父亲',
        3 => '单亲-母亲',
        4 => '其他长辈'
    ];

    /**
     * @var第一语言
     */
    public static $getLanguages = [
        1 => '普通话',
        2 => '英文',
        3 => '日文',
        4 => '粤语',
        5 => '其他'
    ];

    /**
     * @var宗教信仰
     */
    public static $getFaiths = [
        1 => '无',
        2 => '佛教',
        3 => '道教',
        4 => '天主教',
        5 => '基督教',
        6 => '其他'
    ];

    /**
     * @var国籍
     */
    public static $getNationality = [
        1 => '中国大陆',
        215 => '中国香港',
        21 => '中国澳门',
        239 => '中国台湾',
        2 => '阿尔巴尼亚共和国',
        3 => '阿尔及利亚民主人民共和国',
        4 => '阿富汗伊斯兰国',
        5 => '阿根廷共和国',
        6 => '阿拉伯埃及共和国',
        7 => '阿拉伯联合酋长国',
        8 => '阿拉伯叙利亚共和国',
        9 => '阿鲁巴',
        10 => '阿曼苏丹国',
        11 => '阿塞拜疆共和国',
        12 => '埃塞俄比亚',
        13 => '爱尔兰',
        14 => '爱沙尼亚共和国',
        15 => '安道尔公国',
        16 => '安哥拉共和国',
        17 => '安圭拉',
        18 => '安提瓜和巴布达 ',
        19 => '奥地利共和国',
        20 => '澳大利亚联邦',
        22 => '巴巴多斯',
        23 => '巴布亚新几内亚独立国',
        24 => '巴哈马共和国',
        25 => '巴基斯坦伊斯兰共和国',
        26 => '巴拉圭共和国',
        27 => '巴勒斯坦国',
        28 => '巴林国',
        29 => '巴拿马共和国',
        30 => '巴西联邦共和国',
        31 => '白俄罗斯共和国',
        32 => '百慕大群岛',
        33 => '保加利亚共和国',
        34 => '北马里亚娜自由联邦',
        35 => '贝劳共和国',
        36 => '贝宁共和国',
        37 => '比利时王国',
        38 => '冰岛共和国',
        39 => '波多黎各自由联邦',
        40 => '波兰共和国',
        41 => '波斯尼亚和黑塞哥维那共和国',
        42 => '玻利维亚共和国',
        43 => '伯利兹',
        44 => '博茨瓦纳共和国',
        45 => '不丹王国',
        46 => '布基纳法索',
        47 => '布隆迪共和国',
        48 => '布维岛',
        49 => '朝鲜民主主义人民共和国',
        50 => '赤道几内亚共和国',
        51 => '大阿拉伯利比亚人民社会主义民众国',
        52 => '大不列颠及北爱尔兰联合王国',
        53 => '大韩民国',
        54 => '丹麦王国',
        55 => '德意志联邦共和国',
        56 => '东帝汶',
        57 => '多哥共和国',
        58 => '多米尼加共和国',
        59 => '多米尼克联邦',
        60 => '俄罗斯联邦',
        61 => '厄瓜多尔共和国',
        62 => '厄立特里亚国',
        63 => '法兰斯共和国',
        64 => '法罗群岛',
        65 => '法属伯利尼西亚',
        66 => '法属圭亚那',
        67 => '法属南部领土',
        68 => '梵蒂冈城国',
        69 => '菲律宾共和国',
        70 => '斐济共和国',
        71 => '芬兰共和国',
        72 => '佛得角共和国',
        73 => '冈比亚共和国',
        74 => '刚果共和国',
        75 => '哥伦比亚共和国',
        76 => '哥斯达黎加共和国',
        77 => '格林纳达',
        78 => '格陵兰',
        79 => '格鲁吉亚共和国',
        80 => '古巴共和国',
        81 => '瓜德罗普',
        82 => '关岛',
        83 => '圭亚那合作共和国',
        84 => '哈萨克斯坦共和国',
        85 => '海地共和国',
        86 => '荷兰王国',
        87 => '荷属安的列斯',
        88 => '赫德岛和麦克唐纳岛',
        89 => '洪都拉斯共和国',
        90 => '基里巴斯共和国',
        91 => '吉布提共和国',
        92 => '吉尔吉斯共和国',
        93 => '几内亚比绍',
        94 => '几内亚共和国',
        95 => '加拿大',
        96 => '加纳共和国',
        97 => '加蓬共和国',
        98 => '柬埔寨王国',
        99 => '捷克共和国 ',
        100 => '津巴布韦共和国',
        101 => '喀麦隆共和国',
        102 => '卡塔尔国',
        103 => '开曼群岛',
        104 => '科科斯（基林）群岛',
        105 => '科摩罗伊斯兰联邦共和国',
        106 => '科特迪瓦共和国',
        107 => '科威特国',
        108 => '克罗地亚共和国',
        109 => '肯尼亚共和国',
        110 => '库克群岛',
        111 => '拉脱维亚共和国',
        112 => '莱索托王国',
        113 => '老挝人民民主共和国',
        114 => '黎巴嫩共和国',
        115 => '立陶宛共和国',
        116 => '利比里亚共和国',
        117 => '列支敦士登公国',
        118 => '留尼汪',
        119 => '卢森堡大公国',
        120 => '卢旺达共和国',
        121 => '罗马尼亚',
        122 => '马达加斯加共和国',
        123 => '马尔代夫共和国',
        124 => '马尔维纳斯群岛',
        125 => '马耳他共和国',
        126 => '马拉维共和国',
        127 => '马来西亚',
        128 => '马里共和国',
        129 => '马其顿共和国',
        130 => '马提尼克',
        131 => '马约特',
        132 => '玛绍尔群岛共和国',
        133 => '毛里求斯共和国',
        134 => '毛里塔尼亚伊斯兰共和国',
        135 => '美利坚合众国',
        136 => '美属萨摩亚',
        137 => '美属太平洋各群岛',
        138 => '美属维尔京群岛',
        139 => '蒙古国',
        140 => '蒙特塞拉特',
        141 => '孟加拉人民共和国',
        142 => '秘鲁共和国',
        143 => '密克罗尼西亚联邦',
        144 => '缅甸联邦',
        145 => '摩尔多瓦共和国',
        146 => '摩洛哥王国',
        147 => '摩纳哥王国',
        148 => '莫桑比克共和国',
        149 => '墨西哥合众国',
        150 => '纳米比亚共和国',
        151 => '南非共和国',
        152 => '南极洲',
        153 => '南乔治岛和南桑德韦奇岛',
        154 => '瑙鲁共和国',
        155 => '尼泊尔王国',
        156 => '尼加拉瓜共和国',
        157 => '尼日尔共和国',
        158 => '尼日利亚联邦共和国',
        159 => '纽埃',
        160 => '挪威王国',
        161 => '诺福克岛',
        162 => '皮特凯恩岛',
        163 => '葡萄牙共和国',
        164 => '日本国',
        165 => '瑞典王国',
        166 => '瑞士联邦',
        167 => '萨尔多瓦共和国',
        168 => '塞尔维亚和黑山',
        169 => '塞拉利昂共和国',
        170 => '塞内加尔共和国',
        171 => '塞浦路斯共和国',
        172 => '塞舌尔共和国',
        173 => '沙特阿拉伯王国',
        174 => '圣诞岛',
        175 => '圣多美和普林西比民主共和国 ',
        176 => '圣赫勒拿',
        177 => '圣基茨和尼维斯联邦',
        178 => '圣卢西亚',
        179 => '圣马力诺共和国',
        180 => '圣皮埃尔和密克隆',
        181 => '圣文森特和格林纳丁斯',
        182 => '斯里兰卡民族社会主义共和国',
        183 => '斯洛伐克共和国',
        184 => '斯洛文尼亚共和国',
        185 => '斯瓦尔巴群岛',
        186 => '斯威士兰王国',
        187 => '苏丹共和国',
        188 => '苏里南共和国',
        189 => '所罗门群岛',
        190 => '索马里共和国',
        191 => '塔吉克斯坦共和国',
        192 => '泰王国',
        193 => '坦桑尼亚联合共和国',
        194 => '汤加王国',
        195 => '特克斯和凯科斯群岛',
        196 => '特立尼达和多巴哥共和国',
        197 => '突尼斯共和国',
        198 => '图瓦卢',
        199 => '土耳其共和国',
        200 => '土库曼斯坦',
        201 => '托克劳',
        202 => '瓦利斯和富图纳群岛',
        203 => '瓦努阿图共和国',
        204 => '危地马拉共和国',
        205 => '委内瑞拉共和国',
        206 => '文莱达鲁萨兰国',
        207 => '乌干达共和国',
        208 => '乌克兰',
        209 => '乌拉圭东岸共和国',
        210 => '乌兹别克斯坦共和国',
        211 => '西班牙',
        212 => '西撒哈拉',
        213 => '西萨摩亚独立国',
        214 => '希腊共和国',
        216 => '新加坡共和国',
        217 => '新喀里多尼亚',
        218 => '新西兰',
        219 => '匈牙利共和国',
        220 => '牙买加',
        221 => '亚美尼亚共和国',
        222 => '也门共和国',
        223 => '伊拉克共和国',
        224 => '伊朗伊斯兰共和国',
        225 => '以色列国 ',
        226 => '意大利共和国',
        227 => '印度共和国',
        228 => '印度尼西亚共和国',
        229 => '英属维尔京群岛',
        230 => '英属印度洋领土',
        231 => '约旦哈西姆王国',
        232 => '越南社会主义共和国',
        233 => '赞比亚共和国',
        234 => '扎伊尔共和国',
        235 => '乍得共和国',
        236 => '直布罗陀',
        237 => '智利共和国',
        238 => '中非共和国'
    ];

    /**
     * @var array 生产方式
     */
    public static $childBirthWay = [
        1 => '顺产',
        2 => '剖腹产',
        3 => '真空吸引',
        4 => '产钳助产',
    ];

    /**
     * @var array 生产情况
     */
    public static $childBirthCase = [
        1 => '侧切',
        2 => '羊水先破',
        3 => '使用催产素',
        4 => '使用麻醉药品',
        5 => '撕裂'
    ];

    /**
     * @var array 听力筛查
     */
    public static $childBirthHearing = [
        1 => '通过',
        2 => '未通过',
        3 => '未做'
    ];

    public function beforeSave($insert) {

        if (is_array($this->childbirth_case)) {
            $this->childbirth_case = implode(',', $this->childbirth_case);
        }

        return parent::beforeSave($insert);
    }

    /**
     * 
     * @param type  $birthCase  生产情况
     * @return type 格式化生产情况
     */
    public static function formatBirthCase($birthCase) {
        $tmp = explode(',', $birthCase);
        $typeArray = array_map(function ($v) {
            return self::$childBirthCase[$v];
        }, $tmp);
        return implode($typeArray, '、');
    }

    /**
     * 
     * @param type $patientId  患者Id
     * @return type 获取患者相关出生记录的信息
     */
    public static function birthInfo($patientId) {
        $data = PatientSubmeter::find()->select([
                    'pregnancy_week', 'childbirth_heightcm', 'childbirth_weightkg', 'childbirth_head_circumference', 'childbirth_way', 'childbirth_case', 'childbirth_time', 'childbirth_situation',
                    'childbirth_hearing', 'pregnancy_heightcm', 'pre_pregnancy_weightkg', 'pregnancy_max_weightkg', 'pregnancy_min_weightkg', 'pregnancy_situation'
                ])->where(['spot_id'=>  self::$staticParentSpotId,'patient_id'=>$patientId])->asArray()->one();
        if (!empty($data)) {
            $data['childbirth_way'] = (isset($data['childbirth_way']) && $data['childbirth_way']) ? self::$childBirthWay[$data['childbirth_way']] : '';
            $data['childbirth_hearing'] = (isset($data['childbirth_hearing']) && $data['childbirth_hearing']) ? self::$childBirthHearing[$data['childbirth_hearing']] : '';
            $data['childbirth_case'] = (isset($data['childbirth_case']) && $data['childbirth_case']) ? self::formatBirthCase($data['childbirth_case']) : '';
        }
        return $data;
    }

}
