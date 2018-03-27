<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Revisi&oacute;n Controles</li>
</ol>

<?php if(Session::has('sesion_historial')): ?>
  <div class="alert alert-warning" role="alert"><h3>Estás viendo el <strong><?=Session::get('sesion_historial')?></strong></h3></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h3>Instrucciones</h3>
            <p>En esta sección deberá indicar el estado de implementación de los 114 controles de la NCh-ISO 27001 vigente.<br></p>
            <p>Si indica que un control SI se encuentra implementado, deberá
                <ul>
                    <li>Agregar los medios de verificación que den cuenta de la implementación del control (documentación vigente al año 2018, y registros de operación en el año 2019).</li>
                    <li>Indicar el primer año de implementación del control ("2015 o años anteriores", "2016",  "2017"o "2018"). Considerar el primer año en que el control fue documentado, y contó con registros de operación.</li>
                </ul>
            </p>
            <p>Si indica que un control NO se encuentra implementado, deberá:
                <ul>
                    <li>Indicar las razones de dicho incumplimiento, señalando las causas externas o internas a la gestión del Servicio.</li>
                </ul>
            </p>
        </div>
    </div>
</div>

<?php
    $disabled = $habilitado==true ? '' : 'disabled';
    $mostrar = $habilitado==true ? '' : 'style="display:none"';
?>

