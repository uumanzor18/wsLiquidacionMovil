<?php
	class Usuario extends CI_Model{
		function __construct(){
			$this->load->database();
			$this->load->helper('utils');
		}

		function login($user, $pass, $version){
			$P_CODRESULTADO;
			$P_SUPERVISOR;
			$P_AX_ALMACEN_TECNICO;
			$P_EMAIL_ALMACEN;
			$P_TECNICO;
			$P_EMPEXT;

			if ($user != "" && $pass != "") {
				$parametros = [
					[
						'name'=> ':P_USUARIO',
						'value' => $user,
						'type' => SQLT_CHR,
						'length' => 32
					],
					[
						'name'=> ':P_CLAVE',
						'value' => $pass,
						'type' => SQLT_CHR,
						'length' => 50

					],
					[
						'name'=> ':P_VERSION',
						'value' => $version,
						'type' => SQLT_CHR,
						'length' => 50

					],
					[
						'name'=> ':P_CODRESULTADO',
						'value' => &$P_CODRESULTADO,
						'type' => SQLT_CHR,
						'length' => 30
					],
					[
						'name'=> ':P_SUPERVISOR',
						'value' => &$P_SUPERVISOR,
						'type' => SQLT_CHR,
						'length' => 1
					],
					[
						'name'=> ':P_AX_ALMACEN_TECNICO',
						'value' => &$P_AX_ALMACEN_TECNICO,
						'type' => SQLT_CHR,
						'length' => 60
					],
					[
						'name'=> ':P_EMAIL_ALMACEN',
						'value' => &$P_EMAIL_ALMACEN,
						'type' => SQLT_CHR,
						'length' => 100
					],
					[
						'name'=> ':P_TECNICO',
						'value' => &$P_TECNICO,
						'type' => SQLT_CHR,
						'length' => 130
					],
					[
						'name'=> ':P_EMPEXT',
						'value' => &$P_EMPEXT,
						'type' => SQLT_CHR,
						'length' => 130
					]
				];
				$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "authUsuario", $parametros);
				if($result){
					switch ($P_CODRESULTADO) {
						case '00':
							return [true, json_encode(["supervisor" => $P_SUPERVISOR, "almacen" => $P_AX_ALMACEN_TECNICO, "almacen_email" => $P_EMAIL_ALMACEN, "tecnico" => $P_TECNICO, "empresa_ejecutora"=>$P_EMPEXT])];
						break;

						case '01':
							return [false, "error en autenticacion o usuario inactivo"];
						break;

						case '02':
							return [false, "no hay almacen configurado"];
						break;

						case '03':
							return [false, "La version de la aplicacion no se encuentra registrada"];
						break;

						default:
							return [false, P_CODRESULTADO];
						break;
					}
		        }else{
					$error = $this->db->error();
					return [false, $error['message']];
		        }
			}else {
				return [false, "Error en autentificacion el usuario y la clave no pueden contener valores vacios"];
			}
		}

		function loginPortal($user, $pass){
			$P_CODRESULTADO;
			$P_NOMBRE;
			if ($user !="" && $pass !="") {
				$parametros = [
					[
						'name'=> ':P_USUARIO',
						'value' => $user,
						'type' => SQLT_CHR,
						'length' => 30
					],
					[
						'name'=> ':P_CLAVE',
						'value' => $pass,
						'type' => SQLT_CHR,
						'length' => 30

					],
					[
						'name'=> ':P_CODRESULTADO',
						'value' => &$P_CODRESULTADO,
						'type' => SQLT_CHR,
						'length' => 30
					],
					[
						'name'=> ':P_NOMBRE',
						'value' => &$P_NOMBRE,
						'type' => SQLT_CHR,
						'length' => 130
					]
				];
				$result = $this->db->stored_procedure("TAREAS_LQDCNMOVIL", "authUsuarioPortal", $parametros);
				if($result){
					switch ($P_CODRESULTADO) {
						case '00':
							return [true, json_encode(["usuario" => $P_NOMBRE])];
						break;

						case '01':
							return [false, "error en autenticacion o usuario inactivo"];
						break;

						default:
							return [false, $P_CODRESULTADO];
						break;
					}
		        }else{
					$error = $this->db->error();
					return [false, $error['message']];
		        }
			}else {
				return [false, "Error en autentificacion el usuario y la clave no pueden contener valores vacios"];
			}
		}
	}

 ?>
