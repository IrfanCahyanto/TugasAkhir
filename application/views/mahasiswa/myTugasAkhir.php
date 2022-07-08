<head>
	<script type="text/javascript">
		$('input[type="file"]').on('change', function() {
			var val = $(this).val();
			$(this).siblings('label').text(val);
		});
	</script>
</head>
<div class="card-body">
	<div class="form-row">
		<div class="form-group col-md">

			<h5> <i class="fas fa-book fa-sm"></i> <?php foreach ($tugasakhir->result() as $s) {
				echo $s->JudulTugasAkhir; ?> 
			</h5>	

			<?= word_limiter($s->Deskripsi, 100); } ?>

			<div class="form-row mt-3">
				<div class="form-group col-md">

					<?php 
					$result = $pmb === FALSE ? 0 : $pmb->num_rows();
					if ($result === 1) { 
						$file = $s->FileTugasAkhir;
						$sesi = 'TugasAkhir';
					} else { 
						$file = $s->FileProposal;
						$sesi = 'Proposal';
					} ;?>

					<?php 

					$proposal = $s->FileProposal;

					$tugasakhir = $s->FileTugasAkhir;

					if (empty($proposal)) {
						$disablep = 'disabled';
					} else {
						$disablep = '';
					}

					if (empty($tugasakhir)) {
						$disables = 'disabled';
					} else {
						$disables = '';
					} 
					?>

					<a class="card-body" <?php if (empty($proposal)) {
						echo "";
					} else {
						echo "href=".base_url("ControllerGlobal/downloadFile/Proposal/".$proposal);
					} ?>> <i class="fa fa-download"></i> Proposal </a>

					<a class="card-body" <?php if (empty($tugasakhir)) {
						echo "";
					} else {
						echo "href=".base_url("ControllerGlobal/downloadFile/TugasAkhir/".$tugasakhir);
					} ?>> <i class="fa fa-download"></i> Tugas Akhir </a>

				</div>
				
			</div>
		</div>
		<div class="form-group mr-3">
					<form method="post" id="mydata" action="<?php echo base_url('Mahasiswa/uploadData/'.$sesi.'/'.$s->IDTugasAkhir);?>" enctype="multipart/form-data">
						<div class="input-group">
							<div class="custom-file">
								<input id="upload" type="file" name="<?= $sesi ?>" class="custom-file-input col custom-file-control" required>
								<label class="custom-file-label">Upload  <?= $sesi ?></label>					
							</div>
							<div class="input-group-append"> 
								<button class="btn btn-outline-primary" type="submit"> Upload </button>					
							</div>
						</div>
						<small> File harus berbentuk PDF </small>
					</form>		
				</div>
		<div class="form-group">
			<div class=" float-right">
				<a href="<?php echo base_url('Cetak/kartu/').$this->session->userdata('ID');?>"> <button class="btn btn-outline-primary btn-sm"> <i class="fas fa-print"> </i> Cetak </button> </a>	
			</div>
		</div>
	</div>

	<div class="form-row table-responsive" id="tugasakhir">		
		<table class="table table-borderless table-sm">
			<thead class="small">
				<tr>
					<th> Nama </th>
					<th class="text-center"> Proposal </th>
					<th class="text-center">Tugas Akhir</th>
					<th >Status Pembimbing</th>
				</tr>
			</thead>
			<?php foreach ($pembimbing->result() as $p) { ?>
				<tbody class="small"> 
					<td><?php echo $p->Nama; ?></td>
					<td class="text-center"><?php if ($p->StatusProposal) {
						echo "<i class='fas fa-check-square'></i>";
					} else {
						echo "<i class='fas fa-square'></i>";
					} ?></td>
					<td class="text-center"><?php if ($p->StatusTugasAkhir) {
						echo "<i class='fas fa-check-square'></i>";
					} else {
						echo "<i class='fas fa-square'></i>";
					} ?></td>
					<td><?php echo 'Pembimbing '?></td>
				</tbody>
			<?php } ?>
		</table>
	</div>
	<?php if (!$konsultasi) { ?>
		<div class="card card-outline-secondary">
			<div class="row align-items-center m-5">
				<div class="col-md mb-5">
					<h2>Belum Ada Catatan Bimbingan</h2>
					Catatan bimbingan belum di isi oleh pembimbing, jika anda pembimbing silahkan isi catatan bimbingan mahasiswa ini dengan memasukan form catatan di atas. tanggal bimbingan akan secara otomatis masuk saat anda memasukan catatan saat itu juga.
				</div>
				<div class="col-md-auto">
					<img style="height: 10rem" src="<?= base_url('assets/web/sad.jpg') ?>" >	
				</div>
			</div>

		</div>
	<?php } else { ?>
		<div id="table-wrapper">
			<div class="mt-2" style="overflow:auto; height: 16rem">
				<table class="table table-sm small">
					<thead>
						<tr>
							<th style='width: 5rem'>No</th>
							<th style="width: 12rem">Tanggal</th>
							<th>Pembimbing</th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($konsultasi)): ?>
							<?php $no = '1'; ?>
							<?php foreach ($konsultasi->result() as $k) {	?>
								<tr>
									<td><?php echo $no++;?></td>
									<td><?php echo longdate_indo($k->TanggalBimbingan);?></td>
									<td><?php echo $k->Nama;?></td>
								</tr>
								<tr>
									<th>Catatan</th>
									<td colspan="3"><?php echo $k->Catatan;?></td>
								</tr>
							<?php } ?>
						<?php endif ?>
					</tbody>	
				</table>
			</div>
		</div>
	<?php } ?>
</div>