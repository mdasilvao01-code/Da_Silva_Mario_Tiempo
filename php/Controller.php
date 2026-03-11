<?php
require_once __DIR__ . '/Model.php';

class Controller {

    private $model;

    public function __construct() {
        $this->model = new Model();
    }

    public function buscarCiudad($busqueda) {
        $resp = file_get_contents("https://api.openweathermap.org/geo/1.0/direct?q=" . urlencode($busqueda) . "&limit=5&appid=" . API_KEY);
        return json_decode($resp, true);
    }

    public function actual($lat, $lon, $ciudad, $pais) {
        $resp = file_get_contents("https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&units=metric&lang=es&appid=" . API_KEY);
        $d = json_decode($resp, true);
        $this->model->guardar($ciudad, $pais, $lat, $lon, 'actual');
        return [
            'temp'    => round($d['main']['temp']),
            'sens'    => round($d['main']['feels_like']),
            'hum'     => $d['main']['humidity'],
            'viento'  => round($d['wind']['speed'] * 3.6, 1),
            'desc'    => ucfirst($d['weather'][0]['description']),
            'icono'   => $d['weather'][0]['icon'],
            'presion' => $d['main']['pressure'],
            'visib'   => isset($d['visibility']) ? round($d['visibility'] / 1000, 1) : '-',
        ];
    }

    public function horas($lat, $lon, $ciudad, $pais) {
        $resp = file_get_contents("https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&cnt=8&units=metric&lang=es&appid=" . API_KEY);
        $d = json_decode($resp, true);
        $this->model->guardar($ciudad, $pais, $lat, $lon, 'horas');
        $horas = $temps = $lluvias = $descs = $iconos = [];
        foreach ($d['list'] as $e) {
            $horas[]   = date('H:i', $e['dt']);
            $temps[]   = round($e['main']['temp'], 1);
            $lluvias[] = round(($e['pop'] ?? 0) * 100);
            $descs[]   = ucfirst($e['weather'][0]['description']);
            $iconos[]  = $e['weather'][0]['icon'];
        }
        return compact('horas', 'temps', 'lluvias', 'descs', 'iconos');
    }

    public function semana($lat, $lon, $ciudad, $pais) {
        $resp = file_get_contents("https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&units=metric&lang=es&appid=" . API_KEY);
        $d = json_decode($resp, true);
        $this->model->guardar($ciudad, $pais, $lat, $lon, 'semana');
        $dias = [];
        foreach ($d['list'] as $e) {
            $dia = date('Y-m-d', $e['dt']);
            if (!isset($dias[$dia])) $dias[$dia] = ['temps' => [], 'desc' => $e['weather'][0]['description'], 'icon' => $e['weather'][0]['icon'], 'pop' => []];
            $dias[$dia]['temps'][] = $e['main']['temp'];
            $dias[$dia]['pop'][]   = $e['pop'] ?? 0;
        }
        $nombres = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
        $labels = $maxs = $mins = $pops = [];
        foreach ($dias as $fecha => $info) {
            $labels[] = $nombres[date('w', strtotime($fecha))] . ' ' . date('d/m', strtotime($fecha));
            $maxs[]   = round(max($info['temps']), 1);
            $mins[]   = round(min($info['temps']), 1);
            $pops[]   = round(max($info['pop']) * 100);
        }
        return compact('dias', 'labels', 'maxs', 'mins', 'pops');
    }

    public function historial() {
        return [
            'consultas' => $this->model->historial(),
            'total'     => $this->model->total(),
            'tipos'     => $this->model->porTipo(),
        ];
    }
}
