<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
</ol>

<div id="home-container" class="row">
	
	<div class="alert alert-warning" role="alert"><div id="clock" class="lead"></div></div>
	<?php if(!is_null(Auth::user()->institucion->observaciones_aprobador) && Auth::user()->institucion->estado='rechazado'): ?>
		<div class="alert alert-warning" role="alert"><h2><strong>Observaciones aprobador</strong></h2><br><?=Auth::user()->institucion->observaciones_aprobador?></div>
	<?php endif;?>
	<div class="alert alert-success" role="alert"><h2><strong>Controles actualizados</strong></h2><h3>El servicio tiene <?=$controles_actualizados?> controles actualizados de un total de <?=$total_controles?></h3></div>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentaje_actualizados?>%;"><?=$porcentaje_actualizados?>%</div>
	</div>
	
	<div class="well">
		<h2>Sistema de Seguridad de la Información</h2>
		<p>Estimad@s Encargad@s de Seguridad de la Información, les damos la bienvenida a la plataforma de reportabilidad.</p>
	</div>

	<div class="col-md-12">
	<div class="row">
		<div class="col-md-6">
			<div class="">
				<h3>Contacto</h3>
				<p>En caso de dudas contactarse con la Red de Expertos</p>
				<table class="table table-hover">
					<thead>
				        <tr>
				            <th>Analista</th>
				            <th>Correo</th>
				        </tr>
				    </thead>
				    <tbody>
				    	<tr><td>Hernán Espinoza</td><td><a href="mailto:hespinoza@interior.gob.cl">hespinoza@interior.gob.cl</a></td></tr>
				    	<tr><td>Luis Carrasco</td><td><a href="mailto:lcarrasco@interior.gob.cl">lcarrasco@interior.gob.cl</a></td></tr>
				    	<tr><td>Juan Pablo Meier</td><td><a href="mailto:fmeier@interior.gob.cl">fmeier@interior.gob.cl</a></td></tr>
				    	<tr><td>Nicole Merino</td><td><a href="mailto:nmerino@minsegpres.gob.cl">nmerino@minsegpres.gob.cl</a></td></tr>
				    	<tr><td>Sebastián Beeche</td><td><a href="mailto:sebastian.beeche@subtel.gob.cl">sebastian.beeche@subtel.gob.cl</a></td></tr>
				    	<tr><td>Rodrigo Pérez</td><td><a href="mailto:rperez@subtel.gob.cl">rperez@subtel.gob.cl</a></td></tr>
				    </tbody>
				</table>
			</div>
		</div>
		<div class="col-md-3">
			<div class="">
				<h3>Acciones</h3>
				<?php
					if(Auth::user()->perfil=='reporte' && in_array(Auth::user()->institucion->estado,array("ingresado","rechazado"))):
				?>
				<a href="<?=URL::to('institucion/aprobar')?>" class="btn btn-success" onclick="return confirm('Está seguro de enviar la información a aprobar?')">Enviar a aprobador</a>
				<?php
					elseif(Auth::user()->perfil=='aprobador' && in_array(Auth::user()->institucion->estado,array("enviado"))):
				?>
				<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalaprobador" data-whatever="@getbootstrap">Rechazar</button>
				<br><br>
				<a href="<?=URL::to('institucion/cerrar')?>" class="btn btn-success" onclick="return confirm('¿Está seguro de cerrar el proceso y enviar a Red de Expertos?')">Aprobar y cerrar Proceso</a>
				<?php endif;?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="">
				<h3>Descarga</h3>
				
			</div>
		</div>
	</div>
	</div>
</div>

<!-- Modal aprobador comentarios opcionales -->
<div class="modal fade" id="modalaprobador" tabindex="-1" role="dialog" aria-labelledby="modalaprobador">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalaprobador">Indique cuales son las razones del <strong>rechazo</strong> de la información</h4>
      </div>
      <div class="modal-body">
    <form action="<?=URL::to('institucion/rechazar')?>" id="myform" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="message-text" class="control-label">Observaciones (opcional):</label>
            <textarea cols="10" rows="5" style="resize:none" class="form-control" id="observaciones" name="observaciones"></textarea>
          </div>
      </div>
      <input type="hidden" name="control_id" id="control_id">
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
      <div class="modal-footer">
        <button type="button" class="btn btn-default upload-image" data-dismiss="modal">Cerrar</button>
        <input type="submit" class="btn btn-success upload-image" id="actualizar" value="Enviar">
      </div>
    </div>
    </form>
  </div>
</div>

<script type="text/javascript">

$('#clock').countdown('<?=$fecha_termino?>', function(event) {
  var $this = $(this).html(event.strftime('<strong>Quedan '
    + '<span>%d</span> días '
    + '<span>%H</span> horas '
    + '<span>%M</span> minutos '
    + 'para cerrar el hito de avance</strong>'));
});

</script>
