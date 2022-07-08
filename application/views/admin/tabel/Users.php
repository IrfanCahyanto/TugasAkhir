<head>
	<script type="text/javascript">
		$(function () {
			$(".a-href").click(function (e) {
				e.preventDefault();
				var form = $(this);
				var formdata = false;
				var id = $(this).attr("id");
				var title = $(this).attr("title");
				var text = $(this).attr("text");

				if (window.FormData) {
					formdata = new FormData(form[0]);
				}
				swal({
					title: title,
					text: text,
					icon: "warning",
					buttons: ["Tidak", "Ya"],
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'POST',
							url: form.attr('href'),
							data: formdata ? formdata : form.serialize(),
							contentType: false,
							processData: false,
							cache: false,
							beforeSend: function () {
								$('.loading').fadeIn();
							},
							success: function (result) {
								swal(result);
								$("#tabelMahasiswa").load("<?php echo base_url('admin/tabelNavigasi/0/Mahasiswa'); ?>");
								$("#tabelDosen").load("<?php echo base_url('admin/tabelNavigasi/0/Dosen'); ?>");
								$('.loading').fadeOut();
							}
						});
					}
				});
			});

		});

		$(function(){

			$(document).on("click","div.small",function(){
				$(this).find("span[class~='caption']").hide();
				$(this).find("input[class~='editor']").fadeIn().focus();
			});


			$(document).on("keydown",".editor",function(e){
				if(e.keyCode==13){
					var target=$(e.target);
					var value=target.val();
					var id=target.attr("data-id");
					var data={id:id,value:value};
					if (target.is(".field-ID")){
						data.modul="ID";
					}

					$.ajax({
						type:"post",
						cache:false,
						dataType: "json",
						data:data,
						url:"<?php echo base_url('ControllerGlobal/update'); ?>",
						success: function(a){
							target.hide();
							target.siblings("span[class~='caption']").html(value).fadeIn();
						},
						error: function(e) {
							swal(e.statusText, JSON.stringify(e))
						}

					})

				}

			});
		});

		$(".form<?=$status?>").load("<?=base_url('admin/formUsers/'.$status);?>");

	</script>
</head>
	
<h6 class="row ml-2 mr-2">Tambah <?=$status?></h6>
<div class="row ml-2 mr-2 form<?=$status?>"></div>

<?php if (!empty($users)) {?>
	<div class='table-responsive'>
		<table class="table small">
			<thead>
				<tr>
					<th scope="col" class='w-auto'><?= $users->row()->Status === 'Dosen' ? "NIP" : "NPM" ?></th>
					<th scope="col" class="w-auto">Nama</th>
					<th scope="col">Program Studi</th>
					<th scope="col">Bidang Minat</th>
					<th scope="col">Email</th>
					<th scope="col">No HP</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users->result() as $m): ?>
					<tr id="tabel<?=$m->ID?>" class="list-item tabel<?=$m->ID?> <?php if (empty($m->Password)) { echo 'table-warning'; }?>">
						<td> <div class="card-text small">	<span class='span-ID caption' data-id='<?= $m->ID;?>'>  <?=$m->ID;?> </span>
							<input type='number' class='field-ID form-control-sm form-control editor' value='<?=$m->ID;?>' data-id='<?= $m->ID;?>' style="display: none;"/> </div>  				
						</td>
						<td><a class="a-href" title="Kirim Password?" text="Ini akan mengubah password dan akan di kirim melalui email." href="<?=base_url('Admin/sendPassword/' . $m->ID)?>"> <?=$m->Nama;?> </a> </td>
						<td> <?=$m->ProgramStudi;?> </td>
						<td> <?=$m->BidangMinat;?> </td>
						<td> <?=$m->Email;?> </td>
						<td> <?=$m->NoHP;?> </td>
						<td> <?php if ($m->Status === 'Mahasiswa') { ?>

							<a title="Mengubah Status Mahasiswa? Mahasiswa Akan Mengakses Semua Fitur Untuk Tugas Akhir, Pastikan Semua Persyaratan Telah Terpenuhi Sebelum Mengubah Status" href="<?=base_url('Admin/statusTugasAkhir/'.$m->ID);?>" class="a-href" id="<?=$m->ID;?>"> <span class="badge badge-primary"> <?= $m->Status; ?> </span> </a>

						<?php } else { echo "<span class='badge badge-primary'>".$m->Status."</span>" ; } ?>

					</td>
					<td><button class="btn btn-success btn-sm edit" data-id='<?= $m->ID;?>'><i class="fas fa-edit"></i></button>
						<a href="<?php echo base_url(); ?>Admin/delete_user/<?= $m->ID;?>" class="btn btn-danger btn-sm delete" onclick="return confirm('Apakah Yakin Menghapus Akun Ini?');"><i class="fas fa-trash"></i></a>
					</td>
				</tr>

				<tr class="list-item tabel<?=$m->ID?> w-auto" id="formtabel<?= $m->ID;?>" style="display: none">
					<td><input type='number' class='field-ID form-control-sm form-control editor' value='<?=$m->ID;?>'> </td>
					<td><input class="form-control form-control-sm" type="text" name="updateUser[]" value="<?=$m->Nama;?>">  </td>
					<td><select id="programstudi" name="updateUser[]" class="form-control-sm" value='<?=$m->IDProgramStudi?>'>
						<?php foreach($programstudi->result() as $data) {
							echo "<option value='".$data->IDProgramStudi."; ".$data->ProgramStudi."'>".$data->ProgramStudi."</option>";
						} ?> </select>
					</td>
					<td>
						<select id="bidangminat" name="updateUser[]" class="form-control-sm">
							<?php foreach($bidangminat->result() as $data) {
								echo "<option value='".$data->IDBidangMinat."; ".$data->BidangMinat."'>".$data->BidangMinat."</option>";
							} ?> </select>

						</td>
						<td><input class="form-control form-control-sm" type="email" name="updateUser[]" value="<?=$m->Email;?>">  </td>
						<td><input class="form-control form-control-sm" type="number" name="updateUser[]" value="<?php $m->NoHP ? $m->NoHP : '';?>"> </td>
						<td><input class="form-control form-control-sm" type="text" name="updateUser[]" value="<?= $m->Status;?>" disabled> 	</td>
						<td><button class="btn btn-success btn-sm save" data-id='<?= $m->ID;?>'><i class="fas fa-check"></i></button></td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>



	<div class="alert alert-info" role="alert">
		<div class="close">
			<i class="fas fa-info"> </i>
		</div>
		Password Default Untuk Setiap Pengguna Adalah 12345
	</div>


	<?php echo $this->ajax_pagination->create_links(); ?>
	
<?php } else {?>
	<div class='container mt-5'>
		<div class="form<?=$status?>"></div>
		<div class='row align-items-center'>
			<div class='col-md'>
				<h2>Data <?=$status ?> tidak ditemukan.</h2>
				Data <?=$status?> tidak ditemukan. Silahkan tambahkan data melalui form di atas menggunakan data valid dan email password login akan dikirimkan melalui email pengguna.
			</div>
			<div class='col-md-3'>
				<img src="<?=base_url('assets/web/sad.jpg')?>">
			</div>
		</div>
	</div>
<?php }?>
