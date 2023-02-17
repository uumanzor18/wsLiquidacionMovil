<?php
class Ordenes extends CI_Model{

  function __construct(){
    $this->load->database();
    $this->load->helper('utils');
  }

  function crearOrden($workorder,$actividad,$comentario){
    $P_TICKET_APP;
    $envio;
    $P_RESPUESTA_TICKET = 0;
    $parametros = [
      [
        'name'=> ':P_TICKET_APP',
        'value' => intval($workorder),
        'type' => SQLT_INT,
        'length' => -1
      ],
      [
        'name'=> ':P_COMENTARIO',
        'value' => $comentario,
        'type' => SQLT_CHR,
        'length' => 512
      ],
      [
        'name'=> ':P_ACTIVIDAD',
        'value' => $actividad,
        'type' => SQLT_CHR,
        'length' => 6
      ],
      [
        'name'=> ':P_RESPUESTA_TICKET',
        'value' => &$P_RESPUESTA_TICKET,
        'type' => SQLT_INT,
        'length' => -1
      ]
    ];
    $result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "CREAR_TICKET_ACTIVIDAD", $parametros);
    //echo json_encode($result);
    switch ($P_RESPUESTA_TICKET) {
      case -1:
      $envio = ["status" => false, "response" => "Error de Ejecucion", "cod" => -1];
      break;
      case -2:
      $envio = ["status" => false, "response" => "No existe la actividad o no esta autorizada a ser creada desde APP", "cod" => -2];
      break;
      case -3:
      $envio = ["status" => false, "response" => "No se encuentra informacion en ticket_actividad del numero de orden recibido", "cod" => -3];
      break;
      case -4:
      $envio = ["status" => false, "response" => "Datos incompletos para crear nueva orden", "cod" => -4];
      break;
      case -5:
        $envio = ["status" => true, "response" => 1, "cod" => -5, "log"=>"ClaseCliente no asignada a la actividad: ".$actividad];
      break;
      default:
      $envio = ["status" => true, "response" => $P_RESPUESTA_TICKET, "cod" => 200];
      break;
    }
    if (!$result) {
      $error = $this->db->error();
      $envio["response"] = $error['message'];
      return  $envio;
    }
    return $envio;
  }

  function cerrarOrden($orden, $cs, $idCierre){
		$p_respuesta;

    	$parametros = [
			[
        		'name'=> ':p_orden', 
        		'value' => $orden,
        		'type' => SQLT_CHR,
        		'length' => 100
			],
			[
				'name'=> ':p_cs', 
				'value' => $cs,
				'type' => SQLT_CHR,
				'length' => 100
			],
			[
				'name'=> ':p_cierre', 
				'value' => $idCierre,
				'type' => SQLT_CHR,
				'length' => 100
			],
      [
        'name'=> ':p_respuesta',
        'value' => &$p_respuesta,
        'type' => SQLT_INT,
        'length' => -1
      ]
		];

    try{
      $result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "CERRAR_ORDEN", $parametros);
      switch ($p_respuesta) {
        case 0:
          $envio = ["status" => false, "response" => "Error de Ejecucion", "log"=>""];
        break;
        case 2:
          $envio = ["status" => false, "response" => "No se encontro registro para el numero de cierre: ".$idCierre];
        break;
        case 3:
          $envio = ["status" => false, "response" => "la actividad del cierre ".$idCierre." no coincide con la actividad de la orden ".$orden];
        break;
        case 1:
          $envio = ["status" => true, "response" => "Operacion Exitosa"];
        break;
      }
      if (!$result) {
        $error = $this->db->error();
        $envio["log"] = $error['message'];
        return  $envio;
      }
      return $envio;
    }
    catch (Exception $e) {
      return [false, $e->getMessage()];
    }
	}
}

?>
