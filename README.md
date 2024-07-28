# Bumbu: A Symfony Analog of Lombok

Welcome to Bumbu! This library aims to simplify your Symfony development by providing powerful annotations that automate repetitive tasks. Here you will find detailed documentation on how to install, configure, and use Bumbu in your Symfony projects.

---

## Table of Contents

1. [Introduction](#introduction)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Attributes](#attributes)
   - [RequestBody](#requestbody)
   - [Valid](#valid)
   - [Getter](#getter)
   - [Setter](#setter)
5. [Usage Examples](#usage-examples)
6. [Contributing](#contributing)
7. [License](#license)

---

## Introduction

Bumbu is a Symfony library inspired by Lombok, designed to reduce boilerplate code in your projects. With Bumbu, you can use custom attributes to streamline common tasks such as request handling, validation, and getter/setter generation.

## Installation

To install Bumbu, use Composer:

```bash
composer require dkart/bumbu:dev-main
```

## Configuration
After installing Bumbu, you need to enable it in your Symfony project.

1. Add Bumbu to bundles.php:

```php
return [
   // Other bundles
   DKart\Bumbu\BumbuBundle::class => ['all' => true],
];
```

2. Update Doctrine Configuration in config/packages/doctrine.yaml:
```yaml
doctrine:
    orm:
        class_metadata_factory_name: DKart\Bumbu\Doctrine\CustomClassMetadataFactory
```

## Attributes
Bumbu provides several useful attributes to make your Symfony development smoother:

### RequestBody
The RequestBody attribute maps the request body directly to a parameter in your controller action.

Usage:
```php
use DKart\Bumbu\Attribute\RequestBody;

class MyController extends AbstractController
{
    #[Route('/endpoint', name: 'endpoint', methods: ['POST'])]
    public function myAction(#[RequestBody] MyRequestDto $dto)
    {
        // $dto will be automatically filled with data from the request body
    }
}
```
### Valid
The Valid attribute triggers validation on the given parameter. It is often used together with the RequestBody attribute to validate data automatically when it is mapped from the request body.
```php
use DKart\Bumbu\Attribute\RequestBody;
use DKart\Bumbu\Attribute\Valid;

class MyController extends AbstractController
{
    #[Route('/endpoint', name: 'endpoint', methods: ['POST'])]
    public function myAction(#[RequestBody, Valid] MyRequestDto $dto)
    {
        // The $dto parameter will be populated from the request body and validated automatically
    }
}
```
### Getter
The Getter attribute generates a getter method for a specified property.
```php
use DKart\Bumbu\Attribute\Getter;

class MyEntity
{
    #[Getter]
    private $property;

    // Bumbu will create a getProperty() method
}
```
### Setter
The Setter attribute generates a setter method for a specified property.
```php
use DKart\Bumbu\Attribute\Setter;

class MyEntity
{
    #[Setter]
    private $property;

    // Bumbu will create a setProperty($value) method
}
```

## Usage Examples
### Example 1: Using RequestBody and Valid
```php
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class MyRequestDto
{
    #[NotBlank]
    #[Length(min: 3)]
    private string $name;

    #[NotBlank]
    #[Type(type: 'integer')]
    private int $age;

    #[NotBlank]
    #[Email]
    private string $email;
}
```
```php
use DKart\Bumbu\Attribute\RequestBody;
use DKart\Bumbu\Attribute\Valid;

class MyController extends AbstractController
{
    #[Route('/submit', name: 'submit', methods: ['POST'])]
    public function submit(#[RequestBody, Valid] MyRequestDto $dto)
    {
        // Handle the validated request DTO
    }
}
```
### Example 2: Using Get and Set
```php
use DKart\Bumbu\Attribute\Get;
use DKart\Bumbu\Attribute\Set;

class User
{
    #[Getter]
    #[Setter]
    private $username;

    #[Getter]
    private $email;

    #[Setter]
    private $password;

    // Bumbu will generate getUsername(), setUsername($value), getEmail(), and setPassword($value) methods
}
```
**Note**: To use the User class with the generated getter and setter methods, you should create an instance of it through dependency injection (DI) where applicable.

```php
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SomeController extends AbstractController
{
    public function someAction(User $user): Response
    {
        $username = $user->getUsername();
        // Use $username and other methods as needed

        return new Response('User username is ' . $username);
    }
}
```
## Contributing
I welcome contributions to Bumbu! If you want to help out, please follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Implement your changes and add tests.
4. Submit a pull request with a clear description of what you’ve done.

### License
Bumbu is licensed under the MIT License. See the LICENSE file for more details.

____
Thanks for using Bumbu! I hope it makes your Symfony development easier. If you have any questions or feedback, feel free to open an issue on the GitHub repository.