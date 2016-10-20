<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Alarmas extends CI_Controller {

	public function index()
	{
		$alarma = array();
		$data = array();
		$data_trans = array();
		$session_data = $this->session->userdata('logged_in');	
		if($session_data['tipo']!='1'){
			redirect('home');
		}
		$data = $this->Database->cargar_alarmas_workflow();
		$cant = count($data);
		for ($i=0;$i<$cant;$i++){
			if ($data[$i]['tiempo_max']!='')
				$data[$i]['alarmas'] = $this->Database->alarmaMaxWorkflow($data[$i]['workflow'],$data[$i]['instancia'],$data[$i]['tipo_usuario'],$data[$i]['usuario'],$data[$i]['tiempo_max']);
			/*
			else if ($data[$i]['tiempo_min']!='')
				$data[$i]['alarmas'] = $this->Database->alarmaMinWorkflow($data[$i]['workflow'],$data[$i]['instancia'],$data[$i]['tipo_usuario'],$data[$i]['usuario'],$data[$i]['tiempo_min']);
			*/
		}
		$data_trans = $this->Database->cargar_alarmas_transicion();
		$cant = count($data_trans);
		for ($i=0;$i<$cant;$i++){
			if ($data_trans[$i]['tiempo_max']!='')
				$data_trans[$i]['alarmas'] = $this->Database->alarmaMaxTransicion($data_trans[$i]['workflow'],$data_trans[$i]['instancia'],$data_trans[$i]['tipo_usuario'],$data_trans[$i]['usuario'],$data_trans[$i]['tiempo_max']);
		}
		$header = array(
			'session'=>$session_data
		);
		$vista = array(
			'data'				=>$data,
			'data_trans'		=>$data_trans
		);
		$contenido = array(
			'data'		=>$data
		);
		$contenido_trans = array(
			'data_trans'		=>$data_trans
		);
		$this->load->view('header',$header, FALSE);
		$this->load->view('alarmas',$vista, FALSE);
		$this->load->view('footerbegin','', FALSE);			
		$this->load->view('alarma_workflow',$contenido, FALSE);	
		$this->load->view('alarma_transicion',$contenido_trans, FALSE);	
		$this->load->view('footerend','', FALSE);	
	}

}

/* End of file Alarmas.php */
/* Location: ./application/controllers/Alarmas.php */