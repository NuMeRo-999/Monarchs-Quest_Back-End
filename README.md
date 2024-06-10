# Proyecto Final - DAW - Pedro Vílchez Peña

## Tecnologías Utilizadas
- React
- Symfony 7
- Tailwind CSS
- Mysql
- Docker
- Docker Compose
- Nginx
  

## Instrucciones para montar el proyecto

### Configuración Inicial en AWS

1. **Configuración de Puertos en AWS:**
   - Accede a tu consola de administración de AWS.
   - Ve a la sección de `Security Groups` en `EC2`.
   - Abre los siguientes puertos en el grupo de seguridad asociado a tu instancia:
     - HTTP: puerto 80
     - TCP: puerto 3000

2. **Asignar una IP Elástica:**
   - Ve a la sección de `Elastic IPs` en la consola de AWS.
   - Asigna una IP elástica a tu instancia.
   - Anota la IP elástica asignada.

3. **Modificar el Archivo de Configuración:**
   - Clona el repositorio del proyecto:
     ```sh
     git clone https://github.com/AbelMP17/finalproject.git
     ```
   - Navega hasta el directorio del proyecto:
     ```sh
     cd finalproject
     ```
   - Abre el archivo `./src/constants.js` y reemplaza la IP existente con la IP elástica asignada en AWS:
     ```javascript
     // ./src/constants.js
     export const API_URL = 'http://<your-elastic-ip>/api';
     ```

### Instalación de Dependencias

4. **Instalar Dependencias del Proyecto:**
   - Asegúrate de estar en el directorio raíz del proyecto.
   - Ejecuta el siguiente comando para instalar las dependencias:
     ```sh
     npm install
     ```
   - Navega al directorio `backend` y ejecuta el mismo comando:
     ```sh
     cd backend
     npm install
     cd ..
     ```

### Iniciar el Proyecto

5. **Iniciar el Proyecto con Docker:**
   - Desde el directorio raíz del proyecto, ejecuta el siguiente comando para construir y levantar los contenedores Docker:
     ```sh
     docker-compose up -d --build
     ```

### Configuración de la Base de Datos

6. **Credenciales para acceder a la Base de Datos:**
   - Usuario: `root`
   - Contraseña: `root`
