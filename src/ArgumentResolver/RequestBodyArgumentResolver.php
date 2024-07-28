<?php

namespace DKart\Bumbu\ArgumentResolver;

use DKart\Bumbu\Attribute\RequestBody;
use DKart\Bumbu\Attribute\Valid;
use DKart\Bumbu\Exception\RequestBodyConvertException;
use DKart\Bumbu\Exception\ValidationException;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class RequestBodyArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface  $validator
    )
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        if (!count($argument->getAttributes(RequestBody::class, ArgumentMetadata::IS_INSTANCEOF))) {
            return [];
        }

        try {
            $model = $this->serializer->deserialize(
                $request->getContent(),
                $argument->getType(),
                JsonEncoder::FORMAT
            );
        } catch (Throwable $throwable) {
            throw new RequestBodyConvertException($throwable);
        }

        if (!empty($argument->getAttributes(Valid::class, ArgumentMetadata::IS_INSTANCEOF))) {

            $errors = $this->validator->validate($model);

            if ($errors->count()) {
                throw new ValidationException($errors);
            }
        }

        yield $model;
    }
}