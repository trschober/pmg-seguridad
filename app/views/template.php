<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguridad de la Información - <?=$title?></title>

    <!-- Bootstrap -->
    <link href="<?= asset('css/bootstrap.min.css') ?>" rel="stylesheet" >
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    <script src="<?=URL::asset('js/jquery2.min.js')?>"></script>
    <script src="<?=URL::asset('js/bootstrap.min.js')?>"></script>
    <script src="<?=URL::asset('js/jquery.countdown.min.js')?>"></script>
    <script src="<?=URL::asset('js/jquery.form.js')?>"></script>
    

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <?php if(Auth::check()):?>
            <a href="<?=URL::to('bienvenida')?>" class="navbar-brand">Seguridad de la Información</a>
        <?php else: ?>
            <a href="<?=URL::to('/')?>" class="navbar-brand">Seguridad de la Información</a>
        <?php endif ?>
    </div>
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <?php if(Auth::check()):?>
        <ul class="nav navbar-nav" id="menu-superior">
            <li><a href="<?=URL::to('documentos')?>">Documentos</a></li>
            <li><a href="<?=URL::to('controles')?>">Controles</a></li>
            <li><a href="<?=URL::to('riesgos')?>">Análisis de riesgo</a></li>
            <li><a href="<?=URL::to('retroalimentacion')?>">Observaciones Generales</a></li>
        </ul>
        <?php endif?>

        <ul class="nav navbar-nav navbar-right">
            <?php if(Auth::check()):?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="true"><?=Auth::user()->nombres." ".Auth::user()->apellidos?><span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                    <?php if(Auth::user()->perfil==='experto'): ?>
                    <li class="menu-item dropdown dropdown-submenu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Gesti&oacute;n</a>
                        <ul class="dropdown-menu">
                            <li><a href="<?=URL::to('gestion/instituciones')?>">Instituciones</a></li>
                            <li><a href="<?=URL::to('gestion/usuarios')?>">Usuarios</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <li><a href="<?=URL::to('logout')?>">Cerrar sesión</a></li>
                </ul>
            </li>
            <?php endif?>
        </ul>
    </div>
    </div>
    </nav>
    
    <?php if(Auth::check()):?> 
    <nav class="nav-perfil">
        <div class="container print-hide"><strong><?=Auth::user()->institucion->servicio?></strong></div>
    </nav>
    <? endif ?>
    
    <main>
    <div class='container container-form'>
    <div class="col-lg-offset-1 col-lg-11">
    <div class="well well-lg">
        <?=$content?>
    </div>
    </div>
    </div>
    </main>
    
    <footer class="print-hide">
        <div class="navbar navbar-default"></div>
        <div class="area2">
            <div class="container">
                <div class="row">
                  <div class="col-xs-2">
                      <a class="main-logo" href="http://www.minsegpres.gob.cl" target="_blank"><img src="<?=asset('img/logo.png')?>" alt="Ministerio Secretaría General de la Presidencia"></a>
                  </div>
                  <div class="col-xs-2">
                      <a class="main-logo" href="http://www.interior.gob.cl" target="_blank"><img src="<?=asset('img/logo-interior.png')?>" alt="Ministerio del Interior y Seguridad Pública"></a>
                  </div>
                  <div class="col-xs-2">
                      <a class="main-logo" href="http://www.subtel.gob.cl" target="_blank"><img src="<?=asset('img/logo-subtel.png')?>" alt="Ministerio de Transportes y Telecomunicacione"></a>
                  </div>
                  <div class="col-xs-3">
                      <h3>Enlaces</h3>
                      <ul>
                          <li><a href="http://www.minsegpres.gob.cl" target="_blank">Ministerio Secretaría General de la Presidencia</a></li>
                      </ul>
                      <ul>
                          <li><a href="https://www.csirt.gob.cl" target="_blank">Ministerio del Interior y Seguridad Pública</a></li>
                      </ul>
                  </div>
                  <div class="col-xs-3">
                      <div class="politicas">
                          <a href="#">Politicas de Privacidad</a> | <a href="">Visualizadores y Plugins</a> | <a href="#">CC</a>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </footer>


    
</body>
</html>