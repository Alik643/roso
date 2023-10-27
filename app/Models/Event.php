<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';
    protected $fillable = ['title', 'place', 'date', 'period', 'period_type'];


    public function formatPeriod() {
        if ($this->period == 0) {
            return 'сегодня';
        } elseif ($this->period < 0) {
            return 'через ' . abs($this->period) . ' ' . trim($this->period_type);
        } else {
            return 'было ' . $this->period . ' ' . trim($this->period_type) . ' назад';
        }
    }
}
