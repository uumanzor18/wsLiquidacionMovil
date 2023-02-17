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
                $contador=0;
                $items1 = json_encode($items);
                $itemsArray = json_decode($items1);
                writeActionLog("items recibidos a array", print_r($items1, true));


                // if (isset($itemsArray['items'])) {
                //     writeActionLog("items recibidos a array", print_r($itemsArray['items'], true));
                // } else {
                //     $return = array(
                //         "codigo"=> 104,
                //         "mensaje"=> 'Lista de objetos items[] vacia'
                //     );
                // }
                
               

                // foreach ($itemsArray as $key => $value) {
                //     $contador++;
                //     writeActionLog("Variables Recibidas", $contador);
                // }
            }else {
                $return = array(
                    "codigo"=> 104,
                    "mensaje"=> 'Lista de objetos items vacia'
                );
                return $return;
            }
            // $this->DB2->select('*');
            // $this->DB2->from('ordenes_items');
            // //$this->DB2->where('id', 25);
            // $query = $this->DB2->get();

            // if ($query->num_rows() > 0) {
            //     return $query->result_array();
            // }else {
            //     return 0;
            // }
        }
    }
?>