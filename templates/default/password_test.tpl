{include file="header.tpl" TITLE="Home"}

<h1>Password Test</h1>
<strong>with {$hash.times} loops</strong><br /><br />
<strong>With PBKDF2</strong>
<p>
    <i>call</i> createPBKDF2('ezRPG'): <var>{$hash.pbk_origin}</var>
    <br />createPBKDF2 took an average of {$hash.pbk_time} seconds.<br />
    <i>call</i> comparePBKDF2(<i>above</i>) returned {$hash.pbk_bool}
</p>
<hr />
<strong>With bcrypt</strong>
<p>
    <i>call</i> createBcrypt('ezRPG') - {$hash.bcr_origin}<br />
    createBcrypt took an average of {$hash.bcr_time} seconds.<br />
    <i>call</i> compareBcrypt(<i>above</i>) returned {$hash.bcr_bool}
</p>

{include file="footer.tpl"}