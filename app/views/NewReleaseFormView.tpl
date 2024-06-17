{extends file="Main.tpl"}

{$card_title = 'Magazyn - nowe wydanie'}

{block name=content}
    <br><br><br>
    <div style="width: 60%; margin: 0 auto;">
        <ol class="breadcrumb">
            <li><a href={$conf->action_root}homeClient>Strona główna</a></li>
            <li class="active">Wydanie zewnętrzne</li>
        </ol>
        
        <header class="page-header">
            <h1 class="page-title">Zleć przygotowanie towarów do odbioru</h1>
        </header>
        
    <form action="{$conf->action_root}doneReleaseForm">
        <br>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th style="width: 20%;">Ilość</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $prod_list as $row}
                    <tr>
                        <td>
                            <input type="hidden" name="product_id[]" value="{$row.prod_id}">
                            <p>{$row.prod_id} - {$row.prod_name}</p>
                        </td>
                        <td>
                            <input class="form-control" type="number" name="quantity[]" placeholder="Ilość" value="{$row.stock_count}" min="0" max="{$row.stock_count}">
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        
        <br>
        <div class="row">
            <div class="col-sm-9 col-sm-offset-5">
                <div class="row">
                    <div class="col-sm-6 text-right">
                        <label>Data odbioru *:</label>
                    </div>
                    <div class="col-sm-3">
                        <input class="form-control" type="date" name="release_date" placeholder="Data odbioru" value="" required>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-sm-3 col-sm-offset-9 text-right">
                <input type="hidden" name="supplier_id" value="{$supplier_id}">
                <input class="btn btn-action" type="submit" name="submit" value="☑︎︎ Zleć wydanie">
            </div>
        </div>

    </form>
</div>          
{/block}

{block name=operations}
    <h3 class="text-center thin">Dostępne operacje magazynowe:</h3>

    <div class="row">
        <div class="h-caption"><h4><i class="fa fa-cogs fa-5"></i> Zlecenie wydania towarów</h4></div>
    </div>
{/block}