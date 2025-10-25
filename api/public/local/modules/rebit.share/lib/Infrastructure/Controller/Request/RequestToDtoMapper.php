<?php

namespace Rebit\Share\Infrastructure\Controller\Request;

use Bitrix\Main\HttpRequest;
use Doctrine\Common\Annotations\AnnotationReader;
use Rebit\Share\Infrastructure\Exception\DtoInterfaceNotImplementException;
use Rebit\Share\Infrastructure\Exception\RequestParameterException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Infrastructure\Helpers\RequestHelper;
use Rebit\Share\Shared\Interface\RequestDtoInterface;
use Rebit\Share\Infrastructure\Interface\RequestMapperInterface;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validation;

/**
 * Мапит результаты запроса в DTO c интерфейсом RequestDtoInterface
 *
 * @template T of RequestDtoInterface
 */
final readonly class RequestToDtoMapper implements RequestMapperInterface
{
    private DenormalizerInterface $denormalizer;

    public function __construct(
        private HttpRequest $request,
    ) {
        $this->denormalizer = $this->createDenormalizer();
    }
    public function supports(string $className): bool
    {
        return is_subclass_of($className, RequestDtoInterface::class);
    }

    /**
     * @throws \Rebit\Share\Infrastructure\Exception\DtoInterfaceNotImplementException
     * @throws ExceptionInterface
     * @throws RequestParameterException
     * @throws \Rebit\Share\Infrastructure\Exception\ValidationHttpException
     */
    public function map(string $className): object
    {
        if (!$this->supports($className)) {
            throw new DtoInterfaceNotImplementException(sprintf('%s does not implement RequestDtoInterface', $className));
        }

        $requestData = RequestHelper::collectRequestValues($this->request);

        if (!$this->denormalizer->supportsDenormalization($requestData, $className)) {
            throw new RequestParameterException("Cannot denormalize into {$className}");
        }

        try {
            /** @var RequestDtoInterface $dto */
            $dto = $this->denormalizer->denormalize(
                $requestData,
                $className,
                null,
                ['allow_extra_attributes' => false],
            );
        } catch (MissingConstructorArgumentsException $e) {
            $missingFields = implode(', ', $e->getMissingConstructorArguments());

            throw new ValidationHttpException('В запросе не были переданы поля: ' . $missingFields);
        } catch (ExtraAttributesException $e) {
            $extraFields = implode(', ', $e->getExtraAttributes());

            throw new ValidationHttpException(
                sprintf(
                    'В запросе переданы поля, отсутствующие в DTO (%s):  %s',
                    $className,
                    $extraFields,
                ),
            );
        }

        $this->validate($dto);

        return $dto;
    }

    /**
     * Валидация DTO
     *
     * @throws \Rebit\Share\Infrastructure\Exception\ValidationHttpException
     */
    private function validate(RequestDtoInterface $dto): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator()
        ;

        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            // список ошибок сам умеет корректно сериализоваться в строку
            throw new ValidationHttpException($errors);
        }
    }

    private function createDenormalizer(): DenormalizerInterface
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new ArrayDenormalizer(),
            new ObjectNormalizer(
                classMetadataFactory: new ClassMetadataFactory(
                    new AnnotationLoader(new AnnotationReader()),
                ),
                propertyTypeExtractor: new PhpDocExtractor(),
            ),
        ];

        return new Serializer($normalizers, $encoders);
    }
}
