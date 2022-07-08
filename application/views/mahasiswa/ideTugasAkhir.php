<?php if ($ide_tugasakhir) { ?>
	<div style="height: 30rem; overflow: auto">
		<?php foreach ($ide_tugasakhir->result() as $u) {	?>

			<h6 class="card-title"> <i class="fas fa-book fa-xs"></i> <?php echo $u->JudulIde;?></h6>
			<h6 class="card-subtitle mb-2 text-muted"><i class="fas fa-calendar-alt fa-xs"></i> <?php echo $u->TanggalIde;?></h6>

			<p class="card-text text-justify"><?php echo $u->DeskripsiIde;?></p>
			<hr>

		<?php } ?>
	</div> 
<?php } else { ?>
	<div class='row align-items-center'>
		<div class='col-md'>
			<h2>Idea Concept Paper Tidak Ditemukan</h2>
			Hi, Taruna Satria!!! Silahkan ajukan ICPmu selengkap dan sebagus mungkin ya!! Isi form di sebelah kiri untuk mengajukan ICP yang ingin kamu ajukan! Semangat tarunaa!!
		</div>
		<div class='col-md-5'>
			<img class="card-img-top" src="<?= base_url('assets/web/ide.jpg')?>">
		</div>
	</div>
<?php } ?>

