# MonolitDemoApp

## Kluczowe cele i wymagania

1. Aplikacja API JSON zbudowana z użyciemem frameworka Symofony.
2. Architektura: monolit z podziałem na moduły.
3. Moduły wydzielane według granic funkcjonalności (czasowniki). Dane w bazie danych grupowane według użycia w modułach.
4. Moduły jawnie deklarują interfejsy i klasy, które magą być używane przez inne moduły.
5. CQRS w czytej postaci.
6. Zero CRUD, zero REST.
7. Jedna baza danych, z logicznym podziałem na moduły, dla wielu klientów (multi tenant).
8. Duże pokrycie testami jednostkowymi.
