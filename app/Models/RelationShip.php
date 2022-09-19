<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationShip extends Model
{
    use HasFactory;
    protected $table = "relationships";
    protected $fillable = ['user_id', 'friend', 'application_id', 'status', 'who_request'];
}
