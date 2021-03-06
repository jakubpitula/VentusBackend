# VentusBackend

```
url: ventusapi.herokuapp.com
```

### WAŻNE: WE WSZYSTKICH REQUESTACH DO ŚCIEŻEK ZACZYNAJĄCYCJ SIĘ OD ```/api/user``` DODAĆ HEADER Z JWT - ```Authorization: Bearer {token}```, TRZEBA BYĆ DO NICH ZALOGOWANYM.

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

### 4.1 Zwracanie danych akualnie zalogowanego użytkownika:

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

## 5. Odświeżanie tokenu JWT:

```
/api/token/refresh
```

W requeście POST form data podać ```'refresh_token'``` wygenerowany poprzednio po zalogowaniu. Wygenerowany zostanie nowy JWT dla usera.

## 6. Kategorie:
### 6.1. Lista wszystkich kategorii:

```
/api/category
```
### 6.2. Kategoria wg id:

```
/api/category/{id}
```
### 6.3. Podkategorie danej kategorii:

```
/api/category/{id}/subcategories
```
### 6.4. Tworzenie nowej kategorii:

```
/api/category/new
```
W POST requeście form-data wysłać ```'name'```.
W przypadku niewysłania zwrócony zostanie status 400 i error message.
Jeśli wszystko potoczy się pomyślnie, zwrócone zostanie 201.

## 7. Dodawanie kategori do zalogowanego usera:

```
/api/user/category/new
```
W requeście POST dodać JSONem tablicę id kategorii, które chcemy dodać, np. ```[1,2,3]```. 
W przypadku niepomyślnej autentykacji zwrócone zostanie 405. Jeśli będzie się próbowało dodać kategorię, która nie istnieje, zwrócone zostanie 418. Jeśli wszystko potoczy się pomyślnie, zwrócone zostanie 200.

## 8. Dodawanie podkategorii do zalogowanego usera:

```
/api/user/subcategory/new
```
W requeście POST dodać JSONem id kategorii, którą chcemy dodać oraz procent wybrany przez usera, w formacie
```
{
	"subcategory": 1,
	"percentage": 100
}
```
W przypadku niepomyślnej autentykacji zwrócone zostanie 405. Jeśli będzie się próbowało dodać kategorię, która nie istnieje, zwrócone zostanie 418. Jeśli wszystko potoczy się pomyślnie, zwrócone zostanie 200.

## 9. Podkategorie:
### 9.1. Tworzenie nowej podkategorii:

```
/api/subcategory/new
```
W requeście post przesłać `name` - nazwę podkategorii i `category` - id kategorii, pod którą ma się ona znajdować.
W przypadku niepodania którejś z tych danych zwrócony zostanie status 400 i error message.
W przypadku podania id kategorii, której nie ma w bazie, zwrócony zostanie status 418.
Jeśli wszystko potoczy się pomyślnie, zwrócone zostanie 201.

## 10. Rekomendacje:
### 10.1. Rekomendowane podkategorie:

```
/api/user/recommended_subcategories
```
Request POST. Zwraca listę podkategorii przypisanych do kategorii zalogowanego usera posortowaną według ich popularności.

### 10.2 Rekomendacje użytkownikóœ:

```
/api/user/recommendations
```
Zwraca listę użytkowników dopasowanych do zalogowanego użytkownika wraz z procentowym stopniem ich dopasowania.
