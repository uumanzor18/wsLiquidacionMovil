<?php 
	class cableModem extends CI_Model{
		
		function __construct(){
			$this->load->database();
			$this->load->helper('utils');
		}

		function resetCm($datos){
			$P_RESULT;
			$P_CODRESULT;
			$parametros = [
				[
					'name'=> ':P_CS', 
					'value' => $datos["cs"],
					'type' => SQLT_CHR,
					'length' => 100

				],
				[
					'name'=> ':P_CMMAC', 
					'value' => $datos["cm_mac"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_RESULT', 
					'value' => &$P_RESULT,
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_CODRESULT', 
					'value' => &$P_CODRESULT,
					'type' => SQLT_CHR,
					'length' => 100
				]
			];

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "resetCm", $parametros);

			$envio = ["cod" => $P_CODRESULT, "message" => $P_RESULT];

			if (!$result) {
				$error = $this->db->error();
				return ["cod" => $P_CODRESULT, "message" => $error['message']];
			}
			return $envio;
		}
	}
