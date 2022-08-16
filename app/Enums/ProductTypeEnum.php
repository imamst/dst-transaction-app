<?php

namespace App\Enums;

enum ProductTypeEnum: string
{
    case FOOD = "food";
    case FASHION = "fashion";
    case ELECTRONIC = "electronic";
    case GADGET = "gadget";
    case BOOK = "book";

    public static function random(): string
    {
        return self::cases()[array_rand(self::cases())]->value;
    }
}
