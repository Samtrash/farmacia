<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model
{
    protected $table         = 'producto';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'codigo', 'detalle', 'contiene',
        'pcompra', 'pventa', 'ppromo',
        'stock', 'expmes', 'expanio',
        'negocio_id',
    ];
}
