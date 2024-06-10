# Monarchs-Quest Backend

Este repositorio contiene el código del backend para el juego web "Monarchs-Quest", desarrollado con Symfony 7 y dockerizado para facilitar su despliegue.

## Requisitos previos

- Docker
- Docker Compose

## Clonación del repositorio

Primero, clona el repositorio en tu máquina local:

```bash
git clone https://github.com/NuMeRo-999/Monarchs-Quest_Back-end.git
cd Monarchs-Quest_Back-end
```
## Despliegue de contenedores
Para construir la imagen y levantar los contenedores de Docker, sigue estos pasos:

Construcción de la imagen y ejecución
```bash
docker compose up --build
```

 # Estructura del proyecto
  - Dockerfile: Configuración para construir la imagen de Docker.
  - docker-compose.yml: Configuración de Docker Compose para levantar los servicios necesarios.
  - entrypoint.sh: Script de entrada para manejar la inicialización del contenedor.

# Acceso a los servicios
  - Backend (Symfony 7): http://localhost:8080
  - Base de Datos (MySQL): puerto 3307 en localhost
  - phpMyAdmin: http://localhost:8081

# Notas adicionales
 - Asegúrate de que los puertos 8080 y 8081 estén libres antes de ejecutar los contenedores.
 - Si necesitas detener y eliminar los contenedores, puedes usar el siguiente comando:
  ```bash
  docker-compose down
  ```
# Advertencia
Para despliegues locales y en producción, asegúrate de configurar las variables de entorno adecuadamente en el archivo `docker-compose.yml`. Por ejemplo, puedes necesitar cambiar `DATABASE_URL` para apuntar a una base de datos diferente o ajustar `APP_ENV` para el entorno de producción.

# Contribuciones
Las contribuciones son bienvenidas. Si deseas contribuir, por favor, crea un fork del repositorio y envía un pull request.
