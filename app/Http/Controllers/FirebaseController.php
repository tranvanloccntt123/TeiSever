<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
class FirebaseController extends Controller
{
    //
    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->testTable = "test";
    }

    public function test(){
        $this->database->getReference('test/name')->set('New name');
    }
}
