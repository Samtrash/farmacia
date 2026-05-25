<?php

namespace App\Controllers;

class UsuarioController extends BaseController
{
    public function index()
    {
        return view('usuarios/index', [
            'titulo' => 'Gestión de Usuarios',
            'css'    => [],
            'js'     => ['07_usuario.js'],
        ]);
    }

    // AJAX: listar todos
    public function listar()
    {
        $db = db_connect();
        $usuarios = $db->table('usuario')
            ->join('persona', 'persona.id = usuario.persona_id')
            ->select('persona.id as persona_id, persona.dni_ruc, persona.nombre_razon, persona.apellidos, usuario.pass, usuario.master')
            ->orderBy('persona.id')
            ->get()->getResult();

        return $this->response->setJSON($usuarios);
    }

    public function crear()
    {
        $master = $this->request->getPost('master');
        $dni    = $this->request->getPost('dni_ruc');
        $pass   = $this->request->getPost('pass');
        $nombre = $this->request->getPost('nombre_razon');
        $apell  = $this->request->getPost('apellidos');

        if (!$dni || !$pass || !$nombre || !$apell) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Complete todos los campos']);
        }

        $db = db_connect();
        $result = $db->query("CALL Add_U(?,?,?,?,?)", [$master, $dni, $pass, $nombre, $apell])->getRow();
        $db->close();

        if ($result->error == 1) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'El DNI ya existe en el sistema']);
        }

        return $this->response->setJSON(['error' => 0, 'msg' => 'Usuario agregado: ' . $nombre . ' ' . $apell]);
    }

    public function editar($id)
    {
        $master = $this->request->getPost('master');
        $dni    = $this->request->getPost('dni_ruc');
        $pass   = $this->request->getPost('pass');
        $nombre = $this->request->getPost('nombre_razon');
        $apell  = $this->request->getPost('apellidos');

        $db = db_connect();
        $result = $db->query("CALL Upd_U(?,?,?,?,?,?)", [$id, $master, $dni, $pass, $nombre, $apell])->getRow();
        $db->close();

        if ($result->error == 0) {
            return $this->response->setJSON(['error' => 0, 'msg' => 'Usuario actualizado correctamente']);
        }
        return $this->response->setJSON(['error' => 1, 'msg' => 'Error al actualizar']);
    }

    public function eliminar($id)
    {
        $db = db_connect();
        $result = $db->query("CALL Del_U(?)", [$id])->getRow();
        $db->close();

        if ($result->error == 0) {
            return $this->response->setJSON(['error' => 0, 'msg' => 'Usuario eliminado correctamente']);
        }
        return $this->response->setJSON(['error' => 1, 'msg' => 'Error al eliminar']);
    }
}
