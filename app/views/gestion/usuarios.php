<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Usuarios</li>
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

<?php if(Auth::user()->perfil==='experto'): ?>
    <div class="form-group pull-right">
        <form action="<?=URL::to('gestion/usuarios')?>" id="myform" method="POST" enctype="multipart/form-data">
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
        <input type="button" class="btn btn-info" value="Nuevo" id="nuevo" name="nuevo" />
        </form>
    </div>
<?php endif ?>

<table id="instituciones" class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Rut</th>
            <th>Usuario</th>
            <th>Servicio</th>
            <th>Perfil</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach ($usuarios as $usuario):  ?>
    	<tr>
    		<td><?=$usuario->rut ?></td>
            <td><?=$usuario->nombres ." ".$usuario->apellidos ?></td>
            <td><?=$usuario->institucion->servicio ?></td>
            <td><?=$usuario->perfil ?></td>
            <td><div class="cid"><input type="hidden" name="cidv" value="<?=$usuario->id ?>"></div></td>
            <td><a href="#" class="ver"><span class="label label-info">Actualizar</span></a></td> 
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
          <form action="<?=URL::to('gestion/usuarios/actualizar')?>" id="myform" method="POST" enctype="multipart/form-data" >
            <!--<div id="links" class="form-group cumpleform"></div>-->
            <div class="form-group">
                <label for="rut">Rut</label>
                <input type="text" class="form-control validar" id="rut" name="rut" placeholder="Rut">
            </div>
            <div class="form-group">
                <label for="nombres">Nombres</label>
                <input type="text" class="form-control validar" id="nombres" name="nombres" placeholder="Nombres">
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos</label>
                <input type="text" class="form-control validar" id="apellidos" name="apellidos" placeholder="Apellidos">
            </div>
            <div class="form-group">
                <label for="correo">Correo</label>
                <input type="text" class="form-control validar" id="correo" name="correo" placeholder="Correo">
            </div>
            <div class="form-group">
               <label for="institucion_usuario">Instituciones</label>
                <select class="form-control validar" id="institucion_usuario" name="institucion_usuario">
                    <option value="" disabled selected>Seleccione opción</option>
                    <?php foreach($instituciones as $i): ?>
                       <?php if($i->id == Session::get('sesion_institucion')): ?>
                        <option value="<?=$i->id?>" selected><?=$i->servicio?></option>
                       <?php else: ?>
                        <option value="<?=$i->id?>"><?=$i->servicio?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="form-group">
                <label for="perfil">Perfil</label>
                <select class="form-control validar" name="perfil" id="perfil">
                    <option value="" disabled selected>Seleccione opción</option>
                    <option value="ingreso">ingreso</option>
                    <option value="validador">validador</option>
                    <option value="experto">experto</option>
                    <option value="evaluador">evaluador</option>
                </select>
            </div>
            <!-- Errores -->
            <div class="alert alert-danger avatar_alert" role="alert" style="display: none">
                <ul></ul>
            </div>
            <ul></ul>
            <input type="hidden" name="usuario_id" id="usuario_id">
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary" id="actualizar" name="actualizar" data-dismiss="modal">Guardar</button>
        </div>
      </div>
    </div>
</div>

<script type="text/javascript">

	$(document).ready(function(){
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
            data: 'institucion_id='+id+'&estado='+estado,
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

    $('.ver').click(function(e) {
        var cid = $(this).parents('tr').find('.cid input[type="hidden"]').val();
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('gestion/usuarios/detalle')?>",
            data: 'usuario_id='+cid ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                if(data.success==true){
                    $("#institucion_id").val(cid);
                    $('h4.modal-title').text('Detalle');
                    $('#rut').val(data.usuario.rut);
                    $('#nombres').val(data.usuario.nombres);
                    $('#apellidos').val(data.usuario.apellidos);
                    $('#correo').val(data.usuario.correo);
                    $('#institucion_usuario').val(data.usuario.institucion_id);
                    $('#perfil').val(data.usuario.perfil);
                    $('#actualizar').val('Actualizar');
                    $('#modalcomentario').modal('show');
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
    });

    $('#nuevo').click(function(e) {
        $('#nombres').val('');
        $('#apellidos').val('');
        $('#correo').val('');
        $('#institucion_usuario').val('');
        $('#perfil').val('');
        $('#actualizar').removeAttr('disabled');
        $('#modalcomentario').modal('show');
    });

    /*
    $('.validar').on("change", function(){
        console.log('a');
        if($('#rut').val()!='' && $('#nombres').val()!='' && $('#apellidos').val()!='' && $('#correo').val()!=''){
            $('#actualizar').removeAttr('disabled');
        }
    });
    */
    
    $(document).on('submit', '#myform', function(event){
        var info = $('.avatar_alert');
        //event.preventDefault();
        var data = { rut: $("#rut").val(),nombres: $("#nombres").val() };
        console.log(data);
        return false;
        $.ajax({
            url: "/dashboard/avatar",
            type: "POST",
            data: data,
        }).done(function(response) {
                info.hide().find('ul').empty();
            if(response.errors)
            {
                $.each(response.errors, function(index, error){
                    info.find('ul').append(error);
                });
                info.slideDown();
            }
            else if(response.success){
              window.location.href = "/dashboard";
            }
        });
    });
    

    /*
    $('#actualizar').click(function(e) {
        //showPleaseWait();
        var data = { rut: $("#rut").val(),nombres: $("#nombres").val() }
        console.log(data);
        return false;
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
    */

    /*
    $('#comentario_incumplimiento').bind('input propertychange', function() {
        if(!$.trim($("#comentario_incumplimiento").val())){
            $('#actualizar').attr('disabled', true);
        }else{
            $('#actualizar').removeAttr('disabled');
        }
    });
    */

</script>