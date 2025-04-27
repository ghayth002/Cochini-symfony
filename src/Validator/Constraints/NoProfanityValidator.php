<?php

namespace App\Validator\Constraints;

use App\Service\BadWordApiService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoProfanityValidator extends ConstraintValidator
{
    private $badWordApiService;

    public function __construct(BadWordApiService $badWordApiService)
    {
        $this->badWordApiService = $badWordApiService;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoProfanity) {
            throw new UnexpectedTypeException($constraint, NoProfanity::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // Check if the text contains profanity
        $result = $this->badWordApiService->checkProfanity($value);

        if ($result['success'] && $result['hasProfanity']) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
} 