# Da_Silva_Mario_Tiempo
# Weather App 🌤

Aplicación web en PHP para consultar el tiempo atmosférico de cualquier ciudad del mundo usando la API de OpenWeatherMap.

## Funcionalidades

- Búsqueda de ciudades por nombre
- Tiempo actual (temperatura, humedad, viento, presión)
- Previsión por horas (cada 3h durante el día)
- Previsión semanal (5 días con máximas y mínimas)
- Historial de consultas guardado en base de datos
- Gráficas interactivas con Chart.js

## Tecnologías

- PHP 8.2
- MariaDB 11
- Chart.js
- Docker / Docker Compose
- API: [OpenWeatherMap](https://openweathermap.org/api)

## Instalación

### Requisitos
- Docker y Docker Compose instalados
- API Key gratuita de [openweathermap.org](https://openweathermap.org/api)

### Pasos

1. Clona el repositorio
```bash
   git clone https://github.com/TU_USUARIO/weather-app.git
   cd weather-app
```

2. Pon tu API key en `config.php`
```php
   define('API_KEY', 'key');
```

3. Levanta los contenedores
```bash
   docker compose up -d --build
```

4. Abre el navegador en `http://localhost`

## Estructura del proyecto
```
weather-app/
├── index.php        # Búsqueda de ciudades
├── actual.php       # Tiempo actual
├── horas.php        # Previsión por horas
├── semana.php       # Previsión semanal
├── historial.php    # Historial de consultas
├── config.php       # Configuración y conexión BD
├── schema.sql       # Esquema de la base de datos
├── Dockerfile
└── docker-compose.yml
```

## Base de datos

Una tabla `consultas` que registra cada búsqueda realizada:

| Campo   | Tipo        | Descripción              |
|---------|-------------|--------------------------|
| id      | INT         | Identificador            |
| ciudad  | VARCHAR     | Nombre de la ciudad      |
| pais    | VARCHAR     | Código de país           |
| lat     | DECIMAL     | Latitud                  |
| lon     | DECIMAL     | Longitud                 |
| tipo    | ENUM        | actual / horas / semana  |
| fecha   | DATETIME    | Fecha de la consulta     |

## Despliegue en AWS

1. Lanza una instancia EC2 (Ubuntu 22.04, t2.micro)
2. Abre el puerto 80 en el Security Group
3. Instala Docker en la instancia
4. Sube el proyecto y ejecuta `docker compose up -d --build`
5. Accede desde `http://IP_PUBLICA_EC2`
