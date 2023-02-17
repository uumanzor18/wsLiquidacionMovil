<?php 
	class equipoTvDig extends CI_Model{
		
		function __construct(){
			$this->load->database();
			$this->load->helper('utils');
		}

		function resetCaja($datos){
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
					'name'=> ':P_TARJETA', 
					'value' => $datos["tarjeta"],
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

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "resetCaja", $parametros);

			$envio = ["cod" => $P_CODRESULT, "message" => $P_RESULT];

			if (!$result) {
				$error = $this->db->error();
				$envio["message"] = $error['message'];
				return  $envio;
			}

			return $envio;
		}

		function actualizarCaja($datos){
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
					'name'=> ':P_TARJETA', 
					'value' => $datos["tarjeta"],
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

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "actualizarCaja", $parametros);

			$envio = ["cod" => $P_CODRESULT, "message" => $P_RESULT];

			if (!$result) {
				$error = $this->db->error();
				$envio["message"] = $error['message'];
				return  $envio;
			}
			return $envio;
		}

		function actualizarPaquetes($datos){
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
					'name'=> ':P_TARJETA', 
					'value' => $datos["tarjeta"],
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

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "actualizarPaquetes", $parametros);

			$envio = ["cod" => $P_CODRESULT, "message" => $P_RESULT];

			if (!$result) {
				$error = $this->db->error();
				$envio["message"] = $error['message'];
				$envio["cod"] = "-1";
				return  $envio;
			}

			return $envio;
		}

		function formatEquipo($datos){
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
					'name'=> ':P_TARJETA', 
					'value' => $datos["tarjeta"],
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

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "formatEquipo", $parametros);

			$envio = ["cod" => $P_CODRESULT, "message" => $P_RESULT];

			if (!$result) {
				$error = $this->db->error();
				$envio["message"] = $error['message'];
				return  $envio;
			}

			return $envio;
		}

		function resetTarjeta($datos){
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
					'name'=> ':P_TARJETA', 
					'value' => $datos["tarjeta"],
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

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "resetTarjeta", $parametros);

			$envio = ["cod" => $P_CODRESULT, "message" => $P_RESULT];

			if (!$result) {
				$error = $this->db->error();
				$envio["message"] = $error['message'];
				return  $envio;
			}			
			
			return $envio;

		}
	}
