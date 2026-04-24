<?php

namespace App\Enums\Billing;

enum ServiceCategory: string
{
    case Consultation = 'consultation';
    case Lab          = 'lab';
    case Pharmacy     = 'pharmacy';
    case Procedure    = 'procedure';
    case Bed          = 'bed';
    case Other        = 'other';
}
