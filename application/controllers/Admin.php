<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('Status') != "Admin") {
            redirect(base_url("Home"));
        }
        $this->load->library('Ajax_pagination');
        $this->perPage = 10;
    }

    public function index()
    {
        $data['bidangminat'] = $this->M_data->find('bidangminat');
        $this->load->view('template/navbar');
        $this->load->view('admin/home', $data);
    }

    public function navigasiUsers($nav)
    {

        $where = $nav == 'Mahasiswa' ? "Status='Mahasiswa' OR Status='TugasAkhir'" : "Status='Dosen'";
        if ($nav != 'Settings') {
            $data['users'] = $this->M_data->find('users', $where);
            $data['nav'] = $nav;
        } else {
            $data = '';
        }

        $this->load->view('admin/nav/' . $nav, $data);
    }

    public function formProgramStudi()
    {
        $this->load->view('admin/form/ProgramStudi');
        $this->load->view('template/jquery/formSubmit');
    }

    public function formBidangMinat()
    {
        $where = array('Status' => 'Dosen');

        $data['result'] = 'Masukkan Program Studinya Dahulu!!!';
        $data['programstudi'] = $this->M_data->find('programstudi');
        $data['users'] = $this->M_data->find('users', $where);

        $this->load->view('admin/form/BidangMinat', $data);
        $this->load->view('template/jquery/formSubmit');
    }

    public function formAdminprodi($id)
    {
        $where = array('IDBidangMinatUser' => $id, 'Status' => 'Dosen');
        $data['dosen'] = $this->M_data->find('users', $where);
        $data['ID'] = $id;
        $this->load->view('admin/form/Adminprodi', $data);
    }

    public function formUsers($user)
    {
        $data['bidangminat'] = $this->M_data->find('bidangminat');
        $data['programstudi'] = $this->M_data->find('programstudi');
        $data['user'] = $user;
        $this->load->view('admin/form/Users', $data);
        $this->load->view('template/jquery/formSubmit');
    }

    public function tabelProgramStudiAdmin()
    {
        $data['programstudi'] = $this->M_data->find('programstudi');
        $this->load->view('admin/tabel/ProgramStudi', $data);
    }

    public function tabelBidangMinatAdmin($id)
    {
        $where = array('IDProgramStudiKsn' => $id);

        $data['bidangminat'] = $this->M_data->find('bidangminat', $where, '', '', 'users', 'users.ID = bidangminat.IDDosen');

        if ($data['bidangminat']) {
            foreach ($data['bidangminat']->result() as $k) {
                $whereUsers = array(
           //       'IDBidangMinatUser' => $k->IDBidangMinat,
                    'Status' => 'Dosen',
                );
            }
            $data['users'] = $this->M_data->find('users', $whereUsers);
        } else {
            $data['users'] = 'Tidak Ditemukan Bidang Minat';
        }

        $this->load->view('admin/tabel/BidangMinat', $data);

    }

    public function submitAdminprodi($id)
    {
        $where = array(
            'IDBidangMinat' => $id,
        );

        $data['IDDosen'] = $this->input->post('adminprodi');

        $this->M_data->update('IDBidangMinat', $id, 'bidangminat', $data);
        redirect('Admin');
    }

    public function tabelNavigasi($page, $user)
    {
        if (!$page) {
            $offset = 0;
        } else {
            $offset = $page;
        }

        $keywords = $this->input->post('keywords');
        $sortBy = $this->input->post('sortBy');
        $search = $this->input->post('search');

        if (!empty($keywords)) {
            $conditions['search']['keywords'] = $keywords;
        }
        if (!empty($sortBy)) {
            $conditions['search']['sortBy'] = $sortBy;
        }
        if ($user != 'Daftar') {
            $conditions['start'] = $offset;
            $conditions['limit'] = $this->perPage;
        } else {
            $conditions['start'] = '';
            $conditions['limit'] = '';
        }

        if ($user === 'Mahasiswa') {
            $where = "(Status='Mahasiswa' OR Status='TugasAkhir')";
        } else {
            $where['Status'] = $user;
        }

        $data['users'] = $this->M_data->find('users', $where, '', '', 'programstudi', 'programstudi.IDProgramStudi = users.IDProgramStudiUser', 'bidangminat', 'bidangminat.IDBidangMinat = users.IDBidangMinatUser', '', '', $conditions, $search);

        $total = $this->M_data->find('users', $where, '', '', 'programstudi', 'programstudi.IDProgramStudi = users.IDProgramStudiUser', 'bidangminat', 'bidangminat.IDBidangMinat = users.IDBidangMinatUser');

        if ($data['users']) {

            $data['programstudi'] = $this->M_data->find('programstudi');

            $wherebidangminat = array(
              'IDProgramStudiKsn' => $data['users']->row()->IDProgramStudiUser   
          );

            $data['bidangminat'] = $this->M_data->find('bidangminat', $wherebidangminat);

        }

        $totalRec = $total != false ? $total->num_rows() : 0;
        $config['target'] = '#tabelUsers';
        $config['base_url'] = base_url() . 'Admin/tabelNavigasi/' . $page . '/' . $user;
        $config['total_rows'] = $totalRec;
        $config['per_page'] = $this->perPage;
        $config['link_func'] = 'search' . $user;

        $user === 'Daftar' ? '' : $this->ajax_pagination->initialize($config);


        $data['status'] = $user;



        $this->load->view('admin/tabel/Users', $data, false);
        $this->load->view('template/jquery/btnSubmit');
    }

    public function delete_dosen($nik)
    {
        $where = array('ID' => $nik);
        $cek = $this->M_data->find('users', $where);

        foreach ($cek->result() as $c) {
            unlink('./assets/images/User/' . $c->foto_dsn);
            $this->M_data->delete($where, 'users');
        }
    }

    public function delete_mahasiswa($nim)
    {
        $where = array('ID' => $nim);
        $cek = $this->M_data->find('users', $where);

        foreach ($cek->result() as $c) {
            unlink('./assets/images/User/' . $c->foto_dsn);
            $this->M_data->delete($where, 'users');
        }
    }

    public function saveProgramStudi()
    {
        $data['IDProgramStudi'] = $this->input->post('id_programstudi');
        $data['ProgramStudi'] = $this->input->post('programstudi');

        $this->M_data->save($data, 'programstudi');

        $notif = array(
            'head' => 'Data Berhasil Disimpan',
            'isi' => 'Program Studi Telah Disimpan. Silahkan Isi Bidang Minat Untuk Program Studi Yang Baru DiBuat',
            'sukses' => 1,
            'ID' => 'ProgramStudiAdmin',
            'func' => 'Admin/tabelProgramStudiAdmin',
        );
        echo json_encode($notif);
    }

    public function saveBidangMinat()
    {
        $prodi = $this->input->post('prodi');
        $data['IDBidangMinat'] = $this->input->post('id');
        $data['bidangminat'] = $this->input->post('bidangminat');
        $data['IDProgramStudiKsn'] = $this->input->post('id_programstudi');
        $data['IDDosen'] = $prodi === null ? '' : $prodi;
        $this->M_data->save($data, 'bidangminat');
        $notif = array(
            'head' => 'Data Berhasil Disimpan',
            'isi' => 'Bidang Minat Telah Di Simpan',
            'sukses' => 1,
            'ID' => 'ProgramStudiAdmin',
            'func' => 'Admin/tabelProgramStudiAdmin',
        );
        echo json_encode($notif);
    }

    private function sendEmail($email, $nama, $password)
    {
        $this->email->from('cahyantoirfan@gmail.com', 'Politeknik Siber dan Sandi Negara');
        $this->email->to($email);

        $this->email->subject('Sistem E-Control Tugas Akhir');
        $this->email->message('Selamat Datang ' . $nama . ' di Jurusan Kriptografi Politeknik Siber dan Sandi Negara. Sekarang anda bisa login sistem dengan menggunakan ID anda. Password : ' . $password . '  Semoga harimu menyenangkan.');

        return $this->email->send();

    }

    private function imageProses($path)
    {

        $config['image_library'] = 'gd2';
        $config['source_image'] = './assets/images/User/' . $path;
        $config['create_thumb'] = false;
        $config['maintain_ratio'] = true;
        $config['width'] = 300;
        $config['height'] = 300;

        $this->load->library('image_lib', $config);
        return $this->image_lib->resize();
    }

    public function sendPassword($id)
    {
        $where = array('ID' => $id);
        $user = $this->M_data->find('users', $where);
        foreach ($user->result() as $u) {
            $nama = $u->Nama;
            $email = $u->Email;
        }
        $password = random_string('alnum', 12);
        if ($this->sendEmail($email, $nama, $password)) {
            $data = array('Password' => md5($password));
            $this->M_data->update('ID', $id, 'users', $data);
            echo "Password Telah di Kirim ke Email Pengguna";
        } else {
            echo "Password Gagal diKirim Periksa Server Anda";
        }
    }

    public function saveUsers($user) // Menyimpan Form Dosen

    {
        $id = $this->input->post('id');
        $nama = $this->input->post('name');
        $email = $this->input->post('email');
        $programstudi = $this->input->post('id_programstudi');
        $bidangminat = $this->input->post('bidangminat');

        $data = array(
            'ID' => $id,
            'Nama' => $nama,
            'Email' => $email,
            'IDBidangMinatUser' => $bidangminat,
            'IDProgramStudiUser' => $programstudi,
            'Password' => md5('12345'),
            'Status' => $user,
        );

        $this->M_data->save($data, 'users');

        $notif = array(
            'head' => 'Data Berhasil Ditambahkan',
            'isi' => 'Silahkan Ke Bagian Tabel Untuk Mengirim Password Untuk '.$user,
            'sukses' => 1,
            'ID' => $user,
            'func' => 'Admin/tabelNavigasi/0/'.$user,
        );

        echo json_encode($notif);
    }

    public function acceptDaftar($ID, $disetujui)
    {
        if ($disetujui === 'true') {

            $data['Status'] = 'Mahasiswa';
            $this->M_data->update('ID', $ID, 'users', $data);

            $this->imageProses($result->Foto);
            echo "Pendaftaran Diterima";

        } else {

            $where = array('ID' => $ID);
            $cek = $this->M_data->find('users', $where);

            foreach ($cek->result() as $c) {
                if ($this->M_data->delete($where, 'users')) {
                    echo "Pendaftaran Ditolak";
                    unlink('./assets/images/User/' . $c->Foto);
                }
            }

        }
    }

    public function filterAdminprodi()
    {
        $id_programstudi = $this->input->post('id_programstudi');
        $where = array(
            //'ID' < 4,
            'IDProgramStudiUser' => $id_programstudi,
            'Status' => 'Dosen',
        );
        $data = $this->M_data->find('users', $where);

        if ($data) {
            $lists = "<option value=''> Pilih Admin Program Studi </option>";
            foreach ($data->result() as $d) {
                $lists .= "<option value='" . $d->ID . "'>" . $d->Nama . "</option>";
            }
        } else {
            $lists = "<option value=''> Tidak Ada </option>";
        }

        $callback = array('list' => $lists);
        echo json_encode($callback);
    }

    public function statusTugasAkhir($ID)
    {
        $data['Status'] = 'TugasAkhir';
        if (!$this->M_data->update('ID', $ID, 'users', $data)) {
            echo 0;
        } else {
            echo 'Status Mahasiswa Berhasil Diubah';
        }

    }

    function updateUser()
    {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $programstudi = $this->input->post('programstudi');
        $bidangminat = $this->input->post('bidangminat');
        $email = $this->input->post('email');
        $nohp = $this->input->post('nohp');
        $idlama = $this->input->post('idlama');

        $data = array('ID' => $id, 'Nama' => $name, 'IDProgramStudiUser' => $programstudi, 'IDBidangMinatUser' => $bidangminat, 'Email' => $email, 'NoHP' => $nohp );

        $this->M_data->update('ID', $idlama, 'users', $data);
    }

    public function update()
    {
        $id = $this->input->post("id");
        $value = $this->input->post("value");
        $modul = $this->input->post("modul");
        $data[$modul] = $value;
        $this->M_data->update('ID', $id, 'users', $data);
        echo "{}";
    }

    public function delete_user($id)
    {
        $this->db->where('ID', $id);
        $result=$this->db->delete('users');

        if ($result) {
            redirect('Admin');
        }
    }
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
