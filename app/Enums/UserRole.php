<?php

namespace App\Enums;

enum UserRole: int
{
    case CITIZEN = 0;
    case AGENT = 1;
    case ADMIN = 2;
}