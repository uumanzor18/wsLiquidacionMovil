<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_controller.php';

class Api extends REST_Controller{
    function __construct(){
        parent::__construct();
        $this->load->model('usuario');
        $this->load->model('geoTicket');
        $this->load->model('cableModem');
        $this->load->model('equipoTvDig');
        $this->load->model('Actividades');
        $this->load->model('Ordenes');
        $this->load->model('Items');
        $this->load->helper('utils');
    }

    function login_post(){
        $user = $this->post('user');
        $pass = $this->post('pass');
        $version = $this->post('version');
        //writeActionLog("login", json_encode($this->post()));
        if(empty($user) || empty($pass)){
            writeActionLog("Login APP / Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);
        }
        elseif (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
              writeActionLog("Login APP/ Acceso Denegado", json_encode($this->post()));
              $this->response("Acceso Denegado", 200);
          }
        elseif (empty($version)) {
            writeActionLog("Login APP / Version del app vacio", json_encode($this->post()));
            $this->response("Version del app vacio", 400);
        }
        else{
            $result = $this->usuario->login($user, $pass, $version);

            if ($result[0]) {
                writeActionLog("Login APP / Exito al iniciar sesion".json_encode($this->post()), $result[1]);
                $this->response($result[1], 200);
            }
            else{
                writeActionLog("Login APP / Error al iniciar sesion: ".json_encode($this->post()), $result[1]);
                $this->response("Error al autenticar", 404);
            }
        }
    }

    function agregarFoto_post(){
        /*$temp = explode(",", $this->post('geoposicion'));
        $data["LATITUD"] = trim($temp[0]);
        $data["LONGITUD"] = trim($temp[1]);*/
        $data["DATE"] = $this->post('fecha');
        $data["HORA"] = $this->post('hora');
        $data["FECHA"] = $this->post('fecha') . " " . $this->post('hora');
        $data["TICKET_ACTIVIDAD"] = $this->post('orden');
        $data["COMENTARIO"] = preg_replace("/[^a-zA-Z_\-\/+()0-9\ ]+/", "", trim($this->post('texto')));
        $data["USER"] = $this->post('usuario');
        $data["WS_U"] = $this->post('ws_u');
        $data["WS_P"] = $this->post('ws_p');
        $data["FOTO"] = $this->post('foto');
        $data["ESTADO"] = $this->post('commenttype');

        writeActionLog("agregarFoto", json_encode($this->post()));

        if (WSUSERNAME !== $data["WS_U"] || WSPASSWORD !== $data["WS_P"]) {
            writeActionLog("Agregar Foto / Acceso Denegado", json_encode(["orden"=>$this->post('orden'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["DATE"])){
            writeActionLog("Agregar FOTO / Fecha vacia", json_encode(["orden"=>$this->post('orden'), "fecha"=>$this->post("fecha")]));
            $this->response("Se necesita ingresar la fecha", 400);
        }
        elseif (empty($data["HORA"])){
            writeActionLog("Agregar FOTO / Hora vacia", json_encode(["orden"=>$this->post('orden'), "hora"=>$this->post("hora")]));
            $this->response("Se necesita ingresar la hora", 400);
        }
        elseif (empty($data["FOTO"])){
            writeActionLog("Agregar FOTO / Foto vacia", json_encode(["orden"=>$this->post('orden'), "foto"=>$this->post("foto")]));
            $this->response("Se necesita ingresar la fotografia", 400);
        }
        elseif (empty($data["TICKET_ACTIVIDAD"])){
            writeActionLog("Agregar FOTO / Orden vacia", json_encode(["orden"=>$this->post("orden")]));
            $this->response("Se necesita ingresar la orden", 400);
        }
        elseif (empty($data["USER"])){
            writeActionLog("Agregar FOTO / Usuario vacio", json_encode(["orden"=>$this->post('orden'), "usuario"=>$this->post("usuario")]));
            $this->response("Se necesita el usuario", 400);
        }
        else{
            $result = $this->geoTicket->agregarFoto($data);
            $data["FOTO"] = substr($data["FOTO"], 0, 100);
            if ($result["estado"]) {
                $data["ARCHIVO"] = $result["archivo"];
                writeActionLog("Agregar Foto / Foto agregada", json_encode($data));
                $this->response(true, 200);
            }
            else{
                if ($result["tipo"] == 2) {
                    writeActionLog("Agregar Foto / Error al agregar foto: Numero maximo de fotos permitidas", json_encode($data));
                    $this->response("Numero maximo de fotos permitidas.", 404);
                }
                else{
                    writeActionLog("Agregar Foto / Error al agregar foto: " . $result["mensaje"], json_encode($data));
                    $this->response("Error al agregar fotografia.", 404);
                }
            }
        }
    }

    function agregarPdf_post(){
        $data["TICKET_ACTIVIDAD"] = $this->post('orden');
        $data["WS_U"] = $this->post('ws_u');
        $data["WS_P"] = $this->post('ws_p');
        $data["PDF"] = $this->post('pdf');

        if (WSUSERNAME !== $data["WS_U"] || WSPASSWORD !== $data["WS_P"]) {
            writeActionLog("Agregar PDF / Acceso Denegado", json_encode(["orden"=>$this->post('orden'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["PDF"])){
            writeActionLog("Agregar PDF / PDF vacio", json_encode(["orden"=>$this->post('orden'), "pdf"=>$this->post("pdf")]));
            $this->response("Se necesita ingresar el pdf", 400);
        }
        elseif (empty($data["TICKET_ACTIVIDAD"])){
            writeActionLog("Agregar PDF / Orden vacio", json_encode(["orden"=>$this->post("orden")]));
            $this->response("Se necesita ingresar la orden", 400);
        }
        else{
            $result = $this->geoTicket->agregarPdf($data);
            unset($data["PDF"]);
            if ($result[0]) {
                writeActionLog("Agregar PDF / PDF agregado", json_encode($data));
                $this->response(true, 200);
            }
            else{
                writeActionLog("Agregar PDF / Error al agregar PDF: " . $result[1], json_encode($data));
                $this->response("Error al agregar pdf.", 404);
            }
        }
    }

    function agregarAudio_post(){
        $data["FECHA"] = $this->post('fecha') . " " . $this->post('hora');
        $data["TICKET_ACTIVIDAD"] = $this->post('orden');
        $data["USER"] = $this->post('usuario');
        $data["DATE"] = $this->post('fecha');
        $data["HORA"] = $this->post('hora');
        $data["WS_U"] = $this->post('ws_u');
        $data["WS_P"] = $this->post('ws_p');
        $data["AUDIO"] = $this->post('audio');

        if (WSUSERNAME !== $data["WS_U"] || WSPASSWORD !== $data["WS_P"]) {
            writeActionLog("Agregar Audio / Acceso Denegado", json_encode(["orden"=>$this->post('orden'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["DATE"])){
            writeActionLog("Agregar Audio / Fecha vacio", json_encode(["orden"=>$this->post('orden'), "fecha"=>$this->post("fecha")]));
            $this->response("Se necesita ingresar la fecha", 400);
        }
        elseif (empty($data["HORA"])){
            writeActionLog("Agregar Audio / Hora vacio", json_encode(["orden"=>$this->post('orden'), "hora"=>$this->post("hora")]));
            $this->response("Se necesita ingresar la hora", 400);
        }
        elseif (empty($data["AUDIO"])){
            writeActionLog("Agregar Audio / Audio vacio", json_encode(["orden"=>$this->post('orden'), "audio"=>$this->post("audio")]));
            $this->response("Se necesita ingresar el audio", 400);
        }
        elseif (empty($data["TICKET_ACTIVIDAD"])){
            writeActionLog("Agregar Audio / Orden vacio", json_encode(["orden"=>$this->post("orden")]));
            $this->response("Se necesita ingresar la orden", 400);
        }
        elseif (empty($data["USER"])){
            writeActionLog("Agregar Audio / Usuario vacio", json_encode(["orden"=>$this->post('orden'), "usuario"=>$this->post("usuario")]));
            $this->response("Se necesita el usuario", 400);
        }
        else{
            $result = $this->geoTicket->agregarAudio($data);
            $data["AUDIO"] = substr($data["AUDIO"], 0, 100);
            if ($result["estado"]) {
                $data["ARCHIVO"] = $result["archivo"];
                writeActionLog("Agregar Audio / Audio agregado", json_encode($data));
                $this->response(true, 200);
            }
            else{
                if ($result["tipo"] == 2) {
                    writeActionLog("Agregar Audio / Error al agregar Audio: Numero maximo de audios permitidos" , json_encode($data));
                    $this->response("Numero maximo de audios permitidos.", 404);
                }
                else{
                    writeActionLog("Agregar Audio / Error al agregar audio: " . $result["mensaje"], json_encode($data));
                    $this->response("Error al agregar audio.", 404);
                }
            }
        }
    }

    function agregarSeguimiento_post(){
        $data["FECHA"] = $this->post('fecha') . " " . $this->post('hora');
        $data["TICKET_ACTIVIDAD"] = $this->post('orden');
        $data["COMENTARIO"] = preg_replace("/[^a-zA-Z_\-\/+()0-9\ ]+/", "", trim($this->post('texto')));
        $data["USER"] = $this->post('usuario');
        $data["DATE"] = $this->post('fecha');
        $data["HORA"] = $this->post('hora');
        $data["FOTO"] = substr($this->post('foto'), 0, 100);
        $data["AUDIO"] = substr($this->post('audio'), 0, 100);
        $data["ESTADO"] = $this->post('commenttype');

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Agregar Seguimiento / Acceso Denegado", json_encode(["orden"=>$this->post('orden'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["DATE"])){
            writeActionLog("Agregar Seguimiento / Fecha vacio", json_encode(["orden"=>$this->post('orden'), "fecha"=>$this->post("fecha")]));
            $this->response("Se necesita ingresar la fecha", 400);
        }
        elseif (empty($data["HORA"])){
            writeActionLog("Agregar Seguimiento / Hora vacio", json_encode(["orden"=>$this->post('orden'), "hora"=>$this->post("hora")]));
            $this->response("Se necesita ingresar la hora", 400);
        }
        /*elseif (empty($data["COMENTARIO"])){
            writeActionLog("Agregar Seguimiento / Comentario vacio", json_encode(["orden"=>$this->post('orden'), "texto"=>$this->post("texto")]));
            $this->response("Se necesita ingresar un comentario", 400);
        }*/
        elseif (empty($data["TICKET_ACTIVIDAD"])){
            writeActionLog("Agregar Seguimiento / Orden vacio", json_encode(["orden"=>$this->post("orden")]));
            $this->response("Se necesita ingresar la orden", 400);
        }
        elseif (empty($data["USER"])){
            writeActionLog("Agregar Seguimiento / Usuario vacio", json_encode(["orden"=>$this->post('orden'), "usuario"=>$this->post("usuario")]));
            $this->response("Se necesita el usuario", 400);
        }
        else{
            $result = $this->geoTicket->agregarSeguimiento($data);
            if ($result[0]) {
                writeActionLog("Agregar Seguimiento / Seguimiento agregado", json_encode($data));
                $this->response(true, 200);
            }
            else{
                writeActionLog("Agregar Seguimiento / Error al agregar Seguimiento: ".$result[1], json_encode($data));
                $this->response("Error al ingresar seguimiento.", 404);
            }
        }
    }

    function resetCm_post(){
        $data["cs"] = $this->post('cs');
        $data["cm_mac"] = $this->post('cm_mac');

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Reset CM / Acceso Denegado", json_encode(["cs"=>$this->post('cs'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif(empty($data["cs"])){
            writeActionLog("Reset CM / Contrato servicio vacio", json_encode(["cs"=>$this->post("cs")]));
            $this->response("Se necesita ingresar el contrato servicio", 200);
        }
        elseif (empty($data["cm_mac"])){
            writeActionLog("Reset CM / cm_mac vacio", json_encode(["cs"=>$this->post('cs'), "cm_mac"=>$this->post("cm_mac")]));
            $this->response("Se necesita el identificador mac", 200);
        }
        else{
            $result = $this->cableModem->resetCm($data);
            if ($result["cod"] == "0000") {
                writeActionLog("Reset CM / Reset Ejecutado", json_encode($this->post()));
                $this->response(json_encode($result), 200);
            }
            else{
                writeActionLog("Reset CM / error al realizar reset: " . $result["message"], json_encode($this->post()));
                $result["message"] = "error al realizar reset";
                $this->response(json_encode($result), 200);
            }
        }
    }

    function resetCaja_post(){
        $data["cs"] = $this->post('cs');
        $data["tarjeta"] = substr($this->post('tarjeta'),0,10);

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Reset Caja / Acceso Denegado", json_encode(["cs"=>$this->post('cs'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["tarjeta"])){
            writeActionLog("Reset Caja / Serie de la tarjeta vacio", json_encode(["cs"=>$this->post('cs'), "tarjeta"=>$this->post("tarjeta")]));
            $this->response("Se necesita el numero de serie de la tarjeta", 200);
        }
        else{
            $result = $this->equipoTvDig->resetCaja($data);
            if ($result["cod"] == "0000") {
                writeActionLog("Reset Caja / Reset Ejecutado", json_encode($this->post()));
                $this->response(json_encode($result), 200);
            }
            else{
                writeActionLog("Reset Caja / Error al realizar reset: " . $result["message"], json_encode($this->post()));
                $result["message"] = "error al realizar reset";
                $this->response(json_encode($result), 200);
            }
        }
    }

    function actualizarCaja_post(){
        $data["cs"] = $this->post('cs');
        $data["tarjeta"] = substr($this->post('tarjeta'),0,10);

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Actualizar Caja / Acceso Denegado", json_encode(["cs"=>$this->post('cs'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["tarjeta"])){
            writeActionLog("Actualizar Caja / Serie de la tarjeta vacio", json_encode(["cs"=>$this->post('cs'), "tarjeta"=>$this->post("tarjeta")]));
            $this->response("Se necesita el numero de serie de la tarjeta", 200);
        }
        else{
            $result = $this->equipoTvDig->actualizarCaja($data);
            if ($result["cod"] == "0000") {
                writeActionLog("Actualizar Caja / Actualizacion Correcta", json_encode($this->post()));
                $this->response(json_encode($result), 200);
            }
            else{
                writeActionLog("Actualizar Caja / Error al actualizar: " . $result["message"], json_encode($this->post()));
                $result["message"] = "error al realizar actualizacion";
                $this->response(json_encode($result), 200);
            }
        }
    }

    function actualizarPaquetes_post(){
        $data["cs"] = $this->post('cs');
        $data["tarjeta"] = substr($this->post('tarjeta'),0,10);

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Actualizar Paquetes / Acceso Denegado", json_encode(["cs"=>$this->post('cs'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["tarjeta"])){
            writeActionLog("Actualizar Paquetes / Serie de la tarjeta vacio", json_encode(["cs"=>$this->post('cs'), "tarjeta"=>$this->post("tarjeta")]));
            $this->response("Se necesita el numero de serie de la tarjeta", 200);
        }
        else{
            $result = $this->equipoTvDig->actualizarPaquetes($data);
            if ($result["cod"] == "0000") {
                writeActionLog("Actualizar Paquetes / Actualizacion correcta", json_encode($this->post()));
                $this->response(json_encode($result), 200);
            }
            else{
                writeActionLog("Actualizar Paquetes / Error al actualizar: " . $result["message"], json_encode($this->post()));
                $result["message"] = "error al realizar actualizacion";
                $this->response(json_encode($result), 200);
            }
        }
    }

    function loginPortal_post(){
        $user = $this->post('user');
        $pass = $this->post('pass');
        //writeActionLog("login", json_encode($this->post()));
        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Login Portal / Acceso Denegado", json_encode($this->post()));
            $this->response("Acceso Denegado", 200);
        }
        elseif(empty($user) || empty($pass)){
            writeActionLog("Login Portal/ Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);

        }else{

            $result = $this->usuario->loginPortal($user, $pass);

            if ($result[0]) {
                writeActionLog("Login Portal/ Exito al iniciar sesion".json_encode($this->post()), $result[1]);
                $this->response($result[1], 200);
            }
            else{
                writeActionLog("Login Portal/ Error al iniciar sesion: ".json_encode($this->post()), $result[1]);
                $this->response("Error al autenticar", 404);
            }
        }
    }

    function formatTarjeta_post(){
        $data["cs"] = $this->post('cs');
        $data["tarjeta"] = substr($this->post('tarjeta'),0,10);

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Format Tarjeta / Acceso Denegado", json_encode(["cs"=>$this->post('cs'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["tarjeta"])){
            writeActionLog("Format Tarjeta / Serie de la tarjeta vacio", json_encode(["cs"=>$this->post('cs'), "tarjeta"=>$this->post("tarjeta")]));
            $this->response("Se necesita el numero de serie de la tarjeta", 200);
        }
        else{
            $result = $this->equipoTvDig->formatEquipo($data);
            if ($result["cod"] == "0000") {
                writeActionLog("Format Tarjeta / Reset Ejecutado", json_encode($this->post()));
                $this->response(json_encode($result), 200);
            }
            else{
                writeActionLog("Format Tarjeta / Error al realizar format: " . $result["message"], json_encode($this->post()));
                $result["message"] = "error al realizar format";
                $this->response(json_encode($result), 200);
            }
        }
    }

    function actualizarTarjeta_post(){
        $data["cs"] = $this->post('cs');
        $data["tarjeta"] = substr($this->post('tarjeta'),0,10);

        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Reset Tarjeta / Acceso Denegado", json_encode(["cs"=>$this->post('cs'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["tarjeta"])){
            writeActionLog("Reset Tarjeta / Serie de la tarjeta vacio", json_encode(["cs"=>$this->post('cs'), "tarjeta"=>$this->post("tarjeta")]));
            $this->response("Se necesita el numero de serie de la tarjeta", 200);
        }
        else{
            $result = $this->equipoTvDig->resetTarjeta($data);
            if ($result["cod"] == "0000") {
                writeActionLog("Reset Tarjeta / Reset Ejecutado", json_encode($this->post()));
                $this->response(json_encode($result), 200);
            }
            else{
                writeActionLog("Reset Tarjeta / Error al realizar reset: " . $result["message"], json_encode($this->post()));
                $result["message"] = "error al realizar reset";
                $this->response(json_encode($result), 200);
            }
        }
    }

    function agregarGPS_post(){
        $temp = explode(",", $this->post('geoposicion'));
        $data["LATITUD"] = trim($temp[0]);
        $data["LONGITUD"] = trim($temp[1]);
        $data["TICKET_ACTIVIDAD"] = $this->post('orden');
        $data["WS_U"] = $this->post('ws_u');
        $data["WS_P"] = $this->post('ws_p');
        $data["ESTADO"] = $this->post('commenttype');

        if (WSUSERNAME !== $data["WS_U"] || WSPASSWORD !== $data["WS_P"]) {
            writeActionLog("Agregar gps / Acceso Denegado", json_encode(["orden"=>$this->post('orden'), "ws_u"=>$this->post('ws_u'), "ws_p"=>$this->post('ws_p')]));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($data["LATITUD"])){
            writeActionLog("Agregar GPS / Latitud vacia", json_encode(["orden"=>$this->post('orden'), "geoposicion"=>$this->post("geoposicion")]));
            $this->response("Se necesita ingresar latitud", 400);
        }
        elseif (empty($data["LONGITUD"])){
            writeActionLog("Agregar GPS / Longitud vacia", json_encode(["orden"=>$this->post('orden'), "geoposicion"=>$this->post("geoposicion")]));
            $this->response("Se necesita ingresar la hora", 400);
        }
        else{
            $result = $this->geoTicket->agregarGPS($data);
            if ($result["estado"]) {
                writeActionLog("Agregar GPS / GPS agregado", json_encode($data));
                $this->response(json_encode(["estado"=>true, "mensaje"=>"Ingreso de GPS correcto."]), 200);
            }
            else{
                writeActionLog("Agregar GPS / Error al agregar GPS: " . $result["mensaje"], json_encode($data));
                $this->response(json_encode(["estado"=>false, "mensaje"=>"Error al agregar GPS."]), 404);
            }
        }
    }

    function listarActividades_post(){
        $user = $this->post('ws_u');
        $pass = $this->post('ws_p');
        //writeActionLog("login", json_encode($this->post()));
        if(empty($user) || empty($pass)){
            writeActionLog("listarActividades / Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);
        }elseif (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Listar Actividades / Acceso Denegado: ", json_encode($this->post()));
            $this->response("Acceso Denegado", 200);
        }
        else{
            $result = $this->Actividades->listarActividades();
            if ($result["status"]) {
              writeActionLog("Listar Actividades / Exito al Cargar los datos: ".json_encode($this->post()), print_r($result["response"],true));
              $this->response($result, 200);
            }
            else{
              writeActionLog("Listar Actividades / Error al Cargar los datos: ".json_encode($this->post()), $result["message"]);
              $this->response("Error al Cargar los Datos", 404);
            }
        }
    }

    function crearOrden_post(){
        //writeActionLog("crearOrden / Datos de entrada", serialize($this->post()));
        //$user = $this->post('user');
        //$pass = $this->post('pass');
        //$version = $this->post('version');
        $workorder = $this->post('Workorder');
        $actividad = $this->post('Actividad');
        $temporalComentario = $this->post('Comentario');
        $comentario = cleanedString($temporalComentario);
        $comentario = substr($comentario, 0, 512);
        $ws_p = $this->post('ws_p');
        $ws_u = $this->post('ws_u');
        //$variables =["orden" => $workorder,"actividad" => $actividad,"cometario"=>$comentario];
        //writeActionLog("contrato", json_encode($variables));
        if(empty($ws_u) || empty($ws_p)) {
            writeActionLog("CrearOrden / Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);
        }elseif (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("CrearOrden / Acceso Denegado: ", json_encode($this->post()));
            $this->response("Acceso Denegado", 200);
        }
        else{
            if (ctype_digit(strval($workorder))) {
                if (strlen($actividad) <=6 && strlen($actividad) > 0) {
                    if (strlen($comentario) > 0) {
		                $result = $this->Ordenes->crearOrden($workorder,$actividad,$comentario);
                        if ($result["status"]) {
                            if ($result["cod"] == 200) {
                                writeActionLog("CrearOrden", json_encode(["datos_entrada"=> $this->post(), "datos_salida"=>$result]));
                                $this->response($result, 200);
                            }
                            else{
                                writeActionLog("CrearOrden", json_encode(["datos_entrada"=> $this->post(), "datos_salida"=>$result]));
                                $result["cod"] = 200;
                                unset($result["log"]); 
                                $this->response($result, 200);
                            }
                        }
                        else{
                            $envio = ["response"=>$result["cod"], "message" => $result["response"]];
                            writeActionLog("CrearOrden / Error al realizar la accion".json_encode($this->post()), $result["response"]);
                            $this->response($envio, 404);
                        }
                    }else {
                        $envio = [ "response" => -1, "message" => "El comentario esta vacio"];
                        writeActionLog("CrearOrden / Error al realizar la accion".json_encode($this->post()), $envio["message"]);
                        $this->response($envio, 404);
                    }
                }else {
                    $envio = [ "response" => -1,"message" => false, "response" => "El codigo de actividad consta de 1 a 6 caracteres alfanumericos"];
                    writeActionLog("CrearOrden / Error al realizar la accion: ".json_encode($this->post()), $envio["response"]);
                    $this->response($envio, 404);
                }
            }else {
                $envio = ["response" => -1, "message" => "El numero de orden no es entero"];
                writeActionLog("CrearOrden / Error al realizar la accion".json_encode($this->post()), $envio["response"]);
                $this->response($envio, 404);
            }
        }
    }

    function catalogoCierre_post(){
        $user = $this->post('ws_u');
        $pass = $this->post('ws_p');
        $actividad = $this->post('actividad');
        
        if(empty($user) || empty($pass)){
            writeActionLog("catalogoCierre / Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);
        }elseif (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("Catalogo Cierre / Acceso Denegado: ", json_encode($this->post()));
            $this->response("Acceso Denegado", 200);
        }
        else{
            
            $result = $this->Actividades->catalogoCierre($actividad);
            if ($result["status"]) {
              writeActionLog("Catalogo Cierre / Exito al Cargar los datos: ".json_encode($this->post()), print_r($result["response"],true));
              $this->response($result, 200);
            }
            else{
              writeActionLog("Catalogo Cierre / Error al Cargar los datos: ".json_encode($this->post()), $result["message"]);
              $this->response("Error al Cargar los Datos", 404);
            }
        }
    }

    function cierreOrden_post(){
        $user = $this->post('ws_u');
        $pass = $this->post('ws_p');
        $orden = $this->post('orden');
        $cs = $this->post('cs');
        $idCierre = $this->post('cierre');

        if(empty($user) || empty($pass)){
            writeActionLog("cierreOrden / Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);
        }elseif (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("cierreOrden / Acceso Denegado: ", json_encode($this->post()));
            $this->response("Acceso Denegado", 200);
        }
        elseif (empty($orden)) {
            writeActionLog("cierreOrden / Numero de orden vacio", json_encode($this->post()));
            $this->response("Numero de orden vacio", 400);
        }
        elseif (empty($cs)) {
            writeActionLog("cierreOrden / Contrato servicio vacio", json_encode($this->post()));
            $this->response("Numero de contrato servicio vacio", 400);
        }
        elseif (empty($idCierre)) {
            writeActionLog("cierreOrden / Numero de cierre vacio", json_encode($this->post()));
            $this->response("Numero de cierre vacio", 400);
        }
        else{
            $result = $this->Ordenes->cerrarOrden($orden, $cs, $idCierre);
            if ($result["status"]) {
                writeActionLog("cierreOrden / Exito al cerrar orden ", json_encode(["datos_entrada"=> $this->post(), "datos_salida"=>$result]));
                $this->response($result, 200);
            }
            else{
                writeActionLog("cierreOrden / Error al cerrar orden ", json_encode(["datos_entrada"=> $this->post(), "datos_salida"=>$result]));
                unset($result["log"]); 
                $this->response($result, 200);
            }
        }
    }

    function registrarItemsUsados_post()
    {
        $user = $this->post('ws_u');
        $pass = $this->post('ws_p');
        $orden = $this->post('orden');
        $cs = $this->post('cs');
        $items = $this->post('items');

        if (empty($user) || empty($pass)) {
            writeActionLog("crearItems / Usuario o clave vacio", print_r($this->post(), true));
            $this->response("Usuario o clave vacio", 400);
        }
        elseif (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("crearItems / Acceso Denegado: ", print_r($this->post(), true));
            $this->response("Acceso Denegado", 400);
        }
        elseif (empty($orden)) {
            writeActionLog("crearItems / Numero de orden vacio", print_r($this->post(), true));
            $this->response("Numero de orden vacio", 400);
        }
        elseif (empty($cs)) {
            writeActionLog("crearItems / Numero de contrato servicio vacio", print_r($this->post(), true));
            $this->response("Numero de contrato servicio vacio", 400);
        }
        elseif (empty($items)) {
            writeActionLog("crearItems / Lista de items vacia", print_r($this->post(), true));
            $this->response("Lista de items vacia", 400);
        }
        else {
            $result = $this->Items->creacionItems($orden, $cs, $items);
            if ($result['status'] == 100) {
                writeActionLog("crearItems / Exito al crear items ", print_r(["datos_entrada"=> $this->post(), "datos_salida"=>$result], true));
                $this->response($result, 200);
            } else {
                writeActionLog("crearItems / Error al crear items ", print_r(["datos_entrada"=> $this->post(), "datos_salida"=>$result], true));
                unset($result["log"]); 
                $this->response($result, 400);
            }
        }
    }

    function actualizarData_post(){
        $user = $this->post('ws_u');
        $pass = $this->post('ws_p');
        $data = json_decode($this->post('data'));
        $tecnico = $this->post('tecnico');
        


        if(empty($user) || empty($pass)){
            writeActionLog("actualizarData / Usuario o clave vacio", json_encode($this->post()));
            $this->response("Usuario o clave vacio", 400);
        }
        if (WSUSERNAME !== $this->post('ws_u') || WSPASSWORD !== $this->post('ws_p')) {
            writeActionLog("actualizarData / Acceso Denegado: ", json_encode($this->post()));
            $this->response("Acceso Denegado", 200);
        }
        if (empty($data->id)) {
            writeActionLog("actualizarData / id vacio", json_encode($this->post()));
            $this->response("Id vacio", 400);
        }
        else{
            if(floor($data->id) != $data->id){
                writeActionLog("actualizarData / id vacio", json_encode($this->post()));
                $this->response("El campo Id debe ser numerico", 400);
            }
        }
        if (empty($data->campo)) {
            writeActionLog("actualizarData / campo vacio", json_encode($this->post()));
            $this->response("Campo vacio", 400);
        }
        if (empty($data->valor)) {
            writeActionLog("actualizarData / valor vacio", json_encode($this->post()));
            $this->response("Valor vacio", 400);
        }
        if (empty($tecnico)) {
            writeActionLog("actualizarData / tecnico vacio", json_encode($this->post()));
            $this->response("tecnico vacio", 400);
        }

        switch ($data->campo) {
            case 'electricity_account':
                $result = $this->Actividades->actualizarCS($data->id, $data->campo, $data->valor, $tecnico);
            break;
            
            default:
                writeActionLog("actualizarData / variable campo no reconocido ", json_encode($this->post()));
                $this->response("Campo no reconocido", 400);
            break;
        }
        
        if ($result["status"]) {
            writeActionLog("actualizarData / Exito al actualizar ", json_encode(["datos_entrada"=> $this->post(), "datos_salida"=>$result]));
            $this->response($result, 200);
        }
        else{
            writeActionLog("actualizarData / Error al actualizar ", json_encode(["datos_entrada"=> $this->post(), "datos_salida"=>$result]));
            $result["response"] = "Error de Ejecucion"; 
            $this->response($result, 200);
        }

    }
}
?>
