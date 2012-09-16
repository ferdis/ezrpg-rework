{include file="header.tpl" TITLE="Register"}

<h1>Register</h1>

<p>
Want to join the fun? Fill out the form below to register!
</p>

{if isset($MSG)}
    <span class="msg">{$MSG}</span>
    <br />
{/if}

<form method="POST" action="index.php?mod=Register">
    <table width="80%" align=center>
        <tr>
            <td valign=top>
                <label>Username</label>
                <input type="text" size="40" name="username"{if isset($GET_USERNAME)} value="{$GET_USERNAME}"{/if} zindex="1" />
                <label>Email</label>
                <input type="text" size="40" name="email"{if isset($GET_EMAIL)} value="{$GET_EMAIL}"{/if} zindex="2" />
            </td>
            <td valign=top>
                <label>Password</label>
                <input type="password" size="40" name="password" zindex="3" />

                <label>Verify Password</label>
                <input type="password" size="40" name="password2" zindex="4" />
            </td>
        </tr>
        <tr>
            <td colspan=2 align="center">
            <!--    <label>Enter The Code</label>
                <img src="./captcha.php" /><br />
                <input type="text" size="40" name="reg_verify" autocomplete="off" />
-->
                <br />
                <input name="register" type="submit" value="Register" class="button" zindex="5" />
            </td>
        </tr>
                
    </table>







</form>

{include file="footer.tpl"}