{include file="header.tpl" TITLE="Home"}

<h1>Home</h1>

{if isset($MSG)}
    <span class="msg">{$MSG}</span>
    <br />
{/if}
<div class="left">
    <p>
        Welcome to ezRPG! Login now!
    </p>
</div>

<div class="right">
    <form method="post" action="index.php?mod=Login">
        <label for="username">Username</label>
        <input type="text" name="username" />

        <label for="password">Password</label>
        <input type="password" name="password" />
		
        <input name="login" type="submit" class="button" value="Login">
    </form>
</div>

{include file="footer.tpl"}