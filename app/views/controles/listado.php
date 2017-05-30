<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Revisi&oacute;n Controles</li>
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

<table id="controles" class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Nº</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Año implementación</th>
            <th>Cumple</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($controles as $control):  ?>
            <tr>
                <td><?=$control->id ?></td>
                <td><?=$control->codigo ?></td>
                <td><?=$control->nombre ?></td>
                <td>
                    <?php if(count($control->comentarios)>0):?>    
                        <?php foreach ($control->comentarios as $comentario): ?>
                        <select class="form-control" name="anio_<?=$control->id?>" id="anio_<?=$control->id?>" >
                            <option value="" disabled selected> Seleccion opción </option>
                            <option value="<?=$comentario->anio_implementacion ?>" <?=$comentario->anio_implementacion=='2017' ? 'selected' : '' ?>>2017</option>
                            <option value="<?=$comentario->anio_implementacion ?>" <?=$comentario->anio_implementacion=='2016' ? 'selected' : '' ?>>2016</option>
                            <option value="<?=$comentario->anio_implementacion ?>" <?=$comentario->anio_implementacion=='2015' ? 'selected' : '' ?>>2015</option>
                            <option value="<?=$comentario->anio_implementacion ?>" <?=$comentario->anio_implementacion=='2014' ? 'selected' : '' ?>>2014</option>
                            <option value="<?=$comentario->anio_implementacion ?>" <?=$comentario->anio_implementacion=='-' ? 'selected' : '' ?>>-</option>
                        </select>
                        <? endforeach ?>
                    <?php else: ?>
                        <select class="form-control" name="anio_<?=$control->id?>" id="anio_<?=$control->id?>" >
                            <option value="" disabled selected>Seleccione opción</option>
                            <option value="2017">2017</option>
                            <option value="2016">2016</option>
                            <option value="2016">2015</option>
                            <option value="2016">2014</option>
                            <option value="-">-</option>
                        </select>
                    <?php endif ?>
                    <div class="cid">
                        <input type="hidden" name="cidv" value="<?=$control->id ?>">
                    </div>
                </td>
                <td>
                    <?php if(count($control->comentarios)>0):?>    
                        <?php foreach ($control->comentarios as $comentario): ?>
                        <select class="form-control cumple" name="cumple_<?=$control->id?>" id="cumple_<?=$control->id?>" >
                            <option value="" disabled selected> Seleccion opción </option>
                            <option value="<?=is_null($comentario->cumple) ? 'si' : $comentario->cumple ?>" <?=$comentario->cumple=='si' ? 'selected' : '' ?>>Si</option>
                            <option value="<?=is_null($comentario->cumple) ? 'no' : $comentario->cumple ?>" <?=$comentario->cumple=='no' ? 'selected' : '' ?>>No</option>
                        </select>
                        <? endforeach ?>
                    <?php else: ?>
                        <select class="form-control cumple" name="cumple_<?=$control->id?>" id="cumple_<?=$control->id?>" >
                            <option value="" disabled selected>Seleccione opcióasdn</option>
                            <option value="si">Si</option>
                            <option value="no">No</option>
                        </select>
                    <?php endif ?>
                    <div class="cid">
                        <input type="hidden" name="cidv" value="<?=$control->id ?>">
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<div class="modal fade" id="modalcomentario" tabindex="-1" role="dialog" aria-labelledby="modalcomentario">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalcomentario"></h4>
      </div>
      <div class="alert alert-info modal-archivo">
        Se pueden agregar varios archivos a la vez.
      </div>
      <div id="thanks"></div>
      <div class="modal-body">
    <form action="<?=URL::to('controles/cumplimiento')?>" id="myform" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="message-text" class="control-label">Observaciones:</label>
            <textarea cols="10" rows="5" style="resize:none" class="form-control" id="comentario_incumplimiento" name="comentario_incumplimiento"></textarea>
          </div>
      </div>
      <input class="form-control modal-archivo" type="file" name="archivo[]" id="archivo" multiple />
      <input type="hidden" name="control_id" id="control_id">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
      <div class="modal-footer">
        <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-danger upload-image registrar" data-dismiss="modal">Registrar</button>
      </div>
    </div>
    </form>
  </div>
</div>

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

<center><?php echo $controles->links(); ?></center>

<script type="text/javascript">
    //modal cumplimiento(si/no)
    $('.cumple').change(function(){
        var cid = $(this).parents('tr').find('.cid input[type="hidden"]').val();
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('controles/estado')?>",
            data: 'control_id='+cid ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){

                //si el registro no existe verificar que opcion esta marcando. Si es "si" habilitar botón cargar archivos, de lo contrario levantar modal para registrar razones de no cumplimiento
                if(data.success==false){
                    $("#control_id").val(cid);
                    if($('#cumple_'+cid).val()=='no'){
                        $('h4.modal-title').text('Indique las razones del no cumplimiento');
                        $('.form-group').show();
                        $('.modal-archivo').hide();
                        $('.registrar').show();
                    }else{
                        $('h4.modal-title').text('Selección de archivos');
                        $('.form-group').hide();
                        $('.modal-archivo').show();
                        $('.registrar').hide();
                    }
                    $('#modalcomentario').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
     });

    //files
    $(document).ready(function() {
        var options = { 
            //beforeSubmit:  showRequest,
            success: showResponse,
            dataType: 'json' 
            }; 
        $('body').delegate('#archivo','change', function(){
            showPleaseWait();
            $('#myform').ajaxForm(options).submit();        
        });
        var pleaseWait = $('#pleaseWaitDialog'); 
        showPleaseWait = function() {
            pleaseWait.modal('show');
        };
        hidePleaseWait = function () {
            pleaseWait.modal('hide');
        };

    });
    /*
    function showRequest(formData, jqForm, options) { 
        //$("#validation-errors").hide().empty();
        //$("#output").css('display','none');
        return true; 
    }
    */
    function showResponse(response, statusText, xhr, $form) {
        if(response.success == false){
            var arr = response.errors;
            $.each(arr, function(index, value){
                if (value.length != 0){
                    $("#validation-errors").append('<div class="alert alert-error"><strong>'+ value +'</strong><div>');
                }
            });
            $("#validation-errors").show();
        }else{
             hidePleaseWait();
             $("#thanks").html(response.message);
             setTimeout(function() {
                $('#thanks').html('');
                $('#archivo').val('');
            },3000);
        }
    }

</script>
