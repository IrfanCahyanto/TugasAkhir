<div id="Panduan">
	<?php { ?>
	<div class="card">
		<div class='row align-items-center m-4'>
			<div class='col-md'>
				<?php if ($_SESSION['Status'] === 'Dosen') { ?>
				<h2>Berikut Panduan Dalam Menggunakan Sistem Ini, <?= $_SESSION['Nama']?>.</h2>
				<br>1. Anda dapat</br>
				<th>2. mejmfef</br>
				<th>3. mejmfef</br>
				<th>4. mejmfef</br>
				<th>5. mejmfef</br>
				<?php }

				elseif ($_SESSION['Status'] === 'Dosen' && $_SESSION['ID'] < 4) { ?>
				<h2>Berikut Panduan Dalam Menggunakan Sistem Ini, <?= $_SESSION['Nama']?>.</h2>
				<br>1. Anda dapat</br>
				<th>2. mejmfef</br>
				<th>3. mejmfef</br>
				<th>4. mejmfef</br>
				<th>5. mejmfef</br>
				<?php }

				elseif ($_SESSION['Status'] === 'Mahasiswa') { ?>
				<h2>Berikut Panduan Dalam Menggunakan Sistem Ini, <?= $_SESSION['Nama']?>.</h2>
				<br>1. Anda dapat</br>
				<th>2. mejmfef</br>
				<th>3. mejmfef</br>
				<th>4. mejmfef</br>
				<th>5. mejmfef</br>
				<?php } ?>
			</div>
			<div class='col-md-3'>
				<img class="card-img-top" src="<?= base_url('assets/web/jelaskan.jpg')?>">
			</div>
		</div>
	</div>

		<?php } } ?>
	</div>

</div>
