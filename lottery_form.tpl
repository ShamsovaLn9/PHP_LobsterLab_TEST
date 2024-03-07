<form action="{$action}" method="post">
    <fieldset>
        <h3>Участвуйте в нашей лотерее!</h3>
        {if isset($lottery_result)}
            <p>{$lottery_result}</p>
        {else}
            <p>Нажмите кнопку ниже, чтобы узнать, выиграли ли вы.</p>
            <button type="submit" name="submit_lottery">Принять участие!</button>
        {/if}
    </fieldset>
</form>