<?php if(Auth::user()->perfil==='experto' || Auth::user()->perfil==='evaluador'): ?>
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
            <th>Acciones</th>
            <th>Implementado</th>
            <?php if(Auth::user()->perfil==='experto'): ?>
            <th>Actualizado Red</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($controles as $control):  ?>
            <tr>
                <td><?=$control->id ?>  </td>
                <td><?=$control->codigo ?></td>
                <td><?=$control->nombre ?></td>
                
                <td>
                    <?php
                        $texto = Auth::user()->perfil==='ingreso' ? 'Editar' : 'Revisar';
                        $actualizado = '<a href="javascript:;" class="ver" id='.$control->id.'><span class="label label-success">'.$texto.'</span></a>';
                        

                        /*
                        $marca = '';
                        if(count($control->comentarios)==0){
                            $desplegar = 'style="display:none"';
                            $marca = Session::has('marca') ? '<span id="marca_'.$control->id.'" class="label label-danger">se necesita actualizar</span>' : '';
                        }else{
                            $desplegar = !is_null($control->comentarios[0]->cumple) ? '' : 'style="display:none"';
                            if(is_null($control->comentarios[0]->cumple))
                                $marca = Session::has('marca') ? '<span id="marca_'.$control->id.'" class="label label-danger">se necesita actualizar</span>' : '';
                        }
                        */
                    ?>
                    <span><?=$actualizado?></span>
                    
                    <?php if(Auth::user()->perfil==='experto'): ?>
                    <a href="javascript:;" class="actualizar" id="<?=$control->id ?>"  data-dismiss="modal"><span class="label label-info">Actualizar</span></a>    
                    <?php endif; ?>

                    <div class="cid">
                        <input type="hidden" name="cidv" value="<?=$control->id ?>">
                    </div>

                </td>
                <td>
                    <span id="actualizado_<?=$control->id?>"><?= $control->comentarios[0]->cumple=='si' ? "Si" :  ($control->comentarios[0]->cumple=='no' ? "No" : "") ?></span>
                    <?php if(Auth::user()->perfil==='experto'): ?>
                    <?php
                        $red_expertos = '';
                        $desplegar = count($control->comentarios)==0 ? 'style="display:none"' : (is_null($control->comentarios[0]->observaciones_red) ? 'style="display:none"' : '');
                        $red_expertos="<span id='actualizado_experto_$control->id' class='glyphicon glyphicon-ok-sign text-success' $desplegar></span>";
                    ?>
                    <?=$red_expertos ?>
                    <?php endif; ?>
                </td>
                
                
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

    <!-- The container for the uploaded files -->
    <div id="files" class="files"></div>

          <form action="<?=URL::to('controles/actualizar')?>" id="myform" method="POST" enctype="multipart/form-data" >

            <div class="form-group">
                <label for="cumple" class="control-label">Implementado</label>
                <label class="radio-inline">
                    <input type="radio" name="cumple" id="si" value="si" /> SI
                </label>
                <label class="radio-inline">
                    <input type="radio" name="cumple" id="no" value="no" /> NO
                </label>
            </div>

             <div class="progress cumpleform">
                <div class="bar progress-bar progress-bar-success progress-bar-striped"></div>
                <div class="percent">0%</div >
            </div>
            <div id="status" class="cumpleform"></div>

            <div class="form-group nocumpleform">
              <label for="message-text" class="control-label">Indique las causas del incumplimiento, debe indicar el código del control <span id="titulo_justificacion"></span></label>
              <textarea <?=$disabled?> cols="10" rows="5" style="resize:none" class="form-control" id="comentario_incumplimiento" name="comentario_incumplimiento"></textarea>
            </div>
            <div class="form-group cumpleform">
              <label for="archivo">Archivo</label>
              <input <?=$disabled?> class="form-control datoscumplimiento" type="file" name="archivo[]" id="archivo" data-url="/upload" multiple  />
              <p class="help-block">Se pueden agregar varios archivos a la vez. La cantidad máxima permitida es de 20 archivos por control</p>
            </div>
            <!-- jquery upload -->
            <div id="files_list"></div>
            <p id="loading"></p>
            <input type="hidden" name="file_ids" id="file_ids" value="" />
            <!-- jquery upload -->
            <div id="links" class="form-group cumpleform"></div>
            <div class="form-group cumpleform">
               <label for="anio_implementacion">Año de 1° implementación</label>
               <select <?=$disabled?> class="form-control datoscumplimiento" name="anio_implementacion" id="anio_implementacion" >
                <option value="" disabled selected>Seleccione opción</option>
                <option value="2018">2018</option>
                <option value="2017">2017</option>
                <option value="2016">2016</option>
                <option value="2015">2015 o anteriores</option>
              </select>
            </div>
            <div class="form-group cumpleform">
              <label for="message-text" class="control-label datoscumplimiento">Descripción de los medios de verificación:</label>
              <textarea <?=$disabled?> cols="10" rows="5" style="resize:none" class="form-control" id="des_medios_ver" name="des_medios_ver"></textarea>
            </div>
            <input type="hidden" name="crud" id="crud">
            <input type="hidden" name="cumplimiento" id="cumplimiento">
            <input type="hidden" name="control_id" id="control_id">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>
          <input type="submit" class="btn btn-success upload-image registrar" id="registrar" disabled value="Guardar">
        </div>
        </form>
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
    /*$('.cumple').change(function(){
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
                    var descr_medios_ver = data.comentario===null ? '' : data.comentario.desc_medios_verificacion;
                    $("#control_id").val(cid);
                    if($('#cumple_'+cid).val()=='no'){
                        $('#comentario_incumplimiento').val(comentarios);
                        $('h4.modal-title').text('Indique las causas del no cumplimiento '+'Detalle control '+data.control.codigo);
                        $('.nocumpleform').show();
                        $('.cumpleform').hide();
                        $('.registrar').show();
                    }else{
                        $('#archivo').val('');
                        $('h4.modal-title').text('Selección de archivos '+'Detalle control '+data.control.codigo);
                        $('.nocumpleform').hide();
                        $('.cumpleform').show();
                        $('#anio_implementacion').attr('disabled',false);
                        $('#anio_implementacion').val('');
                        $('#archivo').show();
                        $('#links').hide();
                        $('#des_medios_ver').val(descr_medios_ver);
                        $('#registrar').show();
                    }
                    $('#registrar').attr('disabled', true);
                    $("#crud").val(0);
                    $('#cumplimiento').val($('#cumple_'+cid).val());
                    $('#modalcomentario').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
     });*/

    //archivos
    $(document).ready(function() {
        /*
        var options = { 
            //beforeSubmit:  showRequest,
            success: showResponse,
            dataType: 'json' 
            };
        
        $('body').delegate('#registrar','click', function(){
            $('#registrar').attr('disabled', true);
            $('#marca_'+$('#control_id').val()).text('');
            //showPleaseWait();
            $('#myform').ajaxForm(options).submit();
        });
        */

        var bar = $('.bar');
        var percent = $('.percent');
        var status = $('#status');

        $(".cumpleform").hide();
        $(".nocumpleform").hide();
        $('input:radio[name="cumple"]').change(
        function(){
            if ($(this).is(':checked') && $(this).val() == 'si') {
                $(".cumpleform").show();
                $(".nocumpleform").hide();
            }else{
                $(".cumpleform").hide();
                $(".nocumpleform").show();
            }
        });

        /*
        $('body').delegate('#registrar','click', function(){
            
            //$('#marca_'+$('#control_id').val()).text('');
            //$('#actualizado_experto_'+$('#control_experto').val()).show();
            //showPleaseWait();
            //$('#myformexperto').ajaxForm(options).submit();
            

        });
        */

        $('#myform').ajaxForm({
            beforeSend: function() {
                //alert('ok');
                status.empty();
                var percentVal = '0%';
                bar.width(percentVal)
                percent.html(percentVal);
            },
            uploadProgress: function(event, position, total, percentComplete) {
                var percentVal = percentComplete + '%';
                bar.width(percentVal)
                percent.html(percentVal);
                //console.log(percentVal, position, total);
            },
            success: function() {
                var percentVal = '100%';
                bar.width(percentVal)
                percent.html(percentVal);
            },
            complete: function(xhr) {
                //status.html(xhr.responseText);
                var response = JSON.parse(xhr.responseText);
                //console.log(response);
                
                $('#modalcomentario').modal('hide');
                $('#actualizado_'+response.control).text('');
                $('#actualizado_'+response.control).append(response.implementado);
                $('#actualizado_'+response.control).show();
                
            }
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
    $('.datoscumplimiento').keyup(function(){
        if($('#crud').val()==0){
            console.log('anio--'+$('#anio_implementacion').val());
            console.log('archivo--'+$('#archivo').val());
            console.log('desc--'+$('#des_medios_ver').val());
            if($('#anio_implementacion').val().length !=0 && $('#archivo').val().length !=0 && $('#des_medios_ver').val().length !=0){
                $('#registrar').removeAttr('disabled');
            }
        }else{
            if($('#anio_implementacion').val().length !=0 && $('#des_medios_ver').val().length !=0){
                $('#registrar').removeAttr('disabled');
            }
        }
    });
    */

    $('#archivo').on("change", function(){
        if($('#crud').val()==0){
            if($('#anio_implementacion').val() != null){
                if($('#anio_implementacion').val().length !=0 && $('#archivo').val().length !=0 && $('#des_medios_ver').val().length !=0){
                    $('#registrar').removeAttr('disabled');
                }else{
                    $('#registrar').attr('disabled', true);
                }
            }
        }else{
            if($('#anio_implementacion').val() != null){
                if($('#anio_implementacion').val().length !=0 && $('#archivo').val().length !=0 && $('#des_medios_ver').val().length !=0){
                    $('#registrar').removeAttr('disabled');
                }else{
                    $('#registrar').attr('disabled', true);
                }
            }
        }
    });

    $('#anio_implementacion').bind('input propertychange', function() {
        if($('#anio_implementacion').val().length !=0 && $('#archivo').val().length !=0 && $('#des_medios_ver').val().length !=0){
            $('#registrar').removeAttr('disabled');
        }else{
            $('#registrar').attr('disabled', true);
        }
    });

    $('#des_medios_ver').keyup(function(){
        if($('#crud').val()==0){
            if($('#anio_implementacion').val().length !=0 && $('#archivo').val().length !=0 && $('#des_medios_ver').val().length !=0){
                $('#registrar').removeAttr('disabled');
            }else{
                $('#registrar').attr('disabled', true);
            }
        }else{
            console.log($('#links').text().length);
            if($('#anio_implementacion').val().length !=0 && $('#des_medios_ver').val().length !=0){
                $('#registrar').removeAttr('disabled');
            }else{
                $('#registrar').attr('disabled', true);
            }
        }
    });

    $('#comentario_incumplimiento').bind('input propertychange', function() {
        if(!$.trim($("#comentario_incumplimiento").val())){
            $('#registrar').attr('disabled', true);
        }else{
            $('#registrar').removeAttr('disabled');
        }
    });

    /*
    $('#des_medios_ver').bind('input propertychange', function() {
        if($('#crud').val()==0){
            if($('#anio_implementacion').val()!=null && $('#archivo').val()!='' && $('#des_medios_ver').val()!=''){
                $('#registrar').removeAttr('disabled');
            }else{
                $('#registrar').attr('disabled', true);
            }
        }else{
            if($('#anio_implementacion').val()!=null && $('#des_medios_ver').val()!=''){
                $('#registrar').removeAttr('disabled');
            }else{
                $('#registrar').attr('disabled', true);
            }
        }
    });
    */

    /*function showResponse(response, statusText, xhr, $form) {
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
            
        }
    }*/

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
        $(".nocumpleform").hide();
        $(".cumpleform").hide();
        $("input[name='cumple']").prop('checked',false);
        $('#links').text('');
        $('#anio_implementacion').val('');
        $('#des_medios_ver').val('');

        
        var percentVal = '0%';
        $('.bar').html();
        $('.bar').width(percentVal);
        
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('controles/estado')?>",
            data: 'control_id='+cid ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                //Comentario no existe
                if(data.success==true){

                    var $radios = $('input:radio[name=cumple]');
                    if($radios.is(':checked') === false) {
                        if(data.comentario.cumple!=null){
                            if(data.comentario.cumple=='si'){
                                $("input[name='cumple'][value='si']").prop('checked',true);
                                $(".cumpleform").show();
                            }else{
                                $("input[name='cumple'][value='no']").prop('checked',true);
                                $(".nocumpleform").show();
                            }
                        }
                    }
                    var comentarios = data.comentario===null ? '' : data.comentario.observaciones_institucion;
                    var descr_medios_ver = data.comentario===null ? '' : data.comentario.desc_medio_verificacion;
                    $("#control_id").val(cid);
                    $("#crud").val(1);
                    $('h4.modal-title').text('Detalle control '+data.control.codigo);
                    $('#titulo_justificacion').text(data.control.codigo);
                    //console.log(data.comentario.cumple);
                    if(data.comentario.cumple===null){
                        $("#crud").val(0);
                    }else{
                        if(data.comentario.cumple=='no'){
                            $('#comentario_incumplimiento').val(comentarios);
                        }else{
                            $('#archivo').val('');
                            var links='';
                            $('#links').text('');
                            for(x=0; x<data.archivos.length; x++){
                                links = links + '<div id="div_file_'+data.archivos[x].id+'"><a href="<?=URL::to('controles/download')?>'+"/"+data.archivos[x].id+'" id="'+data.archivos[x].id+'">'+data.archivos[x].filename+'</a> <a <?=$mostrar?> onclick="eliminar_archivo('+data.archivos[x].id+')" href="javascript:;" ><span class="label label-danger">Eliminar</span></a></div>';
                            }
                            $('#links').append(links);
                            $('#anio_implementacion').val(data.comentario.anio_implementacion);
                            $('#des_medios_ver').val(descr_medios_ver);
                            //$('#anio_implementacion').attr('disabled',true);
                            //$('#archivo').hide();
                        }
                    }
                    console.log($("#crud").val());
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
                    if(data.cantidad_archivos==0){
                        $('#actualizado_'+data.control_id).text('');
                        $('#modalcomentario').modal('hide');
                    }
                    
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    }    

</script>