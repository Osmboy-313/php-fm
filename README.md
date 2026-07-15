# Osmboy PHP Framework

A lightweight PHP framework built from scratch to explore how modern backend frameworks work internally.

Rather than relying on existing frameworks like Laravel or Symfony, this project implements the core building blocks myself, including routing, middleware, authentication, validation, request handling, and dynamic module loading to gain a deeper understanding of the request lifecycle.

> **Note**
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
                  Load Route Dependencies
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
                        Service Layer
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
                                                    Service Layer
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

# Design Philosophy

- The framework is intentionally configuration-driven.
- Rather than hardcoding controllers, middleware, validation rules, and actions throughout the application, resources describe their behavior declaratively through configuration arrays.
- The framework interprets those configurations and coordinates the request lifecycle automatically.
- This approach reduces repetitive boilerplate while keeping application logic modular.

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
$rules = [
    "auth" => [
        "register" => [
            "username" => "required|username|range:3-32|unique",
            "email" => "required|email|unique",
            "user_type" => "required",
            "password" => "required|range:8-16|", // this trailing pipe "|" won't cause problems btw, just wanted to clarify ;)
            "confirmPassword" => "required|range:8-16|same:password"
        ],
        "login" => [
            "username" => "required",
            "email" => "required",
            "password" => "required",
        ]
    ],
    "category" => [

    ]
];

// No need for big boy if/else statements for data validation and sanitization, just write rules in this way and done, username, email rules just do regex checks, and unique one checks for availability!

$actions = [
    "single" => [
        "validate" => ["POST", ["api/Services/ActionService", "isUnique"], []],
    ],
    "bulk" => [
        "count" => ["GET", ["api/Services/ActionService", "countTotalRecords"], []],
        "login" => ["POST", ["api/Services/ActionService", "handleLogin"], []],
        "refreshjwt" => ["POST", ["api/Services/AuthService", "handleRefreshToken"], []],
    ]    
];

// The Keys inside the single and bulk in the $actions are the actions names we're gonna use in endpoins like "api/category/count"
// bulk means it will work when no id's are involved "api/category/{id}/count" -> it won't work it will only work when there's no id
// Single means it will work with id's, "api/category/{id}/validate"
// The structure of values that the ation keys should store, means: ["Request Method", ["File", "Function"], ["dependency1", "dependency1"]] or no dependency at al!

$middlewareRegistry = [
    "AuthMiddleware" => ["api/middleware/AuthMiddleware", ["api/Services/AuthService"]],
    "CSRFMiddleware" => ["api/middleware/CsrfMiddleware", []],
    "JwtMiddleware" => ["api/middleware/JwtMiddleware", ["api/Services/JwtService"]],
];

// Middlewares keys must be the same in the $routes for calling functions. In the register it contains the files, ["Main File", ["other dependency files"]] or none.

$routes = [
    "categories" => [
        "controller" => "api/Controllers/CategoryController",
        "handler" => "handleCategory", // ->> Function in the controller
        "service" => "api/Services/CategoryService",
        "middleware" => [
            "before" => [
                "CSRFMiddleware" => [
                    ["verifyCSRF", []],
                //  ["function Name", ["args as many"]],
                ],
                "JwtMiddleware" => [
                    ["verifyJwtAccessToken", []], // No args needed here, so leave it empty as-is
                ],
            ],
            "after" => [
                
            ]
        ],
        "actions" => search_r($actions, "validate", "count"), // This Magic function will just search for the said keys in array and return with structure of $action as-is but with said keys!
        "rules" => $rules["category"], // no rules defined for category, right now it won't be a problem but I'd fix it and make it stricter

    ]
];

Aight! so when everything is done, we're left with $routes and $middlewareRegister, we have to pass both these and the dirname(__DIR__) to the router, so it can load files and call functions! that's it! 

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

## Engineering Concepts Explored

Building this framework required implementing several backend concepts from scratch:

- Request lifecycle management
- Resource-based routing
- Middleware execution pipelines
- JWT authentication and refresh token rotation
- Dynamic module loading
- Validation engine
- HTTP request/response handling
- Modular backend architecture

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
