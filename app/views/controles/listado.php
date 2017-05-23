<ol class="breadcrumb">
  <li><a href="/">PMG-Seguridad</a></li>
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
                    <select class="form-control cumple" name="cumple_<?=$control->id?>" id="cumple_<?=$control->id?>" >
                        <option value="" disabled selected> Seleccion opción </option>
                        <option value="<?=$comentario->cumple ?>" <?=$comentario->cumple=='si' ? 'selected' : '' ?>>Si</option>
                        <option value="<?=$comentario->cumple ?>" <?=$comentario->cumple=='no' ? 'selected' : '' ?>>No</option>
                    </select>
                    <? endforeach ?>
                <?php else: ?>
                    <select class="form-control cumple" name="cumple_<?=$control->id?>" id="cumple_<?=$control->id?>" >
                    <option value="" disabled selected>Seleccione opción</option>
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

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Indique las razones del no cumplimiento</h4>
      </div>
      <div class="modal-body">
    <form action="<?=URL::to('controles/incumplimiento')?>" id="myform" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="message-text" class="control-label">Observaciones:</label>
            <textarea cols="10" rows="5" style="resize:none" class="form-control" id="comentario_incumplimiento" name="comentario_incumplimiento"></textarea>
          </div>
      </div>
      <input class="form-control" type="file" id="archivo" name="archivo" />
      <input type="hidden" name="control_id" id="control_id">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
      <div class="modal-footer">
        <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>  
        <input type="submit" class="btn btn-default btn-danger" name="submit" value="Registrar" />
      </div>
    </div>
    </form>
  </div>
</div>

<center><?php echo $controles->links(); ?></center>

<script type="text/javascript">


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
                    if($('#cumple_'+cid).val()=='no'){
                        $("#control_id").val(cid);
                        $('#exampleModal').modal('show');    
                    }
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
     });


    /* OK
   var form = document.querySelector('form');
   var request = new XMLHttpRequest();

   request.addEventListener('load', function(e){
        varjson = JSON.parse(e.target.responseText);
        //console.log(varjson.success);
   },false);

   form.addEventListener('submit',function(e){
        e.preventDefault();
        var formdata = new FormData(form);
        request.open('post',"<?=URL::to('controles/incumplimiento')?>");
        request.send(formdata);

   },false);

   */

    $(document).ready(function() {
        var options = { 
            beforeSubmit:  showRequest,
            success: showResponse,
            dataType: 'json' 
            }; 
        $('body').delegate('#archivo','change', function(){
            $('#myform').ajaxForm(options).submit();        
        }); 
    });

    function showRequest(formData, jqForm, options) { 
        //$("#validation-errors").hide().empty();
        //$("#output").css('display','none');
        return true; 
    } 
    function showResponse(response, statusText, xhr, $form) {
        //console.log(response) 
        if(response.success == false)
        {
            var arr = response.errors;
            $.each(arr, function(index, value)
            {
                if (value.length != 0)
                {
                    $("#validation-errors").append('<div class="alert alert-error"><strong>'+ value +'</strong><div>');
                }
            });
            $("#validation-errors").show();
        } else {
             //$("#output").html("<img src='"+response.file+"' />");
             //$("#output").css('display','block');
             alert(response.success);
        }
    }

</script>
