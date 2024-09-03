<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qna extends Model
{
    //
    protected $table = 'ms_question_and_answer';
    protected $fillable = ['name', 'description'];
}
