{if $smarty.capture.cp_type_select_showed != "Y"}
    {$show_type = "radio"}
    {$allow_change = $user_data|fn_cp_pt_check_allow_change:$runtime.controller}
    {assign var="r_url" value=$config.current_url|fn_query_remove:"cp_profile_type"}
    
    {if $smarty.session.cart.cp_profile_type && $is_checkout}
        {$cp_profile_type = $smarty.session.cart.cp_profile_type}
    {elseif $smarty.request.cp_profile_type}
        {$cp_profile_type = $smarty.request.cp_profile_type}
    {elseif $user_data.cp_profile_type}
        {$cp_profile_type = $user_data.cp_profile_type}
    {else}
        {$user_type = $user_type|default:$auth.user_type}
        {$cp_profile_type = $user_type|fn_cp_get_default_profile_type}
    {/if}
    
    {if $cp_profile_types}
    <div class="{if $is_checkout}litecheckout__group{else}ty-control-group{/if}">
        <div class="{if $is_checkout}litecheckout__field litecheckout__field--{if $show_type == "radio"}radio{else}input{/if}{/if}">
            <label class="{if $is_checkout}litecheckout__label{else}ty-control-group__title{/if} cm-required" for="elm_cp_profile_type">{__("cp_profile_type")}:</label>
            <div class="ty-controls">
                {if !$allow_change}
                    <span class="cp-profile-type ty-strong">{$cp_profile_types[$cp_profile_type]}</span>
                {else}
                    {assign var="r_url" value=$config.current_url|fn_query_remove:"cp_profile_type"}
                    {if $show_type == "radio"}
                        {foreach from=$cp_profile_types key="cp_type_id" item="cp_type_name" name="cp_profile_types"}
                            {$i = $smarty.foreach.cp_profile_types.iteration}
                            <label class="ty-inline" for="elm_cp_profile_type_select_{$i}">
                                <input class="radio {if $is_checkout}litecheckout__input{/if} cm-change-profile-type" type="radio" id="elm_cp_profile_type_select_{$i}" name="{if $is_checkout}cp_profile_type{else}{$input_name|default:user_data}[cp_profile_type]{/if}" value="{$cp_type_id}" {if $cp_profile_type == $cp_type_id}checked="checked"{/if} data-ca-target-url="{$r_url}" {if $target_id}data-ca-target-id="{$target_id}"{/if}>
                                <span class="radio">{$cp_type_name}</span>
                            </label>
                        {/foreach}
                    {else}
                        <select id="elm_cp_profile_type" class="{if $is_checkout}litecheckout__input{else}ty-input{/if} cm-change-profile-type" name="{if $is_checkout}cp_profile_type{else}{$input_name|default:user_data}[cp_profile_type]{/if}" data-ca-target-url="{$r_url}" {if $target_id}data-ca-target-id="{$target_id}"{/if}>
                            <option value="">{__("none")}</option>
                            {foreach from=$cp_profile_types key="cp_type_id" item="cp_type_name"}
                                <option value="{$cp_type_id}" {if $cp_profile_type == $cp_type_id}selected="selected"{/if}>{$cp_type_name}</option>
                            {/foreach}
                        </select>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
    {/if}
{/if}

{capture name="cp_type_select_showed"}Y{/capture}
