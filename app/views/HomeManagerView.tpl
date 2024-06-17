{extends file="Main.tpl"}

{$card_title = 'Magazyn - strona główna'}

{block name=content}
    <div class="row" style="width: 90%; margin: 0 auto;">
        <article class="col-sm-9 maincontent">
            <header class="page-header"> <h1 class="page-title">Strona główna</h1> </header>
            
            <img src="assets/images/warehouse.jpg" alt="" class="img-rounded pull-left" width="300" >
            <br><h3>Zajętość magazynu {$occupied_percent}%</h3>
            <p>Ilość miejsc w magazynie: <b>{$loc_count}</b>, zajętych: <b>{$stock_count}</b></p>
            
            {*<h3>Drugi nagłówek</h3>
            <p>Drugi tekst</p>*}
            
        </article>
        <aside class="col-sm-3 sidebar sidebar-right">
            <div class="widget">
                <h4>Operacje</h4>
                <ul class="list-unstyled list-spaces">
                    <li><a href={$conf->action_root}warehouseStat>Statystyki</a><br>
                        <span class="small text-muted">Przyjęcia, wydania, lokalizacje magazynowe</span>
                    </li>                    
                    <li><a href={$conf->action_root}employeeList>Lista pracowników</a><br>
                        <span class="small text-muted">Sprawdź listę pracowników</span>
                    </li>
                    <li><a href={$conf->action_root}warehouseLoc>Plan magazynu</a><br>
                        <span class="small text-muted">Sprawdź ile miejsca jest w całym magazynie</span>
                    </li>
                    {*
                    <li><a href={$conf->action_root}warehouseDel>Dostawa</a><br>
                        <span class="small text-muted">Przyjmij dostawę</span>
                    </li>
                    <li><a href={$conf->action_root}productLocation>Lokalizacja produktu</a><br>
                        <span class="small text-muted">Zlokalizuj produkt</span>
                    </li>
                    <li><a href={$conf->action_root}warehouseRel>Wydanie</a><br>
                        <span class="small text-muted">Zrób wydanie zewnętrzne</span>
                    </li>
                    *}
                </ul>
            </div>
        </aside>
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