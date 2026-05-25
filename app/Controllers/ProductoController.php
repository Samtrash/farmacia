<?php

namespace App\Controllers;

use App\Models\ProductoModel;

class ProductoController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProductoModel();
    }

    public function index()
    {
        return view('productos/index', [
            'titulo' => 'Gestión de Productos',
            'css'    => [],
            'js'     => ['06_producto.js'],
        ]);
    }

    // AJAX: listar todos
    public function listar()
    {
        $productos = $this->model->orderBy('detalle')->findAll();
        return $this->response->setJSON($productos);
    }

    // AJAX: buscar por nombre
    public function buscar()
    {
        $name = $this->request->getGet('name');
        $productos = $this->model->like('detalle', $name)->orderBy('detalle')->findAll();
        return $this->response->setJSON($productos);
    }

    public function crear()
    {
        $rules = [
            'codigo'  => 'required|max_length[30]',
            'detalle' => 'required|max_length[70]',
            'pcompra' => 'required|numeric',
            'pventa'  => 'required|numeric',
            'stock'   => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['error' => 1, 'msg' => implode(', ', $this->validator->getErrors())]);
        }

        $this->model->insert([
            'codigo'     => $this->request->getPost('codigo'),
            'detalle'    => $this->request->getPost('detalle'),
            'contiene'   => $this->request->getPost('contiene') ?: null,
            'pcompra'    => $this->request->getPost('pcompra'),
            'pventa'     => $this->request->getPost('pventa'),
            'ppromo'     => $this->request->getPost('ppromo') ?: null,
            'stock'      => $this->request->getPost('stock'),
            'expmes'     => $this->request->getPost('expmes') ?: null,
            'expanio'    => $this->request->getPost('expanio') ?: null,
            'negocio_id' => 1,
        ]);

        return $this->response->setJSON(['error' => 0, 'msg' => 'Producto agregado: ' . $this->request->getPost('detalle')]);
    }

    public function editar($id)
    {
        $rules = [
            'codigo'  => 'required|max_length[30]',
            'detalle' => 'required|max_length[70]',
            'pcompra' => 'required|numeric',
            'pventa'  => 'required|numeric',
            'stock'   => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['error' => 1, 'msg' => implode(', ', $this->validator->getErrors())]);
        }

        $this->model->update($id, [
            'codigo'   => $this->request->getPost('codigo'),
            'detalle'  => $this->request->getPost('detalle'),
            'contiene' => $this->request->getPost('contiene') ?: null,
            'pcompra'  => $this->request->getPost('pcompra'),
            'pventa'   => $this->request->getPost('pventa'),
            'ppromo'   => $this->request->getPost('ppromo') ?: null,
            'stock'    => $this->request->getPost('stock'),
            'expmes'   => $this->request->getPost('expmes') ?: null,
            'expanio'  => $this->request->getPost('expanio') ?: null,
        ]);

        return $this->response->setJSON(['error' => 0, 'msg' => 'Producto actualizado correctamente']);
    }

    public function eliminar($id)
    {
        $p = $this->model->find($id);
        if (!$p) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Producto no encontrado']);
        }

        $this->model->delete($id);
        return $this->response->setJSON(['error' => 0, 'msg' => 'Producto eliminado: ' . $p['detalle']]);
    }
}
