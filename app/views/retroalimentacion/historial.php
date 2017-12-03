<?php if(Session::has('sesion_historial')): ?>
  <div class="alert alert-warning" role="alert"><h3>Estás viendo el historial <strong><?=Session::get('sesion_historial')?></strong></h3></div>
<?php endif; ?>

<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Retroalimentacion</li>
</ol>

<?php if (Session::has('success')): ?>
  <div class="alert alert-success" role="alert"><?php echo Session::get('success');?></div>
<?php endif ?>

<?php if(Auth::user()->perfil==='experto'): ?>
<div class="form-group pull-right">
    <form action="<?=URL::to('retroalimentacion')?>" method="POST" enctype="multipart/form-data">
    <label for="institucion">Instituciones</label>
    <select id="institucion" name="institucion">
        <option value="" disabled selected>Seleccione opción</option>
        <?php foreach($instituciones as $i): ?>
           <?php if($i->id == Session::get('sesion_institucion')): ?>
            <option value="<?=$i->id?>" selected><?=$i->servicio?></option>
           <?php else: ?>
            <option value="<?=$i->id?>"><?=$i->servicio?></option>
            <?php endif ?>
        <?php endforeach ?>
    </select>
    <input type="submit" value="Actualizar" class="btn btn-info" />
    </form>
</div>
<?php endif ?>
<br><br>

<?php $disabled = Auth::user()->perfil!='experto' ? 'disabled' : '' ?>

<div class="form-group">
<form action="<?=URL::to('retroalimentacion/observaciones')?>" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="message-text" class="control-label">Observaciones:</label>
        <textarea disabled cols="20" rows="20" style="resize:none" class="form-control" id="observacion_red" name="observacion_red" <?=$disabled?> ><?=$institucion->observaciones_red?></textarea>
      </div>
  </div>
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
</div>
</form>
</div>