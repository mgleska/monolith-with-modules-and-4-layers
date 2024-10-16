# Monolith application with modules and 4-layers Architecture

## Key goals and requirements

1. **Architecture: modular monolith.**
2. Modules separated according to functional boundaries (verbs).
3. **4-layers Architecture** applied at module level.
4. Command Query Responsibility Segregation (**CQRS**) in its basic form (without events).
5. Automatic control of whether cross-module access rules and 4-layers Architecture access rules are followed.

## Tactical objectives

1. Application built using the Symfony framework.
2. Using Symfony Serializer and Symfony Validator to process input data received by the API.
3. Using Symfony Security. It allows relatively simple use of ready-made solutions for connecting to various identity providers (e.g. OAuth2, SAML).
4. One database, logically divided into modules, for many clients (multi tenant), with a distinguished parent client (he may have access to other clients' data).
5. Zero CRUD, zero PUT/PATCH/DELETE.
6. Using ready-made solutions to create API documentation based on data structures prepared for the Serializer and constraints needed for the Validator.
7. Limiting setters and getters to the necessary minimum.
8. High coverage by unit tests.

## 4-layers Architecture

<img src="docs/img/4-layers-architecture.svg" alt="architecture layers">

### General principles of 4-layers Architecture

* Each module implements set of domain actions.
* Some of the implemented actions are exported.
* Only exported actions can be used by other modules or by "external world" (by API/CLI/Message/Event/direct call).
* Each module holds its own set of data in storage (e.g. database). Only the module has direct access to its data.
* Other modules and "external world" can access and manipulate module's data only by calling actions exported by module.


### Layer 1:Connector

In layer 1:Connector module defines and implements methods which do translation between "external language" (for example: HTTP POST call with JSON structure as input data) and actions defined in layer 2:Export.

Typical connectors:
* method for handling the HTTP endpoint call with JSON data,
* method for handling the HTTP endpoint call with HTTP form data,
* method for handling the command line call,
* method for handling messages incoming from queue (RabbitMQ, SQS),
* method for handling messages incoming from pub-sub topic (Kafka),
* method for handling event emitted by framework (Symfony).

General rule for layer 1:Connector is, that component from this layer can **use** only:
* actions and structures defined at layer 2:Export of given module,
* supplementary classes and structures defined at layer 1:Connections of given module,
* classes and objects from framework and `vendors/*`.

Usually there is no need to define connector for external call from other module in the application.\
The reason is simple - other module can **use** action definition form layer 2:Export of given module and supply input data in form required by the action (as: value with PHP built-in type, DTO, Value Object).


### Layer 2:Export

This layer contains everything what given module exposes to "external world".

Exposed artifacts could be:
* interfaces of actions,
* data structures for input and output for actions (DTOs),
* enums used in DTOs,
* value objects used in DTOs,
* any other PHP language structures.

It is important to note, that 2:Export layer **defines** but **not implement** actions.

General rule for layer 2:Export is, that component from this layer can **use** only:
* interfaces and structures defined in own layer,
* structures defined at layers 3 and 4 with restriction, that used structures are not exposed to "external world"
* classes and objects from framework and `vendors/*`.


### Layer 3:Action

**It is the most important layer in 4-layers Architecture.**

In layer 3:Action module implements **domain actions** which are core responsibility of the module and **domain models** owned by the module.

Each action covers part of business domain and holds (encapsulates) business rules specific for given action.

**An important feature of 4-layers Architecture is, that it focuses on actions. It is worth to note it.**

Good practise is, that implemented actions follow Command Query Responsibility Segregation (**CQRS**) pattern.\
So any action have to be one of:
* command,
* query.

CQRS pattern do not force using events or other complicated patterns.\
In basic form it requires only, that **each command has own model** (DTO from layer 2:Export) which holds input data for command.\
Received model is validated against domain rules suitable for given command and then processed as required by business rules. Result of processing can be: data stored/updated in database, call for other action, computed value, etc.\
Similarly for query - CQRS requires, that each query produce a data as **model specific for this query** (DTO from layer 2:Export).

Good practice is to implement action as one class, with one public method and private supplementary methods.\
If it is required, action can **use** any supplementary methods and structures defined at layers 2-4 of given module and layer 2 of any other module.

Second core responsibility of layer 3:Action is to define and maintain domain models - entities and aggregates.

An important feature of the **4-layers Architecture** is, that **other modules do not have direct access to models** of given module.\
It gives us high level of isolation and prevents data which belongs to given module from direct manipulation by other modules.

General rule for layer 3:Action is, that component from this layer can **use**:
* methods and structures defined at layers 2-4 in given module,
* actions and structures exported (defined at layer 2:Export) by other modules,
* classes and objects from framework and `vendors/*`.


### Layer 4:Infrastructure

Layer 4:Infrastructure is a place, where module defines and implements classes, methods and structures used to perform common supplementary services required by domain actions from layer 3:Action.

Good example of such supplementary service is database access and manipulation implemented by class `Repository`.\
`Repository` implements general access methods (e.g. `find()`, `findBy()`) and also methods specific for corresponding `Entity` (e.g. `changeStatus()`, `getStatus()`).\
It is worth to note, that `Entity` class name do not match 1-to-1 name of table in database. For example class `Order` is mapped to table `ord_order`. This approach allow to group tables of the module by common prefix (`ord_` in this example).

General rule for layer 4:Infrastructure is, that component from this layer can **use** only:
* actions and structures defined at own layer,
* structures defined at layer 2:Export of given module,
* actions and structures exported (defined at layer 2:Export) by other modules,
* classes and objects from framework and `vendors/*`.

## Some implementation details

### Directory structure

At level 1 below `src/` directory, each directory represents a module.\
In this example implementation we have:
```text
src/
   Admin/
   Api/
   Auth/
   Customer/
   Order/
   Printer/
```

At level 2 below `src/` directory, each directory represents a layer in module.\
In module Printer we have example of layer directories named in classic way. As you see, typical IDE or file tool will display directories in alphabetical order.
```text
src/
   ...
   Printer/
      Action/
      Connector/
      Export/
      Infrastructure/
```
For better visualization of layers and its relation, for modules other than Printer, layer directories are named with numeric prefix:
```text
src/
   ...
   Order/
      _1_Connector/
      _2_Export/
      _3_Action/
      _4_Infrastructure/
   ...
```
Extra underscore before digit is required to fulfill PSR-4 rules and PHP `namespace` requirements.

### Module boundary checking

With proposed directory structure we can quite easy check if **4-layers Architecture** rules are preserved.

Supplied program `tools/moduleBoundaryChecker.php` does such check.

Probably we can also use other tools designed for architecture checking - for example `qossmic/deptrac`.

## "Zero CRUD, zero PUT/PATCH/DELETE" - why?

At first reading of this sentence you may think "This guy is crazy! He rejects the foundations of REST API."

Here is the explanation of this architectural decision.

I saw a lot of projects, where domain model is mapped 1-to-1 to database table end exposed directly to external world (mostly frontend SPA).

With such design, frontend SPA can directly manipulate model stored in database - by changing whole entity (PUT action) or only part of the entity (PATCH action).\
Orders coming from frontend SPA contain commands PUT/PATCH, that the backend and database should execute without hesitation.\
Source of the orders is a user, which makes decision based on what he/she see on the screen.

This approach is valid for application designed for direct database manipulation (like phpMyAdmin) or simple blogpost.

Main drawback of this approach is, that the user makes a decision based on data which **was** in database at moment when data was sent to presentation layer. Ignoring the fact, that the same entity/model at the moment the user makes a decision **may already be different**.

Some projects simply ignore this inconsistency between user view and real application state.\
Other project tries to solve this problem - with more or less successful results.

Suppose we build application for transportation of domestic shipments.\
Users see on the screen shipment in state "NEW". Then user goes to grab a coffee.\
Whe he comes back, he decides to send shipment for acceptance by carrier. So he chooses on the screen new shipment state "SENT" and press "Submit" button. Frontend sends PATCH request with "{state = SENT}".\
But it was urgent shipment. In the meantime, another user sent the shipment for acceptance and the carrier responded with confirmation for execution. And current shipment state is "CONFIRMED".

Should we accept PATCH request from coffee lover and change shipment state to "SENT"?\
Technically - yes, it is fundamental rule of REST/PATCH.\
From business point of view - definitely not.

We can easily avoid such dilemmas if we abandon PUT/PATCH/DELETE and focus on CQRS approach with **actions**.

Let's go back to domestic shipments example.\
User comes back with coffee and sends command "I would like to send this shipment for acceptance". By POST request.\
Backend checks command pre-condition and see, that shipment is already confirmed. So backend may answer with user-friendly message with explanation why users' command cannot be done.

This new approach allow to encapsulate domain logic related to the command "I would like to send this shipment for acceptance" in PHP class designed strictly for this one command.


## Usage:

    docker compose up

It will download required images, build custom image for PHP and execute script `start-app.sh` containing commands: 

    composer install
    composer run-script apidoc

    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    php bin/console admin:init

    php -S 0.0.0.0:8000 -t public/

Testing and checking (run the command inside `php-api` container):

    composer run-script test
    composer run-script check

API documentation:

    http://127.0.0.1:8000/api.html
    http://127.0.0.1:8000/api.yaml
    http://127.0.0.1:8000/api.json

Sample query:

    curl --location 'http://127.0.0.1:8000/order/address/list' --header 'Authorization: Bearer user-1'

    curl --location 'http://127.0.0.1:8000/order/address/1' --header 'Authorization: Bearer user-1'

    curl --location 'http://127.0.0.1:8000/order/order/1' --header 'Authorization: Bearer user-1'

Sample command:

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

    curl --location 'http://127.0.0.1:8000/order/order/create' \
    --header 'Content-Type: application/json' \
    --header 'Authorization: Bearer user-1' \
    --data-raw '{
    "loadingDate": "2024-05-19",
    "loadingFixedAddressExternalId": null,
    "loadingAddress": {
    "nameCompanyOrPerson": "Acme Company",
    "address": "ul. Garbary 125",
    "city": "Poznań",
    "zipCode": "61-719"
    },
    "loadingContact": {
    "contactPerson": "Contact Person ",
    "contactPhone": "+48-603-978-106",
    "contactEmail": "person@email.com"
    },
    "deliveryAddress": {
    "nameCompanyOrPerson": "Receiver Company",
    "address": "ul. Wschodnia",
    "city": "Poznań",
    "zipCode": "61-001"
    },
    "deliveryContact": {
    "contactPerson": "Person2",
    "contactPhone": "+48-111-111-111",
    "contactEmail": null
    },
    "lines": [
    {
    "quantity": 3,
    "length": 120,
    "width": 80,
    "height": 100,
    "weightOnePallet": 200,
    "goodsDescription": "computers"
    },
    {
    "quantity": 5,
    "length": 120,
    "width": 80,
    "height": 200,
    "weightOnePallet": 200,
    "goodsDescription": "heavy printers"
    }
    ]
    }'

## Image for Kubernetes:

    docker build -f docker/Dockerfile -t app-kubernetes --target app-kubernetes .
