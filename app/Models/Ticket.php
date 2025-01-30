<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'seat_id', 'price', 'status', 'code', 'uuid'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $ticket->uuid = (string) Str::uuid();
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }
}
