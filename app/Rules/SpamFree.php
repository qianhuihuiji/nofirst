<?php 

namespace App\Rules;

use App\Inspections\Spam;


class SpamFree
{
    public function passes($attributes,$value)
    {
        try {
            return ! resolve(Spam::class)->detect($value);
        }catch (\Exception $e) {
            return false;
        }
    }
}