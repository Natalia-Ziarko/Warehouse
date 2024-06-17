{extends file="Main.tpl"}

{$card_title = 'Magazyn - nowa dostawa'}

{block name=content}
<br><br><br>    
<div style="width: 90%; margin: 0 auto;">
    <ol class="breadcrumb">
        <li><a href={$conf->action_root}homeEmployee>Strona główna</a></li>
        <li><a href={$conf->action_root}warehouseDel>Wybór klienta</a></li>
        <li class="active">Formularz PZ</li>
    </ol>
    
    <header class="page-header">
        <h1 class="page-title">Formularz dostawy klienta <b>{foreach $prod_list as $row}{$row.user_name_surname}{break}{/foreach}</b></h1>
    </header>
   
    {*<select class="form-control" id="product" name="product" required>
        <option value="" disabled selected>Towar</option>
        {foreach $prod_list as $row}
            <option value="{$row.prod_id}">({$row.user_name_surname}) {$row.prod_id} - {$row.prod_name}</option>
        {/foreach}
    </select>*}

    <form action="{$conf->action_root}doneDeliveryForm">
        <br>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID produktu</th>
                        <th>Nazwa produktu</th>
                        <th style="width: 20%;">Ilość</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $prod_list as $row}
                    <tr>
                        <td><input type="hidden" name="product_id[]" value="{$row.prod_id}"><p>{$row.prod_id}</p></td>
                        <td>{$row.prod_name}</td>
                        <td><input class="form-control" type="number" name="quantity[]" placeholder="Ilość" value="0" min="0"></td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        
        <br>
        <div class="row">
            <div class="col-sm-12 text-right">
                <input type="hidden" name="supplier_id" value="{$supplier_id}">
                <input class="btn btn-action" type="submit" name="submit" value="☑︎︎ Przyjmij dostawę">
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