# Osmboy PHP Framework

> A lightweight, custom-built PHP framework exploring the inner workings of web applications.

## What Is This?

This is my **learning project**, a PHP framework I built from scratch to understand how modern web frameworks work under the hood. It started as a deep dive into routing, middleware, authentication, and SQL query building.

**Is this production-ready?** No.  
**Did I learn a ton building it?** Absolutely.

## What I Learned Building This

- **Routing:** How Laravel/Symfony handle URL mapping behind the scenes
- **Middleware:** The request lifecycle and how to intercept/modify requests
- **JWT Authentication:** Token generation, verification, and refresh token rotation
- **Dependency Management:** Why frameworks need autoloading and DI containers
- **SQL Query Builders:** The complexity of generating dynamic SQL safely
- **MVC Architecture:** Separating concerns in web applications

## Architecture Overview
`
root/
├── core/
│ ├── Loader/ # Custom file autoloader
│ ├── Actions/ # Action/controller dispatcher
│ ├── Middleware/ # Middleware runner
│ ├── Routers/ # Resource based REST and RPC router implementations
│ └── Http/ # Request/Response handling
├── utils/
│ ├── jwt.php # JWT generation and verification
│ ├── validator.php # Form/input validation
│ ├── csrf.php # CSRF token handling
│ └── user-agent.php # Browser/OS detection
└── bootstrap.php # Framework entry point`
`
