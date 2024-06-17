{extends file="Main.tpl"}

{$card_title = 'Magazyn - nowa dostawa'}

{block name=content}
    <br><br><br>
    <div style="width: 90%; margin: 0 auto;">
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeEmployee>Strona główna</a></li>
            <li class="active">Wybór klienta</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Dostawa towarów</h1>
        </header>
        
        <form action={$conf->action_root}newDeliveryForm>           
            <div class="row">
                <div class="col-sm-3">
                <select class="form-control" id="supplier" name="supplier" required>
                    <option value="" disabled selected>Wybierz klienta</option>
                    {foreach $supplier_list as $row}
                        <option value="{$row.user_id}">{$row.user_id} - {$row.user_name_surname}</option>
                    {/foreach}
                </select>
            </div>               
                <div class="col-sm-1 text-right">
                    <input class="btn btn-action" type="submit" value="↪ Utwórz formularz">
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