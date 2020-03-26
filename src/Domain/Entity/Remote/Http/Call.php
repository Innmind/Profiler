<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity\Remote\Http;

final class Call
{
    private string $request;
    private string $response;

    public function __construct(string $request, string $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function request(): string
    {
        return $this->request;
    }

    public function response(): string
    {
        return $this->response;
    }
}
