<?php 

namespace App;

class Spam
{
    public function detect($body)
    {
        $this->detectInvalidKeywords($body);

        return false;
    }

    public function detectInvalidKeywords($body)
    {
        $invalidKeyWords = [
            'something forbidden'
        ];

        foreach ($invalidKeyWords as $invalidKeyWord) {
            if(stripos($body,$invalidKeyWord) !== false) {
                throw new \Exception('Your reply contains spam.');
            }
        }
    }
}