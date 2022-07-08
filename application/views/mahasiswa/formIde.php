<!-- <head>
	<script type="text/javascript">
		$('input[type="file"]').on('change', function() {
			var val = $(this).val();
			$(this).siblings('label').text(val);
		});
	</script>
</head> -->

<!-- foreach ($idetugasakhir->result() as $s){
$sesi = 'ICP';
$file = $s->FileIde; -->

<form method="POST" action="<?= base_url('Mahasiswa/sendIde') ;?>">
	<div class="form-group">
		<!-- <input name="fileide" type="file" class="form-control" enctype="multipart/form-data">
		<small>Unggah File Idea Concept Paper</small> -->

		<textarea class="form-control" name="deskripsi" placeholder="Deskripsi Latar Belakang" id="deskripsi" minlength="100" rows="16" value=''></textarea>
		<small>Minimal 100 Kata</small>

	</div>

	<div class="form-row">
		<div class="form-group col-md col">
			<input class="form-control form-control-sm" type="text" name="judul" id="judul" placeholder="Judul Tugas Akhir" required>
		</div>

		<div class="form-group col-auto">
			<button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane fa-sm"></i> Kirim</button>
		</div>
	</div>
</form>	