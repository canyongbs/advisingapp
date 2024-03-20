# CanyonGBS Development Traefik Setup

## Introduction

This document describes the setup of Traefik for the CanyonGBS development environment.

## Prerequisites

- Docker
- Docker Compose

## Setup

1. Clone the repository
2. Create the Docker network required for Traefik
    ```bash
    docker network create -d bridge cgbs-development
    ```
3. Start the Traefik container
    ```bash
    docker-compose up -d
    ```
   
## Self-Signed Certificate Renewal

1. Create a new key
   ```bash
   openssl genrsa -out cgbs-local-dev-key.pem 2048
   ```
2. Create a new certificate signing request
   ```bash
   openssl req -new -out server.csr -key cgbs-local-dev-key.pem -config openssl.cnf
   ```
   > Note: Choose all defaults but the Common Name should be `*.advisingapp.local`
3. Create a new certificate
   ```bash
   openssl x509 -req -days 3650 -in server.csr -signkey cgbs-local-dev-key.pem -out cgbs-local-dev.pem -extensions v3_req -extfile openssl.cnf
   ```