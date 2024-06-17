{extends file="Main.tpl"}

{$card_title = 'Magazyn - strona administratora'}

{block name=content}
    <div style="width: 90%; margin: 0 auto;">
        <header class="page-header">
            <h1 class="page-title">Użytkownicy systemu</h1>
        </header>
        
        <br>
        <table cellpadding="5" cellspacing="0" border="1" style="width: 100%; margin: 0 auto;">
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Nazwisko</th>
                <th>Imię</th>
                <th>Telefon</th>
                <th>Rola</th>
            </tr>
            {foreach $user_list as $row}
            <tr>
                <td>{$row["user_id"]}</td>
                <td>{$row["user_login"]}</td>
                <td>{$row["user_name_surname"]}</td>
                <td>{$row["user_first_name"]}</td>
                <td>{$row["user_phone"]}</td>
                <td>{$row["rol_name"]}</td>
            </tr>
            {/foreach}
        </table>      
    </div>
{/block}

{block name=operations}
    <h3 class="text-center thin">Dostępne operacje magazynowe:</h3>

    <div class="row">   
        <div class="h-caption"><h4><i class="fa fa-ban"></i>Brak uprawnień</h4></div>
    </div>
{/block}