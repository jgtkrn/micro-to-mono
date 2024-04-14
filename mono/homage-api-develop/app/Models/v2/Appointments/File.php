<?php

namespace App\Models\v2\Appointments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ['file_name', 'disk_name', 'user_id', 'event_id'];
    protected $hidden = ['event_id'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
