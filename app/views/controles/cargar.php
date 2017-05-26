<form action="<?=URL::to('controles/upload')?>" id="myform" method="POST" enctype="multipart/form-data">
  <div class="form-group">
  	<label for="archivo">Archivo</label>
  	<input type="file" id="excel" name="excel"></input>
  </div>
  <div class="form-group">
    <input type="submit" class="btn btn-default btn-success" value="Cargar archivo"/>
  	<a href="#" class="text-right btn btn-danger print-hide">Cancelar</a>
  </div>
</form>