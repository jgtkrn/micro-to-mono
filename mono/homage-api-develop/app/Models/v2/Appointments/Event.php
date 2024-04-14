<?php

namespace App\Models\v2\Appointments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start',
        'end',
        'elder_id',
        'case_id',
        'remark',
        'address',
        'category_id',
        'created_by',
        'updated_by',
        'created_by_name',
        'updated_by_name',
    ];

    //protected $hidden = ["elder_id"];

    public function user()
    {
        return $this->hasMany(UserEvent::class);
    }

    public function file()
    {
        return $this->hasMany(File::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
