<head>
	<script type="text/javascript" src="<?php echo base_url('assets/js/myscript.js');?>">
	</script>
</head>
<?php if ($bidangminat) { ?>
	<div>
		<table class="table w-auto">
			<thead>
				<th>ID</th>
				<th>Bidang Minat</th>
				<th>Prodi/Pokil</th>
			</thead>
			<?php foreach ($bidangminat->result() as $k) { ?>
				<tbody>
					<tr>
						<td scope="row"> <?php echo $k->IDBidangMinat;?></td>
						<td> <?php echo $k->BidangMinat;?> </td>
						<td> <?php if (empty($k->Nama)) { ?>
							<?php if ($users) { ?>
								<form class='mb-2' method="POST" action="<?php echo base_url('Admin/submitAdminprodi/'.$k->IDBidangMinat);?>" id="adminprodi<?=$k->IDBidangMinat;?>">
									<div class="form-row">
										<div class="col">
											<select name="adminprodi" class="custom-select form-control-sm small">
												<option selected>Menetapkan Admin Prodi/Pokil <?php echo $k->BidangMinat;?></option>
												<?php foreach ($users->result() as $j) { ?>
												<?php if ($j->IDBidangMinatUser === $k->IDBidangMinat) { ?>
													<option value="<?php echo $j->ID;?>"><?php echo $j->Nama;?></option>
												<?php } ?>
												<?php } ?>
											</select>
										</div>
										<div class="col-auto">
											<button type="submit" class="btn btn-sm btn-primary"> <i class='fas fa-paper-plane'></i> </button>
										</div>
									<?php } else {
										echo "Mohon masukan data dosen untuk bidang minat ini";
									} ?>
								</div>
							</form>
							<?php 
						} else { ?>
							<a id='adminprodi' class='btn_view' href="<?php echo base_url('Admin/formAdminprodi/').$k->IDBidangMinatUser; ?>"><?php echo $k->Nama ?> </a>
						<?php } ?>
					</td>
				</tr>		
			</tbody>
		<?php } ?>
	</table>
	<div class="SHadminprodi" style="overflow: hidden;" style="display: none">
		<div id="SHadminprodi">

		</div>
	</div>	

</div>
<?php } else { ?>
	<div class="col-md-auto text-center">
		<div class='container-fluid mt-5'>
			<div class='row align-items-center'>
				<div class='col-md'>
					<h2>Bidang Minat untuk Program Studi ini tidak ditemukan.</h2>
					Silahkan tambahkan bidang minat dengan memasukannya melalui form bidang minat di atas. 
				</div>
				<div class='col-md-3'>
					<img src="<?= base_url('assets/web/sad.jpg')?>">
				</div>
			</div>
		</div>
	</div>
	<?php } ?>