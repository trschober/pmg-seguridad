<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Revisi&oacute;n Controles</li>
</ol>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h3>Instrucciones</h3>
            <p>En esta sección deberán indicar si el control se encuentra implementado o no implementado.<br>
               En el caso de escoger la opción Sí, el Servicio deberá:
            </p>
            <p>
                <ul>
                    <li>Indicar el año de documentación del control, es decir, el año en que se creó y oficializó el primer documento (política o procedimiento).</li>
                    <li>Agregar los medios de verificación que den cuenta de la implementación del control (documentación al año 2017 y operación en el año 2017).</li>
                </ul>
            </p>
            <p>En el caso de escoger la opción No, el Servicio deberá:
                <ul>
                    <li>Indicar las razones de dicho incumplimiento señalando las causas, sean externas o internas a la gestión del Servicio.</li>
                </ul>
            </p>
            <p>Lo anterior, debe ser realizado para los 114 controles de la NCh-ISO 27001 vigente.</p>
        </div>
    </div>
</div>

<?php 
    $disabled = $habilitado==true ? 'disabled' : 'disabled';
    $mostrar = $habilitado==true ? '' : 'style="display:none"';
?>

<?php if(Auth::user()->perfil==='experto'): ?>
    <div class="form-group pull-right">
        <form action="<?=URL::to('controles')?>" id="myform" method="POST" enctype="multipart/form-data">
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

<table id="controles" class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Nº</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Año de formulación</th>
            <th>Implementado</th>
            <th>Acciones</th>
            <?php if(Auth::user()->perfil==='experto'): ?>
            <th>Actualizado Red</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($controles as $control):  ?>
            <tr>
                <td><?=$control->id ?></td>
                <td><?=$control->codigo ?></td>
                <td><?=$control->nombre ?></td>
                <td><?=count($control->comentarios)>0 ? $control->comentarios[0]->anio_compromiso : '-';?> 
                <div class="cid">
                    <input type="hidden" name="cidv" value="<?=$control->id ?>">
                </div>
                </td>
                <td>
                    <?php if(count($control->comentarios)>0):?>    
                        <?php foreach ($control->comentarios as $comentario): ?>
                        <select <?=$disabled?> class="form-control cumple" name="cumple_<?=$control->id?>" id="cumple_<?=$control->id?>" >
                            <option value="" disabled selected>Seleccion opción</option>
                            <option value="si" <?=$comentario->cumple=='si' ? 'selected' : '' ?>>Si</option>
                            <option value="no" <?=$comentario->cumple=='no' ? 'selected' : '' ?>>No</option>
                        </select>
                        <? endforeach ?>
                    <?php else: ?>
                        <select <?=$disabled?> class="form-control cumple" name="cumple_<?=$control->id?>" id="cumple_<?=$control->id?>" >
                            <option value="" disabled selected>Seleccione opción</option>
                            <option value="si">Si</option>
                            <option value="no">No</option>
                        </select>
                    <?php endif ?>
                    <div class="cid">
                        <input type="hidden" name="cidv" value="<?=$control->id ?>">
                    </div>
                </td>
                <td>
                    <?php
                        $actualizado = '<a href="#" class="ver"><span class="label label-success">Revisar</span></a>';
                        $marca = '';
                        if(count($control->comentarios)==0){
                            $desplegar = 'style="display:none"';
                            $marca = Session::has('marca') ? '<span id="marca_'.$control->id.'" class="label label-danger">se necesita actualizar</span>' : '';
                        }else{
                            $desplegar = !is_null($control->comentarios[0]->cumple) ? '' : 'style="display:none"';
                            if(is_null($control->comentarios[0]->cumple))
                                $marca = Session::has('marca') ? '<span id="marca_'.$control->id.'" class="label label-danger">se necesita actualizar</span>' : '';
                        }
                    ?>
                    <span id="actualizado_<?=$control->id?>" <?=$desplegar?>><?=$actualizado?></span>
                    <?=$marca?>
                    <?php if(Auth::user()->perfil==='experto'): ?>
                        <a href="#" class="actualizar"  data-dismiss="modal"><span class="label label-info">Actualizar</span></a>
                    <?php endif; ?>
                </td>
                <?php if(Auth::user()->perfil==='experto'): ?>
                <?php
                    $red_expertos = '';
                    $desplegar = count($control->comentarios)==0 ? 'style="display:none"' : (is_null($control->comentarios[0]->observaciones_red) ? 'style="display:none"' : '');
                    $red_expertos="<span id='actualizado_experto_$control->id' class='glyphicon glyphicon-ok-sign text-success' $desplegar></span>";
                ?>
                <td><?=$red_expertos ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<!-- Modal cumplimiento -->
