<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seguridad de la Información</title>

    <!-- Bootstrap -->
    <link href="<?= asset('css/bootstrap.min.css') ?>" rel="stylesheet" >
    <link href="<?= asset('css/admin.css') ?>" rel="stylesheet">
    <script src="<?=URL::asset('js/jquery.min.js')?>"></script>
    <script src="<?=URL::asset('js/bootstrap.min.js')?>"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>
    

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
        <a href="<?=URL::to('/')?>" class="navbar-brand">Seguridad de la Información</a>
    </div>
    </div>
    </nav>
    
    <main>
    <div class='container container-form'>
    <div class="col-lg-offset-1 col-lg-11">
    <div class="well well-lg">
        <div class="row">
		    <div class="col-md-12">
		        <div class="alert alert-danger">
		            <h3>Página no encontrada</h3>
		            <p>Favor comuníquese con el administrador del sitio</p>
		        </div>
		    </div>
		</div>
    </div>
    </div>
    </div>
    </main>
    
    <footer class="print-hide">
        <div class="navbar navbar-default"></div>
        <div class="area2">
            <div class="container">
                <div class="row">
                  <div class="col-xs-3">
                      <a class="main-logo" href="http://www.minsegpres.gob.cl" target="_blank"><img src="<?=asset('img/logo.png')?>" alt="Ministerio Secretaría General de la Presidencia"></a>
                  </div>
                  <div class="col-xs-5">
                      <h3>Enlaces</h3>
                      <ul>
                          <li><a href="http://www.minsegpres.gob.cl" target="_blank">Ministerio Secretaría General de la Presidencia</a></li>
                      </ul>
                  </div>
                  <div class="col-xs-4">
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