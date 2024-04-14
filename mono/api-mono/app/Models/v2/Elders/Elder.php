<?php

namespace App\Models\v2\Elders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Elder extends Model
{
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
        'ccec_2_number_2',
    ];

    public static function generateUID($userType, Referral $referral)
    {
        $code = $userType === 'CGA' ? $referral->cga_code : $referral->bzn_code;
        $lastIndex = 0;
        $expectedUidLength = strlen($code) + 4;
        $lastUid = DB::table('elders')
            ->whereRaw("LEFT(uid, LENGTH('{$code}')) = '{$code}'")
            ->whereRaw("LENGTH(uid) = {$expectedUidLength}")
            ->max('uid');
        if ($lastUid) {
            $lastIndex = (int) str_replace($code, '', $lastUid);
        }

        return $code . Str::padLeft($lastIndex + 1, 4, '0');
    }

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

    public function delete()
    {
        Cases::where('elder_id', $this->id)->delete();

        return parent::delete();
    }
}
