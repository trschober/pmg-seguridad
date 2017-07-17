<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Retroalimentacion</li>
</ol>

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

<div class="form-group">
<form action="<?=URL::to('retroalimentacion/observaciones')?>" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label for="message-text" class="control-label">Observaciones:</label>
        <textarea cols="20" rows="20" style="resize:none" class="form-control" id="observacion_red" name="observacion_red"><?=$institucion->observaciones_red?></textarea>
      </div>
  </div>
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
  <?php if(Auth::user()->perfil==='experto'): ?>
  <div class="modal-footer">
    <input type="submit" class="btn btn-success upload-image registrar" id="actualizar" value="Agregar observación"/>
    <a href="<?=URL::to('retroalimentacion/reporte')?>" class="btn btn-info">Generar Informe Red de Expertos</a>
  </div>
  <?php endif ?>
</div>
</form>
</div>