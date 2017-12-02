<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Historial de ejercicios</li>
</ol>

<form action="<?=URL::to('historial')?>" id="myform" method="POST" enctype="multipart/form-data">
    <label for="institucion">Por favor seleccione el periodo con el que desea trabajar:</label>
    <select id="historial" name="historial">
        <option value="" disabled selected>Seleccione opción</option>
        <?php foreach($historial_ejercicios as $h): ?>
           <option value="<?=$h->id?>"><?=$h->anio.'-'.$h->tipo?> <?= $h->activo? '(Activo)' : '' ?></option>
        <?php endforeach ?>
    </select>
    <input type="submit" value="Actualizar" class="btn btn-success" />
</form>