<div class="modal fade" id="modalcomentario" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title" id="modalcomentario"></h4>
        </div>
        <div id="thanks"></div>
        <div class="modal-body">
          <form action="<?=URL::to('controles/actualizar')?>" id="myform" method="POST" enctype="multipart/form-data" >
            <div class="form-group nocumpleform">
              <label for="message-text" class="control-label">Justificaciones:</label>
              <textarea <?=$disabled?> cols="10" rows="5" style="resize:none" class="form-control" id="comentario_incumplimiento" name="comentario_incumplimiento"></textarea>
            </div>
            <div class="form-group cumpleform">
              <label for="archivo">Archivo</label>
              <input <?=$disabled?> class="form-control datoscumplimiento" type="file" name="archivo[]" id="archivo" multiple required />
              <p class="help-block">Se pueden agregar varios archivos a la vez</p>
            </div>
            <div id="links" class="form-group cumpleform"></div>
            <div class="form-group cumpleform">
               <label for="anio_implementacion">Año documentación</label>
               <select <?=$disabled?> class="form-control datoscumplimiento" name="anio_implementacion" id="anio_implementacion" required>
                <option value="" disabled selected>Seleccione opción</option>
                <option value="2017">2017</option>
                <option value="2016">2016</option>
                <option value="2015">2015</option>
                <option value="2014">2014</option>
                <option value="2013">2013</option>
                <option value="2012">2012</option>
                <option value="2011">2011</option>
                <option value="2010">2010</option>
                <option value="2009">2009</option>
                <option value="2008">2008</option>
                <option value="2007">2007</option>
                <option value="2006">2006</option>
                <option value="2005">2005</option>
                <option value="-">-</option>
              </select>
            </div>
            <input type="hidden" name="cumplimiento" id="cumplimiento">
            <input type="hidden" name="control_id" id="control_id">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-success upload-image registrar" id="registrar" data-dismiss="modal" disabled>Registrar</button>
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

