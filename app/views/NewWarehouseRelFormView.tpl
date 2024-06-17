{extends file="Main.tpl"}

{$card_title = 'Magazyn - nowe wydanie'}

{block name=content}
    <br><br><br>
    <div style="width: 90%; margin: 0 auto;">
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeEmployee>Strona główna</a></li>
            <li><a href={$conf->action_root}homeEmployee>Wybór WZ</a></li>
            <li class="active">Formularz WZ</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Przygotuj towary do odbioru</h1>
        </header>
        
    <form action="{$conf->action_root}doneWarehouseRelForm">
        <br>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID produktu</th>
                        <th>Nazwa produktu</th>
                        <th>Ilość</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $release_details_list as $row}
                    <tr>
                        <td>{$row.pos_prod_id}</td>
                        <td>{$row.prod_name}</td>
                        <td>{$row.pos_amount}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        
        <br>
        <div class="row">
            <div class="col-sm-2 col-sm-offset-10 text-right">
                <input type="hidden" name="oper_id" value="{$oper_id}">
                <input class="btn btn-action" type="submit" name="submit" value="☑ ︎Potwierdź przygotowanie">
            </div>
        </div>

    </form>
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