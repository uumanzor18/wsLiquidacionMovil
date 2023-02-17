<?php 
	class Cliente extends CI_Model{
		
		function __construct(){
			$this->load->database();
		}

		function getAllClientes(){
			$this->db->select('*');
	        $this->db->from('CLIENTE');
	        $this->db->where('CLIENTE', 1234);
	        $query = $this->db->get();

	        if($query->num_rows() > 0){
	          return $query->result_array();
	        }else{
	          return 0;
	        }
		}
	}

 ?>