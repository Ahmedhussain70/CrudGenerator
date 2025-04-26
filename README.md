# Laravel CRUD Generator

**Laravel Package to generate CRUD files easily.**  
This is **not** an admin panel generator — instead, it is a simple package that generates CRUD operations following the **repository pattern** (without interfaces). It supports full CRUD features **without you needing to write a single line of code**.

---

## Requirements

- PHP 8.1 or higher
- Laravel 10, 11, or 12

---

## Installation

```bash
composer require ahmed-hussain70/crud-generator:dev-main

---

## Configuration

```bash
php artisan vendor:publish --provider="ahmed-hussain70\crud-generator\CrudGeneratorServiceProvider" --tag=views

---

## To run the package

```bash
php artisan make:repo-crud {name}

---