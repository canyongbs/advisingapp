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