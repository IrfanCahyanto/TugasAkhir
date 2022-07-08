<head>
	<script type="text/javascript">
		$(document).ready(function () {
			// berfungsi untuk menghapus data
			$(".hapusNotif").on('click', (function (e) {
				e.preventDefault();
				var form = $(this);
				var formdata = false;
				var id = $(this).attr("id");

				if (window.FormData) {
					formdata = new FormData(form[0]);
				}
				swal({
						title: "Menghapus Pemberitahuan?",
						icon: "warning",
						buttons: true,
						dangerMode: true,
					})
					.then((willDelete) => {
						if (willDelete) {
							$.ajax({
								type: "POST",
								url: form.attr("href"),
								data: formdata ? formdata : form.serialize(),
								contentType: false,
								processData: false,
								cache: false,
								success: function () {
									$("#Notifikasi").load('ControllerGlobal/notifikasi');
								}
							});
						} else {
							swal("Data Tidak Dihapus!");
						}
					});
			}));
		});

	</script>
</head>

<div id="Notifikasi">
	<?php if (!$Notifikasi) { ?>
	<div class="card">
		<div class='row align-items-center m-4'>
			<div class='col-md'>
				<?php if ($_SESSION['Status'] === 'Dosen') { ?>
				<h2>Selamat Datang,
					<?= $_SESSION['Nama']?>.</h2>
				Pemberitahuan masih kosong, ini halaman dimana anda sebagai pembimbing menerima notifikasi dari prodi/pokil mengenai tugas akhir dan mahasiswa yang anda bimbing untuk melihat mahasiswa siapa yang anda bimbing. Anda bisa melihat di navigasi Tugas Akhir.
				<?php }

				elseif ($_SESSION['Status'] === 'TugasAkhir') { ?>
				<h2>Selamat Datang,
					<?= $_SESSION['Nama']?>. Sang Pejuang Tugas Akhir.</h2>
				Selamat ya karena menempuh semester akhir dimana setiap mahasiswa PoltekSSN pasti akan mengalami yang namanya Fase pengerjaan Tugas Akhir. Saat ini Pemberitahuanmu Kosong. Di Navigasi Idea Concept Paper Kamu Bisa mengajukan ide TA yang mungkin kamu punya. Jadi bersiap siaplah jika sering di <b>TOLAK</b>. Semangat untuk tugas akhirmu !!!
				<?php }

				elseif ($_SESSION['Status'] === 'Mahasiswa') { ?>
				<h2>Selamat Datang,
					<?= $_SESSION['Nama']?>. Mahasiswa Baru Ya.</h2>
				Saat ini sistem hanya bisa melakukan login ! sistem ini akan bisa di gunakan jika admin telah menerima dan memvalidasimu jika kau sudah mulai melakukan tugas akhir ! tetep semangat ya menjalani kehidupanmu di kampus !!! Jika kamu sudah memasuki semester akhir dan masih belum bisa mengakses ide tugas akhir. silahkan tanyakan ke jurusan yah.
				<?php } ?>
			</div>
			<div class='col-md-3'>
				<img class="card-img-top" src="<?= base_url('assets/web/jelaskan.jpg')?>">
			</div>
		</div>
	</div>
	<?php } else { ?>
	<div class='card container' style="height: 30rem; overflow: auto">
		<?php foreach ($Notifikasi->result() as $p) {
			?>
		<div class="tabel<?php echo $p->IDNotifikasi;?>" id="container">
			<div>
				<div class="card-body">
					<div class="form-row">
						<div class="form-group mr-1" style="height: 5rem; width: 7rem">
							<?php if (file_exists('assets/images/User/'.$p->Foto)) {
									$base_url = base_url('assets/images/User/'.$p->Foto); 
								} else {
									$base_url = base_url('assets/web/user.png');
								} 
								?>
							<img class="card-img-top" src="<?= $base_url;?>" alt="Card image">
						</div>
						<div class="form-group col">
							<h5 class="card-title">
								<?php echo $p->Notifikasi ?>
								<?php if ($p->StatusNotifikasi === 'Diterima') { ?>
								<span class="badge badge-success"> Diterima </span>
								<?php } elseif ($p->StatusNotifikasi === 'Ditolak') { ?>
								<span class="badge badge-danger"> Ditolak </span>
								<?php } else { ?>
								<span class="badge badge-info"> Informasi </span>

								<!-- <?php }	?> <!-- <a id="<?php echo $p->IDNotifikasi;?>" class="hapusNotif" href="<?= base_url('ControllerGlobal/deleteNotifikasi/'.$p->IDNotifikasi);?>"><i class="btn btn-danger btn-sm delete"></i></a>-->
								
								<a href="<?= base_url('ControllerGlobal/deleteNotifikasi/'.$p->IDNotifikasi);?>" class="btn btn-danger btn-sm delete" onclick="return confirm('Apakah Yakin Menghapus Notif Ini?');"><i class="fas fa-trash"></i></a>

								<h6 class="card-title"> </h6>

								<div class="form-group">
									<h6 class="card-subtitle text-muted"> <i class="fas fa-calendar fa-sm"></i>
										<?php echo longdate_indo($p->TanggalNotifikasi);?> <i class="fas fa-users fa-sm"></i>
										<?php echo $p->Nama;?>
									</h6>
								</div>
								<div>

									<i class='fas fa-sticky-note'></i>
									<?php echo $p->Catatan;?>
								</div>
						</div>

						<div class="form-group col-md-auto">
							<ul class="nav flex-column">

							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php } } ?>
	</div>

</div>
