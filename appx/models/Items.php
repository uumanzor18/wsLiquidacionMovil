<?php
    class Items extends CI_Model{
        function __construct()
        {
            $this->load->database();
            $this->load->helper('utils');
            $this->DB2 = $this->load->database('postgres', TRUE);
        }

        function creacionItems($workorder, $actividad, $items)
        {
            if (!empty($items)) {
                $itemsArray = json_decode($items, true);
                $itemsInsert = array();
                $resultadoValFalse = 0;
                if (isset($itemsArray['items'])) {
                    foreach ($itemsArray['items'] as $key => $value) {
                        $unidadMedida = $value['unidadMedida'];
                        $action = $value['accion'];
                        $nombreItem = $value['item'];
                        $cant = $value['cant'];

                        if ($action == "SI" && $unidadMedida != "") {
                            $resultadoValFalse++;
                        }elseif ($action == "NO" && $unidadMedida == "") {
                            $resultadoValFalse++;
                        }

                        $valores = array(
                            "numeroorden"=>$workorder,
                            "item"=>$nombreItem,
                            "cantidad"=>$cant,
                            "unidadmedida"=>$unidadMedida,
                            "accion"=>$action
                        );

                        // if ($cant > 0 && $action == "NO") {
                        //     array_push($itemsInsert, $valores);
                        // }
                        if ($unidadMedida != "" && $cant > 0) {
                            array_push($itemsInsert, $valores);
                        }elseif ($unidadMedida == "") {
                            array_push($itemsInsert, $valores);
                        }
                    }

                    writeActionLog("Estado de validacion acumulador", $resultadoValFalse." ,".print_r($itemsInsert, true));
                    if ($resultadoValFalse > 0) {
                        $return = array(
                            "status"=> 102,
                            "mensaje"=> 'Error de semantica, uno o mas items no pasaron las validaciones de acciones.'
                        );
                    } else {
                        $resultado = $this->DB2->insert_batch('ordenes_items', $itemsInsert);
                        if (!$resultado) {
                            $error = $this->DB2->error();
                            $return = array(
                                "status"=> 104,
                                "mensaje"=> $error
                            );
                        }else {
                            $return = array(
                                "status"=> 100,
                                "mensaje"=> 'Exito'
                            );
                        }
                    }
                } else {
                    $return = array(
                        "status"=> 104,
                        "mensaje"=> 'Lista de objetos items[] vacia'
                    );
                }
            }else {
                $return = array(
                    "status"=> 104,
                    "mensaje"=> 'Lista de objetos items vacia'
                );
            }

            return $return;
        }
    }
?>