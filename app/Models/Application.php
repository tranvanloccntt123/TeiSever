<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
    protected $table = "applications";
    public function modules(){
        return $this->belongsToMany(Module::class)->using(SelectModule::class);
    }
}
