<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Listado de instituciones</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h3>Instrucciones</h3>
            <p>Esta sección permite ver el estado de las instituciones y poder actualizarlas en caso de ser necesario. Los estados son los siguientes:</p>
            <p>
            	<strong>Ingresado</strong>: En revisión de perfil ingreso. <br> 
            	<strong>Enviado</strong>: En revisión de perfil validador. <br>
            	<strong>Rechazado</strong>: En revisión de perfil ingreso por rechazo de perfil validador. <br>
            	<strong>Cerrado</strong>: Enviado a la red de expertos. <br>
            </p>
        </div>
    </div>
</div>

<table id="instituciones" class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Nº</th>
            <th>Servicio</th>
            <th>Estado</th>
            <th>Controles actualizados</th>
            <th>Porcentaje actualizados</th>
            <th>Implementado</th>
            <th>No Implementado</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach ($instituciones as $institucion):  ?>
    	<tr>
    		<td><?=$institucion->id ?></td>
            <td><?=$institucion->servicio ?></td>
            <td>
            <div class="cid">
                <input type="hidden" name="cidv" value="<?=$institucion->id ?>">
            </div>
            <select class="form-control cumple" name="cumple_<?=$institucion->id?>" id="cumple_<?=$institucion->id?>">
                <option value="" disabled selected>Seleccione estado</option>
                <option value="ingresado" <?=$institucion->estado==='ingresado' ? 'selected' : '' ?>>Ingresado</option>
                <option value="enviado" <?=$institucion->estado==='enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="rechazado" <?=$institucion->estado==='rechazado' ? 'selected' : '' ?>>Rechazado</option>
                <option value="cerrado" <?=$institucion->estado==='cerrado' ? 'selected' : '' ?>>Cerrado</option>
            </select>
            </td>
            <td><?=$institucion->cumple ?></td>
            <td><?= number_format(($institucion->cumple*100)/$total_controles,1, '.','') ?>%</td>
            <td><?=$institucion->implementado ?></td>
            <td><?=$institucion->no_implementado ?></td>
        </tr>
    	<?php endforeach ?>
    </tbody>
</table>

<!-- Modal loading -->
<div class="modal fade" id="pleaseWaitDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <h1>Procesando...</h1>
      </div>
      <div class="modal-body">
        <div class="progress">
          <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
            <span class="sr-only">40% Complete (success)</span>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

<script type="text/javascript">

	$(document).ready(function() {
        var pleaseWait = $('#pleaseWaitDialog'); 
        showPleaseWait = function() {
            pleaseWait.modal('show');
        };
        hidePleaseWait = function () {
            pleaseWait.modal('hide');
        };
    });
	
	$('.cumple').change(function(){
		showPleaseWait();
        var id = $(this).parents('tr').find('.cid input[type="hidden"]').val();
        var estado = $(this).val();
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('gestion/instituciones/actualizar')?>",
            data: 'institucion_id='+id+'&estado='+estado ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                if(data.success==true){
                    hidePleaseWait();
                }else{
                	hidePleaseWait();
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    });


</script>