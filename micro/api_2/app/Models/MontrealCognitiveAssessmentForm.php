<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="MontrealCognitiveAssessmentForm",
 *     type="object",
 * 
 *     @OA\Property(
 *          property="elderly_central_ref_number",
 *          type="string",
 *          example="WTO1000"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessment_date",
 *          type="string",
 *          format="date",
 *          example="2022-05-13"
 *     ),
 *
 *     @OA\Property(
 *          property="assessor_name",
 *          type="string",
 *          example="John Doe"
 *     ),
 * 
 *     @OA\Property(
 *          property="assessment_kind",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c11",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c12",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c13",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c14",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c15",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c21",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c22",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c23",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c24",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_c25",
 *          type="boolean",
 *          example="true"
 *     ),
 * 
 *     @OA\Property(
 *          property="memory_score",
 *          type="float",
 *          example="5.1"
 *     ),
 * 
 *     @OA\Property(
 *          property="language_fluency1",
 *          type="string",
 *          example="crocodile"
 *     ),
 * 
 *     @OA\Property(
 *          property="language_fluency2",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency3",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency4",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency5",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency6",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency7",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency8",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency9",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency10",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency11",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency12",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency13",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency14",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency15",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency16",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency17",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency18",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency19",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency20",
 *          type="string",
 *          example="crocodile"
 *     ),
 *  
 *     @OA\Property(
 *          property="all_words",
 *          type="float",
 *          example="5.1"
 *     ),
 *  
 *     @OA\Property(
 *          property="repeat_words",
 *          type="float",
 *          example="5.1"
 *     ),
 *  
 *     @OA\Property(
 *          property="non_animal_words",
 *          type="float",
 *          example="5.1"
 *     ),
 *  
 *     @OA\Property(
 *          property="language_fluency_score",
 *          type="float",
 *          example="5.1"
 *     ),
 * 
 *      @OA\Property(
 *          property="orientation_day",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="orientation_month",
 *          type="integer",
 *          example="1"
 *     ),
 * 
 *     @OA\Property(
 *          property="orientation_year",
 *          type="integer",
 *          example="1987"
 *     ),
 * 
 *     @OA\Property(
 *          property="orientation_week",
 *          type="integer",
 *          example="1"
 *     ),
 *  
 *     @OA\Property(
 *          property="orientation_place",
 *          type="string",
 *          example="NYC"
 *     ),
 *   
 *     @OA\Property(
 *          property="orientation_area",
 *          type="string",
 *          example="WDC"
 *     ),
 *   
 *     @OA\Property(
 *          property="orientation_score",
 *          type="float",
 *          example="4.0"
 *     ),
 *    
 *     @OA\Property(
 *          property="face_word",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="velvet_word",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="church_word",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="daisy_word",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="red_word",
 *          type="enum",
 *          format="1, 2, 3",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="delayed_memory_score",
 *          type="float",
 *          example="3.0"
 *     ),
 *    
 *     @OA\Property(
 *          property="category_percentile",
 *          type="enum",
 *          format="1, 2, 3, 4",
 *          example="2"
 *     ),
 * 
 *     @OA\Property(
 *          property="total_moca_score",
 *          type="float",
 *          example="21.0"
 *     ),
 * 
 *     @OA\Property(
 *          property="education_level",
 *          type="string",
 *          example="yes"
 *     ),
 * )
 */

class MontrealCognitiveAssessmentForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['deleted_at'];
    protected $casts = [
        'assessment_case_id' => 'integer',
        'assessment_kind' => 'integer',
        'memory_c11' => 'boolean',
        'memory_c12' => 'boolean',
        'memory_c13' => 'boolean',
        'memory_c14' => 'boolean',
        'memory_c15' => 'boolean',
        'memory_c21' => 'boolean',
        'memory_c22' => 'boolean',
        'memory_c23' => 'boolean',
        'memory_c24' => 'boolean',
        'memory_c25' => 'boolean',
        'memory_score' => 'decimal:2',
        'all_words' => 'decimal:2',
        'repeat_words' => 'decimal:2',
        'non_animal_words' => 'decimal:2',
        'language_fluency_score' => 'decimal:2',
        'orientation_day' => 'integer',
        'orientation_month' => 'integer',
        'orientation_year' => 'integer',
        'orientation_week' => 'integer',
        'orientation_score' => 'decimal:2',
        'face_word' => 'integer',
        'velvet_word' => 'integer',
        'church_word' => 'integer',
        'daisy_word' => 'integer',
        'red_word' => 'integer',
        'delayed_memory_score' => 'decimal:2',
        'category_percentile' => 'integer',
        'total_moca_score' => 'decimal:2',
        'education_level' => 'string'
    ];

    public function assessmentCase()
    {
        return $this->belongsTo(AssessmentCase::class, 'assessment_case_id');
    }
}
