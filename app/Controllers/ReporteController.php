<?php

namespace App\Controllers;

class ReporteController extends BaseController
{
    public function index()
    {
        return view('reportes/index', [
            'titulo' => 'Reportes de Ventas',
            'css'    => [],
            'js'     => ['08_reporte.js'],
        ]);
    }

    public function buscar()
    {
        $mes  = $this->request->getPost('mes');
        $anio = $this->request->getPost('anio');

        if (!$mes || !$anio) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Seleccione mes y año']);
        }

        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $db = db_connect();

        $ventas = $db->table('venta')
            ->select("id, fecha, DATE_FORMAT(fecha, '%d/%m/%Y') as fecha_fmt,
                CONCAT(CASE formato WHEN 3 THEN 'B' WHEN 1 THEN 'F' ELSE 'N' END, LPAD(id,4,'0')) as numero_recibo,
                formato, total, atiende", false)
            ->where('MONTH(fecha)', $mes)
            ->where('YEAR(fecha)', $anio)
            ->orderBy('fecha', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()->getResult();

        // Agrupar por fecha y calcular totales
        $agrupado = [];
        foreach ($ventas as $v) {
            $agrupado[$v->fecha][] = $v;
        }

        // Info de caja por día
        $resumenDias = [];
        foreach ($agrupado as $fecha => $grupo) {
            $totalDia = array_sum(array_column($grupo, 'total'));
            $cajaIni = $db->table('caja')->where('fecha', $fecha)->get()->getRow();
            $inicial = $cajaIni ? $cajaIni->inicial : 0;
            $resumenDias[$fecha] = [
                'totalDia'    => $totalDia,
                'cajaInicial' => $inicial,
                'efectivo'    => $totalDia + $inicial,
            ];
        }

        return $this->response->setJSON([
            'error'       => 0,
            'titulo'      => $meses[$mes] . ' del ' . $anio,
            'ventas'      => $agrupado,
            'resumenDias' => $resumenDias,
        ]);
    }

    public function editarCaja($fecha)
    {
        $inicial = $this->request->getPost('inicial');

        if ($inicial === null || !is_numeric($inicial)) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Ingrese un monto válido']);
        }

        $db = db_connect();
        $result = $db->query("CALL Upd_C(?,?)", [$fecha, $inicial])->getRow();
        $db->close();

        if ($result && $result->error == 0) {
            return $this->response->setJSON(['error' => 0, 'msg' => 'Caja actualizada correctamente']);
        }
        return $this->response->setJSON(['error' => 1, 'msg' => 'Error al actualizar caja']);
    }
}
