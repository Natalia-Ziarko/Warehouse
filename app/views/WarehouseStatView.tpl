{extends file="Main.tpl"}

{$card_title = 'Magazyn - statystyki'}

{block name=content}
    <br><br><br>
    <div style="width: 90%; margin: 0 auto;">
        <br><br><br>
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeManager>Strona główna</a></li>
            <li class="active">Statystyki</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Magazynowe statystyki</h1>
        </header>
            
        <h2>⛫ Zajętość magazynu: {$occupied_percent}%</h2><br>
        Liczba miejsc na magazynie: {$loc_count}<br>
        Liczba towarów na magazynie: {$stock_count}<br>
        
        <h2>♕ Liczba pracowników: {$emp_count}</h2><br>
        
        <h2>✔ Liczba zrealizowanych operacji: {$done_oper_count}</h2><br>
        Dostawy: {$del_count}<br>
        Wydania: {$rel_done_count}<br>
        
        <h2>✘ Oczekujące wydania: {$rel_new_count}</h2><br>
        
    </div>
{/block}

{block name=operations}
    <h3 class="text-center thin">Dostępne operacje magazynowe (tylko z uprawnieniami magazyniera):</h3>

    <div class="row">
        <div class="col-md-4 col-sm-6 highlight">
            <div class="h-caption"><h4><i class="fa fa-cogs fa-5"></i> Przyjęcie dostawy</h4></div>
        </div>
        <div class="col-md-4 col-sm-6 highlight">    
            <div class="h-caption"><h4><i class="fa fa-cogs fa-5"></i> Lokalizacja produktu</h4></div>
        </div>
        <div class="col-md-4 col-sm-6 highlight">
            <div class="h-caption"><h4><i class="fa fa-cogs fa-5"></i> Wydanie zewnętrzne</h4></div>
        </div>
    </div>
{/block}