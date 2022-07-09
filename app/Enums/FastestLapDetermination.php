<?php

namespace App\Enums;

enum FastestLapDetermination: string
{
    case BEST_LAST_STINT = 'best_last_stint';
    case SEPARATE_STINT = 'separate_stint';
}