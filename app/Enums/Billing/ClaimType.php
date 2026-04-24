<?php

namespace App\Enums\Billing;

enum ClaimType: string
{
    case Sha       = 'sha';
    case Nhif      = 'nhif';
    case Corporate = 'corporate';
    case Private   = 'private';
}
