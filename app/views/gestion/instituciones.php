<ol class="breadcrumb">
  <li><a href="/">Seguridad de la Información</a></li>
  <li class="active">Listado de instituciones</li>
</ol>

<table id="instituciones" class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Nº</th>
            <th>Servicio</th>
            <th>Estado</th>
        </tr>
    </thead>
    	
    <tbody>
    	<?php foreach ($instituciones as $institucion):  ?>
    	<tr>
    		<td><?=$institucion->id ?></td>
            <td><?=$institucion->servicio ?></td>
            <td>
             <div class="cid">
                <input type="hidden" name="cidv" value="<?=$institucion->id ?>">
            </div>
            <select class="form-control cumple" name="cumple_<?=$institucion->id?>" id="cumple_<?=$institucion->id?>">
                <option value="" disabled selected>Seleccione estado</option>
                <option value="ingresado" <?=$institucion->estado==='ingresado' ? 'selected' : '' ?>>Ingresado</option>
                <option value="enviado" <?=$institucion->estado==='enviado' ? 'selected' : '' ?>>Enviado</option>
                <option value="rechazado" <?=$institucion->estado==='rechazado' ? 'selected' : '' ?>>Rechazado</option>
                <option value="cerrado" <?=$institucion->estado==='cerrado' ? 'selected' : '' ?>>Cerrado</option>
            </select>
        </tr>
    	<?php endforeach ?>
    </tbody>
</table>

<script type="text/javascript">
	
	 $('.cumple').change(function(){
        var cid = $(this).parents('tr').find('.cid input[type="hidden"]').val();
        var estado = $(this).val();
        return false;
        $.ajax({
            type: 'GET',
            url:  "<?=URL::to('gestion/instituciones/actualizar')?>",
            data: 'control_id='+cid+'&institucion_id='+estado ,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data){
                //Comentario no existe
                if(data.success==true){
                    
                }
            },
            error:function(errors){
                console.log('errors'+errors);
            }
        });
     });


</script>