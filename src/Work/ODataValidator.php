<?php

namespace Orcid\Work;

use function array_key_exists;

trait ODataValidator
{
    //data normalizer
    protected static function tryToNormalizeLanguageCode(string $languageCode): bool
    {
        $language_code = str_replace('-', '_', strtolower(trim($languageCode)));
        /** @noinspection OffsetOperationsInspection */
        return array_key_exists(
            $language_code,
            OAwork::SPECIAL_LANGAGE_CODES
        ) ? OAwork::SPECIAL_LANGAGE_CODES[$language_code] : $language_code;
    }

    protected static function tryToNormalizeCountryCode(string $country): string
    {
        return strtoupper(trim($country));
    }

    protected static function tryToNormalizeWorkType(string $type): string
    {
        return str_replace('_', '-', strtolower(trim($type)));
    }

    protected static function tryToNormalizeAuthorRole(string $role): string
    {
        return str_replace('_', '-', strtolower(trim($role)));
    }

    protected static function tryToNormalizeCitationType(string $citationType): string
    {
        return str_replace('_', '-', strtolower(trim($citationType)));
    }

    protected static function tryToNormalizeAuthorSequence(string $sequence): string
    {
        return strtolower(trim($sequence));
    }

    public static function tryToNormalizeExternalIdRelationType(string $type): string
    {
        return str_replace('_', '-', strtolower(trim($type)));
    }
}
