<?php
namespace App\Dto;
use Spatie\LaravelData\Data;

class ServiceData extends Data
{
    /**
     * Summary of __construct
     * @param string $name
     */
    public function __construct(
        public string $name,
    ) {
    }

    /**
     * Summary of rules
     * @param Spatie\LaravelData\Support\Validation\ValidationContext $context
     * @return array
     */
    public static function rules(Spatie\LaravelData\Support\Validation\ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}
