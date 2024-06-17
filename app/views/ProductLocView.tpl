{extends file="Main.tpl"}

{$card_title = 'Magazyn - stock'}

{block name=content}
    <br><br><br>
    <div style="width: 90%; margin: 0 auto;">
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeManager>Strona główna</a></li>
            <li class="active">Lokalizacja produktu</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Zlokalizuj produkt na magazynie</h1>
        </header>
            
        <form class="pure-form pure-form-stacked" action="{$conf->action_url}productLocation">
            <fieldset>
                <label for="date">Nazwa produktu lub ID:</label>
                <input type="text" placeholder="Nazwa / ID" name="prod" value="{$filter_prod}" style="width: 400px; padding: 10px;"/><br><br>
                <label for="date">Data dostawy:</label>
                <input type="date" name="date" value="{$filter_date}" style="padding: 10px;"/><br><br>
                <button type="submit" class="btn btn-action">Filtruj</button>
            </fieldset>
        </form>
        
        <br>
        <form class="pure-form pure-form-stacked" action="{$conf->action_url}productLocation">
            <fieldset>
                <input type="hidden" name="prod" value=""/>
                <input type="hidden" name="date" value=""/>
                <button type="submit" class="btn">Usuń filtry</button>
            </fieldset>
        </form>                
        
        <br>
        <table cellpadding="5" cellspacing="0" border="1" style="width: 80%;">
            <tr>
                <th>Klient</th>
                <th>ID produktu</th>
                <th>Nazwa produktu</th>
                <th>Data otrzymania</th>
                <th>Aleja</th>
                <th>Strona</th>
                <th>Numer lokalizacji</th>
            </tr>
            {foreach $prod_list as $row}
            <tr>
                <td>{$row["user_name_surname"]}</td>
                <td>{$row["prod_id"]}</td>
                <td>{$row["prod_name"]}</td>
                <td>{$row["oper_receive_date"]}</td>
                <td>{$row["loc_alley"]}</td>
                <td>{$row["loc_side"]}</td>
                <td>{$row["loc_number"]}</td>
            </tr>
            {/foreach}
        </table>        
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