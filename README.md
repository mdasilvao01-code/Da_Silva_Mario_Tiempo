# Aplicación del Tiempo

Aplicación web desarrollada en PHP que permite consultar el tiempo atmosférico de cualquier ciudad del mundo, utilizando la API de OpenWeatherMap. Los datos de las consultas se almacenan en una base de datos MariaDB y la aplicación está desplegada en AWS con Docker.

---

## Funcionalidades

- Búsqueda de ciudades mediante la API de geolocalización de OpenWeatherMap
- Si la ciudad no existe, se muestra un mensaje de error
- Consulta del tiempo actual de la ciudad seleccionada
- Previsión meteorológica por horas (próximas 24h)
- Previsión meteorológica semanal
- Gráficas interactivas con Chart.js para visualizar los datos
- Historial de todas las consultas realizadas, almacenadas en base de datos

---

## Estructura del proyecto (MVC)

El proyecto sigue el patrón **Modelo - Vista - Controlador (MVC)**:

```
tiempo-mvc/
├── php/
│   ├── config.php          # Configuración: API key y conexión a BD
│   ├── Model.php           # Modelo: acceso a la base de datos
│   ├── Controller.php      # Controlador: lógica y llamadas a la API
│   ├── index.php           # Vista: búsqueda de ciudad
│   ├── actual.php          # Vista: tiempo actual
│   ├── horas.php           # Vista: previsión por horas
│   ├── semana.php          # Vista: previsión semanal
│   └── historial.php       # Vista: historial de consultas
├── sql/
│   └── Tiempo.sql          # Script de creación de la base de datos
├── Dockerfile
├── docker-compose.yml
```

- **Model.php** → gestiona todas las operaciones con la base de datos (guardar y consultar registros)
- **Controller.php** → realiza las llamadas a la API de OpenWeatherMap y prepara los datos
- **Vistas (*.php)** → reciben los datos del controlador y solo se encargan de mostrar el HTML

---

## Base de datos

Se utiliza **MariaDB** con una tabla `consultas` que registra cada búsqueda realizada:

```sql
CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ciudad VARCHAR(100),
    pais VARCHAR(10),
    lat DECIMAL(9,6),
    lon DECIMAL(9,6),
    tipo ENUM('actual','horas','semana'),
    fecha DATETIME DEFAULT NOW()
);
```

---

## Despliegue con Docker

El proyecto usa **Docker** y **Docker Compose** con dos servicios:

- `web` → PHP 8.2 con Apache
- `db` → MariaDB 10.6

### Pasos para ejecutar en local

1. Clona el repositorio:
```bash
git clone https://github.com/mdasilvao01-code/Da_Silva_Mario_Tiempo.git
```

2. Levanta los contenedores:
```bash
docker-compose up -d
```

3. Abre el navegador en: [http://localhost:80/](http://localhost:80/)

---

## ☁️ Despliegue en AWS

La aplicación está desplegada en una instancia **EC2 de AWS** y es accesible desde Internet.

### Pasos realizados para el despliegue en AWS

1. Crear una instancia EC2 (debian) en AWS y abrir el puerto 80 en el grupo de seguridad.

2. Conectarse a la instancia por SSH:
```bash
ssh -i "labuser.pem" ubuntu@34.227.123.56
```

3. Instalar Docker y Docker Compose:
```bash
sudo apt update
sudo apt install -y docker.io docker-compose
sudo usermod -aG docker ubuntu
```

4. Clonar el repositorio y levantar los contenedores:
```bash
git clone https://github.com/mdasilvao01-code/Da_Silva_Mario_Tiempo.git
docker-compose up -d
```

5. La aplicación queda accesible en: `http://34.227.123.56/`

> **URL de acceso con ip elastica:** http://34.227.123.56/
> **URL de acceso con el dominio:** http://
---

## API utilizada

- [OpenWeatherMap](https://openweathermap.org/api) — API gratuita de datos meteorológicos
  - Geocoding API → para obtener las coordenadas de una ciudad
  - Current Weather API → para el tiempo actual
  - Forecast API → para la previsión por horas y semanal

---

## Tecnologías utilizadas

- PHP 8.2
- Apache
- MariaDB 10.6
- Chart.js (gráficas)
- Docker & Docker Compose
- AWS EC2
