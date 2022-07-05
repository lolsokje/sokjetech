<?php

namespace App\Enums;

use App\Actions\Action;
use App\Actions\QualifyingFormats\SingleSession;
use App\Actions\QualifyingFormats\ThreeSessionElimination;
use App\Models\Season;

enum QualifyingFormat: string
{
    case THREE_SESSION_ELIMINATION = 'three_session_elimination';
    case SINGLE_SESSION = 'single_session';

    public static function labels(): array
    {
        return [
            self::THREE_SESSION_ELIMINATION->value => 'Three session elimination',
            self::SINGLE_SESSION->value => 'Single session',
        ];
    }

    public function action(Season $season, $request): Action
    {
        return match ($this) {
            self::THREE_SESSION_ELIMINATION => new ThreeSessionElimination($season, $request),
            self::SINGLE_SESSION => new SingleSession($season, $request),
        };
    }

    public function modelFullyQualifiedClassName(): string
    {
        return match ($this) {
            self::THREE_SESSION_ELIMINATION => \App\Models\QualifyingFormats\ThreeSessionElimination::class,
            self::SINGLE_SESSION => \App\Models\QualifyingFormats\SingleSession::class,
        };
    }
}
