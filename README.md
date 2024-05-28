# MonolitDemoApp

## Kluczowe cele i wymagania

1. Aplikacja API JSON zbudowana z użyciem frameworka Symfony.
2. **Architektura: monolit z podziałem na moduły.**
3. **Moduły wydzielane według granic funkcjonalności (czasowniki).**
4. Dane w bazie danych grupowane według użycia w modułach (prefiksy w nazwach tabel).
5. **Moduły jawnie deklarują interfejsy i klasy, które magą być używane przez inne moduły.**
6. **CQRS w formie podstawowej (bez event-ów).**
7. Zero CRUD, zero PUT/PATCH/DELETE.
8. Jedna baza danych, z logicznym podziałem na moduły, dla wielu klientów (multi tenant), z wyróżnionym klientem nadrzędnym (może on mieć dostęp do danych innych klientów)
9. Duże pokrycie testami jednostkowymi.
10. **Automatyczna kontrola, czy przestrzegane są reguły dostępu międzymodułowego.**

## Cele taktyczne (niższej rangi)

1. Wykorzystanie Symfony Serializer i Symfony Validator do przetwarzania danych wejściowych przyjmowanych przez API.
2. Wykorzystanie gotowych rozwiązań do tworzenia dokumentacji API na podstawie struktur danych przygotowanych dla Serializer-a i ograniczeń (constraint) potrzebnych dla Validator-a.
3. Ograniczenie setter-ów i getter-ów do niezbędnego minimum.
4. Użycie Symfony Security. Co teoretycznie powinno dać w miarę łatwą możliwość użycia gotowych rozwiązań do łączenia z różnymi dostawcami tożsamości (np. OAuth2, SAML).

## Uruchomienie:

    composer install
    composer run-script apidoc

    docker compose up -d

    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console admin:init
    symfony server:start

Przykładowe query:

    curl --location 'http://127.0.0.1:8000/order/address/list' --header 'Authorization: Bearer user-1'

    curl --location 'http://127.0.0.1:8000/order/address/1' --header 'Authorization: Bearer user-1'

    curl --location 'http://127.0.0.1:8000/order/order/1' --header 'Authorization: Bearer user-1'

Przykładowe command:

    curl --location 'http://127.0.0.1:8000/order/address/create' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer admin' \
    --data '{
    "customerId": 2,
    "externalId": "W3",
    "nameCompanyOrPerson": "Acme Company Warehouse 3",
    "address": "ul. Wschodnia",
    "city": "Poznań",
    "zipCode": "61-001"
    }'

    curl --location 'http://127.0.0.1:8000/order/order/send' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer user-1' \
    --data '{
    "orderId": 1
    }'

    curl --location 'http://127.0.0.1:8000/order/order/print-label' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer user-1' \
    --data '{
    "orderId": 1
    }'

Sprawdzenie kodu i reguł dostępu międzymodułowego:

    composer run-script check
