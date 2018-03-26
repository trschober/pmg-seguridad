<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Historial de ejercicios</li>
</ol>

<h3>Seleccione el periodo con el que desea trabajar</h3>
<table class="table table-hover table-bordered">
    <thead>
      <tr>
        <td>Año</td>
        <td>Proceso</td>
        <td>Acción</td>
      </tr>
    </thead>
    <tbody>
    <?php foreach($historial_ejercicios as $h): ?>
        <tr <?= $h->activo? 'class="success"' : '' ?> >
          <td><?=$h->anio?></td>
          <td><?=strtoupper($h->tipo)?> <?= $h->activo? '<strong>(Activo)</strong>' : '' ?></td>
          <td><a href="<?=URL::to('ejercicio')."/".$h->id?>">Revisar</a></td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
