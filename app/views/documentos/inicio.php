<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Documentos</li>
</ol>

<?php if(Session::has('sesion_historial')): ?>
  <div class="alert alert-warning" role="alert"><h3>Estás viendo el <strong><?=Session::get('sesion_historial')?></strong></h3></div>
<?php endif; ?>

<?php if(Auth::user()->perfil=='experto'): ?>
<div class="pull-right"><a href="<?= URL::to('documentos/agregar') ?>" class="btn btn-info">Agregar</a></div>
<?php endif ?>

<table id="controles" class="table table-striped table-hover table-condensed">
	<caption>Listado de Documentos</caption>
    <thead>
        <tr>
            <th>Archivo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if(count($documentos)>0): ?>
    	<?php foreach ($documentos as $documento):  ?>
    	<tr>
    		<td><?=$documento->filename?></td>
    		<td>
            <?php if(Auth::user()->perfil==='experto'): ?>
            <a href="<?=URL::to('documentos/eliminar/'.$documento->id)?>" class="eliminar" onclick="return confirm('¿Est&aacute; seguro que desea eliminar el archivo?')"><span class="label label-danger">Eliminar</span></a>
            <?php endif ?>
            <a href="<?=URL::to('documentos/download/'.$documento->id)?>" class="descargar"><span class="label label-info">Descargar</span></a>
            </td>
    	</tr>
    	<?php endforeach ?>
        <?php else: ?>
        <tr><td colspan="2">Sin registros</td></tr>
        <?php endif; ?>
    </tbody>
</table>
