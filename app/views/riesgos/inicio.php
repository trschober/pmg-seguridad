<ol class="breadcrumb">
  <li><a href="<?= Session::has('sesion_historial') ? URL::to('bienvenida') : URL::to('historial')?>">Seguridad de la Información</a></li>
  <li class="active">Análisis de riesgo</li>
</ol>

<?php if(Session::has('sesion_historial')): ?>
  <div class="alert alert-warning" role="alert"><h3>Estás viendo el <strong><?=Session::get('sesion_historial')?></strong></h3></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h3>Instrucciones</h3>
            <p>En esta sección los Servicios deberán agregar el instrumento o metodología utilizada para realizar el análisis de riesgo institucional. Como por ejemplo, el instrumento publicado en DIPRES
            (<a href="http://www.dipres.gob.cl/594/articles-51683_intro_instrumentos_2016_18_05.xlsx" target="_blank">http://www.dipres.gob.cl/594/articles-51683_intro_instrumentos_2016_18_05.xlsx</a>) presentado como medio de verificación en el año 2015, con las respectivas actualizaciones al año 2017.</p>
        </div>
    </div>
</div>

<?php 
    $disabled = $habilitado==true ? '' : 'disabled';
    $mostrar = $habilitado==true ? '' : 'style="display:none"';
?>

<?php if(Auth::user()->perfil==='experto' || Auth::user()->perfil==='evaluador'): ?>
    <div class="form-group pull-right">
        <form action="<?=URL::to('riesgos')?>" id="myform" method="POST" enctype="multipart/form-data">
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
        <input type="submit" value="Actualizar" class="btn btn-success" />
        </form>
    </div>
<?php endif ?>

<?php if(Auth::user()->perfil!='experto'): ?>
<form action="<?=URL::to('riesgos/agregar')?>" method="POST" enctype="multipart/form-data">
  <?php echo $errors->first('email'); ?>
  <div <?=$mostrar?> class="form-group">
    <label for="archivo">Archivo</label>
    <input <?=$disabled?> type="file" id="archivo" name="archivo">
  </div>
  <button <?=$mostrar?> <?=$disabled?> type="submit" class="btn btn-success" disabled id="agregar" name="agregar">Agregar</button>
</form>
<?php endif ?>

<table id="controles" class="table table-striped table-hover table-condensed">
	<caption>Listado de archivos</caption>
    <thead>
        <tr>
            <th>Archivo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($riesgos)>0): ?>
    	<?php foreach ($riesgos as $riesgo):  ?>
    	<tr>
    		<td><?=$riesgo->filename?></td>
    		<td>
            <?php if(Auth::user()->perfil!='experto'): ?>
            <a <?=$mostrar?> href="<?= $habilitado ? URL::to('riesgos/eliminar/'.$riesgo->id) : '#' ?>" class="eliminar" onclick="return confirm('¿Est&aacute; seguro que desea eliminar el archivo?')"><span class="label label-danger">Eliminar</span></a>
            <?php endif ?>
            <a href="<?=URL::to('riesgos/download/'.$riesgo->id)?>" class="descargar"><span class="label label-info">Descargar</span></a>
            </td>
    	</tr>
    	<?php endforeach ?>
        <?php else: ?>
        <tr><td colspan="2">Sin registros</td></tr>
        <?php endif; ?>
    </tbody>
</table>


<script type="text/javascript">
	$('#archivo').on("change", function(){
        if($('#archivo').val()!=''){
            $('#agregar').removeAttr('disabled');
        }
    });
</script>