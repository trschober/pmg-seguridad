<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Usuarios</li>
</ol>

<form action="<?=URL::to('gestion/usuarios/actualizar')?>" id="myform" method="POST" enctype="multipart/form-data" >
    <!-- Rut -->
    <div class="form-group">
        <label for="rut">Rut</label>
        <input type="text" class="form-control validar" id="rut" name="rut" placeholder="Rut" value="<?=$usuario->rut ?>">
    </div>
     <?php if($errors->has('rut')): ?>
      <div class="alert alert-danger">
      <?php foreach($errors->get('rut')as $error): ?>
        <?=$error?><br>
      <?php endforeach; ?>
      </div>
    <?php endif ?>
    <!-- Nombres -->
    <div class="form-group">
        <label for="nombres">Nombres</label>
        <input type="text" class="form-control validar" id="nombres" name="nombres" placeholder="Nombres" value="<?=$usuario->nombres ?>">
    </div>
    <?php if($errors->has('nombres')): ?>
      <div class="alert alert-danger">
      <?php foreach($errors->get('nombres')as $error): ?>
        <?=$error?><br>
      <?php endforeach; ?>
      </div>
    <?php endif ?>
    <!-- Apellidos -->
    <div class="form-group">
        <label for="apellidos">Apellidos</label>
        <input type="text" class="form-control validar" id="apellidos" name="apellidos" placeholder="Apellidos" value="<?=$usuario->apellidos ?>">
    </div>
    <?php if($errors->has('apellidos')): ?>
      <div class="alert alert-danger">
      <?php foreach($errors->get('apellidos')as $error): ?>
        <?=$error?><br>
      <?php endforeach; ?>
      </div>
    <?php endif ?>
    <!-- Correo -->
    <div class="form-group">
        <label for="correo">Correo</label>
        <input type="text" class="form-control validar" id="correo" name="correo" placeholder="Correo" value="<?=$usuario->correo ?>">
    </div>
    <?php if($errors->has('correo')): ?>
      <div class="alert alert-danger">
      <?php foreach($errors->get('correo')as $error): ?>
        <?=$error?><br>
      <?php endforeach; ?>
      </div>
    <?php endif ?>
    <!-- perfil -->
    <div class="form-group">
        <label for="perfil">Perfil</label>
        <select class="form-control validar" name="perfil" id="perfil">
            <option value="" disabled selected>Seleccione opción</option>
            <option value="ingreso" <?=isset($usuario) && $usuario->perfil=='ingreso' ? 'selected':''?>>ingreso</option>
            <option value="validador" <?=isset($usuario) && $usuario->perfil='validador' ? 'selected':''?>>validador</option>
            <option value="experto" <?=isset($usuario) && $usuario->perfil='experto' ? 'selected':''?>>>experto</option>
            <option value="evaluador" <?=isset($usuario) && $usuario->perfil='evaluador' ? 'selected':''?>>>evaluador</option>
        </select>
    </div>
    <?php if($errors->has('perfil')): ?>
      <div class="alert alert-danger">
      <?php foreach($errors->get('perfil')as $error): ?>
        <?=$error?><br>
      <?php endforeach; ?>
      </div>
    <?php endif ?>
    <!-- Instituciones -->
    <div class="form-group">
    <label for="institucion_usuario">Instituciones</label>
    <select class="form-control validar" id="institucion_usuario" name="institucion_usuario">
        <option value="" disabled selected>Seleccione opción</option>
        <?php foreach($instituciones as $i): ?>
           <option value="<?=$i->id?>" <?=isset($usuario) && $usuario->institucion_id==$i->id ? 'selected':''?> ><?=$i->servicio?></option>
        <?php endforeach ?>
    </select>
    </div>
    <?php if($errors->has('institucion_usuario')): ?>
      <div class="alert alert-danger">
      <?php foreach($errors->get('institucion_usuario')as $error): ?>
        <?=$error?><br>
      <?php endforeach; ?>
      </div>
    <?php endif ?>
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="<?=URL::to('gestion/usuarios')?>" class="btn btn-danger" id="cancelar" name="cancelar">Cancelar</a>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
</form>