	<script type="text/javascript" src="<?= base_url('assets/js/myscript.js');?>"></script>

	<?php if (!$programstudi) {?>
		<div class='container-fluid'>
			<div class="row align-items-center">
				<div class="col-md">
					<h2>Selamat Datang Admin</h2>
					Sistem E-Control Tugas Akhir berbasis web di sini merupakan data program studi dan bidang minat. Silahkan masukan data program studi melalui form program studi di atas. 
				</div>
				<div class="col-md-3">
					<img src="<?= base_url('assets/web/welcome.png');?>">
				</div>
			</div>
			</div>
	<?php } else { ?>
		<div id="container" class="row">
			<div class="table-responsive mr-3 col-md-5 col">
				<table class="table">
					<thead>
						<tr>
							<th scope="col">ID Program Studi</th>
							<th scope="col">Program Studi</th>
							<th><i class="fas fa-spinner fa-pulse loading" style="display: none"> </i> 
							</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($programstudi->result() as $j) {
							?>
							<tr class="tabel<?= $j->IDProgramStudi?>">
								<th scope="row"> <?= $j->IDProgramStudi;?></th>
								<td> <a id="programstudi" class="btn_view" href="<?= base_url('Admin/tabelBidangMinatAdmin/').$j->IDProgramStudi;?>"> <?= $j->ProgramStudi?> </a> </td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
			<div class="SHprogramstudi col-md-auto" style="display: none">
				<div id="SHprogramstudi"></div>
			</div>
		</div>
		<?php } ?>

