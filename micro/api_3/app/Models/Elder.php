<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenApi\Annotations as OA;

class Elder extends Model
{

    /**
     * @OA\Schema(
     *     schema="Elder",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="uid",
     *          type="string",
     *          example="WP0001"
     *     ),
     *
     *     @OA\Property(
     *          property="case_type",
     *          type="enum",
     *          format="CGA,BZN",
     *          example="BZN"
     *     ),
     *     @OA\Property(
     *          property="gender",
     *          type="enum",
     *          format="male,female",
     *          example="male"
     *     ),
     *
     *     @OA\Property(
     *          property="name",
     *          type="string",
     *          example="john doe"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="name_en",
     *          type="string",
     *          example="john does"
     *
     *     ),
     *
     *     @OA\Property(
     *          property="birth_day",
     *          type="integer",
     *          example="12"
     *
     *     ),
     *     @OA\Property(
     *          property="birth_month",
     *          type="integer",
     *          example="12"
     *
     *     ),
     *
     *     @OA\Property(
     *          property="birth_year",
     *          type="integer",
     *          example="1992"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="contact_number",
     *          type="string",
     *          example="1234567890"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="second_contact_number",
     *          type="string",
     *          example="1234567890"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="third_contact_number",
     *          type="string",
     *          example="1234567890"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="address",
     *          type="text",
     *          example="Jl.lorem ipsum dol amet 01/01 district new town"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="emergency_contact_number",
     *          type="string",
     *          example="1234567890"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="emergency_contact_name",
     *          type="string",
     *          example="john doe"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="relationship",
     *          type="string",
     *          example="spouse"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="uid_connected_with",
     *          type="string",
     *          example="WP0002"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="health_issue",
     *          type="string",
     *          example="sick"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="medication",
     *          type="string",
     *          example="need medicine"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="elder_remark",
     *          type="string",
     *          example="test elder remark"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="district_id",
     *          type="forignId",
     *          example="2"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="limited_mobility",
     *          type="string",
     *          example="bed rest"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="ccec_number",
     *          type="string",
     *          example="+81"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="ccec_number_2",
     *          type="string",
     *          example="+81"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="ccec_2_number",
     *          type="string",
     *          example="+81"
     *
     *     ),
     *
     *      @OA\Property(
     *          property="ccec_2_number_2",
     *          type="string",
     *          example="+81"
     *
     *     ),
     *
     *     @OA\Property(
     *          property="created_at",
     *          type="string",
     *          format="date-time",
     *          example="2022-05-13T00:00:00Z"
     *     ),
     *
     *     @OA\Property(
     *          property="updated_at",
     *          type="string",
     *          format="date-time",
     *          example="2022-05-13T00:00:00Z"
     *     )
     *
     *
     *
     *
     * )
     */

    use HasFactory;
    protected $guarded = ['id', 'deleted_at'];
    protected $hidden = ['laravel_through_key', 'deleted_at'];
    protected $fillable = [
        'uid',
        'name',
        'name_en',
        'gender',
        'contact_number',
        'second_contact_number',
        'third_contact_number',
        'address',
        'birth_day',
        'birth_month',
        'birth_year',
        'district_id',
        'zone_id',
        'zone_other',
        'language',
        'centre_case_id',
        'centre_responsible_worker_id',
        'centre_responsible_worker_other',
        'responsible_worker_contact',
        'case_type',
        'referral_id',
        'relationship',
        'uid_connected_with',
        'emergency_contact_number',
        'emergency_contact_number_2',
        'emergency_contact_name',
        'emergency_contact_relationship_other',
        'emergency_contact_2_number',
        'emergency_contact_2_number_2',
        'emergency_contact_2_name',
        'emergency_contact_2_relationship_other',
        'elder_remark',
        'ccec_number',
        'ccec_number_2',
        'ccec_2_number',
        'ccec_2_number_2'
    ];


    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function cases()
    {
        return $this->hasMany(Cases::class);
    }

    public function calls()
    {

        return $this->hasManyThrough(
            ElderCalls::class,
            Cases::class,
            'elder_id',
            'cases_id'
        );
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function centreResponsibleWorker()
    {
        return $this->belongsTo(CentreResponsibleWorker::class);
    }

    public static function generateUID($userType, Referral $referral)
    {
        $code = $userType === 'CGA' ? $referral->cga_code : $referral->bzn_code;
        $lastIndex = 0;
        $expectedUidLength = strlen($code) + 4;
        $lastUid = DB::table('elders')
            ->whereRaw("LEFT(uid, LENGTH('$code')) = '$code'")
            ->whereRaw("LENGTH(uid) = $expectedUidLength")
            ->max('uid');
        if ($lastUid) {
            $lastIndex = (int) str_replace($code, '', $lastUid);
        }
        return $code . Str::padLeft($lastIndex + 1, 4, '0');
    }
}
