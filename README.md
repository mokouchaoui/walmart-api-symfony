# Symfony Walmart Integration App

## Table of Contents

- [Overview](#overview)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Usage](#usage)
  - [Running the Application](#running-the-application)
  - [Adding an Item](#adding-an-item)
  - [Fetching Items](#fetching-items)
- [Key Files and Classes](#key-files-and-classes)
- [Error Handling](#error-handling)
- [Credits](#credits)

## Overview

This is a Symfony-based application designed for integrating with the Walmart Marketplace API. It offers two main functionalities:

1. **Adding Items to Walmart Marketplace**: The `ProductController` allows you to add new items to the Walmart Marketplace.
  
2. **Fetching Items from Walmart Marketplace**: The `WalmartController` allows you to fetch existing items from the Walmart Marketplace.

## Prerequisites

- PHP 7.2 or above
- Symfony 5.x
- Composer
- Guzzle HTTP Client

## Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/your-repo/symfony-walmart-app.git
    ```

2. **Navigate into the project directory**

    ```bash
    cd symfony-walmart-app
    ```

3. **Install dependencies using Composer**

    ```bash
    composer install
    ```

## Usage

### Running the Application

Start the Symfony development server:

```bash
symfony server:start
```

### Controllers

ProductController.php: Manages the logic for adding new items to the Walmart Marketplace.

WalmartController.php: Manages the logic for fetching existing items from the Walmart Marketplace.

### Service

WalmartAuthService.php: Handles authentication and token management for Walmart API requests.
Error Handling
This application includes extensive error-handling features, including HTTP error catching and user-friendly alert messages via Flash messages.


## Symfony Framework

Guzzle HTTP Client
For any further queries or concerns, please contact the maintainers.
