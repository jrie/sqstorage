{include file="head.tpl" title=foo}
{include file="nav.tpl" title=foo}

        <div class="content">
            <div class="login-box">
                <div class="card">
                    <div class="card-body login-card-body">

                    {if isset($error)}
                    <div class="alert alert-danger">{$error}></div>
                    {/if}

                    <p class="login-box-msg">
                        {if($createFirstAdmin)}
                            {t}Neue Admin-Zugangsdaten eingeben{/t}
                        {else}
                            {if ($showActivation)}
                                {t}Neue Zugangsdaten eingeben{/t}
                            {else}
                                {t}Zugangsdaten eingeben{/t}</p>
                            {if}
                        {/if}
                    <form action="login.php<?php echo $showActivation ? '?activate='.@$_GET['activate'] : '' ?><?php echo $showRecover ? '?recover' : '' ?>" method="post">
                        <div class="input-group mb-3">
                            <input type="text" id="username" name="username" class="form-control" placeholder="<?php echo gettext('Benutzername'); ?>" value="<?php echo ($showActivation || $showRecover) ? ($_POST['username'] ?? @$user['username']) : ''; ?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <?php if ($showRecover || $createFirstAdmin) { ?>
                        <div class="input-group mb-3">
                            <input type="email" id="mailaddress" name="mailaddress" class="form-control" placeholder="<?php echo gettext('E-Mail'); ?>" value="<?php if(isset($_POST['mailaddress'])) echo $_POST['mailaddress']; ?>">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <?php if (!$showRecover) { ?>
                            <div class="input-group mb-3">
                                <input type="password" id="password" name="password" class="form-control" placeholder="<?php echo gettext('Passwort'); ?>" value="<?php echo $showActivation ? @$_POST['password'] : '' ?>">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <?php if ($showActivation) { ?>
                            <div class="input-group mb-3">
                                <input type="password" id="password_repeat" name="password_repeat" class="form-control" placeholder="<?php echo gettext('Passwort wiederholen'); ?>" value="<?php echo $showActivation ? @$_POST['password_repeat'] : '' ?>">
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-7">
                                    <div class="form-group form-check">
                                    <input type="checkbox" id="remember" class="form-check-input">
                                    <label for="remember" class="form-check-label">
                                        <?php echo gettext('Angemeldet bleiben?'); ?>
                                    </label>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo ($showActivation ? gettext('Speichern') : gettext('Anmelden')); ?></button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="row">
                                <div class="col-7">
                                </div>
                                <div class="col-5">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat"><?php echo gettext('Anfordern'); ?></button>
                                </div>
                            </div>
                        <?php } ?>
                    </form>
                    <?php if(!$showActivation && !$showRecover) { ?>
                    <p class="mb-1">
                        <a href="login.php?recover"><?php echo gettext('Zugangsdaten vergessen?'); ?></a>
                    </p>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>

{include file="footer.tpl"}

{include file="bodyend.tpl"}