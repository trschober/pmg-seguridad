<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li><a href="<?= URL::to('documentos') ?>">Documentos</a></li>
  <li class="active">Agregar</li>
</ol>

<form action="<?=URL::to('documentos/upload')?>" id="myform" method="POST" enctype="multipart/form-data" >
    <?php if (Session::has('error')): ?>
      <div class="alert alert-danger"><?=Session::get('error')?></div>
    <?php endif; ?>
    <div class="form-group">
        <label for="archivo">Archivo</label>
        <input type="file" class="form-control validar" id="archivo" name="archivo" placeholder="Archivo">
    </div>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="<?=URL::to('documentos')?>" class="btn btn-danger" id="cancelar" name="cancelar">Cancelar</a>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
</form>