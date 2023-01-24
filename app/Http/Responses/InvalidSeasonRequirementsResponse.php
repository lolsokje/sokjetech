<?php

namespace App\Http\Responses;

use App\Exceptions\InvalidSeasonRequirements;
use Symfony\Component\HttpFoundation\Response;

class InvalidSeasonRequirementsResponse extends Response
{
    public function __construct(protected InvalidSeasonRequirements $exception)
    {
        parent::__construct($this->exception->getMessage(), self::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function handle(): Response
    {
        return response()->json([
            'error' => $this->getContent(),
        ], $this->getStatusCode());
    }
}
