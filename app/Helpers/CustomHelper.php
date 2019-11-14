<?php

/**
 * @param int $code
 * @return string
 */
function errorMessage(int $code): string
{
    return config('errorCode.' . $code) ?? 'Undefined error. Please contact admin.';
}
