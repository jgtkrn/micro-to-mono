<?php

namespace Database\Seeders;

use App\Models\Appointment;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clusters = [
            'Hong Kong East Cluster (港島東聯網)',
            'Hong Kong West Cluster (港島西聯網)',
            'Kowloon Central Cluster (九龍中聯網)',
            'Kowloon West Cluster (九龍西聯網)',
            'Kowloon East Cluster (九龍東聯網)',
            'New Territories East Cluster (新界東聯網) ',
            'New Territories West Cluster  (新界西聯網)',
        ];
     
        $types = [
            'Hospital',
            'SOPC',
            'GOPC',
        ];

        $names = [
            'Hong Kong East Cluster (港島東聯網)' => 
            [
                'Hospital' => 
                [
                    ['Pamela Youde Nethersole Eastern Hospital (PYNEH)','東區尤德夫人那打素醫院'],
                    ['Ruttonjee Hospital (RH)','律敦治醫院'],
                    ['Tang Shiu Kin Hospital (TSKH)','鄧肇堅醫院 '],
                    ['Tung Wah Eastern Hospital (TWTH)','東華東院'],
                    ['Wong Chuk Hang Hospital (WCHH)','黃竹坑醫院'],
                ],
                'SOPC' => 
                [
                    ['Anaesthesiology (Pain Clinic)','麻醉科(痛症科門診)'],
                    ['Cardiothoracic Surgery','心胸外科'],
                    ['Clinical Oncology','臨床腫瘤科'],
                    ['Ear, Nose and Throat','耳鼻喉科'],
                    ['Eye','眼科'],
                    ['Gynaecology','婦科'],
                    ['Medicine','內科'],
                    ['Neurosurgery','神經外科'],
                    ['Obstetrics','產科'],
                    ['Orthopaedics & Traumatology','矯形及創傷外科(骨科)'],
                    ['Psychiatry','精神科'],
                    ['Surgery','外科'],
                ],
                'GOPC' => 
                [
                    ['Anne Black General Out-patient Clinic','柏立基夫人普通科門診診所'],
                    ['Chai Wan General Out-patient Clinic','柴灣普通科門診診所'],
                    ['Sai Wan Ho General Out-patient Clinic','西灣河普通科門診診所'],
                    ['Shau Kei Wan Jockey Club General Out-patient Clinic','筲箕灣賽馬會普通科門診診所'],
                    ['Wan Tsui General Out-patient Clinic','環翠普通科門診診所'],
                    ['Tung Wah Eastern Hospital General Out-patient Department ( Tung Wah Eastern Hospital )','東華東院普通科門診部 ( 東華東院 )'],
                    ['Violet Peel General Out-patient Clinic','貝夫人普通科門診診所'],
                ],

            ],
            'Hong Kong West Cluster (港島西聯網)' => 
            [
                'Hospital' => 
                [
                    ['Grantham Hospital (GH)','葛量洪醫院'],
                    ['MacLehose Medical Rehabilitation Centre (MMRC)','麥理浩復康院'],
                    ['Queen Mary Hospital (QM)','瑪麗醫院'],
                    ['Tung Wah Group of Hospitals Fung Yiu King Hospital','東華三院馮堯敬醫院'],
                    ['Tung Wah Hospital (TWH)','東華醫院'],
                ],
                'GOPC' => 
                [
                    ['Central District Health Centre GOPC','中區健康院普通科門診診所'],
                    ['Kennedy Town Jockey Club GOPC','堅尼地城賽馬會普通科門診診所'],
                    ['Sai Ying Pun Jockey Club GOPC','西營盤賽馬會普通科門診診所 '],
                    ['Tung Wah Hospital GOPC','東華醫院普通科門診診所'],
                    ['Aberdeen Jockey Club GOPC','香港仔賽馬會普通科門診診所'],
                    ['Ap Lei Chau GOPC','鴨脷洲普通科門診診所'],
                ],
            ],
            'Kowloon Central Cluster (九龍中聯網)' => [
                'Hospital' => 
                [
                    ['Hong Kong Buddhist Hospital(BH)','香港佛教醫院'],
                    ['Hong Kong Eye Hospital (HKE)','香港眼科醫院'],
                    ['Kowloon Hospital (KH)','九龍醫院'],
                    ['Kwong Wah Hospital (KWH)','廣華醫院'],
                    ['Our Lady of Maryknoll Hospital (OLMH)','聖母醫院'],
                    ['Queen Elizabeth Hospital (QEH)','伊利沙伯醫院'],
                    ['TWGHs Wong Tai Sin Hospital (WTSH) ','東華三院黃大仙醫院'],
                ],
                'GOPC' => 
                [
                    ['Central Kowloon Health Centre','中九龍診所'],
                    ['Community Rehabilitation Service Support Centre','社區復康中心'],
                    ['East Kowloon General Out-patient Clinic','東九龍普通科門診診所'],
                    ['Hung Hom Clinic','紅磡診所'],
                    ['Kwong Wah Hospital GOPD','廣華醫院全科門診部'],
                    ['Lee Kee Memorial Dispensary','李基紀念醫局'],
                    ['Li Po Chun General Out-patient Clinic','李寶椿普通科門診診所'],
                    ['Our Lady of Maryknoll Hospital Family Medicine Clinic','聖母醫院家庭醫學診所'],
                    ['Robert Black General Out-patient Clinic','柏立基普通科門診診所'],
                    ['Shun Tak Fraternal Association Leung Kau Kui Clinic','順德聯誼會梁銶琚診所'],
                    ['Wang Tau Hom Jockey Club General Out-patient Clinic','橫頭磡賽馬會普通科門診診所'],
                    ['Wu York Yu General Out-patient Clinic','伍若瑜普通科門診診所'],
                    ['Yau Ma Tei Jockey Club General Outpatient Clinic','油麻地賽馬會普通科門診診所'],
                ],
            ],
            'Kowloon West Cluster (九龍西聯網)' => [
                'Hospital' => 
                [
                        ['Caritas Medical Centre (CMC)','明愛醫院'],
                        ['Kwai Chung Hospital (KWH)','葵涌醫院'],
                        ['North Lantau Hospital (NLH)','北大嶼山醫院'],
                        ['Princess Margaret Hospital (PMH)','瑪嘉烈醫院'],
                        ['Yan Chai Hospital (YCH)','仁濟醫院'],
                ],
                'GOPC' => 
                [
                    ['Caritas Medical Centre Family Medicine Clinic','(明愛醫院全科門診部) 明愛醫院家庭醫學診所'],
                    ['Cheung Sha Wan Jockey Club General Out-patient Clinic','長沙灣賽馬會普通科門診診所'],
                    ['Nam Shan General Out-patient Clinic','南山普通科門診診所 '],
                    ['Shek Kip Mei General Out-patient Clinic','石硤尾普通科門診診所'],
                    ['West Kowloon General Out-patient Clinic','西九龍普通科門診診所'],
                    ['North Lantau Community Health Centre','北大嶼山社區健康中心'],
                    ['Ha Kwai Chung General Out-patient Clinic','下葵涌普通科門診診所'],
                    ['Mrs Wu York Yu General Out-patient Clinic ','伍若瑜夫人普通科門診診所'],
                    ['North Kwai Chung General Out-patient Clinic','北葵涌普通科門診診所 '],
                    ['South Kwai Chung Jockey Club Gerenal Out-patient Clinic','南葵涌賽馬會普通科門診診所'],
                    ['Tsing Yi Cheung Hong General Out-patient Clinic','青衣長康普通科門診診所'],
                    ['Tsing Yi Town General Out-patient Clinic','青衣市區普通科門診診所 '],
                    ['Lady Trench General Out-patient Clinic','戴麟趾夫人普通科門診診所'],
                    ['Yan Chai Hospital General Practice Clinic','仁濟醫院全科診所'],
                ],
            ],
            'Kowloon East Cluster (九龍東聯網)' => [
                'Hospital' => 
                [
                        ['United Christian Hospital','基督教聯合醫院'],
                        ['Tseung Kwan O Hospital','將軍澳醫院'],
                        ['Haven of Hope Hospital','靈實醫院'],
                ],
                'GOPC' => 
                [
                        ['Kowloon Bay Health Centre','九龍灣健康中心普通科門診'],
                        ['Kwun Tong Community Health Centre','觀塘社區健康中心'],
                        ['Lam Tin Polyclinic GOPC','藍田分科診所普通科門診診所'],
                        ['Ngau Tau Kok Jockey Club','牛頭角賽馬會普通科門診診所'],
                        ['Shun Lee GOPC','順利普通科門診診所'],
                        ['Mona Fong GOPC','方逸華普通科門診診所'],
                        ['Tseung Kwan O (Po Ning Road)','將軍澳（寶寧路）普通科門診診所'],
                        ['Tseung Kwan O Jockey Club','將軍澳賽馬會普通科門診診所'],
                ],
            ],
            'New Territories East Cluster (新界東聯網)' => [
                'Hospital' => 
                [
                    ['Alice Ho Miu Ling Nethersole Hospital (AHNH)','雅麗氏何妙齡那打素醫院'],
                    ['North District Hospital (NDH)','北區醫院'],
                    ['Prince of Wales Hospital (PWH)','威爾斯親王醫院'],
                    ['Shatin Hospital (SH)','沙田醫院'],
                    ['Tai Po Hospital (TPH)','大埔醫院'],
                ],
                'GOPC' => 
                [
                    ['Fanling family Medicine Centre','粉嶺家庭醫學中心'],
                    ['Shek Wu Hui Jockey Club General Out-patient Clinic','石湖墟賽馬會普通科門診診所'],
                    ['Lek Yuen General Out-patient Clinic','瀝源普通科門診診所'],
                    ['Shatin (Tai Wai) General Out-patient Clinic ','沙田（大圍）普通科門診診所'],
                    ['Yuen Chau Kok General Out-patient Clinic','圓洲角普通科門診診所'],
                    ['Ma On shan Family Medicine Centre','馬鞍山家庭醫學中心'],
                    ['Tai Po Jocky Club General Out-patient Clinic','大埔賽馬會普通科門診診所'],
                    ['Wong Siu ching Family Medicine  Centre','王少清家庭醫學中心'],
                ],
            ],
            'New Territories West Cluster  (新界西聯網)' => [
                'Hospital' => 
                [
                    ['Castle Peak Hospital (CPH)','青山醫院'],
                    ['Pok Oi Hospital (POH)','博愛醫院'],
                    ['Tin Shui Wai Hospital (TSWH)','天水圍醫院'],
                    ['Tuen Mun Hospital (TMH)','屯門醫院'],
                ],
                'GOPC' => 
                [
                    ['Tuen Mun Clinic','屯門診所'],
                    ['Tuen Mun Wu Hong Clinic','屯門湖康診所'],
                    ['Yan Oi General Out-patient Clinic','仁愛普通科門診診所'],
                    ['Kam Tin Clinic','錦田診所'],
                    ['Madam Yung Fung Shee Health Centre','容鳳書健康中心'],
                    ['Yuen Long Jockey Club Health Centre','元朗賽馬會健康院'],
                    ['Tin Shui Wai (Tin Yip Road) Community Health Centre','天水圍（天業路）社區健康中心'],
                    ['Tin Shui Wai Health Centre (Tin Shui Road)','天水圍健康中心（天瑞路）'],
                ],
            ],
        ];

        $record = [
            'cluster' => '',
            'type' => '',
            'name_en' => '',
            'name_sc' => '',
        ];

        foreach ($clusters as $cluster) {
            $record['cluster'] = $cluster;
            foreach ($types as $type) {
                $record['type'] = $type;
                foreach ($names as $nameKey => $nameValue) {
                    if ($nameKey == $record['cluster']) {
                        foreach ($nameValue as $key => $value) {
                            if ($key == $record['type']) {
                                for ($i=0; $i < count($value); $i++) {
                                    $record['name_en'] = $value[$i][0];
                                    $record['name_sc'] = $value[$i][1];
                                    $this->createAppointment($record);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function createAppointment($record) {
        Appointment::factory()->create($record);
    }
}
