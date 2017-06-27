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

<form action="<?=URL::to('riesgos/agregar')?>" method="POST" enctype="multipart/form-data">
  <?php echo $errors->first('email'); ?>
  <div class="form-group">
    <label for="archivo">Archivo</label>
    <input type="file" id="archivo" name="archivo">
  </div>
  <button type="submit" class="btn btn-success" disabled id="agregar" name="agregar">Agregar</button>
</form>

<?php if(count($riesgos)>0): ?>
<table id="controles" class="table table-striped table-hover table-condensed">
	<caption>Listado de archivos</caption>
    <thead>
        <tr>
            <th>Archivo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach ($riesgos as $riesgo):  ?>
    	<tr>
    		<td><?=$riesgo->filename?></td>
    		<td><a href="<?=URL::to('riesgos/eliminar/'.$riesgo->id)?>" class="eliminar" onclick="return confirm('¿Est&aacute; seguro que desea eliminar el archivo?')"><span class="label label-danger">Eliminar</span></a></td>
    	</tr>
    	<?php endforeach ?>
    </tbody>
</table>
<?php endif; ?>

<script type="text/javascript">
	$('#archivo').on("change", function(){
        if($('#archivo').val()!=''){
            $('#agregar').removeAttr('disabled');
        }
    });
</script>