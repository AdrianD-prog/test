<?php

    namespace App\Enums;

/**
 * Typy żądań HTTP obsługiwane przez aplikację.
 */
enum RequestType
{
    /** Żądanie typu GET */
    case GET;
    /** Żądanie typu POST */
    case POST;
    /** Żądanie typu PUT */
    case PUT;
    /** Żądanie typu DELETE */
    case DELETE;
}
