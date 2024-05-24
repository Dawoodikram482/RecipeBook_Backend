<?php

namespace Models;

use InvalidArgumentException;

enum role: string
{
    case visitor = 'Visitor';
    case admin = 'Admin';

    public function getRoleType(): string
    {
        return match ($this) {
            role::visitor => 'Visitor',
            role::admin => 'Admin',
        };
    }

    public static function createFrom(string $value): role
    {
        $value = ucfirst(strtolower($value));
        switch ($value) {
            case 'Visitor':
                return role::visitor;
            case 'Admin':
                return role::admin;
            default:
                // Handle unknown values or throw an exception
                throw new InvalidArgumentException("Invalid role value: $value");
        }
    }
}