<?php

namespace App\Enum;

enum PromotionCategory: string
{
    case WINTER_SALES = 'winter_sales';
    case SUMMER_SALES = 'summer_sales';
    case CUSTOM = 'custom';
}