<?php

declare(strict_types=1);

namespace AlephTools\DDD\Common\Model\Exceptions;

use AlephTools\DDD\Common\Model\Identity\AbstractId;
use Throwable;

class EntityNotFoundException extends DomainException
{
    /**
     * Constructor.
     *
     * @param string|AbstractId $message
     */
    public function __construct($message = '', int $code = 0, Throwable $previous = null)
    {
        if ($message instanceof AbstractId) {
            $message = $this->errorMessageFrom($message);
        }
        parent::__construct($message, $code, $previous);
    }

    private function errorMessageFrom(AbstractId $id): string
    {
        $entity = ucfirst($this->separateWordsFromCamelCase($this->entityNameFromId($id)));
        return "$entity [ID: $id] is not found.";
    }

    private function entityNameFromId(AbstractId $id): string
    {
        $entity = $id->domainName();
        return substr($entity, 0, strlen($entity) - 2);
    }

    private function separateWordsFromCamelCase(string $entity): string
    {
        return strtolower(
            implode(' ', preg_split('/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', $entity))
        );
    }
}
