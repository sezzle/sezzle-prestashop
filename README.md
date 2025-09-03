<div align="center">
    <a href="https://sezzle.com">
        <img src="https://media.sezzle.com/branding/2.0/Sezzle_Logo_FullColor.svg" width="300px" alt="Sezzle" />
    </a>
</div>


# Prestashop Docker Installation Guide

This repository contains a Docker Compose setup for running PrestaShop with MySQL database and phpMyAdmin for database management.

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- [Docker](https://docs.docker.com/get-docker/)

## Start the services
   ```bash
   docker-compose up -d
   ```

## Configuration Details

#### Database Configuration
- **Host**: `presta-mysql`
- **Database Name**: `prestashop`
- **Username**: `root`
- **Password**: `admin`
- **Port**: `3306`

#### PrestaShop Configuration
- **Admin Folder**: `backoffice`
- **Install Folder**: `install-ps`
- **Web Access**: `http://localhost:8090`

## Installation Steps

1. **Start the containers**
   ```bash
   docker-compose up -d
   ```

2. **Wait for services to initialize**
   - MySQL may take a few minutes to fully start
   - Check container status: `docker-compose ps`

3. **Access PrestaShop**
   - Navigate to `http://localhost:8090/install-ps`


4. **Complete PrestaShop Installation**
   - Follow the installation wizard
   - Use the database credentials provided above
   - Create your admin account
   - Configure your store settings

5. **Access Admin Panel**
   - After installation, access admin at: `http://localhost:8090/backoffice`
   - Use the credentials you created during installation

## Sezzle module for PrestaShop

Documentation for the Sezzle payment module can be found on <a href="https://docs.sezzle.com/docs/plugins/prestashop">docs.sezzle.com</a>.