{extends file="Main.tpl"}

{$card_title = 'Magazyn - strona wylogowywania'}

{block name=content}       
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{$conf->action_root}home">Strona główna</a></li>
            <li class="active">Wylogowywanie</li>
        </ol>

        <div class="row">
            <article class="col-xs-12 maincontent">
                <header class="page-header"><h1 class="page-title"> </h1></header>               
                <div class="col-md-6 col-md-offset-3 col-sm-1 col-sm-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <h3>Zostałeś poprawnie wylogowany!</h3>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>     
{/block}