<!-- Modal Red de expertos -->
<div class="modal fade" id="modalexpertos" tabindex="-1" role="dialog" aria-labelledby="modalexpertos">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalexpertos"></h4>
      </div>
      <div class="modal-body">
    <form action="<?=URL::to('controles/red')?>" id="myformexperto" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="message-text" class="control-label">Observaciones:</label>
            <textarea cols="10" rows="5" style="resize:none" class="form-control" id="observaciones_expertos" name="observaciones_expertos"></textarea>
          </div>
      </div>
      <input type="hidden" name="control_experto" id="control_experto">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
      <div class="modal-footer">
        <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-success upload-image registrar" id="actualizar" data-dismiss="modal">Actualizar</button>
      </div>
    </div>
    </form>
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
                //Comentario no existe
                if(data.success==true){
                    var comentarios = data.comentario===null ? '' : data.comentario.observaciones_institucion;
                    $("#control_id").val(cid);
                    if($('#cumple_'+cid).val()=='no'){
                        $('#comentario_incumplimiento').val(comentarios);
                        $('h4.modal-title').text('Indique las causas del no cumplimiento');
                        $('.nocumpleform').show();
                        $('.cumpleform').hide();
                        $('.registrar').show();
                    }else{
                        $('#archivo').val('');
                        $('h4.modal-title').text('Selección de archivos');
                        $('.nocumpleform').hide();
                        $('.cumpleform').show();
                        $('#anio_implementacion').attr('disabled',false);
                        $('#anio_implementacion').val('');
                        $('#archivo').show();
                        $('#links').hide();
                        $('#registrar').show();
                    }
                    $('#registrar').attr('disabled', true);
                    $('#cumplimiento').val($('#cumple_'+cid).val());
                    $('#modalcomentario').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
     });

    //archivos
    $(document).ready(function() {

        var options = { 
            //beforeSubmit:  showRequest,
            success: showResponse,
            dataType: 'json' 
            }; 
        $('body').delegate('#registrar','click', function(){
            $('#registrar').attr('disabled', true);
            $('#marca_'+$('#control_id').val()).text('');
            showPleaseWait();
            $('#myform').ajaxForm(options).submit();
        });

        $('body').delegate('#actualizar','click', function(){
            //$('#marca_'+$('#control_id').val()).text('');
            $('#actualizado_experto_'+$('#control_experto').val()).show();
            showPleaseWait();
            $('#myformexperto').ajaxForm(options).submit();
        });
        
        var pleaseWait = $('#pleaseWaitDialog'); 
        showPleaseWait = function() {
            pleaseWait.modal('show');
        };
        hidePleaseWait = function () {
            pleaseWait.modal('hide');
        };
    });

    $('.datoscumplimiento').on("change", function(){
        if($('#anio_implementacion').val()!=null && $('#archivo').val()!=''){
            $('#registrar').removeAttr('disabled');
        }
    });

    $('#comentario_incumplimiento').bind('input propertychange', function() {
        if(!$.trim($("#comentario_incumplimiento").val())){
            $('#registrar').attr('disabled', true);
        }else{
            $('#registrar').removeAttr('disabled');
        }
    });

    function showResponse(response, statusText, xhr, $form) {
        //alert(response);
        if(response.success == false){
            var arr = response.errors;
            $.each(arr, function(index, value){
                if (value.length != 0){
                    $("#validation-errors").append('<div class="alert alert-error"><strong>'+ value +'</strong><div>');
                }
            });
            $("#validation-errors").show();
        }else{
            //$('#actualizado_'+response.control).text('');
            //$('#actualizado_'+response.control).append('<a href="#" class="ver"><span class="label label-success">ver</span></a>');
            $('#actualizado_'+response.control).show();
            //$('#actualizado_'+response.control).css('display', 'block');
            hidePleaseWait();
            /*
            $("#thanks").html(response.message);
             setTimeout(function() {
                $('#thanks').html('');
                $('#archivo').val('');
            },3000);
            */
        }
    }

    $('.actualizar').click(function(e) {
        //showPleaseWait();
        var cid = $(this).parents('tr').find('.cid input[type="hidden"]').val();
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('controles/estado')?>",
            data: 'control_id='+cid ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                //Comentario no existe
                if(data.success==true){
                    $("#control_experto").val(cid);
                    var observaciones = data.comentario===null ? '' : data.comentario.observaciones_red;
                    $('#observaciones_expertos').val(observaciones);
                    $('#modalexpertos').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    });

    

    $('.ver').click(function(e) {
        var cid = $(this).parents('tr').find('.cid input[type="hidden"]').val();
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('controles/estado')?>",
            data: 'control_id='+cid ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                //Comentario no existe
                if(data.success==true){
                    var comentarios = data.comentario===null ? '' : data.comentario.observaciones_institucion;
                    $("#control_id").val(cid);
                    $('h4.modal-title').text('Detalle');
                    if($('#cumple_'+cid).val()=='no'){
                        $('#comentario_incumplimiento').val(comentarios);
                        //$('#comentario_incumplimiento').attr('disabled',true);
                        $('.nocumpleform').show();
                        $('.cumpleform').hide();
                    }else{
                        $('#archivo').val('');
                        $('.nocumpleform').hide();
                        $('.cumpleform').show();
                        var links='';
                        $('#links').text('');
                        for(x=0; x<data.archivos.length; x++){
                            links = links + '<div id="div_file_'+data.archivos[x].id+'"><a href="<?=URL::to('controles/download')?>'+"/"+data.archivos[x].id+'" id="'+data.archivos[x].id+'">'+data.archivos[x].filename+'</a> <a <?=$mostrar?> onclick="eliminar_archivo('+data.archivos[x].id+')" href="#" >(X)</a></div>';
                        }
                        $('#links').append(links);
                        $('#anio_implementacion').val(data.comentario.anio_implementacion);
                        //$('#anio_implementacion').attr('disabled',true);
                        //$('#archivo').hide();
                    }
                    //$('#registrar').hide();
                    $('#cumplimiento').val($('#cumple_'+cid).val());
                    $('#modalcomentario').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    });

    function eliminar_archivo(file){
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('controles/archivo/eliminar')?>",
            data: 'archivo_id='+file,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                if(data.success==true){
                    $('#div_file_'+file).hide();
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    }     

</script>