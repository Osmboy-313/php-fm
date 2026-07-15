# Osmboy PHP Framework

A lightweight PHP framework built from scratch to explore how modern backend frameworks work internally.

Rather than relying on existing frameworks like Laravel or Symfony, this project implements the core building blocks myself, including routing, middleware, authentication, validation, request handling, and dynamic module loading—to gain a deeper understanding of the request lifecycle.

> ⚠️ **Note**
> This project was built primarily for learning and experimentation. It is not intended to replace production frameworks, but to demonstrate my understanding of backend architecture and framework internals.

---

# Why I Built This

Most developers learn how to *use* frameworks.

I wanted to learn **how frameworks work.**

Instead of treating routing, middleware, dependency loading, authentication, and validation as black boxes, I implemented them myself to understand how requests flow through a backend application.

The goal wasn't to recreate Laravel feature-for-feature, it was to understand the engineering decisions behind modern backend frameworks.

---

# What This Framework Implements

## Request Routing

- Resource-based REST routing
- URL parsing and validation
- Route configuration
- Resource actions
- Query parameter parsing

---

## Middleware Pipeline

- Before-request middleware
- Configurable middleware execution
- Dependency loading
- Authentication middleware
- Authorization middleware
- Request interception

---

## Authentication

- JWT Access Tokens
- JWT Refresh Tokens
- Refresh Token Rotation
- Secure token verification
- HTTP-only cookies

---

## Validation

- Rule-based validation
- Custom validation rules
- Username validation
- Email validation
- Length validation
- Unique field validation
- Password confirmation

---

## Security

- CSRF Tokens
- Password Hashing
- Prepared Statements
- JWT Signature Verification

---

## Dynamic File Loading

Instead of hardcoding file paths throughout the project, the framework scans the project once, builds an internal registry, and dynamically loads controllers, services, middleware, repositories, and utilities when they're needed.

This keeps routing configuration clean and avoids repetitive `require_once` statements.

---

# Request Lifecycle

```text
                     Incoming HTTP Request
                              │
                              ▼
                     Parse & Validate URL
                              │
                              ▼
                     Match Resource Route
                              │
                              ▼
                  Load Required Framework Files
                              │
                              ▼
                 Execute Before Middleware
                              │
                              ▼
                  Load Required Controlllers, business logic and etc defined in the routes
                              │
                              ▼
                    Dispatch Controller
                              │
                 ┌────────────┴────────────┐
                 │                         │
                 ▼                         ▼
          Standard Request         Resource Action
                 │                         │
                 ▼                         ▼
            Service Layer         Action Dispatcher
                 │                         │
                 └────────────┬────────────┘
                              ▼
                    Repository / Database
                              │
                              ▼
                      Business Logic
                              │
                              ▼
                         Controller
                              │
                              ▼
                  Execute After Middleware
                              │
                              ▼
                      Build JSON Response
                              │
                              ▼
                        Send Response
```

---

# Internal Framework Flow

```text
                       Incoming HTTP Request
                                │
                                ▼
                         REST Router Entry
                                │
                                ▼
                       Parse Incoming URL
                                │
                                ▼
                     Validate Resource Route
                                │
                    ┌───────────┴────────────┐
                    │                        │
             Invalid Request           Valid Request
                    │                        │
                    ▼                        ▼
           Build Error Response      Load Required Files
                    │                        │
                    ▼                        ▼
                  Exit             Run Before Middleware
                                             │
                                ┌────────────┴────────────┐
                                │                         │
                        Middleware Failed        Middleware Passed
                                │                         │
                                ▼                         ▼
                     Build Error Response      Dispatch Controller
                                │                         │
                                ▼                         ▼
                              Exit          ┌─────────────┴─────────────┐
                                            │                           │
                                            ▼                           ▼
                                    Standard Request          Resource Action
                                            │                           │
                                            ▼                           ▼
                                     Service Layer         Action Dispatcher
                                            │                           │
                                            └─────────────┬─────────────┘
                                                          ▼
                                                  Repository Layer
                                                          │
                                                          ▼
                                                    Database
                                                          │
                                                          ▼
                                                 Business Logic
                                                          │
                                                          ▼
                                                     Controller
                                                          │
                                                          ▼
                                            Execute After Middleware
                                                          │
                                                          ▼
                                                Build JSON Response
                                                          │
                                                          ▼
                                                     Send Response
```
---
# Project Structure

```
root/
│
├── core/
│   ├── Loader/
│   │   └── Dynamic file registry & loader
│   │
│   ├── Routers/
│   │   ├── REST Router
│   │   └── RPC Router
│   │
│   ├── Middleware/
│   │   └── Middleware execution pipeline
│   │
│   ├── Actions/
│   │   └── Action dispatcher
│   │
│   └── Http/
│       ├── Request
│       ├── Response
│       └── Exception handling
│
├── utils/
│   ├── JWT
│   ├── Validator
│   ├── CSRF
│   └── User Agent Parser
│
└── bootstrap.php
```

---

# Core Features

| Feature | Description |
|----------|-------------|
| REST Router | Resource-based routing with configurable endpoints |
| Middleware | Before-request middleware pipeline |
| Dynamic Loader | Registry-based file loading without hardcoded paths |
| JWT Authentication | Access & Refresh Tokens with rotation |
| Validation Engine | Rule-based input validation |
| CSRF Protection | Session-based CSRF token generation |
| Request Parsing | URL and query parameter parsing |
| Structured Responses | Consistent JSON response format |
| Action Dispatcher | Configurable resource actions |

---

# Example Resource Configuration

Resources are configured declaratively.

Instead of manually wiring controllers, middleware, validation, and actions, each resource describes its behavior in a configuration array.

```php
$routes = [
    "category" => [

        "controller" => "CategoryController",

        "service" => "CategoryService",

        "repository" => "CategoryRepository",

        "handler" => "handleCategory",

        "middleware" => [

            "before" => [

                "AuthMiddleware" => [

                    "dependencies" => [
                        "AuthService"
                    ],

                    "functions" => [

                        [
                            "func" => "session_require_login",
                            "args" => true
                        ]

                    ]

                ]

            ]

        ],

        "actions" => [

            "bulk" => [

                "count" => [
                    "GET",
                    ["ActionService", "countTotalRecords"],
                    []
                ]

            ]

        ]

    ]
];
```

---

# Technologies

- PHP
- MySQL
- JWT
- REST APIs
- Procedural PHP
- MVC Architecture

---

# What I Learned

Building this framework helped me understand:

- How routing systems work internally
- How middleware pipelines execute requests
- How JWT authentication is implemented
- Refresh token rotation
- Dynamic module loading
- Request/response lifecycles
- Validation engines
- Backend architecture
- Modular application design

---

# Future Improvements

Some ideas I'd like to explore in the future:

- Dependency Injection Container
- PSR Compliance
- Composer Autoloading
- Event System
- Service Providers
- Unit Testing
- Better Exception Handling
- Caching Layer
- Rate Limiting
- Logging
- CLI Commands

---

# Disclaimer

This framework was built as a personal engineering project to better understand backend architecture.

Its purpose is educational and experimental rather than production use, but the concepts implemented here mirror many of the building blocks found in modern PHP frameworks.
