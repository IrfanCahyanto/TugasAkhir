<!DOCTYPE html>
<html>

<head>
	<script type="text/javascript">
		$(document).ready(function () {
			$("#form_<?=$user?>_jrsn").change(function () {
				$("#form_<?=$user?>_ksn").hide();
				$.ajax({
					type: "POST",
					url: "<?php echo base_url('Home/filterBidangMinat'); ?>",
					data: {
						IDProgramStudi: $("#form_<?=$user?>_jrsn").val()
					},
					dataType: "json",
					success: function (response) {
						$("#div_<?=$user?>_ksn").show('fast', function () {
							$("#form_<?=$user?>_ksn").html(response.list).show();
						});
					},
				});
			});
		});

		function readUrl(input) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					$('#blah').attr('src', e.target.result);
				}
				reader.readAsDataURL(input.files[0]);
			}
		}
		$("#foto").change(function () {
			readUrl(this);
		});

	</script>
</head>
<?php if (!$bidangminat) {?>
<div class='container-fluid mt-5'>
	<div class='row align-items-center'>
		<div class='col-md'>
			<h2>Hai Admin!!!</h2>
			Masukkan Program Studi & Bidang Minat Terlebih Dahulu.
		</div>
		<div class='col-md-3'>
			<img src="<?=base_url('assets/web/sad.jpg')?>">
		</div>
	</div>
</div>
<?php } else {?>
<form method="post" id="Dosen" action="<?=base_url('Admin/saveUsers/' . $user);?>" class="formSimpan">
	<div class="form-row">
		<div class="form-group col-md">
			<input type="number" id="nik" name="id" class="form-control" placeholder="ID <?=$user?>" required>
		</div>
		<div class="form-group col-md">
			<input id="nama" type="text" name="name" class="form-control" placeholder='Nama' required>
		</div>
		<div class="form-group col-md">
			<select name="id_programstudi" id="form_<?=$user?>_jrsn" class="custom-select">
				<option selected>Program Studi</option>
				<?php foreach ($programstudi->result() as $j) {?>
				<option value="<?php echo $j->IDProgramStudi; ?>">
					<?php echo $j->ProgramStudi; ?>
				</option>
				<?php }?>
			</select>
		</div>
		<div class="form-group col-md" id="div_<?=$user?>_ksn" style="display: none">
			<select name="bidangminat" id="form_<?=$user?>_ksn" class="custom-select">
			</select>
		</div>
		<div class="form-group col-md col">
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text"> <i class="fas fa-envelope m-1"></i></span>
				</div>
				<div class="custom-file">
					<input id="email" name="email" type="email" class="form-control" required>
				</div>
			</div>
		</div>
		<div class="col-2">
			<button class="btn btn-primary" type="submit" id="daftar"> <i class='fas fa-plus'></i> </button>
		</div>
	</div>
</form>
<?php }?>

</html>
