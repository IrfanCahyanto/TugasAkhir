<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->view('home');
	}

	function formDaftar()
	{
		$data['bidangminat'] = $this->M_data->find('bidangminat');
		$data['programstudi'] = $this->M_data->find('programstudi');

		$this->load->view('formDaftar', $data);
		$this->load->view('template/jquery/formSubmit');
		
	}

	function filterBidangMinat(){

		$IDProgramStudi = $this->input->post('IDProgramStudi');
		$where = array('IDProgramStudiKsn' => $IDProgramStudi );
		$data = $this->M_data->find('bidangminat', $where);
		
		if ($data) {
			$lists = "<option value=''>Pilih</option>";
			foreach($data->result() as $u){
				$lists .= "<option value='".$u->IDBidangMinat."'>".$u->BidangMinat."</option>"; 
			}
		} else {
			$lists = "<option disabled> Belum Ada Bidang Minat </option>";
		}

		$callback = array('list'=> $lists); 
		echo json_encode($callback);
	}

	function daftarMahasiswa()
	{
		$ID = $this->input->post('nim');
		$Nama = $this->input->post('nama');
		$ProgramStudi = $this->input->post('programstudi');
		$BidangMinat = $this->input->post('bidangminat');
		$NoHP = $this->input->post('nohp');
		$Email = $this->input->post('email');

		$filename = "file_".time('upload');

		$config['upload_path'] = './assets/images/User/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['file_name']	= $filename;

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('foto'))
		{
			$error = array('error' => $this->upload->display_errors());
			$notif = array(
				'head' => "Maaf Terjadi Kesalahan",
				'isi' => "Terjadi Kesalahan Saat Mengupload Gambar",
				'sukses' => 0
			);

			print_r($error);

		}
		else {

			$foto = $this->upload->data();
			
			$data = array(
				'ID' => $ID,
				'Nama' => $Nama,
				'IDProgramStudiUser' => $ProgramStudi,
				'IDBidangMinatUser' => $BidangMinat,
				'NoHP' => $NoHP,
				'Email' => $Email, 
				'Foto' => $foto['file_name'],
				'Status' => 'Daftar'
			);
			$this->M_data->save($data, 'users');
			$notif = array(
				'head' => "Pendaftaran Berhasil",
				'isi' => "Mohon Tunggu Validasi Dari Jurusan",
				'user' => "Daftar",
				'func' => "Home/daftarMahasiswa",
				'sukses' => 1
			);
		}
		echo json_encode($notif);

	}

	function session()
	{
		
		$username = $this->input->post('nim');
		$password = md5($this->input->post('password'));

		$where = "ID='$username' AND Password='$password'";

		$where_admin = "username='$username' AND Password='$password'";

		$users = $this->M_data->find('users', $where, '', '', 'bidangminat','bidangminat.IDBidangMinat = users.IDBidangMinatUser');

		$admin = $this->M_data->find('admin', $where_admin);

		if ($users) {

			foreach ($users->result() as $u) {

				$data = array(
					'ID' => $u->ID,
					'Status' => $u->Status,
					'Nama' => $u->Nama,
					'BidangMinat' => $u->BidangMinat,
				);
				
				$status = $u->Status;
				if ($u->ID === $u->IDDosen) {
					$data['Adminprodi'] = 1;
					echo 3;
				} elseif($status === 'Dosen') {
					$data['Adminprodi'] = 0;
					echo 1;
				} else {
					echo 2;
					$data['Adminprodi'] = 0;
				}

				$this->session->set_userdata($data);

			}

		} elseif ($admin) {

			foreach ($admin->result() as $b) {

				$data = array(
					'id_admin' => $b->id_admin,
					'username' => $b->username,
					'password' => $b->Password,
					'Status' => 'Admin',
				);

				$this->session->set_userdata($data);
				echo 4;
			}
		} else {
			redirect('Home');
		}
	}

	public function Logout()
	{
		$this->session->sess_destroy();
		redirect('Home');
	}
}
