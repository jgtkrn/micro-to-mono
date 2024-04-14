<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    /**
     * @OA\Schema(
     *     schema="Cases",
     *     type="object",
     *
     *     @OA\Property(
     *          property="id",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="case_name",
     *          type="string",
     *          example="Bz Nurse"
     *     ),
     *     @OA\Property(
     *          property="caller_name",
     *          type="string",
     *          example="Sung Kang"
     *     ),
     *     @OA\Property(
     *          property="case_number",
     *          type="integer",
     *          example="1"
     *     ),
     *
     *     @OA\Property(
     *          property="case_status",
     *          type="string",
     *          example="Completed"
     *     ),
     *     @OA\Property(
     *          property="last_update",
     *          type="datetime",
     *          example="2022-06-08 20:33:15"
     *     ),
     *
     *     @OA\Property(
     *          property="elder_id",
     *          type="integer",
     *          example="1"
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
     * )
     */

    use HasFactory;
    protected $guarded = ['id', 'deleted_at'];
    protected $hidden = ['deleted_at', 'updated_at', 'created_at'];


    public function elder()
    {
        return $this->belongsTo(Elder::class);
    }

    public function calls()
    {
        return $this->hasMany(ElderCalls::class);
    }

    public function meetingNotes()
    {
        return $this->hasMany(MeetingNotes::class);
    }

    public function scopeCustomSort($query, $rawSortBy, $sortDir)
    {
        $allowedFields = ['uid', 'name', 'user_type', 'district', 'created_at', 'updated_at'];
        $sortBy = in_array($rawSortBy, $allowedFields) ? $rawSortBy : 'created_at';
        $sortDirection = $sortDir == 'asc' ? 'ASC' : 'DESC';

        $sortByField = match ($sortBy) {
            'uid' => 'elders.uid',
            'name' => 'elders.name',
            'district' => 'districts.district_name',
            'user_type' => 'cases.case_name',
            default => "cases.$sortBy",
        };

        return $query->orderBy($sortByField, $sortDirection);
    }

    public function delete()
    {
        MeetingNotes::where('cases_id', $this->id)->delete();
        return parent::delete();
    }
}
