<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Análisis de riesgo</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h3>Instrucciones</h3>
            <p>A continuación, se presenta el listado de trámites correspondientes a su servicio, los cuales se identifican en el catastro de trámites del año 2016.</p>
            <p>Para cada trámite registrado usted deberá hacer click en el botón “Actualizar”, donde podrá modificar  el nombre, nivel de digitalización, URL, descripción, y clave única. Además, de ser necesario, usted podrá dejar sus observaciones para cada trámite.</p>
        </div>
    </div>
</div>

<?php if(Auth::user()->perfil==='experto'): ?>
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
  <div class="form-group">
    <label for="archivo">Archivo</label>
    <input type="file" id="archivo" name="archivo">
  </div>
  <button type="submit" class="btn btn-success" disabled id="agregar" name="agregar">Agregar</button>
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
            <a href="<?=URL::to('riesgos/eliminar/'.$riesgo->id)?>" class="eliminar" onclick="return confirm('¿Est&aacute; seguro que desea eliminar el archivo?')"><span class="label label-danger">Eliminar</span></a>
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