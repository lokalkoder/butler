<?php

namespace Lokal\Butler\Manager;

use Spatie\Enum\Exceptions\UnknownEnumProperty;
use Spatie\Enum\Laravel\Enum;

class AbstractEnum extends Enum
{
    /**
     * @var array
     */
    protected static array $parameters = [];

    /**
     * @return array|int[]|string[]
     */
    protected static function values()
    {
        return static::$parameters;
    }

    /**
     * @return string[]|int[]
     */
    public static function toLabels(): array
    {
        $values = array_keys(static::toArray());
        foreach ($values as $key => $value) {
            $values[$key] = __(
                str($value)->headline()->toString()
            );
        }

        return $values;
    }

    /**
     * @return int|string
     *
     * @throws UnknownEnumProperty
     */
    public function __get(string $name)
    {
        if ($name === 'label') {
            return str($this->label)->headline()->toString();
        }

        if ($name === 'raw') {
            return $this->label;
        }

        if ($name === 'translated') {

            return localize(str($this->label)->headline()->toString());
        }

        if ($name === 'value') {
            return $this->value;
        }

        throw UnknownEnumProperty::new(static::class, $name);
    }
}
