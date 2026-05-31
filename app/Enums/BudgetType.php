<?php

namespace App\Enums;

enum BudgetType: string
{
    case Fixed = 'fixed';
    case Hourly = 'hourly';
}
