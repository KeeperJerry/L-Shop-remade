<?php
declare(strict_types = 1);

namespace app\Services\Monitoring\Drivers;

use app\Services\Monitoring\ResponseException;

/**
 * Class ResponseParseException
 * It throws in case you can not parse the response from the server on the underlying pattern.
 */
class ResponseParseException extends ResponseException
{
    public function __construct(string $response, string $pattern)
    {
        parent::__construct(
            "It is not possible to parse the response from the server `{$response}`, using the pattern `{$pattern}`"
        );
    }
}
