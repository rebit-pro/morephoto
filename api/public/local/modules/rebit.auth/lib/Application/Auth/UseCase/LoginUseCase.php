<?php

declare(strict_types=1);

namespace Rebit\Auth\Application\Auth\UseCase;

use Bitrix\Main\Type\DateTime;
use Rebit\Auth\Application\Auth\Dto\Request\LoginRequestDto;
use Rebit\Auth\Application\Auth\Dto\Result\LoginResultDto;
use Rebit\Auth\Domain\User\Repository\UserRepository;
use Rebit\Auth\Domain\User\Service\TokenGenerator;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class LoginUseCase
{
    public function __construct(
        private UserRepository $userRepository,
        private TokenGenerator $tokenGenerator,
        private int $tokenTtlHours,
    ) {}

    /**
     * @throws HttpException
     * @throws \Random\RandomException
     */
    public function execute(LoginRequestDto $dto): LoginResultDto
    {
        $user = $this->userRepository->findActiveByEmail($dto->email);

        if (null === $user) {
            throw new HttpException('Invalid credentials', 401);
        }

        if (!password_verify($dto->password, $user->passwordHash)) {
            throw new HttpException('Invalid credentials', 401);
        }

        $token = $this->tokenGenerator->generate();
        $expiresAt = DateTime::createFromTimestamp(
            time() + ($this->tokenTtlHours * 3600),
        );

        $this->userRepository->updateToken($user->id, $token, $expiresAt);

        return new LoginResultDto(
            token: $token,
            expiresAt: $expiresAt->format('c'),);
    }
}
