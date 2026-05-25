<?php

namespace App\Controllers;
use App\Models\CiudadModel;

class CiudadController extends BaseController
{
    private $cm;

    public function __construct(){
        $this->cm = new CiudadModel();
    }
    
    public function index(){
        return view('vista/ciudad', [
            'titulo' => 'Ciudad'
        ]);
    }

    public function listar(){
        try{
            $filtro = $this->request->getGet('filtro');
            $buscar = trim($this->request->getGet('buscar'));

            $builder = $this->cm;

            // BUSQUEDA
            if($buscar !== ''){
                $builder = $builder->like('lugar', $buscar);
            }

            // FILTRO
            if($filtro == "1"){
                $builder = $builder->orderBy('lugar', 'ASC');
            }else{
                $builder = $builder->orderBy('id', 'DESC'); // orden original
            }

            $data = $builder->findAll();

            return $this->response->setJSON($data);

        }catch(\Throwable $e){
            return $this->response->setJSON([]);
        }
    }

    public function create(){
        try{
            $lugar = trim($this->request->getPost('lugar'));
            $lugar = ucfirst(strtolower($lugar));

            if($lugar === ''){
                return $this->response->setJSON([1, "La ciudad es obligatoria"]);
            }

            $id = $this->cm->insert([
                'lugar' => $lugar
            ]);

            if(!$id){
                return $this->response->setJSON([
                    2,
                    "Error insert",
                    $this->cm->errors(), // ERROR REAL
                    $this->cm->db->error() // SQL ERROR
                ]);
            }

            return $this->response->setJSON([0, "Se agregó correctamente: ".$lugar]);

        }catch(\Throwable $e){
            return $this->response->setJSON([3, "Error del sistema"]);
        }
    }

    public function update($id){
        try{
            if(!$this->cm->find($id)){
                return $this->response->setJSON([2, "Registro no existe"]);
            }

            $lugar = trim($this->request->getPost('lugar'));
            $lugar = ucfirst(strtolower($lugar));

            if($lugar === ''){ return $this->response->setJSON([1, "La ciudad es obligatoria"]); }

            if(!$this->cm->update($id, [
                'lugar' => $lugar
            ])){
                return $this->response->setJSON([2, "No se pudo actualizar"]);
            }

            return $this->response->setJSON([0, "Se actualizó correctamente: ".$lugar]);

        }catch(\Throwable $e){
            return $this->response->setJSON([3, "Error del sistema"]);
        }
    }
    
    public function delete($id){
        try{
            if(!$this->cm->find($id)){
                return $this->response->setJSON([2, "El registro no existe"]);
            }

            if(!$this->cm->delete($id)){
                return $this->response->setJSON([2, "No se pudo eliminar"]);
            }

            return $this->response->setJSON([0, "Se eliminó correctamente"]);

        }catch(\Throwable $e){
            return $this->response->setJSON([3, "Error FK: No se puede eliminar por integridad del sistema."]);
        }
    }
}