<?php

declare(strict_types=1);

namespace Rebit\Share\Infrastructure\Controller\Serializers;

use Rebit\Share\Infrastructure\Controller\Normalizer\CommonNormalizer;
use Rebit\Share\Infrastructure\Controller\Normalizer\DateNormalizer;
use Rebit\Share\Infrastructure\Controller\Normalizer\DateTimeNormalizer;
use Rebit\Share\Infrastructure\Controller\Normalizer\EnumNormalizer;
use Rebit\Share\Infrastructure\Controller\Normalizer\ObjectNormalizer;
use Rebit\Share\Infrastructure\Controller\Normalizer\ScalarNormalizer;
use Rebit\Share\Shared\Interface\NormalizerInterface;
use Rebit\Share\Infrastructure\Interface\SerializerInterface;

/**
 * Сериализует данные в JSON-строку. Основной сериализатор.
 */
final readonly class CommonSerializer implements SerializerInterface
{
    public function __construct(
        private NormalizerInterface $commonNormalizer,
    ) {
    }

    /**
     * Создает дефолтовый сериализатор.
     * При необходимости частой иной сериализации можно добавить тут аналогичные кастомные конструкторы.
     */
    public static function createDefault(): SerializerInterface
    {
        return new self(new CommonNormalizer(
            new ObjectNormalizer(
                new EnumNormalizer(),
                new DateTimeNormalizer(),
                new DateNormalizer(),
                new ScalarNormalizer(),
            ),
            new ScalarNormalizer(),
        ));
    }

    public function serialize(
        mixed $data,
        int $jsonOptions = JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE,
    ): string {
        return json_encode($this->commonNormalizer->normalize($data), $jsonOptions);
    }
}
