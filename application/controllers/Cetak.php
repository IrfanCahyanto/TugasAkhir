<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cetak extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Pdf');
	}


	public function index()
	{
		$pdf = new FPDF('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetFont('Times', 'B', 12);
		$pdf->Cell(0, 0, 'FORMULIR PENGAJUAN JUDUL TUGAS AKHIR', 0,0,'C');
		$pdf->Ln(15);
		$pdf->SetFont('Times', '', 12);
		$pdf->Cell(0, 0, 'Kepada Yth,', 0,0,'L');
		$pdf->Cell(0, 0, 'Bogor,.../.../2022', 0,0,'R');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'Ketua Prodi RPKK/RPLK/RSK', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'Politeknik Siber dan Sandi Negara', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'Di', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'Tempat', 0,0,'L');
		$pdf->ln(10);
		$pdf->Cell(0, 0, 'Dengan hormat,', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(20, 0, '', 0,0,'L');
		$pdf->Cell(0,0,'Saya yang bertanda tangan dibawah ini:',0,1,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'Nama 	   : .............', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'NPM 	     : .............', 0,0,'L');
		$pdf->ln(5);
		
		$pdf->Cell(0, 0, 'Adapun judul yang akan saya ajukan:', 0,0,'L');
		$pdf->ln(5);
		$pdf->MultiCell(0,5,'...........................................................................................................................................................................................................................................................................................................................',0,'J');
		$pdf->ln(10);
		$pdf->Cell(150, 0, '', 0,0,'L');
		$pdf->Cell(0, 0, 'Hormat Saya,', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(150, 0, '', 0,0,'L');
		$pdf->Cell(0, 0, 'Mahasiswa', 0,0,'L');
		$pdf->ln(20);
		$pdf->Cell(150, 0, '', 0,0,'L');
		$pdf->Cell(0, 0, 'Muhammad Irfan Cahyanto', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, 'Dosen Konsultasi Judul:', 0,0,'L');
		$pdf->ln(5);
		$pdf->Cell(0, 0, '1.', 0,0,'L');
		$pdf->ln(15);
		$pdf->SetFont('Times', 'U', 12);
		$pdf->Cell(0, 0, '............', 0,0,'L');
		$pdf->ln(5);
		$pdf->SetFont('Times', '', 12);
		$pdf->Cell(0, 0, 'NIP', 0,0,'L');
		$pdf->ln(10);
		$pdf->Cell(0, 0, '2.', 0,0,'L');
		$pdf->ln(15);
		$pdf->SetFont('Times', 'U', 12);
		$pdf->Cell(0, 0, '...........', 0,0,'L');
		$pdf->ln(5);
		$pdf->SetFont('Times', '', 12);
		$pdf->Cell(0, 0, 'NIP', 0,0,'L');
		
		$pdf->Output();
	}

	public function kartu($nim)
	{
		$where = array('ID' => $nim);
		$where1 = array('IDKartuMahasiswa' => $nim);
		$data = $this->M_data->find('kartubimbingan', $where1, '', '', 'users', 'users.ID = kartubimbingan.IDDosenPembimbing');

		$where2 = array('IDMahasiswaTugasAkhir' => $nim);
		
		$tugasakhir = $this->M_data->find('tugasakhir', $where2);
		
		$mhs = $this->M_data->find('users', $where,'', '', 'programstudi', 'programstudi.IDProgramStudi = users.IDProgramStudiUser', 'bidangminat', 'bidangminat.IDBidangMinat = users.IDBidangMinatUser');
		$pdf = new Pdf('P','mm','A4');
		$pdf->AddPage();
		$pdf->SetFont('Times', 'BU', 12);
		$pdf->Cell(0, 0, 'KARTU KONTROL BIMBINGAN', 0,0,'C');
		$pdf->ln(10);
		$pdf->SetFont('Times', '', 12);
		foreach ($mhs->result() as $m) {
			$pdf->Cell(30, 5, 'Nama', 0,0,'L');
			$pdf->Cell(0, 5, ': '.$m->Nama, 0,0,'L');	
			$pdf->ln(7);
			$pdf->Cell(30, 5, 'NPM', 0,0,'L');
			$pdf->Cell(0, 5, ': '.$m->ID, 0,0,'L');
			$pdf->ln(7);
			$pdf->Cell(30, 5, 'Program Studi', 0,0,'L');
			$pdf->Cell(0, 5, ': '.$m->ProgramStudi, 0,'J');
			$pdf->ln(7);       
			$pdf->Cell(30, 5, 'Bidang Minat', 0,0,'L');
			$pdf->Cell(0, 5, ': '.$m->BidangMinat, 0,'J');
			$pdf->ln(7);
			$pdf->Cell(30, 5, 'Judul', 0,0,'L');
			foreach ($tugasakhir->result() as $s) {
				$pdf->MultiCell(0, 5, ': '.$s->JudulTugasAkhir
					, 0,'J');
				if (file_exists('assets/images/QRCode/'.$s->QRCode)) {
					$pdf->Image(base_url('assets/images/QRCode/'.$s->QRCode),170,6,30);
				}
			}
		}
		$pdf->ln(10);
		/*srand(microtime()*1000000);*/
		if ($data) {

			$pdf->Cell(10,7,'No',1);		
			$pdf->Cell(45,7,'Tanggal', 1);
			$pdf->Cell(70,7,'Catatan', 1);
			$pdf->Cell(65,7,'Pembimbing', 1);
			$pdf->Ln();
			$no = 1;
			$pdf->SetWidths(array(10,45,70,65));
			foreach ($data->result() as $d) {

				for($i=0;$i<1;$i++)
					$pdf->Row(array($no++,longdate_indo($d->TanggalBimbingan),$d->Catatan,$d->Nama));
			}		
		} else {
			$pdf->MultiCell(100,7, 'Tidak Ditemukan Catatan Bimbingan Dosen Untuk Tugas Akhir Ini', 0);
		}


		$pdf->Cell(120, 0, '', 0,0,'L');
		$pdf->Cell(0, 35, 'Bogor, '.date_indo(date('Y-m-d')), 0,2,'L');

		foreach ($mhs->result() as $k) {
			$where = array('IDBidangMinat' => $k->IDBidangMinatUser);
			$programstudi = $this->M_data->find('bidangminat', $where,  '', '', 'users', 'users.ID = bidangminat.IDDosen', 'programstudi', 'programstudi.IDProgramStudi = bidangminat.IDProgramStudiKsn');	
		}
		foreach ($programstudi->result() as $j) {
			$pdf->Cell(0, 9, $j->Nama, 0,2,'L');
			$pdf->Cell(0, 0, 'Ka. Prodi '.$j->ProgramStudi.' '.$j->BidangMinat, 0,2,'L');
		}
		$pdf->Output();
	}
}
/* End of file controllername.php */
/* Location: ./application/controllers/controllername.php */