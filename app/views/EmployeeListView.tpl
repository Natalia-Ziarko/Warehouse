{extends file="Main.tpl"}

{$card_title = 'Magazyn - lista pracowników'}

{block name=content}
    <br><br><br>
    <div style="width: 90%; margin: 0 auto;">
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeManager>Strona główna</a></li>
            <li class="active">Lista pracowników</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Pracownicy (z uprawnieniami magazyniera)</h1>
        </header>
        
        <br>
        <table cellpadding="5" cellspacing="0" border="1" style="width: 100%; margin: 0 auto;">
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Nazwisko</th>
                <th>Imię</th>
                <th>Telefon</th>
            </tr>
            {foreach $emp_list as $row}
            <tr>
                <td>{$row["user_id"]}</td>
                <td>{$row["user_login"]}</td>
                <td>{$row["user_name_surname"]}</td>
                <td>{$row["user_first_name"]}</td>
                <td>{$row["user_phone"]}</td>
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
