<?php
class Actividades extends CI_Model{

  function __construct(){
    $this->load->database();
    $this->load->helper('utils');
  }

  function listarActividades(){
	
    $P_CURSOR = $this->db->get_cursor();
    $parametros = [[
      'name'=> ':p_cursor',
      'value' => &$P_CURSOR,
      'type' => OCI_B_CURSOR,
      'length'=> -1
      ]];

      try{
        $data=null;
        $result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "listaActividades", $parametros);
        $response = array();
        if (!$result) {
          $error = $this->db->error();
          return [false, $error["message"]];
        }
        else{
          if (ociexecute($P_CURSOR)) {
            while (($row = $data = oci_fetch_assoc($P_CURSOR)) != false) {
                  $temporal  = array("ACTIVIDAD" => preg_replace('!\s+!', '',$row["ACTIVIDAD"]) , "NOMBRE" => preg_replace('!\s+!', '',$row["NOMBRE"]) );
                  $response [] = $temporal;
              }
              $return["status"] = true;
              $return["response"] = $response;
              //var_dump($return);
              return $return;
          }
          else{
            return [false, "No se ejecuto el cursor"];
          }
        }
      }
      catch (Exception $e) {
        return [false, $e->getMessage()];
      }
    }

	function catalogoCierre($actividad){
		$P_CURSOR = $this->db->get_cursor();
		writeActionLog("Catalogo Cierre /act ". $actividad, "");
    	$parametros = [
			[
        		'name'=> ':p_actividad', 
        		'value' => $actividad,
        		'type' => SQLT_CHR,
        		'length' => 100
      		],
      		[
      			'name'=> ':p_cursor',
      			'value' => &$P_CURSOR,
      			'type' => OCI_B_CURSOR,
      			'length'=> -1
	  		]
		];

      	try{
        	$data=null;
        	$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "catalogoCierre", $parametros);
        	$response = array();
        	if (!$result) {
          		$error = $this->db->error();
          		return [false, $error["message"]];
        	}
        	else{
          		if (ociexecute($P_CURSOR)) {
            		while (($row = $data = oci_fetch_assoc($P_CURSOR)) != false) {
                  		if (array_key_exists($row["ACTIVIDAD"], $response)) {
                    			$response[$row["ACTIVIDAD"]][] = [
                          			"CIERRE" => $row["IDCIERRE"],
                          			"NOMBRE" => $row["NOMBRE"]
								];
                  		}
                  		else{
							$response[$row["ACTIVIDAD"]][] = [
								"CIERRE" => $row["IDCIERRE"],
								"NOMBRE" => $row["NOMBRE"]
							];
							
                  		}
					  }
					writeActionLog("Catalogo Cierre /act ". print_r($response, true), "");
              		$return["status"] = true;
              		$return["response"] = $response;
              		//var_dump($return);
              		return $return;
          		}
          		else{
            		return [false, "No se ejecuto el cursor"];
          		}
        	}
      	}
      	catch (Exception $e) {
        	return [false, $e->getMessage()];
      	}
	}

	function actualizarCS($id, $campo, $valor, $tecnico){
		$p_respuesta;
		$parametros = [
			[
				'name'=> ':P_CS',
				'value' => $id,
				'type' => SQLT_CHR,
				'length' => 100

			],
			[
				'name'=> ':P_CAMPO', 
				'value' => $campo,
				'type' => SQLT_CHR,
				'length' => 100
			],
			[
				'name'=> ':P_VALOR', 
				'value' => $valor,
				'type' => SQLT_CHR,
				'length' => 1000
			],
			[
				'name'=> ':P_TECNICO', 
				'value' => $tecnico,
				'type' => SQLT_CHR,
				'length' => 1000
			],
			[
			  'name'=> ':p_respuesta',
			  'value' => &$p_respuesta,
			  'type' => SQLT_INT,
			  'length' => -1
			]
		];
		try {
			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "actualizarCS", $parametros);
			switch ($p_respuesta) {
				case 1:
				  $envio = ["status" => true, "response" => "Operacion Exitosa"];
				break;
				case 2:
					$envio = ["status" => false, "response" => "id del contrato servicio ".$id." no existe"];
				break;
				default:
					$envio = ["status" => false, "response" => $p_respuesta];
                break;
			}
			if (!$result) {
				$error = $this->db->error();
				return ["status"=>false, "response" =>$error["message"]];
			}
			else{
				return ["status"=>true, "response" => "Operacion Exitosa"];
			}
		} catch (Exception $e) {
			return [false, $e->getMessage()];
		}
	}
}
  ?>
