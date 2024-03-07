<form action="{$lottery_form_url}" method="post">
    <fieldset>
        <legend>Участвуйте в нашей лотерее!</legend>
        {if isset($lottery_result)}
            <p class="alert alert-success">{$lottery_result}</p>
        {else}
            <p>Нажмите кнопку ниже, чтобы узнать, выиграли ли вы.</p>
            <button type="submit" name="submit_lottery" class="btn btn-primary">Принять участие!</button>
        {/if}
    </fieldset>
</form>
