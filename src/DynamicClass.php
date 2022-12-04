<?php /** @noinspection TypoSafeNamingInspection */

namespace Orcid;

use Illuminate\Support\Str;

use function count;

abstract class DynamicClass
{
    /** @noinspection PhpMixedReturnTypeCanBeReducedInspection */
    public function __call(string $name, array $arguments): mixed
    {
        $property = Str::snake($name);
        if (property_exists($this, $property)) {
            if (count($arguments) > 0) {
                $this->_property_setter($property, $arguments);
                return $this;
            }
            return $this->_property_getter($property, $arguments);
        }
        return null;
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function _property_getter(string $property, array $arguments): mixed
    {
        return $this->$property;
    }

    protected function _property_setter(string $property, array $arguments): void
    {
        $this->$property = $arguments[0];
    }
}
