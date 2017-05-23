function getBanner(tramites_nivel4, cantidad_tramites, porcentaje){

	var str;
    var $ = document;
    var cssId = 'css-pmg';
    var dominio = 'http://localhost:8000';
    
    if (!$.getElementById(cssId))
    {
        var head  = $.getElementsByTagName('head')[0];
        var link  = $.createElement('link');
        link.id   = cssId;
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = dominio+'/css/banner.css';
        link.media = 'all';
        head.appendChild(link);
    }
    
    str = '<div id="widget" class="list-group">';
    str += '<h2 class="fh2">Resultados PMG MEI</h2>';
    str += '<ul class="list-group">';
    str += '<li class="list-group-item">Trámites nivel 4 <span class="badge">'+tramites_nivel4+'</span></li>';
    str += '<li class="list-group-item">Cantidad trámites <span class="badge">'+cantidad_tramites+'</span></li>';
    str += '<li class="list-group-item">Porcentaje <span class="badge">'+porcentaje+'%</span></li>';
    str += '</ul>';
    str += '</div>';
    document.getElementById('container-banner').innerHTML = str;
}