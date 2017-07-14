<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Usuarios</li>
</ol>

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
        <a href="<?=URL::to('gestion/usuarios/editar')?>" class="btn btn-info" value="Nuevo" id="nuevo" name="nuevo">Nuevo</a>
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
            <td><a href="<?=URL::to('gestion/usuarios/editar/'.$usuario->id)?>" class="ver"><span class="label label-info">Editar</span></a></td> 
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