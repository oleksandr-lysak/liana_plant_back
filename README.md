# Master Directory Platform

This project is a full-featured platform for managing and browsing a **catalog of professionals (masters)** such as hairdressers, beauticians, and other service providers. It includes a modern web interface and a RESTful API for mobile applications.

## 🧱 Tech Stack

- **Backend**: [Laravel](https://laravel.com)  
- **Frontend**: [Vue.js](https://vuejs.org) + [Inertia.js](https://inertiajs.com)  
- **API for mobile app**: Separate Laravel-based project ([See Mobile API Repository](#))  
- **Containers**: [Docker](https://www.docker.com)

## 📦 Features

- User registration and authentication
- Profile management for service providers (photo, description, specialization, location)
- Search and filtering by location and specialization
- Booking management system
- Admin dashboard for managing content and users
- API integration for mobile clients (separate repo)
- Multi-language support
- Responsive and mobile-friendly design

## 📱 Mobile API

The API used by the mobile application is hosted in a separate repository.  
It includes endpoints for registration, authentication, fetching masters, booking, and more.

👉 [View Mobile API Repository](#)

## 🐳 Dockerized Environment

The entire application is containerized using Docker for easy deployment and development.

### Containers

- `nginx`: Web server
- `php-fpm`: Laravel backend
- `node`: Vue.js frontend (for building assets)
- `mysql`: Database
- `redis`: Queue & caching
- `mailhog`: Local email testing
- `scheduler/worker`: For Laravel jobs and scheduling
