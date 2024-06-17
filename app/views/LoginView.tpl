{extends file="Main.tpl"}

{$card_title = 'Magazyn - strona logowania'}

{block name=content}       
    <div class="container">
        <ol class="breadcrumb">
            <li><a href="{$conf->action_root}home">Strona główna</a></li>
            <li class="active">Logowanie</li>
        </ol>

        <div class="row">
            <article class="col-xs-12 maincontent">
                <header class="page-header"> <h1 class="page-title">Zaloguj się</h1> </header>
                
                <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form action="{$conf->action_root}login" method="post">
                                <div class="top-margin">
                                    <label for="id_login">Login <span class="text-danger">*</span></label>
                                    <input class="form-control" id="id_login" type="text" name="login" value="{$form->login}"/>
                                </div>
                                <div class="top-margin">
                                    <label for="id_pass">Hasło <span class="text-danger">*</span></label>
                                    <input class="form-control" id="id_pass" type="password" name="pass" value="{$form->pass}"/>
                                </div>

                                <hr>

                                <div class="row">
                                    <div class="col-lg-4 text-right">
                                        <button class="btn btn-action" value="zaloguj" type="submit">Zaloguj</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>     
{/block}