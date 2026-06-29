<?php

namespace App\Controllers;

class AjustesController extends BaseController
{
    /**
     * Solo superusuario (master == 3) puede acceder.
     */
    private function checkSuper()
    {
        if (session('master') != 3) {
            return redirect()->to(base_url('ventas'));
        }
        return null;
    }

    public function index()
    {
        if ($r = $this->checkSuper()) return $r;

        $db = db_connect();
        $negocio  = $db->query("SELECT * FROM negocio LIMIT 1")->getRow();
        $licencia = $db->query("CALL Chk_K()")->getRow();
        $db->close();

        return view('ajustes/index', [
            'titulo'   => 'Ajustes del Sistema',
            'css'      => ['09_ajustes.css'],
            'js'       => ['09_ajustes.js'],
            'negocio'  => $negocio,
            'licencia' => $licencia,
        ]);
    }

    public function guardarNegocio()
    {
        if ($r = $this->checkSuper()) return $r;

        $nombre    = $this->request->getPost('nombre');
        $rubro     = $this->request->getPost('rubro');
        $direccion = $this->request->getPost('direccion');
        $sede      = $this->request->getPost('sede');
        $ruc       = $this->request->getPost('ruc');
        $foot      = $this->request->getPost('foot');

        if (!$nombre || !$rubro || !$direccion || !$sede) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Complete los campos obligatorios']);
        }

        $db = db_connect();
        $db->table('negocio')->where('id', 1)->update([
            'nombre'    => $nombre,
            'rubro'     => $rubro,
            'direccion' => $direccion,
            'sede'      => $sede,
            'ruc'       => $ruc,
            'foot'      => $foot,
        ]);
        $db->close();

        // Refrescar datos del negocio en sesión
        $session = session();
        $session->set([
            'h1' => $rubro,
            'h2' => $nombre,
            'h3' => $direccion,
            'h4' => $sede,
            'h5' => $ruc,
            'pie' => $foot,
        ]);

        return $this->response->setJSON(['error' => 0, 'msg' => 'Datos del negocio actualizados correctamente']);
    }

    public function guardarLicencia()
    {
        if ($r = $this->checkSuper()) return $r;

        $clave   = trim($this->request->getPost('licencia') ?? '');
        $soporte = trim($this->request->getPost('soporte') ?? '');

        if (!$clave) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Ingrese la clave de licencia']);
        }

        $db = db_connect();
        $data = ['licencia' => $clave];
        if ($soporte) {
            $data['soporte'] = $soporte;
        }
        $db->table('negocio')->where('id', 1)->update($data);
        $db->close();

        return $this->response->setJSON(['error' => 0, 'msg' => 'Licencia actualizada. El sistema verificará el estado al reiniciar sesión.']);
    }
}
