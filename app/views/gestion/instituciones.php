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

<div class="row">
    <div class="text-right">
        <a class='btn btn-success' href="<?=URL::to('gestion/certificados/exportar')?>">Exportar Certificados Red de Expertos SSI</a>
        <a class='btn btn-success' href="<?=URL::to('gestion/cumplimientos/exportar')?>">Exportar Informes de cumplimiento</a>
        <?php if(Auth::user()->perfil=='experto'): ?>
        <a class='btn btn-success' href="<?=URL::to('gestion/informes/exportar')?>">Exportar informes red de expertos</a>
        <a class='btn btn-success' href="<?=URL::to('gestion/detalle/exportar')?>">Exportar Detalle</a>
        <a class='btn btn-success' href="<?=URL::to('gestion/instituciones/exportar')?>">Exportar</a>
        <?php endif ?>
    </div>
</div>

<div class="row">
    <div class="text-right">
        <a class='btn btn-success' href="<?=URL::to('gestion/codigos/exportar')?>">Exportar Códigos y Servicios</a>
    </div>
</div>

<?php $habilitacion_perfil = Auth::user()->perfil=='evaluador' ? 'disabled' : ''; ?>

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
            <?php if(Auth::user()->perfil=='experto'): ?>
            <th>Análisis de riesgo</th>
            <th>Acciones</th>
            <?php endif ?>
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
            <select class="form-control cumple" name="cumple_<?=$institucion->id?>" id="cumple_<?=$institucion->id?>" <?=$habilitacion_perfil?> >
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
            <?php if(Auth::user()->perfil=='experto'): ?>
            <td><?=$institucion->cantidad_archivos_riesgo==0? 'No' : 'Si' ?></td>
            <td><a href="javascript:;" onclick="editar_institucion(<?=$institucion->id?>)"><span class="label label-info">Editar</span></a></td>
            <?php endif ?>
        </tr>
    	<?php endforeach ?>
    </tbody>
</table>

<!-- Modal instituciones -->
<div class="modal fade" id="modalcomentario" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="modalcomentario"></h4>
        </div>
        <span id="thanks"></span>
        <div class="modal-body">
          <form action="<?=URL::to('gestion/instituciones/grabar')?>" id="myform" name="myForm" method="POST" enctype="multipart/form-data" >
            <div class="form-group nocumpleform">
              <label for="message-text" class="control-label">Código Indicador</label>
              <input type="text" name="indicador" id="indicador" class="form-control codigos" />
            </div>
            <div id="links" class="form-group cumpleform"></div>
            <div class="form-group cumpleform">
               <label for="anio_implementacion">Código Servicio</label>
               <input type="text" name="servicio" id="servicio" class="form-control datepicker codigos" />
            </div>
            <div class="form-group cumpleform">
               <label for="anio_implementacion">Sigla</label>
               <input type="text" name="sigla" id="sigla" class="form-control datepicker codigos" />
            </div>
            <input type="hidden" name="institucion_id" id="institucion_id">
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-success upload-image registrar" id="registrar" data-dismiss="modal" disabled>Guardar</button>
        </div>
      </div>
    </div>
</div>

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

        var options = { 
            beforeSubmit:  showPleaseWait,
            success: hidePleaseWait,
            dataType: 'json'
        }; 

        $(".codigos").keyup(function(){
            if($('#indicador').val().length !=0 && $('#servicio').val().length !=0 && $('#sigla').val().length !=0){
                $('#registrar').removeAttr('disabled');
            }else{
                $('#registrar').attr('disabled', true);
            }
        });

        $('body').delegate('#registrar','click', function(){
            $('#myform').ajaxForm(options).submit();
        });
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

    function editar_institucion(institucion_id){
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('gestion/instituciones/editar')?>",
            data: 'institucion_id='+institucion_id,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                if(data.success==true){
                    $('h4.modal-title').text(data.institucion.servicio);
                    $('#indicador').val(data.institucion.codigo_indicador);
                    $('#servicio').val(data.institucion.codigo_servicio);
                    $('#sigla').val(data.institucion.sigla);
                    $('#registrar').removeAttr('disabled');
                    $('#institucion_id').val(data.institucion.id);
                    $('#modalcomentario').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    }

</script>