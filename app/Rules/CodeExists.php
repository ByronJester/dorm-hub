<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class CodeExists implements Rule
{
    private $table;
    private $column;

    public function __construct($table, $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function passes($attribute, $value)
    {
        return DB::table($this->table)
            ->where($this->column, $value)
            ->exists();
    }

    public function message()
    {
        return 'The :attribute does not exist in the specified table.';
    }
}
