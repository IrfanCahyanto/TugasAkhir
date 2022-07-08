<?php defined('BASEPATH') or exit('No direct script access allowed');

class Adminprodi extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $where = array('IDDosen' => $_SESSION['ID']);
        $dosen = $this->M_data->find('bidangminat', $where);
        if (!$dosen) {
            redirect(base_url("Home"));
        }

        $this->load->library('Ajax_pagination');
        $this->perPage = 5;
    }

    public function index()
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

    public function filterPembimbing()
    {

        $pmb1 = $this->input->post('pmb1');
        $id = array('ID' => $_SESSION['ID']);
        $adminprodi = $this->M_data->find('users', $id);

        $result = $adminprodi->row();

        $where = array(
            'IDBidangMinatUser' => $result->IDBidangMinatUser,
            'Status' => 'Dosen',
            'ID <>' => $pmb1,
        );

        $data = $this->M_data->find('users', $where);

        $lists = "<option value=''>Pilih</option>";

        foreach ($data->result() as $u) {
            $lists .= "<option value='" . $u->ID . "'>" . $u->Nama . "</option>";
        }

        $callback = array('list' => $lists);
        echo json_encode($callback);
    }

    public function nilai()
    {
        $id = $this->input->post("id");
        $value = $this->input->post("value");
        $modul = $this->input->post("modul");

        $where = array('IDTugasAkhir' => $id);

        $tugasakhir = $this->M_data->find('tugasakhir', $where);
        foreach ($tugasakhir->result() as $m) {
            $ID = $m->IDMahasiswaTugasAkhir;
            $status = array('status' => 'Alumni');
            $this->M_data->update('ID', $ID, 'users', $status);
        }
        $data[$modul] = $value;
        $this->M_data->update('IDTugasAkhir', $id, 'tugasakhir', $data);
        echo "{}";
    }

    public function formKegiatan()
    {
        $data = array('ID' => $_SESSION['ID']);
        $users = $this->M_data->find('users', $data);

        $result = $users->row();

        $where = array(
            'IDBidangMinatUser' => $result->IDBidangMinatUser,
            'Status' => 'TugasAkhir'
        );

        $data['users'] = $this->M_data->find('users', $where);
        $this->load->view('adminprodi/formKegiatan', $data);
    }

    public function ideTugasAkhir()
    {

        $id = array('ID' => $_SESSION['ID']);
        $adminprodi = $this->M_data->find('users', $id);

        $result = $adminprodi->row();
        $where = array('IDBidangMinatUser' => $result->IDBidangMinatUser);
        $dosen = array('IDBidangMinatUser' => $result->IDBidangMinatUser, 'Status' => 'Dosen');

        $data['dosen'] = $this->M_data->find(
            'users', $dosen
        );

        $data['idetugasakhir'] = $this->M_data->find('idetugasakhir', $where, 'IDIde', 'DESC', 'users', 'users.ID = idetugasakhir.IDIdeMahasiswa');

        $this->load->view('adminprodi/ideTugasAkhir', $data);
    }

    public function acceptTugasAkhir($idTugasAkhir, $sta)
    {
        $note = $this->input->post('catatan');
        $where['IDIde'] = $idTugasAkhir;

        $pengirim = $_SESSION['ID'];
        $tanggal = date('Y-m-d');

        $ideTugasAkhir = $this->M_data->find('idetugasakhir', $where, '', '', 'users', 'users.ID = idetugasakhir.IDIdeMahasiswa');

        foreach ($ideTugasAkhir->result() as $d) {
            $IDIde = $d->IDIde;
            $judul = $d->JudulIde;
            //$fileide = $d->FileIde;
            $deskripsi = $d->DeskripsiIde;
            $ID = $d->ID;
            $nama = $d->Nama;
        }

        if (!is_dir('./assets/images/QRCode')) {
            mkdir('./assets/images/QRCode');
        }

        if ($sta === 'true') {

            $hasil = 'Ditolak';

            $whereIde = array('IDIde' => $IDIde);

            $this->M_data->delete($whereIde, 'idetugasakhir');

        } else {

            $this->load->library('ciqrcode');

            $config['cacheable'] = true;
            $config['cachedir'] = './assets/';
            $config['errorlog'] = './assets/';
            $config['imagedir'] = './assets/images/QRCode/';
            $config['quality'] = true;
            $config['size'] = '1024';
            $config['black'] = array(224, 255, 255);
            $config['white'] = array(70, 130, 180);
            $this->ciqrcode->initialize($config);

            $params['data'] = base_url('Cetak/kartu/' . $ID);
            $params['level'] = 'H';
            $params['size'] = 10;
            $params['savename'] = FCPATH . $config['imagedir'] . $ID . '.png';

            $this->ciqrcode->generate($params);

            $sh = array('IDTugasAkhir' => $IDIde, 'JudulTugasAkhir' => $judul, 'QRCode' => $ID . '.png', 'Deskripsi' => $deskripsi, 'IDMahasiswaTugasAkhir' => $ID, 'Tanggal' => $tanggal);
            $this->M_data->save($sh, 'tugasakhir');

            for ($i = 1; $i < 2; $i++){

                $pmb = $this->input->post('pmb' . $i);

                // Memasukan Dosen Pembimbing Ke Database
                $dosen = array('IDDosenPmb' => $pmb, 'IDTugasAkhirPmb' => $IDIde, 'StatusProposal' => 0, 'StatusTugasAkhir' => 0, 'StatusPembimbing' => $i);
                $this->M_data->save($dosen, 'pembimbing');

                // Mengirim Pemberitahuan Ke Dosen Pembimbing
                $Catatan = 'Anda Di Tetapkan Sebagai Dosen Pembimbing ' . $nama . ' Anda sekarang bisa menyetujui proposal maupun tugas akhir ' . $nama . 'dan juga menambah kartu bimbingan ';

                $NotifDosen = array('Notifikasi' => $judul, 'Catatan' => $Catatan, 'TanggalNotifikasi' => $tanggal, 'IDPengirim' => $pengirim, 'IDPenerima' => $pmb, 'StatusNotifikasi' => 'Informasi');
                $this->M_data->save($NotifDosen, 'notifikasi');
            }

            $whereIde = array('IDIdeMahasiswa' => $ID);

            $this->M_data->delete($whereIde, 'idetugasakhir');

            $hasil = 'Diterima';

        }

        echo $sta;

        $NotifMhs = array('Notifikasi' => $judul, 'Catatan' => $note, 'TanggalNotifikasi' => $tanggal, 'IDPengirim' => $pengirim, 'IDPenerima' => $ID, 'StatusNotifikasi' => $hasil);
        $this->M_data->save($NotifMhs, 'notifikasi');

    }

    public function aksiKegiatan()
    {
        $kegiatan = $this->input->post('kegiatan');
        $jam = $this->input->post('jam');
        $tempat = $this->input->post('tempat');
        $penerima = $this->input->post('penerima');
        $tanggal = $this->input->post('tanggal');

        foreach ($penerima as $p) {

            $data = array(
                'Notifikasi' => 'Kegiatan ' . $kegiatan . ' Telah Ditetapkan',
                'Catatan' => 'Dimohon Persiapkan diri Pada : <br> <i class="fas fa-clock mr-auto"></i>  ' . $jam . '<br> <i class="fas fa-map-marker mr-auto"></i>  ' . $tempat . '<br> <i class="fas fa-calendar-alt"></i> ' . longdate_indo($tanggal),
                'IDPengirim' => $_SESSION['ID'],
                'IDPenerima' => $p,
                'TanggalNotifikasi' => date('Y-m-d'),
                'StatusNotifikasi' => $kegiatan,
            );

            $this->M_data->save($data, 'notifikasi');

        }


    }

}
