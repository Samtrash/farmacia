<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    public function index()
    {
        // Si ya hay sesión activa, ir a ventas
        if ($this->session->get('usuario')) {
            return redirect()->to(base_url('ventas'));
        }

        // Verificar licencia
        $db = db_connect();
        $k = $db->query("CALL Chk_K()")->getRow();
        $db->close();

        if ($k && $k->error == 1) {
            return view('auth/activar');
        }

        return view('auth/login');
    }

    public function login()
    {
        $user = $this->request->getPost('user');
        $pass = $this->request->getPost('pass');

        if (!$user || !$pass) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Complete todos los campos']);
        }

        $db = db_connect();

        // Login via Stored Procedure
        $a = $db->query("CALL Login(?, ?)", [$user, $pass])->getRow();
        $db->close();

        if (!$a || $a->tmaster == 0) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Usuario o contraseña incorrectos']);
        }

        // Obtener datos del usuario y negocio
        $db2 = db_connect();
        $b = $db2->query("CALL Get_N(?)", [$user])->getRow();
        $db2->close();

        // Guardar en sesión
        $this->session->set([
            'master'  => $a->tmaster,
            'usuario' => $b->us,
            'h1'      => $b->rubro,
            'h2'      => $b->nombre,
            'h3'      => $b->direccion,
            'h4'      => $b->sede,
            'h5'      => $b->ruc,
            'exp'     => $b->expira,
            'pie'     => $b->foot,
        ]);

        // Verificar caja del día
        $db3 = db_connect();
        $c = $db3->query("CALL Rev_C()")->getRow();
        $db3->close();

        $redirect = ($c->ini == 0) ? 'caja' : 'ventas';

        return $this->response->setJSON([
            'error'    => 0,
            'msg'      => 'Bienvenido, ' . $b->us,
            'redirect' => base_url($redirect),
            'needCaja' => ($c->ini == 0),
        ]);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('/'));
    }

    public function abrirCaja()
    {
        $inicial = $this->request->getPost('inicial');

        if ($inicial === null || $inicial === '') {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Ingrese el monto inicial']);
        }

        $db = db_connect();
        $d = $db->query("CALL Set_C(?)", [$inicial])->getRow();
        $db->close();

        if ($d->error == 0) {
            return $this->response->setJSON(['error' => 0, 'redirect' => base_url('ventas')]);
        }

        return $this->response->setJSON(['error' => 1, 'msg' => 'Error al abrir la caja']);
    }
}
