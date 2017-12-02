<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
</ol>

<div id="home-container" class="row">
	
	<?php if(Auth::user()->perfil!='experto' && Auth::user()->institucion->estado!='cerrado'): ?>
	<div class="alert alert-warning" role="alert"><div id="clock" class="lead"></div></div>
	<?php if(!is_null(Auth::user()->institucion->observaciones_aprobador) && Auth::user()->institucion->estado=='rechazado'): ?>
		<div class="alert alert-warning" role="alert"><h2><strong>Observaciones validador</strong></h2><br><?=Auth::user()->institucion->observaciones_aprobador?></div>
	<?php endif;?>
	<div class="alert alert-success" role="alert"><h2><strong>Controles actualizados</strong></h2><h3>El servicio tiene <?=$controles_actualizados?> controles actualizados de un total de <?=$total_controles?></h3></div>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: <?=$porcentaje_actualizados?>%;"><?=$porcentaje_actualizados?>%</div>
	</div>
	<?php endif ?>
	
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
				    	<tr><td>Hernán Espinoza</td><td><a href="mailto:ssi@interior.gob.cl">ssi@interior.gob.cl</a></td></tr>
				    	<tr><td>Luis Carrasco</td><td><a href="mailto:ssi@interior.gob.cl">ssi@interior.gob.cl</a></td></tr>
				    	<tr><td>Juan Pablo Meier</td><td><a href="mailto:ssi@interior.gob.cl">ssi@interior.gob.cl</a></td></tr>
				    	<tr><td>Nicole Merino</td><td><a href="mailto:ssi@minsegpres.gob.cl">ssi@minsegpres.gob.cl</a></td></tr>
				    	<tr><td>Tomás Riveros</td><td><a href="mailto:ssi@minsegpres.gob.cl">ssi@minsegpres.gob.cl</a></td></tr>
				    	<tr><td>Macarena Calderón</td><td><a href="mailto:ssi@minsegpres.gob.cl">ssi@minsegpres.gob.cl</a></td></tr>
				    	<tr><td>Rodrigo Pérez</td><td><a href="mailto:ssi@subtel.gob.cl">ssi@subtel.gob.cl</a></td></tr>
				    </tbody>
				</table>
			</div>
		</div>
		<?php if(Auth::user()->perfil!='experto'): ?>
		<div class="col-md-3">
			<div class="">
				<h3>Acciones</h3>
				<?php
					if(Auth::user()->perfil=='ingreso' && in_array(Auth::user()->institucion->estado,array("ingresado","rechazado")) && $habilitado):
				?>
				<a href="<?=URL::to('institucion/aprobar')?>" class="btn btn-success" id="validar" name="validar">Enviar a validador</a>
				<?php
					elseif(Auth::user()->perfil=='validador' && in_array(Auth::user()->institucion->estado,array("enviado"))):
				?>
				<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalaprobador" data-whatever="@getbootstrap" id="rechazar" name="rechazar">Rechazar</button>
				<br><br>
				<a href="<?=URL::to('institucion/cerrar')?>" class="btn btn-success" id="cerrar" name="cerrar">Aprobar y cerrar Proceso</a>
				<?php endif;?>
			</div>
		</div>
		<div class="col-md-3">
			<div class="">
				<h3>Descarga</h3>
				<?php
					if(Auth::user()->institucion->estado=='cerrado'):
				?>
				<a href="<?=URL::to('institucion/informe-cierre')?>" class="btn btn-success">Informe de cierre</a><br/><br/>
				<?php endif;?>
				<?php
					if(file_exists('uploads/reportes/reporte-red-'.Auth::user()->institucion_id.'.xls')):
				?>
					<a href="<?=URL::to('retroalimentacion/resultado')?>" class="btn btn-success">Reporte red de expertos</a>
				<?php endif;?>
				
			</div>
		</div>
		<?php endif ?>
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
        <input type="submit" class="btn btn-success upload-image" value="Enviar" id="devolver" name="devolver">
      </div>
    </div>
    </form>
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
});

$('#validar').click(function() {
	if(confirm('¿Está seguro de enviar la información a aprobar?')){
		showPleaseWait();
		$(this).attr('disabled',true);
	}else{
		return false;
	}
});

$('#cerrar').click(function() {
	if(confirm('¿Está seguro de cerrar el proceso e informar a la Red?')){
		showPleaseWait();
		$('#rechazar').attr('disabled',true);
		$(this).attr('disabled',true);
	}else{
		return false;
	}
});

var fechaTermino = new Date('<?=$fecha_termino?>');
$('#clock').countdown(fechaTermino, function(event) {
  var $this = $(this).html(event.strftime('<strong>Quedan '
  	+ '<span>%w</span> semana(s) '
    + '<span>%d</span> día(s) '
    + '<span>%H</span> horas '
    + '<span>%M</span> minutos '
    + 'para cerrar el hito de avance</strong>'));
});

</script>
