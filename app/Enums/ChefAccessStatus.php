<?php

namespace App\Enums;

enum ChefAccessStatus : int {
    case approved=0;
    case notApproved=1;
    case notRegistered=2;
    case notVerified=3;
    case blocked=4;
    case rejected=5;
}
