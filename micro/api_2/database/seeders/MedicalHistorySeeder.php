<?php

namespace Database\Seeders;

use App\Models\MedicalHistory;
use Illuminate\Database\Seeder;

class MedicalHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = array(
            'Blood problem',
            'Cardiovascular system',
            'Digestive system',
            'Endocrine system',
            'Musculoskeletal System',
            'Nervous System',
            'Excretory system',
            'Mental disorder',
            'Neoplasm',
            'Skin problem',
            'Respiratory system',
            'Reproductive System',
        );

        $diagnoses = array(
            [
                'Anemia', 'Leukemia', 'Myeloma', 'Lymphoma'
            ],
            [
                'Atherosclerosis', 'Abdominal aortic aneurism (AAA)', 'Angina pectoris', 'Atrial fibrillation',
                'Arrhythmia /dysrhythmia', 'Ischemia Heart Disease (IHD)', 'Coronary artery disease', 'Deep vein thrombosis and pulmonary embolism',
                'Hypertension', 'Hyperlipidemia', 'Heart failure', 'Mitral valve problems', 'Myocardiac Infarction', 'Peripheral artery disease'
            ],
            [
                'Cholangitis', 'Cholecystectitis', 'Constipation', 'Crohn\`s disease', 'Dysphagia', 'Gastroenteritis (GE)', 
                'Acid reflux / Gastroesophageal reflux disease (GERD)', 'Liver cirrhosis', 'Peptic ulcers '
            ],
            [
                'Diabetes mellitus', 'Hyperthyroidism', 'Hypothyroidism'
            ],
            [
                'Arthritis : _____(body part)', 'Carpal tunnel syndrome', 'Fracture: _____(body part)', 
                'Osteoporosis', 'Rheumatoid arthritis (RA):  _____(body part)', 'Tendinitis', 'Trauma'
            ],
            [
                'Cerebrovascular accident (CVA) /Stroke', 'Cerebral palsy', 'Dementia', 'Epilepsy', 'Parkinson\'s Disease', 
                'Motor neurone disease (MND)', 'Multiple sclerosis (MS)', 'Sciatica', 'Spinal cord diseases', 'Transient ischaemic attack (TIA)'
            ],
            [
                'Benign Prostatic Hyperplasia (BPH)', 'Bladder malfunction', 'Incontinence', 'Chronic renal failure', 
                'Urinary tract infection/ Lower urinary tract symptoms (UTI/LUTS)', 'Kidney stone'
            ],
            [
                'Anxiety disorders', 'Depression', 'Mood disorders', 'Personality disorders', 
                'Psychotic disorders (autism, attention deficit-hyperactivity disorder, bipolar disorder, major depressive disorder and schizophrenia)', 
                'Post-traumatic stress disorder (PTSD)', 'Obsessive-compulsive disorder (OCD)'
            ],
            [
                'Text Box',
            ],
            [
                'Eczematous dermatitis (Eczema)',
                'Bacterial and Viral infections',
                'Fungal infections',
                'Pruritus',
            ],
            [
                'Asthma', 
                'Bronchiectasis', 
                'Chronic obstructive pulmonary disease (COPD)', 
                'Chronic Bronchitis', 
                'Pulmonary tuberculosis (TB)', 
                'Emphysema', 
                'Pneumonia', 
                'Pleural Effusion', 
            ],
            [
                'Endometriosis',
                'Uterine fibroids',
                'Uterine prolapse',
            ],
        );

        for ($i=0; $i < count($categories); $i++) { 
            $medicalHistory['medical_category_name'] = $categories[$i];
            for ($j=0; $j < count($diagnoses[$i]); $j++) {
                $medicalHistory['medical_diagnosis_name'] = $diagnoses[$i][$j];
                $this->createMedicalHistory($medicalHistory);
            }
        }
    }
    
    public function createMedicalHistory($medicalHistory) {
        MedicalHistory::factory()->create($medicalHistory);
    }

}
