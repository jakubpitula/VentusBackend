# VentusBackend

```
url: ventusapi.herokuapp.com
```

## 1. Login:

```
/api/login_check
```

Przesyłając POST request pod /login trzeba dodać header ```'Content-Type': 'application/json'``` . W JSON'ie przesłać ```'username'``` (de facto email)
i ```'password'```. W przypadku pomyślnej autentykacji zwrócony zostanie status 200, token oraz refresh_token, a w przypadku niepomyślnej - 401 i message error.

## 2. Sprawdzanie maila:

```
/api/check_email
```


W requeście POST przesłać ```'email'```.
Jeśli email jest w bazie, zwracane jest ```'status': 'login'.```
Jeśli nie ma - ```'status': 'register'```

## 3. Rejestracja:

```
/api/register
```

Wymagane dane w requeście POST: ```'password', 'email', 'gender', 'location', 'first_name', 'birthday', 'messenger'```.
Niewymagane, ale zalecane: ```'picture'```. ```'picture'``` jest typem File. Reszta to typ string.

Zwracany jest pusty status 200, jeśli udało się zarejestrować.
W przypadku błędu walidacji zwracane jest 400 i 'error': 'wiadomość o błędzie'.

## 4.1 Zwracanie danych akualnie zalogowanego użytkownika:

```
/api/user
```

W requeście konieczny header z JWT - Authorization: Bearer {token}
Nie podawać żadnych danych w requeście.
```'picture'``` zawiera nazwę pliku. Ścieżka do plików:``` public/images/pictures```

### 4.2. Zwracanie danych dowolnego użytkownika wg id:

```
/api/user/{id}
```

W requeście GET podać ```'id'``` użytkownika, którego chcemy sprawdzić.

### 5. Odświeżanie tokenu JWT:

```
/api/token/refresh
```

W requeście POST form data podać ```'refresh_token'``` wygenerowany poprzednio po zalogowaniu. Wygenerowany zostanie nowy JWT dla usera.

### 6. Kategorie:

jutro dokumentacja, ale są
