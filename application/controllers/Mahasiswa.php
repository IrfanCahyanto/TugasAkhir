<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mahasiswa extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $status = $this->session->userdata('Status');
        if (!(($status == "Mahasiswa") or ($status == "TugasAkhir"))) {
            redirect(base_url("Home"));
        }
    }

    public function index()
    {
        $where = array('IDPenerima' => $_SESSION['ID']);
        $skrip = array('IDMahasiswaTugasAkhir' => $_SESSION['ID']);
        $data['pemberitahuan'] = $this->M_data->find('notifikasi', $where, 'IDNotifikasi', 'DESC', 'users', 'users.ID = notifikasi.IDPengirim');
        $whereUsers = array('ID' => $_SESSION['ID']);
        $data['users'] = $this->M_data->find('users', $whereUsers, '', '', 'programstudi', 'programstudi.IDProgramStudi = users.IDProgramStudiUser');

        $data['tugasakhir'] = $this->M_data->find('tugasakhir', $skrip);
        $this->load->view('template/navbar')->view('mahasiswa/home', $data);
    }

    public function sendIde()
    {
        $id_ide = time();
        $judul = $this->input->post('judul');
        $deskripsi = $this->input->post('deskripsi');
        $tanggal = longdate_indo(date('Y-m-d'));
        $nim = $_SESSION['ID'];

        $ide = array('IDIde' => $id_ide, 'IDIdeMahasiswa' => $nim, 'JudulIde' => $judul, 'DeskripsiIde' => $deskripsi, 'Tanggalide' => $tanggal);

        $where = array('JudulTugasAkhir' => $judul);

        $tugasakhir = $this->M_data->find('tugasakhir', $where);

        if ($tugasakhir) {
            $notif = array(
                'head' => 'Idea Concept Paper gagal diajukan!',
                'isi' => 'Judul Tugas Akhir yang sama sudah pernah ada',
                'sukses' => 0,
            );
        } else {
            $this->M_data->save($ide, 'idetugasakhir');
            $notif = array(
                'head' => 'Idea Concept Paper berhasil diajukan!',
                'isi' => 'Silahkan tunggu validasi dari Prodi/Pokil',
                'ID' => 'ideTugasAkhir',
                'func' => 'Mahasiswa/ideTugasAkhir',
                'sukses' => 1,
            );
        }

        echo json_encode($notif);
    }

    public function sendIde2()
    {
        $id_ide = time();
        $judul = $this->input->post('judul');
        $fileide = $this->input->post('fileide');
        $tanggal = longdate_indo(date('Y-m-d'));
        $nim = $_SESSION['ID'];

        $ide = array('IDIde' => $id_ide, 'IDIdeMahasiswa' => $nim, 'JudulIde' => $judul, 'FileIde' => $fileide, 'Tanggalide' => $tanggal);

        $where = array('JudulTugasAkhir' => $judul);

        $tugasakhir = $this->M_data->find('tugasakhir', $where);

        if ($tugasakhir) {
            $notif = array(
                'head' => 'Ide tugas akhir gagal diajukan!',
                'isi' => 'Judul tugas akhir yang sama sudah pernah ada',
                'sukses' => 0,
            );
        } else {
            $this->M_data->save($ide, 'idetugasakhir');
            $notif = array(
                'head' => 'Ide tugas akhir berhasil diajukan!',
                'isi' => 'Silahkan tunggu validasi dari Prodi/Pokil',
                'ID' => 'ideTugasAkhir',
                'func' => 'Mahasiswa/ideTugasAkhir',
                'sukses' => 1,
            );
        }

        echo json_encode($notif);
    }

    public function form_ide()
    {
        $this->load->view('mahasiswa/formIde');
    }

    function ideTugasAkhir()
    {
        $where = array('IDIdeMahasiswa' => $_SESSION['ID']);
        $data['ide_tugasakhir'] = $this->M_data->find('idetugasakhir', $where, 'IDIde', 'DESC');
        $this->load->view('mahasiswa/ideTugasAkhir', $data);
    }

    public function myTugasAkhir()
    {
        $whereSK = array('IDMahasiswaTugasAkhir' => $_SESSION['ID']);
        $data['tugasakhir'] = $this->M_data->find('tugasakhir', $whereSK, '', '', 'users', 'users.ID = tugasakhir.IDTugasAkhir');
        $whereKB = array('IDKartuMahasiswa' => $_SESSION['ID']);
        $data['konsultasi'] = $this->M_data->find('kartubimbingan', $whereKB, '', '', 'users', 'users.ID = kartubimbingan.IDKartuMahasiswa');

        $nim = $_SESSION['ID'];

        foreach ($data['tugasakhir']->result() as $s) {
            $proposal = true;
            $ID = $s->IDTugasAkhir;
            $where = array(
                'IDTugasAkhirPmb' => $ID,
                'StatusProposal' => $proposal,
            );
            $data['pmb'] = $this->M_data->find('pembimbing', $where);

        }

        foreach ($data['tugasakhir']->result() as $m) {
            $wherePmb = array('IDTugasAkhirPmb' => $m->IDTugasAkhir);
            $data['pembimbing'] = $this->M_data->find('pembimbing', $wherePmb, '', '', 'users', 'users.ID = pembimbing.IDDosenPmb');
        }

        $this->load->view('template/jquery/formSubmit');
        $this->load->view('mahasiswa/myTugasAkhir', $data);
    }

    public function uploadData($sesi, $ID)
    {

		$config['upload_path'] = './assets/' . $sesi . '/';
		$config['allowed_types'] = 'pdf';
		$config['overwrite'] = true;
		$config['max_size'] = 0;
        $config['file_name'] = $ID;

        if (!is_dir('./assets/' . $sesi)) {
            mkdir('./assets/' . $sesi);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($sesi)) {
            $notif = array('head' => "Maaf Terjadi Kesalahan",
                'isi' => $this->upload->display_errors(),
                'sukses' => 0);

        } else {
        
            $file = $this->upload->data();

			$data = array(
				'file' . $sesi => $file['file_name'],
				'Uploader' => $_SESSION['ID']
            );
            
            if ($this->M_data->update('IDTugasAkhir', $ID, 'tugasakhir', $data)) {

                $notif = array(
                    'head' => "File " . $sesi . " Berhasil di Upload",
                    'isi' => "Silahkan Minta Dosen Untuk Mengecek",
                    'ID' => "MyTugasAkhir",
                    'func' => "/Mahasiswa/myTugasAkhir",
                    'sukses' => 1,

                );
            } else {
                $notif = array(
                    'head' => "File " . $sesi . " Tidak Berhasil di Upload",
                    'isi' => "Maaf Terjadi Kesalahan",
                    'sukses' => 0);
            }
        }
        echo json_encode($notif);
    }

    public function uploadICP($sesi, $ID)
    {
        $config['upload_path'] = './assets/' . $sesi . '/';
        $config['allowed_types'] = 'pdf';
        $config['overwrite'] = true;
        $config['max_size'] = 0;
        $config['file_name'] = $ID;

        if (!is_dir('./assets/' . $sesi)) {
            mkdir('./assets/' . $sesi);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($sesi)) {
            $notif = array('head' => "Maaf Terjadi Kesalahan",
                'isi' => $this->upload->display_errors(),
                'sukses' => 0);

        } else {
        
            $file = $this->upload->data();

            $data = array(
                'file' . $sesi => $file['file_name'],
                'Uploader' => $_SESSION['ID']
            );
            
            if ($this->M_data->update('IDIde', $ID, 'idetugasakhir', $data)) {

                $notif = array(
                    'head' => "File " . $sesi . " Berhasil di Upload",
                    'isi' => "Silahkan Minta Dosen Untuk Mengecek",
                    'ID' => "FormIde",
                    'func' => "/Mahasiswa/formIde",
                    'sukses' => 1,

                );
            } else {
                $notif = array(
                    'head' => "File " . $sesi . " Tidak Berhasil di Upload",
                    'isi' => "Maaf Terjadi Kesahalah Teknis",
                    'sukses' => 0);
            }
        }
        echo json_encode($notif);
    }

}
