{extends file="Main.tpl"}

{$card_title = 'Magazyn - lokalizacje mag'}

{block name=content}
    <br><br><br>
    <div style="width: 90%; margin: 0 auto;">
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeManager>Strona główna</a></li>
            <li class="active">Lokalizacje magazynowe</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Wszystkie lokalizacje w magazynie</h1>
        </header>
            
        {*<h2>Podsumowanie:</h2><br>*}
        <table cellpadding="5" cellspacing="0" border="1" style="width: 40%">
            <tr>
                <th>ID</th>
                <th>Wymiar 1 (cm)</th>
                <th>Wymiar 2 (cm)</th>
                <th>Wymiar 3 (cm)</th>
                <th>Ilość</th>
            </tr>
            {foreach $warehouse_layout as $row}
            <tr>
                <td>{$row["size_id"]}</td>
                <td>{$row["size_dim1_max"]}</td>
                <td>{$row["size_dim2_max"]}</td>
                <td>{$row["size_dim3_max"]}</td>
                <td>{$row["total_places"]}</td>
            </tr>
            {/foreach}
        </table>
        
        <h2>Lista lokalizacji magazynowych:</h2>
        <table cellpadding="5" cellspacing="0" border="1" style="width: 20%">
            <tr>
                <th>Aleja</th>
                <th>Strona</th>
                <th>Numer</th>
                <th>Wielkość (ID)</th>
            </tr>
            {foreach $location_list as $row}
            <tr>
                <td>{$row["loc_alley"]}</td>
                <td>{$row["loc_side"]}</td>
                <td>{$row["loc_number"]}</td>
                <td>{$row["loc_size_id"]}</td>
            </tr>
            {/foreach}
        </table>
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