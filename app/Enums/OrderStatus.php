<?php

namespace App\Enums;

enum OrderStatus : int {
    case pending=0;
    case approved=1;
    case notApproved=2;
    case prepared=3;
    case failedAssigning=4;
    case picked=5;
    case delivered=6;
    case notDelivered=7;
    case canceled=8;
}
