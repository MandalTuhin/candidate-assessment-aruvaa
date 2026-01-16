<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // We are doing this here so, php understands
    // and treats the 'options' as an array
    protected $casts = [
        'options' => 'array',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    //
}
