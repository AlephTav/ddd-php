<?php

namespace AlephTools\DDD\Common\Model;

use AlephTools\DDD\Common\Infrastructure\Sanitizer;
use AlephTools\DDD\Common\Infrastructure\ValueObject;

/**
 * @property-read string $address
 */
class Email extends ValueObject
{
    public const ADDRESS_REGEX = '/([a-z0-9][-a-z0-9_\+\.]*[a-z0-9])@([a-z0-9][-a-z0-9\.]*[a-z0-9]\.(arpa|root|aero|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|me|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)|([0-9]{1,3}\.{3}[0-9]{1,3}))/i';
    public const ADDRESS_MAX_LENGTH = 255;

    protected $address;

    /**
     * Constructor. Available formats:
     * Email()
     * Email(string $address)
     * Email(array $properties)
     *
     * @param array|string|null $address
     */
    public function __construct($address = null)
    {
        if (is_array($address)) {
            parent::__construct($address);
        } else {
            parent::__construct(['address' => $address]);
        }
    }

    protected function setAddress(?string $address): void
    {
        $this->address = Sanitizer::sanitizeEmail($address);
    }

    protected function validateAddress(): void
    {
        $this->assertArgumentMaxLength(
            $this->address,
            static::ADDRESS_MAX_LENGTH,
            'Email address must be at most ' . static::ADDRESS_MAX_LENGTH . ' characters.'
        );
        if (strlen($this->address) > 0) {
            $this->assertArgumentPatternMatch($this->address, static::ADDRESS_REGEX, 'Invalid email format.');
        }
    }
}
