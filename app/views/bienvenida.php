<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
</ol>

<div id="home-container" class="row">
	
	<div class="alert alert-warning" role="alert"><div id="clock" class="lead"></div></div>
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
				<!--
				<button type="button" class="btn btn-danger upload-image registrar" id="registrar">Rechazar</button><br>
				<button type="button" class="btn btn-success upload-image registrar" id="registrar">Enviar a aprobador</button><br>
				<button type="button" class="btn btn-success upload-image registrar" id="registrar">Enviar a red de expertos</button><br>
				-->
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

<script type="text/javascript">

$('#clock').countdown('<?=$fecha_termino?>', function(event) {
  var $this = $(this).html(event.strftime('<strong>Quedan '
    + '<span>%d</span> días '
    + '<span>%H</span> horas '
    + '<span>%M</span> minutos '
    + 'para cerrar el hito de avance</strong>'));
});

</script>
