<?php
declare(strict_types = 1);

namespace Innmind\Profiler\Domain\Entity;

use Innmind\Profiler\Domain\Entity\Section\Identity;

final class Http implements Section
{
    private Identity $identity;
    private string $request;
    private ?string $response = null;

    private function __construct(Identity $identity, string $request)
    {
        $this->identity = $identity;
        $this->request = $request;
    }

    public static function received(Identity $identity, string $request): self
    {
        return new self($identity, $request);
    }

    public function identity(): Identity
    {
        return $this->identity;
    }

    public function request(): string
    {
        return $this->request;
    }

    public function response(): string
    {
        return $this->response;
    }

    public function respondedWith(string $response): void
    {
        $this->response = $response;
    }

    public function hasRespondedYet(): bool
    {
        return \is_string($this->response);
    }
}
