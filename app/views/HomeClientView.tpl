{extends file="Main.tpl"}

{$card_title = 'Magazyn - strona główna'}

{block name=content}
    <div style="width: 60%; margin: 0 auto;">
        {* Zalogowany użytkownik: {$logged_user}<br>
        ID użytkownika: {$logged_user_id}<br><br> *}

        <form class="pure-form pure-form-stacked" action="{$conf->action_url}homeClient">
            <legend>Opcje wyszukiwania</legend>
            <fieldset>
                <input type="text" placeholder="Nazwa" name="product" value="{$search_from_get}" style="width: 400px; padding: 10px;"/>
                <br><br>
                <button type="submit" class="btn btn-action">Filtruj</button>
            </fieldset>
        </form>
        <br>
        <form class="pure-form pure-form-stacked" action="{$conf->action_url}homeClient">
            <fieldset>
                <input type="hidden" name="product" value=""/>
                <button type="submit" class="btn">Usuń filtry</button>
            </fieldset>
        </form>                
        
        <br>
        <legend>Stan magazynowy</legend>
        <table cellpadding="5" cellspacing="0" border="1" style="width: 100%">
            <tr>
                <th>ID produktu</th>
                <th>Nazwa</th>
                <th>Ilość</th>
            </tr>
            {foreach $prod_list as $wiersz}
            <tr>
                <td>{$wiersz["prod_id"]}</td>
                <td>{$wiersz["prod_name"]}</td>
                <td>{$wiersz["stock_count"]}</td>
            </tr>
            {/foreach}
        </table>      
    </div>
{/block}

{block name=operations}
    <h3 class="text-center thin">Dostępne operacje magazynowe:</h3>

    <div class="row">
        <div class="h-caption"><h4><i class="fa fa-cogs fa-5"></i> Zlecenie wydania towarów</h4></div>
    </div>
{/block}