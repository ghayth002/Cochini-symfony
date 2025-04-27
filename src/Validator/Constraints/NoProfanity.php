<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
class NoProfanity extends Constraint
{
    public string $message = 'Votre texte contient des mots inappropriés. Veuillez utiliser un langage respectueux.';
} 