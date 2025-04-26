
# Laravel CRUD Generator For API

**A lightweight Laravel package to generate CRUD files quickly and efficiently.**  
This is **not** an admin panel generator — instead, it creates clean CRUD operations based on the **repository pattern** (without interfaces).  
With this package, you can generate a full CRUD setup **without writing a single line of code**.

---

## Requirements

- PHP 8.1 or higher
- Laravel 10 or higher

---

## Installation

Install the package via Composer:

```bash
composer require ahmed-hussain70/crud-generator:dev-main
```

---

## Configuration

Publish the package views (optional, if you want to customize them):

```bash
php artisan vendor:publish --provider="ahmed-hussain70\crud-generator\CrudGeneratorServiceProvider" --tag=views
```

---

## Usage

Generate a complete CRUD (Model, Controller, Repository, Service, Api Route) by running:

```bash
php artisan make:repo-crud {name}
```

Replace `{name}` with your desired entity name (e.g., `Product`, `Order`, etc.).

---

## Notes

- This package follows the repository pattern **without requiring interfaces**.
- Generate API files **not views**
- Generated files are clean, minimal, and ready to use.
- No complex setup — simply install, configure (optional), and generate.

---

<!-- ## License

This package is open-sourced software licensed under the [MIT license](LICENSE). -->
