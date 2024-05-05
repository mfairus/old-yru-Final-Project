<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ColorModel extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'color';

    static public function getSingle($id)
    {
        return self::find($id);
    }

    static public function getRecord()
    {
        return self::select('color.*','users.name as created_by_name')
                ->join('users', 'users.id', '=', 'color.created_by')
                ->where('color.is_delete', '=' , 0)
                ->orderBy('color.id', 'desc')
                ->get();
    }
    static public function getRecordActive()
    {
        return self::select('color.*')
                ->join('users', 'users.id', '=', 'color.created_by')
                ->where('color.is_delete', '=' , 0)
                ->where('color.status', '=' , 0)
                ->orderBy('color.name', 'asc')
                ->get();
    }
}