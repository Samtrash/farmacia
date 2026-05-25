<?php

namespace App\Controllers;

class DevolucionController extends BaseController
{
    public function migrar()
    {
        $db = db_connect();
        
        $sql = "CREATE TABLE IF NOT EXISTS devolucion (
          id INT NOT NULL AUTO_INCREMENT,
          venta_id INT NOT NULL,
          producto_id INT NOT NULL,
          cantidad INT NOT NULL,
          monto DECIMAL(10,2) NOT NULL,
          fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          INDEX (venta_id),
          INDEX (producto_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        $db->query($sql);

        $sqlProc = "DROP PROCEDURE IF EXISTS Devolver_Producto;";
        $db->query($sqlProc);

        $sqlProc2 = "CREATE PROCEDURE Devolver_Producto(
          IN p_venta_id INT,
          IN p_producto_id INT,
          IN p_cantidad INT,
          IN p_monto DECIMAL(10,2)
        )
        BEGIN
          DECLARE v_hoy DATE;
          SET v_hoy = CURDATE();
        
          -- 1. Registrar la devolucion
          INSERT INTO devolucion (venta_id, producto_id, cantidad, monto)
          VALUES (p_venta_id, p_producto_id, p_cantidad, p_monto);
        
          -- 2. Sumar stock al producto
          UPDATE producto 
          SET stock = stock + p_cantidad
          WHERE id = p_producto_id;
        
          -- 3. Restar dinero de la caja de hoy (si existe caja abierta)
          UPDATE caja 
          SET dinero = dinero - p_monto 
          WHERE fecha = v_hoy;
        
          SELECT 0 AS error, 'Devolución procesada correctamente' AS msg;
        END;";
        $db->query($sqlProc2);

        return $this->response->setJSON(['status' => 'Migración exitosa']);
    }

    public function procesar()
    {
        $venta_id    = $this->request->getPost('venta_id');
        $producto_id = $this->request->getPost('producto_id');
        $cantidad    = $this->request->getPost('cantidad');
        $monto       = $this->request->getPost('monto');

        try {
            $db = db_connect();
            $result = $db->query("CALL Devolver_Producto(?,?,?,?)", [
                $venta_id, $producto_id, $cantidad, $monto
            ])->getRow();
            $db->close();

            if ($result && $result->error == 0) {
                return $this->response->setJSON(['error' => 0, 'msg' => $result->msg]);
            }
            return $this->response->setJSON(['error' => 1, 'msg' => 'No se pudo procesar la devolución']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }
}
