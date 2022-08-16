<?php

namespace App\Enums;

enum UserRoleEnum: string
{
    case ADMIN = "1";
    case CUSTOMER = "2";

    public static function random(): string
    {
        return self::cases()[array_rand(self::cases())]->value;
    }
}
