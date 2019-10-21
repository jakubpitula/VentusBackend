# VentusBackend

1. Login:

path: /api/login

Przesyłając POST request pod /login trzeba dodać header 'Content-Type': 'application/json'. W JSON'ie przesłać 'username' 
i 'password'. W przypadku pomyślnej autentykacji zwrócony zostanie status 200, a w przypadku niepomyślnej - 401 i message error.

2. Sprawdzanie maila:

path: /api/check_email

W requeście POST przesłać 'email'.
Jeśli email jest w bazie, zwracane jest 'status': 'login'.
Jeśli nie ma - 'status': 'register'

3. Rejestracja:

path: /api/register

Wymagane dane w requeście POST: 'password', 'username', 'email', 'picture', 'gender', 'location', 'first_name', 'birthday', 'messenger'. 'picture' jest typem File. Reszta to typ string.

Zwracany jest pusty status 200, jeśli udało się zarejestrować.
W przypadku błędu walidacji zwracane jest 401 i 'error': 'wiadomość o błędzie'.

4.1 Zwracanie danych akualnie zalogowanego użytkownika:

path: /api/user

Nie podawać żadnych danych w requeście.
'picture' zawiera nazwę pliku. Ścieżka do plików: public/images/pictures

4.2. Zwracanie danych dowolnego użytkownika wg id:

path: /api/user/{id}

W requeście GET podać 'id' użytkownika, którego chcemy sprawdzić.
