<?php

namespace App;

trait HRHTrait
{
    public function formatUsername($firstName, $middleName, $lastName): string
    {
        $username = substr($firstName, 0, 1) . substr($middleName ?? '', 0, 1) . $lastName;
        return strtolower($username);
    }
}
