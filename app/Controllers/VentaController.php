<?php

namespace App\Controllers;

use App\Models\ProductoModel;

class VentaController extends BaseController
{
    public function index()
    {
        return view('ventas/index', [
            'titulo' => 'Punto de Venta',
            'css'    => ['05_pos.css'],
            'js'     => ['05_pos.js'],
        ]);
    }

    // Búsqueda por código de barras (exact match)
    public function buscarPorCodigo()
    {
        $code = $this->request->getGet('code');
        $model = new ProductoModel();
        $productos = $model->where('codigo', $code)->orderBy('detalle')->findAll();
        return view('ventas/tabla_resultados', ['productos' => $productos]);
    }

    // Búsqueda por nombre (LIKE)
    public function buscarPorNombre()
    {
        $name = $this->request->getGet('name');
        $model = new ProductoModel();
        $productos = $model->like('detalle', $name)->orderBy('detalle')->findAll();
        return view('ventas/tabla_resultados', ['productos' => $productos]);
    }

    // Procesar venta via SP
    public function vender()
    {
        $formato = $this->request->getPost('formato');
        $dniRuc  = $this->request->getPost('dni_ruc') ?: '';
        $nombre  = $this->request->getPost('nombre_razon') ?: '';
        $atiende = $this->request->getPost('atiende');
        $lot     = $this->request->getPost('lot');
        $array   = $this->request->getPost('array');

        // Validar datos del cliente para boleta/factura
        if ($formato > 0 && ($lot == 0 || empty($dniRuc) || empty($nombre))) {
            return $this->response->setJSON([
                'error' => 1,
                'msg'   => 'Debe ingresar los datos del cliente para emitir un comprobante.'
            ]);
        }

        if ($lot == 0) {
            return $this->response->setJSON([
                'error' => 1,
                'msg'   => 'Debe agregar productos a la venta.'
            ]);
        }

        try {
            $db = db_connect();
            $result = $db->query("CALL Venta_P(?,?,?,?,?,?)", [
                $formato, $dniRuc, $nombre, $atiende, $lot, $array
            ])->getRow();
            $db->close();

            if ($result && $result->error == 0) {
                return $this->response->setJSON([
                    'error' => 0,
                    'msg'   => 'Venta realizada correctamente',
                    'url'   => base_url('ventas/ticket'),
                ]);
            }

            return $this->response->setJSON(['error' => 1, 'msg' => 'Error al procesar la venta. Verifique el stock.']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Imprimir último ticket
    public function ticket()
    {
        $db = db_connect();
        $todoV = $db->query("CALL PrintLast_V()")->getResult();
        $db->close();

        return view('ventas/ticket', ['todoV' => $todoV]);
    }

    // Imprimir ticket por ID
    public function ticketById($id)
    {
        $db = db_connect();
        $todoV = $db->query("CALL PrintById(?)", [$id])->getResult();
        $db->close();

        return view('ventas/ticket', ['todoV' => $todoV]);
    }

    // Anular venta
    public function anular($id)
    {
        try {
            $db = db_connect();
            $result = $db->query("CALL Del_Venta(?)", [$id])->getRow();
            $db->close();

            if ($result && $result->error == 0) {
                return $this->response->setJSON(['error' => 0, 'msg' => 'Venta anulada correctamente']);
            }
            return $this->response->setJSON(['error' => 1, 'msg' => 'No se pudo anular la venta']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 1, 'msg' => $e->getMessage()]);
        }
    }

    // Detalle de venta (para modal en reportes)
    public function detalle($id)
    {
        $db = db_connect();
        $lineas = $db->table('linea')
            ->join('producto', 'producto.id = linea.producto_id')
            ->select('linea.producto_id, producto.detalle, linea.cantidad, linea.precio')
            ->where('venta_id', $id)
            ->get()->getResult();

        $venta = $db->table('venta')->where('id', $id)->get()->getRow();

        return $this->response->setJSON([
            'venta'  => $venta,
            'lineas' => $lineas,
        ]);
    }
}
