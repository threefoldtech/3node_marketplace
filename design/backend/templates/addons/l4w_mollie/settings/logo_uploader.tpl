{include file="common/subheader.tpl" title=__("text_mollie_logo_uploader") target="#mollie_logo_uploader"}
<div id="mollie_logo_uploader" class="in collapse{if !$runtime.company_id && !fn_allowed_for('MULTIVENDOR') && !$runtime.simple_ultimate} disable-overlay-wrap{/if}">
    {if !$runtime.company_id && !fn_allowed_for('MULTIVENDOR') && !$runtime.simple_ultimate}
    <div class="disable-overlay" id="mollie_logo_disable_overlay"></div>
    {/if}
    <div class="control-group">
        <label class="control-label" for="elm_mollie_logo">{__("l4w_mollie_logo")}:</label>
        <div class="controls">
            {include file="common/attach_images.tpl" image_name="l4w_mollie_logo" image_object_type="l4w_mollie_logo" image_pair=$mollie_settings.main_pair no_thumbnail=true}
            {if fn_allowed_for("ULTIMATE") && !$runtime.company_id}
            <div class="right update-for-all">
                {include file="buttons/update_for_all.tpl" display=true object_id="mollie_settings" name="mollie_settings[mollie_logo_update_all_vendors]" hide_element="mollie_logo_uploader"}
            </div>
            {/if}
        </div>
    </div>
</div>
<script type="text/javascript">
    Tygh.$(document).ready(function(){
    var $ = Tygh.$;
    $('.cm-update-for-all-icon[data-ca-hide-id=mollie_logo_uploader]').on('click', function() {
        $('#mollie_logo_uploader').toggleClass('disable-overlay-wrap');
        $('#mollie_logo_disable_overlay').toggleClass('disable-overlay');
    });
});
</script>
