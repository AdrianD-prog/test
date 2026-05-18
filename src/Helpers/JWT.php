<?php

    namespace App\Helpers;

    use InvalidArgumentException;

/**
 * Klasa pomocnicza do obsługi tokenów JWT (JSON Web Token).
 */
class JWT
{
    /** @var string Klucz do podpisywania i weryfikacji tokenów */
    private string $key;

    /**
     * @param string $key Klucz tajny
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Dekoduje i weryfikuje token JWT.
     *
     * @param string $token Token JWT do zdekodowania
     * @param array $user
     * @return bool Zdekodowany payload tokenu lub pusta tablica w przypadku błędnego formatu
     */
    public function decode(string $token, array &$user): bool
    {
        if (preg_match(
            "/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
            $token,
            $matches
        ) !== 1
        ) {
            return false;
        }

        $signature = hash_hmac(
            "sha256",
            $matches["header"] . "." . $matches["payload"],
            $this->key,
            true
        );

        $signature_from_token = $this->base64URLDecode($matches["signature"]);

        if (!hash_equals($signature, $signature_from_token)) {
            header("Location: $_ENV[ROOT_DIR]/logout");
            exit();
        }

        $user = json_decode($this->base64URLDecode($matches["payload"]), true);
        return true;
    }

    /**
     * Dekoduje ciąg znaków z formatu Base64URL do Base64.
     *
     * @param string $text Ciąg znaków w formacie Base64URL
     * @return string Zdekodowany ciąg znaków
     */
    private function base64URLDecode(string $text): string
    {
        return base64_decode(
            str_replace(
                ["-", "_"],
                ["+", "/"],
                $text
            )
        );
    }
}
