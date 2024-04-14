<?php

namespace App\Models\v2\Appointments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'name'];

    public function category()
    {
        return $this->hastOne(Event::class);
    }
}
