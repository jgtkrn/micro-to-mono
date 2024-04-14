<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

## Development Guide

### Branch
- development server	: develop
- production server		: master
- contibution branch	: follow branch format bellow

### Branch Rule
Prefix			:
- bugfix		: used when fixing bugs, errors, etc.
- feature		: used when develop new feature.
- hotfix		: used when fix missing parts, codes, or revisions.

Branch Format	:
prefix/ticket-name
^^^ this format will be called ticket-branch

Example 		:
feature/TICKET-000-add-new-feature

### Flow

Installing		:
composer install --ignore-platform-reqs
request .env from current developer

Running Repo	:
php artisan serve

Developing Repo	:
- run "git checkout ticket-branch"
- developing
- run "./vendor/bin/duster fix" to auto fixing fixable errors and bad syntax
- run "./vendor/bin/duster lint"
- revise unfixable errors and syntax
- run "git add -A"
- run "git commit -m BRANCH-CODE: what things worked in", for example "git commit -m TICKET-000: add new feature"
- run "git push origin ticket-branch"
- pull request from ticket-branch to develop
- fill the description format and request for review code from the developer