<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tplan extends Model
{
    protected $table = 'tplan';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'project_type',
        'Dep_id',
        'who_present',
        'tel',
        'email',
        'cojob',
        'budget_detail',
        'year_long',
        'month_long',
        'day_long',
        'sdate',
        'edate',
        'add_date',
        'flag',
    ];
}
