<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'venue_id',
        'start_date',
        'end_date',
        'status'
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function reservations()
    {
        return $this->hasMany(Event::class);
    }
}
