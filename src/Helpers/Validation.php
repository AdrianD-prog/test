<?php
    namespace App\Helpers;

    // session_start();

    class Validation
    {
        public static function validateUserData(array $data): bool
        {
            $validationError = false;
            $requiredFields = ['email', 'password', 'first_name', 'last_name', 'phone', 'country', 'city', 'address'];
            foreach ($requiredFields as $field)
            {
                if(isset($data[$field]))
                {
                    if (empty($data[$field]))
                    {
                        $_SESSION['error'][$field] = 'Pole ['.ucfirst(str_replace('_', ' ', $field)) . '] jest wymagany.';
                        $validationError = true;
                    }

                    if ($data[$field] === '')
                    {
                        $_SESSION['error'][$field] = 'Pole ['.ucfirst(str_replace('_', ' ', $field)) . '] nie może być puste.';
                        $validationError = true;
                    }
                    if(strlen($data[$field]) < 3)
                    {
                        $_SESSION['error'][$field] = 'Pole ['.ucfirst(str_replace('_', ' ', $field)) . '] musi mieć co najmniej 3 znaki.';
                        $validationError = true;
                    }
                }
            }

            // Walidacja Email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            {
                $_SESSION['error']['email'] = 'Nieprawidłowy format email.';
                $validationError = true;
            }
            if (strlen($data['email']) > 255)
            {
                $_SESSION['error']['email'] = 'Email nie może być dłuższy niż 255 znaków.';
                $validationError = true;
            }

            // Walidacja Hasła
            if(isset($data['password']))
            {
                if (strlen($data['password']) < 8)
                {
                    $_SESSION['error']['password'] = 'Hasło musi mieć co najmniej 8 znaków.';
                    $validationError = true;
                }
                if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $data['password']))
                {
                    $_SESSION['error']['password'] = 'Hasło musi zawierać co najmniej jedną wielką, jedną małą literę i jedną cyfrę i jeden znak specjalny.';
                    $validationError = true;
                }
            }

            // Walidacja Imienia
            if (!preg_match('/^[a-zA-Z\p{L}]+$/u', $data['first_name']))
            {
                $_SESSION['error']['first_name'] = 'Imię może zawierać tylko litery.';
                $validationError = true;
            }
            if (strlen($data['first_name']) > 50)
            {
                $_SESSION['error']['first_name'] = 'Imię nie może być dłuższe niż 50 znaków.';
                $validationError = true;
            }

            // Walidacja Nazwiska
            if (!preg_match('/^[a-zA-Z\p{L}]+$/u', $data['last_name']))
            {
                $_SESSION['error']['last_name'] = 'Nazwisko może zawierać tylko litery.';
                $validationError = true;
            }
            if (strlen($data['last_name']) > 50)
            {
                $_SESSION['error']['last_name'] = 'Nazwisko nie może być dłuższe niż 50 znaków.';
                $validationError = true;
            }

            // Walidacja Telefonu
            if (!preg_match('/^([+]?\d{1,2}[-\s]?|)\d{3}[-\s]?\d{3}[-\s]?\d{3}$/', $data['phone']))
            {
                $_SESSION['error']['phone'] = 'Nieprawidłowy format numeru telefonu.';
                $validationError = true;
            }

            // Walidacja Kraj
            if (!preg_match('/^[a-zA-Z\s\p{L}]+$/u', $data['country']))
            {
                $_SESSION['error']['country'] = 'Kraj może zawierać tylko litery';
                $validationError = true;
            }
            if (strlen($data['country']) > 50)
            {
                $_SESSION['error']['country'] = 'Kraj nie może być dłuższy niż 50 znaków.';
                $validationError = true;
            }

            // Walidacja Miasta
            if (!preg_match('/^[a-zA-Z\s\p{L}]+$/u', $data['city']))
            {
                $_SESSION['error']['city'] = 'Miasto może zawierać tylko litery i spacje.';
                $validationError = true;
            }
            if (strlen($data['city']) > 50)
            {
                $_SESSION['error']['city'] = 'Miasto nie może być dłuższe niż 50 znaków.';
                $validationError = true;
            }

            // Walidacja Adresu
            if (!preg_match('/^[a-zA-Z0-9\s.,\-\p{L}]+$/u', $data['address']))
            {
                $_SESSION['error']['address'] = 'Adres może zawierać tylko litery, cyfry, spacje oraz znaki ., -';
                $validationError = true;
            }
            if (strlen($data['address']) > 150)
            {
                $_SESSION['error']['address'] = 'Adres nie może być dłuższy niż 150 znaków.';
                $validationError = true;
            }

            if(isset($data['password']) && isset($data['repassword']))
            {
                if($data['password'] !== $data['repassword'])
                {
                    $_SESSION['error']['repassword'] = 'Hasła nie są identyczne.';
                    $validationError = true;
                }
            }
            return $validationError;
        }
    }