<?php

namespace Models;

use mysql_xdevapi\Session;

enum category: string
{
    case Breakfast = 'Breakfast';
    case Lunch = 'Lunch';
    case Dinner = 'Dinner';

    public static function getCategoryType(self $value): string
    {
        return match ($value->value) {
            self::Breakfast => 'Breakfast',
            self::Lunch => 'Lunch',
            self::Dinner => 'Dinner',
            default => throw new \InvalidArgumentException("Invalid category value: $value"),
        };
    }

    public static function createFrom(string $value): category
    {
        $value = ucfirst(strtolower($value));
        switch ($value) {
            case 'Breakfast':
                return category::Breakfast;
            case 'Lunch':
                return category::Lunch;
            case 'Dinner':
                return category::Dinner;
            default:
                // Handle unknown values or throw an exception
                throw new \InvalidArgumentException("Invalid category value: $value");
        }
    }
}