{if $in_popup}
    <div class="adv-search">
    <div class="group">
{else}
    <div class="sidebar-row">
    <h6>{__("search")}</h6>
{/if}
<form action="{""|fn_url}" name="orders_search_form" method="get" class="{$form_meta}">
{capture name="simple_search"}

    {if $smarty.request.redirect_url}
    <input type="hidden" name="redirect_url" value="{$smarty.request.redirect_url}" />
    {/if}
    {if $selected_section != ""}
    <input type="hidden" id="selected_section" name="selected_section" value="{$selected_section}" />
    {/if}
    {$extra nofilter}
    <div class="sidebar-field">
        <label for="country">{__("country")}</label>
        <select id="country" name="country_code">
            <option value="">{__('all')}</option>
            {foreach from=fn_get_simple_countries() key=country_code item=country}
                <option value="{$country_code}" {if $search.country_code==$country_code}selected{/if}>{$country}</option>
            {/foreach}
        </select>
    </div>
    <div class="sidebar-field">
        <label for="language">{__("language")}</label>
         <select id="language" name="lang_code">
            <option value="">{__('all')}</option>
            {foreach from=$languages key=lang_code item=lang}
                <option value="{$lang.lang_code}" {if $search.lang_code eq $lang.lang_code}selected{/if}>{$lang.name}</option>
            {/foreach}
        </select>
    </div>
    <div class="sidebar-field">
        <label for="currency">{__("currency")}</label>
        <select id="currency" name="currency_code">
            <option value="">{__('all')}</option>
            {foreach from=$currencies key=currency_code item=currency}
                <option value="{$currency_code}" {if $search.currency_code eq $currency_code}selected{/if}>{$currency.description}</option>
            {/foreach}
        </select>
    </div>
{/capture}
{include file="common/advanced_search.tpl" simple_search=$smarty.capture.simple_search dispatch=$dispatch  no_adv_link=no_adv_link}
</form>