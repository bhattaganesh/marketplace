# Online Shopping Website Setup Guide

## Prerequisites

- PHP with required extensions installed
- MySQL or a compatible database system
- Composer (PHP package manager)
- Git for version control
- SSL certificate for your domain

## Setup Instructions

### 1. Database Creation

To create the database, execute the following SQL command:

```sql
CREATE DATABASE online_shopping_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Clone the Repository

To clone the project repository and navigate into its directory, execute the following commands:

```bash
git clone git@github.com:bhattaganesh/marketplace.git
cd marketplace
```


### 3. Environment Configuration

1. Duplicate the sample environment file:

```bash
cp .env.example .env
```

```env
DB_HOST=localhost
DB_NAME=online_shopping_db
DB_USER=root
DB_PASS=''
DB_CHARSET=utf8mb4
JWT_SECRET_KEY=124349ecee8b43320f5d19d254b9e4b624936354d3c628ba3b14fe21789b9069
DOMAIN=https://marketplace.test
```

Ensure you adjust DB_USER, DB_PASS, and DOMAIN to fit your local environment.

### 4. Install Dependencies

To fetch and install the necessary PHP packages for the project, run the following command in your terminal:

```bash
composer install
```

### 5. SSL Requirement

For the secure operation of the project, it's essential to have an SSL certificate set up for the domain specified in the `DOMAIN` environment variable. If you haven't already, ensure you:

1. Obtain an SSL certificate, either by purchasing one or obtaining a free certificate from providers like [Let's Encrypt](https://letsencrypt.org/).
2. Install and configure the SSL certificate on your server or hosting platform according to its documentation.
3. Verify that your website is accessible via `https://` and that browsers recognize the certificate as valid.

Failure to set up SSL correctly might lead to security issues or diminished functionality.
