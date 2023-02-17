<?php 
	class GeoTicket extends CI_Model{
		
		function __construct(){
			$this->load->database();
			$this->load->helper('utils');
		}

		function agregarFoto($datos){
			$P_ARCHIVO;
			$parametros = [
				[
					'name'=> ':P_ORDEN', 
					'value' => $datos["TICKET_ACTIVIDAD"],
					'type' => SQLT_CHR,
					'length' => 100

				],
				/*[
					'name'=> ':P_LATITUD', 
					'value' => $datos["LATITUD"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_LONGITUD', 
					'value' => $datos["LONGITUD"],
					'type' => SQLT_CHR,
					'length' => 100
				],*/
				[
					'name'=> ':P_FECHA', 
					'value' => $datos["FECHA"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_COMENTARIO', 
					'value' => $datos["COMENTARIO"],
					'type' => SQLT_CHR,
					'length' => 1000
				],
				[
					'name'=> ':P_USER', 
					'value' => $datos["USER"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_ESTADO', 
					'value' => $datos["ESTADO"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_ARCHIVO', 
					'value' => &$P_ARCHIVO,
					'type' => SQLT_CHR,
					'length' => 100
				]
			];

			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "agregarFoto", $parametros);
			if (!$result) {
				$error = $this->db->error();
				
				return ["estado"=>false, "mensaje"=>$error['message'], "tipo"=>1];
			}
			if ($P_ARCHIVO == 'false') {
				return ["estado"=>false, "tipo"=>2];
			}
			try {
				

				$uploadfile = DIRECTORIO . $P_ARCHIVO;
				
				/*$test = file_get_contents( "/usr/local/apache2/htdocs/wsLiquidacionMovil/geo_ticket/038.png" );
				$resp = base64_encode($test);
				$Base64Img = 'data:image/png;base64,' . $resp;*/

				$Base64Img = $datos["FOTO"];
				list(, $Base64Img) = explode(';', $Base64Img);
				list(, $Base64Img) = explode(',', $Base64Img);
				$Base64Img = base64_decode($Base64Img);
				file_put_contents($uploadfile, $Base64Img);

				return ["estado"=>true, "archivo"=>$P_ARCHIVO, "tipo"=>0];
			} catch (Exception $e) {
				return ["estado"=>false, "mensaje"=>$e->getMessage(), "tipo"=>3];
			}
		}

		function agregarAudio($datos){
			$P_ARCHIVO;
			$parametros = [
				[
					'name'=> ':P_ORDEN', 
					'value' => $datos["TICKET_ACTIVIDAD"],
					'type' => SQLT_CHR,
					'length' => 100

				],
				[
					'name'=> ':P_FECHA', 
					'value' => $datos["FECHA"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_USER', 
					'value' => $datos["USER"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_ARCHIVO', 
					'value' => &$P_ARCHIVO,
					'type' => SQLT_CHR,
					'length' => 100
				]
			];
			try {
				$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "agregarAudio", $parametros);

				if (!$result) {
					$error = $this->db->error();
					return ["estado"=>false, "mensaje"=>$error["message"], "tipo"=>1];
				}
				if ($P_ARCHIVO == 'false') {
					return ["estado"=>false,"tipo"=>2];
				}

				$uploaddir = DIRECTORIO . 'geo_ticket/audios/';
				$uploadfile = $uploaddir . $P_ARCHIVO;
				
				/*$test = file_get_contents( "/usr/local/apache2/htdocs/wsLiquidacionMovil/geo_ticket/pajarito.mp3" );
				$resp = base64_encode($test);
				$Base64Aud = 'data:audio/mp3;base64,' . $resp;
				var_dump($Base64Aud);
				die();*/

				$Base64Aud = $datos["AUDIO"];
				list(, $Base64Aud) = explode(';', $Base64Aud);
				list(, $Base64Aud) = explode(',', $Base64Aud);
				$Base64Aud = base64_decode($Base64Aud);
				file_put_contents($uploadfile, $Base64Aud);

				return ["estado"=>true, "archivo"=>$P_ARCHIVO, "tipo"=>1];
			} catch (Exception $e) {
				return ["estado"=>false, "mensaje"=>$e->getMessage(), "tipo"=>3];
			}
		}

		function agregarSeguimiento($datos){
			$parametros = [
				[
					'name'=> ':P_ORDEN', 
					'value' => $datos["TICKET_ACTIVIDAD"],
					'type' => SQLT_CHR,
					'length' => 100

				],
				[
					'name'=> ':P_FECHA', 
					'value' => $datos["FECHA"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_COMENTARIO', 
					'value' => $datos["COMENTARIO"],
					'type' => SQLT_CHR,
					'length' => 1000
				],
				[
					'name'=> ':P_USER', 
					'value' => $datos["USER"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_ESTADO', 
					'value' => $datos["ESTADO"],
					'type' => SQLT_CHR,
					'length' => 100
				],
			];
			try {
				$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "agregarSeguimiento", $parametros);

				if (!$result) {
					$error = $this->db->error();
					return [false, $error["message"]];
				}

				return [true, ""];
			} catch (Exception $e) {
				return [false, $e->getMessage()];
			}
		}

		function agregarPdf($datos){
			$P_CURSOR = $this->db->get_cursor();
			$parametros = [
				[
					'name'=> ':P_ORDEN', 
					'value' => $datos["TICKET_ACTIVIDAD"],
					'type' => SQLT_CHR,
					'length' => 100

				],
				[
					'name'=> ':P_CURSOR', 
					'value' => &$P_CURSOR,
					'type' => OCI_B_CURSOR,
					'length'=> -1
				]
			];
			try{
				$data = [];
				$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "getFotos", $parametros);
				
				if (!$result) {
					$error = $this->db->error();
					return [false, $error["message"]];
				}
				else{
					if (ociexecute($P_CURSOR)) {
						if (ocifetchstatement($P_CURSOR, $data) > 0) {
							$uploaddir = DIRECTORIO . 'geo_ticket/pdf/';
							$uploadfile = $uploaddir . $datos["TICKET_ACTIVIDAD"].".pdf";
							
							$Base64Pdf = $datos["PDF"];
							list(, $Base64Pdf) = explode(';', $Base64Pdf);
							list(, $Base64Pdf) = explode(',', $Base64Pdf);
							$Base64Pdf = base64_decode($Base64Pdf);
							file_put_contents($uploadfile, $Base64Pdf);

							foreach ($data["ARCHIVO"] as $value) {
								unlink(DIRECTORIO . 'geo_ticket/fotos/'.$value);
				    		}
						}
						else{
							return [false, "Orden no cuenta con fotografias en GEO_TICKET"];
						}
					}
					else{
						return [false, "No se realizo procedimiento getFotos"];
					}
				}
				return [true, "pdf enviado."];
			}
			catch (Exception $e) {
				return [false, $e->getMessage()];
			}
		}

		function agregarGPS($datos){
			$P_ARCHIVO;
			$parametros = [
				[
					'name'=> ':P_ORDEN', 
					'value' => $datos["TICKET_ACTIVIDAD"],
					'type' => SQLT_CHR,
					'length' => 100

				],
				[
					'name'=> ':P_LATITUD', 
					'value' => $datos["LATITUD"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_LONGITUD', 
					'value' => $datos["LONGITUD"],
					'type' => SQLT_CHR,
					'length' => 100
				],
				[
					'name'=> ':P_ESTADO', 
					'value' => $datos["ESTADO"],
					'type' => SQLT_CHR,
					'length' => 100
				]
			];
			$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "agregarGPS", $parametros);
			if (!$result) {
				$error = $this->db->error();
				
				return ["estado"=>false, "mensaje"=>$error['message'], "tipo"=>1];
			}
			
			return ["estado"=>true, "mensaje"=>"", "tipo"=>0];
		}
	}

?>
