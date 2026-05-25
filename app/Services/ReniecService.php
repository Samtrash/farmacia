<?php

namespace App\Services;

class ReniecService
{
    private $token;

    public function __construct()
    {
        $this->token = 'apis-token-9364.q5h4NiCt4-SkqtHSg3IJWsmyEJvE-Zh5';
    }

    public function buscarDNI($dni)
    {
        try {
            $url = "https://api.apis.net.pe/v2/reniec/dni?numero=" . $dni;

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->token,
                    'Referer: https://apis.net.pe/consulta-dni-api'
                ],
            ]);

            $response = curl_exec($curl);

            if(curl_errno($curl)){
                return [
                    'status' => 3,
                    'msg' => 'Error CURL: ' . curl_error($curl)
                ];
            }

            curl_close($curl);

            $persona = json_decode($response);

            if(!$persona || !isset($persona->nombres)){
                return [
                    'status' => 2,
                    'msg' => 'DNI no encontrado'
                ];
            }

            $nombre = ucwords(strtolower(
                $persona->nombres . ' ' .
                $persona->apellidoPaterno . ' ' .
                $persona->apellidoMaterno
            ));

            return [
                'status' => 0,
                'data' => [
                    'nombres' => $nombre
                ]
            ];

        } catch (\Exception $e) {
            return [
                'status' => 3,
                'msg' => $e->getMessage()
            ];
        }
    }
}