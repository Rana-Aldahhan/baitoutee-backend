<?php

namespace App\Enums;

enum UserAccessStatus : int {
    case active=0;
    case notApproved=1;
    case notRegistered=2;
    case notVerified=3;
    case inactive=4;
    case blocked=5;
    case rejected=6;
}