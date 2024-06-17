{extends file="Main.tpl"}

{$card_title = 'Magazyn - strona główna'}

{block name=content}
    <div class="row" style="width: 90%; margin: 0 auto;">
        <article>
            <header class="page-header">
                <h1 class="page-title">Strona główna</h1>
            </header>
            
            <div class="row">
                <div class="col-md-2 col-sm-8 highlight">
                    <div class="h-caption"><img src="assets/images/warehouse.jpg" alt="" class="img-rounded pull-left" width="300"></div>
                </div>
                <div class="col-md-4 col-sm-6 highlight">
                    <div class="h-caption"><h3>Zajętość magazynu: {$occupied_percent}%</h3></div>
                    <div class="h-caption"><h3>Oczekujące wydania: {$rel_new_count}</h3></div>
                </div>
                <div class="col-md-2 col-sm-8 highlight">
                    <div class="h-caption"><img src="assets/images/stat.jpg" alt="" class="img-rounded pull-left" width="300"></div>
                </div>
                <div class="col-md-4 col-sm-6 highlight">           
                    <div class="h-caption"><h3>Zrealizowane przyjęcia: {$done_deliveries}</h3></div>
                    <div class="h-caption"><h3>Zrealizowane wydania: {$done_releases}</h3></div>
                </div>    
            </div>
        </article>
    </div>
{/block}

{block name=operations}
    <h3 class="text-center thin">Dostępne operacje magazynowe:</h3>

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