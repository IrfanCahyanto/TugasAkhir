<form method="POST" action="<?= base_url('Admin/saveProgramStudi');?>" id="fakultas">
  <div class="form-row align-items-center">
    <div class="col-md-auto">
      <label class="sr-only" for="inlineFormInput">ID Program Studi</label>
      <input name="id_programstudi" min="0" type="number" class="form-control btn-sm mb-4" placeholder="ID Program Studi" required>
    </div>
    <div class="col-md col">
      <label class="sr-only" for="inlineFormInputGroup">Program Studi</label>
      <div class="input-group mb-4">
        <input name="programstudi" type="text" class="form-control btn-sm" placeholder="Program Studi" required>
      </div>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary mb-4 btn-sm">Simpan</button>
    </div>
  </div>
</form>