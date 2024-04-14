<?php

namespace App\Models\v2\Elders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
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
            default => "cases.{$sortBy}",
        };

        return $query->orderBy($sortByField, $sortDirection);
    }

    public function delete()
    {
        MeetingNotes::where('cases_id', $this->id)->delete();

        return parent::delete();
    }
}
