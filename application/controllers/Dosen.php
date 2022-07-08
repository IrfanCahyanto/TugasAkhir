<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dosen extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$status = $this->session->userdata('Status');
		if (!(($status == "Dosen") or ($status == "Adminprodi"))) {
			redirect(base_url("Home"));
		}
		$this->load->library('Ajax_pagination');
		$this->perPage = 2;
	}

	function index()
	{

		$id = array('ID' => $_SESSION['ID']);

		$Penerima = array('IDPenerima' => $_SESSION['ID']);

		$data['Notifikasi'] = $this->M_data->find('notifikasi', $Penerima, '', '', 'users', 'users.ID = notifikasi.IDPengirim');

		$data['users'] = $this->M_data->find('users', $id, '', '', 'programstudi', 'programstudi.IDProgramStudi = users.IDProgramStudiUser');

		$result = $data['users']->row();
		$where = array('IDBidangMinatUser' => $result->IDBidangMinatUser);

		$data['idetugasakhir'] = $this->M_data->find('idetugasakhir', $where, 'IDIde', 'DESC', 'users', 'users.ID = idetugasakhir.IDIdeMahasiswa');

		$this->load->view('template/navbar');
		$this->load->view('dosen/home', $data);
	}

	function tabelTugasAkhir()
	{

		$ID = $_SESSION['ID'];

		$where = array('ID' => $ID);

		$page = $this->input->post('page');
		if (!$page) {
			$offset = 0;
		} else {
			$offset = $page;
		}

		$keywords = $this->input->post('keywords');
		$search = $this->input->post('search');

		if (!empty($keywords)) {
			$conditions['search']['keywords'] = $keywords;
		}
		if (!empty($sortBy)) {
			$conditions['search']['sortBy'] = $sortBy;
		}

		$conditions['start'] = $offset;
		$conditions['limit'] = $this->perPage;

		$users = $this->M_data->find('users', $where);

		foreach ($users->result() as $u) {
			$IDBidangMinat = $u->IDBidangMinatUser;
			$Status = $u->Status;

			$whereID = array(
				'IDBidangMinatUser' => $u->IDBidangMinatUser,
				'Nilai =' => '',
				'Status' => 'TugasAkhir'
			);
		}


		$data['users'] = $this->M_data->find('tugasakhir', $whereID, '', '', 'users', 'users.ID = tugasakhir.IDMahasiswaTugasAkhir', '', '', '', '', $conditions, $search);

		$total = $this->M_data->find('tugasakhir', $whereID, '', '', 'users', 'users.ID = tugasakhir.IDMahasiswaTugasAkhir');

		$totalData = $total != FALSE ? $total->num_rows() : 0;

		$config['target'] = '#tabelUser';
		$config['base_url'] = base_url() . 'Adminprodi/tabelTugasAkhir';
		$config['total_rows'] = $totalData;
		$config['per_page'] = $this->perPage;
		$config['link_func']   = 'searchmhs';

		$this->ajax_pagination->initialize($config);

		if ($data['users']) {
			foreach ($data['users']->result() as $d) {

				$finish = array(
					'IDTugasAkhirPmb' => $d->IDTugasAkhir,
					'StatusTugasAkhir' => 1
				);
			}
			$data['finish'] = $this->M_data->find('pembimbing', $finish, '', '', 'users', 'users.ID = pembimbing.IDDosenPmb');
		}

		$data['pembimbing'] = $this->M_data->find('pembimbing', '', '', '', 'users', 'users.ID = pembimbing.IDDosenPmb');
		$this->load->view('dosen/tabelTugasAkhir', $data, false);
	}

	function detailDosen($nik)
	{
		$where = array('ID' => $nik);
		$wherep = array('IDDosenPmb' => $nik);
		$data['pembimbing'] = $this->M_data->find('pembimbing', $wherep, '', '', 'users', 'users.ID = pembimbing.IDDosenPmb', 'tugasakhir', 'tugasakhir.IDTugasAkhir = pembimbing.IDTugasAkhirPmb');
		$data['dosen'] = $this->M_data->find('users', $where);
		$this->load->view('template/navbar')->view('adminprodi/detailDosen', $data);
	}

	function detailMahasiswa($ID)
	{
		$where = array(
			'IDMahasiswaTugasAkhir' => $ID
		);

		$data['tugasakhir'] = $this->M_data->find('tugasakhir', $where,  '', '', 'users', 'ID = IDMahasiswaTugasAkhir');
		$data['uploader'] = $this->M_data->find('tugasakhir', $where,  '', '', 'users', 'ID = Uploader');

		$wherePMB = array(
			'IDTugasAkhirPmb' => $data['tugasakhir']->row_array()['IDTugasAkhir'],
			'IDDosenPmb' => $_SESSION['ID']
		);

		// Mengambil data pembimbing yang sedang melihat tugasakhir
		$data['pembimbing'] = $this->M_data->find('pembimbing', $wherePMB);

		$whereProp = array(
			'IDTugasAkhirPmb' => $data['tugasakhir']->row_array()['IDTugasAkhir']
		);

		// Array Proposal Berfungsi Untuk Menghitung Proposal Tugas Akhir Yang Di ACC
		$data['proposal'] =  $this->M_data->find('pembimbing', $whereProp);

		$isTugasAkhir = array(
			'StatusProposal' => 1,
			'IDTugasAkhirPmb' => $data['tugasakhir']->row_array()['IDTugasAkhir']
		);

		$data['isTugasAkhir'] = $this->M_data->find('pembimbing', $isTugasAkhir) ? $this->M_data->find('pembimbing', $isTugasAkhir)->num_rows() === 0 ? 'TugasAkhir' : 'Proposal' : 'Proposal';

		$whereIDCard = array('IDKartuMahasiswa' => $ID);
		$data['konsultasi'] = $this->M_data->find('kartubimbingan', $whereIDCard, '', '', 'users', 'users.ID = kartubimbingan.IDDosenPembimbing');

		$this->load->view('template/navbar');
		$this->load->view('dosen/detailMahasiswa', $data);
	}

	function accUsers($ID, $users)
	{
		$where = array(
			'IDTugasAkhirPmb' => $ID,
			'IDDosenPmb' => $_SESSION['ID']
		);

		$cek['Pembimbing'] = $this->M_data->find('tugasakhir', $where, '', '', 'pembimbing', 'pembimbing.IDTugasAkhirPmb = tugasakhir.IDTugasAkhir');

		foreach ($cek['Pembimbing']->result() as $c) {

			$data['Notifikasi'] = $users . ' ' . $c->JudulTugasAkhir . ' Telah Di ACC';
			$data['Catatan'] = $users . ' Telah Di ACC Oleh : <br>' . $this->session->userdata('Nama') . ' Sebagai Pembimbing ';
			$data['IDPenerima'] = $c->IDMahasiswaTugasAkhir;
			$data['IDPengirim'] = $_SESSION['ID'];
			$data['TanggalNotifikasi'] = date('Y-m-d');
			$data['StatusNotifikasi'] = $users;

			$accept['Status' . $users] = 1;

			$this->M_data->update('IDPembimbing', $c->IDPembimbing, 'pembimbing', $accept);
			$this->M_data->save($data, 'notifikasi');
		}
	}

	function catatan($IDtugasakhir, $ID, $status)
	{

		$config['upload_path'] = './assets/' . $status . '/';
		$config['allowed_types'] = 'pdf';
		$config['overwrite'] = true;
		$config['max_size'] = 0;
		$config['file_name'] = $ID;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file' . $status)) {
			$error = array('error' => $this->upload->display_errors());
			echo json_encode($error);
	
		} else {

			$file = $this->upload->data();

			$dataTugasAkhir = array(
				'file' . $status => $file['file_name'],
				'Uploader' => $_SESSION['ID']
			);
			
			$data['TanggalBimbingan'] = date('Y-m-d');
			$data['Catatan'] = $this->input->post('note');
			$data['IDDosenPembimbing'] = $_SESSION['ID'];
			$data['IDKartuMahasiswa'] = $ID;
			$berhasil = array('hasil' =>  'Berhasil');

			$this->M_data->update('IDTugasAkhir', $IDtugasakhir, 'tugasakhir', $dataTugasAkhir);
			$this->M_data->save($data, 'kartubimbingan');
			echo json_encode($berhasil);
		}
	}

}
