# MonolitDemoApp

## Kluczowe cele i wymagania

1. Aplikacja API JSON zbudowana z użyciem frameworka Symfony.
2. Architektura: monolit z podziałem na moduły.
3. Moduły wydzielane według granic funkcjonalności (czasowniki).
4. Dane w bazie danych grupowane według użycia w modułach.
5. Moduły jawnie deklarują interfejsy i klasy, które magą być używane przez inne moduły.
6. CQRS w czystej postaci.
7. Zero CRUD, zero PUT/PATCH/DELETE.
8. Jedna baza danych, z logicznym podziałem na moduły, dla wielu klientów (multi tenant), z wyróżnionym klientem nadrzędnym (może on mieć dostęp do danych innych klientów)
9. Duże pokrycie testami jednostkowymi.
10. 
